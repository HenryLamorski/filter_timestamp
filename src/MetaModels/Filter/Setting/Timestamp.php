<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 *
 * @package    MetaModels
 * @subpackage Filtertimestamp
 * @author     Henry Lamorski <henry.lamorski@mailbox.org>
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Filter\Setting;

use MetaModels\Attribute\IAttribute;
use MetaModels\Filter\IFilter;
use MetaModels\Filter\Rules\Comparing\GreaterThan;
use MetaModels\Filter\Rules\Comparing\LessThan;
use MetaModels\Filter\Rules\SimpleQuery;
use MetaModels\Filter\Rules\StaticIdList;
use MetaModels\FrontendIntegration\FrontendFilterOptions;

/**
 * Timestamp filter setting.
 */
class Timestamp extends SimpleLookup
{
    /**
     * {@inheritDoc}
     */
    protected function getParamName()
    {
        if ($this->get('urlparam')) {
            return $this->get('urlparam');
        }

        $objAttribute = $this->getMetaModel()->getAttributeById($this->get('attr_id'));
        if ($objAttribute) {
            return $objAttribute->getColName();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareRules(IFilter $objFilter, $arrFilterUrl)
    {
		
        $objMetaModel  = $this->getMetaModel();
        $objAttribute  = $objMetaModel->getAttributeById($this->get('attr_id'));
        $objAttribute2 = $objMetaModel->getAttributeById($this->get('attr_id2'));
        $strParamName  = $this->getParamName();

        if (!$objAttribute2) {
            $objAttribute2 = $objAttribute;
        }
        
       
        $arrParamValue = null;
        if (array_key_exists($strParamName, $arrFilterUrl) && !empty($arrFilterUrl[$strParamName])) {
            if (is_array($arrFilterUrl[$strParamName])) {
                $arrParamValue = $arrFilterUrl[$strParamName];
            } else {
                $arrParamValue = explode('__', $arrFilterUrl[$strParamName]);
            }
        }

		if ($objAttribute && $strParamName && $arrParamValue && ($arrParamValue[0] || $arrParamValue[1])) {
            if ($this->get('mode') == 'groups') {
                $this->handleGroupMode($objFilter, $arrParamValue, $objAttribute, $objAttribute2);
            }

            if ($this->get('mode') == 'datepicker') {
                $this->handleDatePickerMode($objFilter, $arrParamValue, $objAttribute, $objAttribute2);
            }

            return;
        }

        $objFilter->addFilterRule(new StaticIdList(null));
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function getParameterFilterWidgets(
        $arrIds,
        $arrFilterUrl,
        $arrJumpTo,
        FrontendFilterOptions $objFrontendFilterOptions
    ) {
		
		if(
			isset($GLOBALS['TL_CONFIG']['locale'])
			&& $GLOBALS['TL_CONFIG']['locale']
		) {
			setlocale(LC_TIME, $GLOBALS['TL_CONFIG']['locale']);
		}
		
        $objAttribute  = $this->getMetaModel()->getAttributeById($this->get('attr_id'));
        $objAttribute2 = $this->getMetaModel()->getAttributeById($this->get('attr_id2'));

        $arrOptions = array();
        if ($this->get('mode') == 'groups') {
            foreach ($arrIds as $strId) {
                $objMm = $this->getMetaModel()->findById($strId);

                $start = $objMm->get($objAttribute->getColName());

                if (!$objAttribute2) {
                    $arrOptions[mktime(0, 0, 0, date('n', $start), 1, date('Y', $start))] = strftime(
                        $this->get('dateFormatPattern'),
                        mktime(0, 0, 0, date('n', $start), 1, date('Y', $start))
                    );
                    continue;
                }

                $end = $objMm->get($objAttribute2->getColName());

                for ($i = $start; $i < $end; $i = mktime(0, 0, 0, (date('n', $i) + 1), 1, date('Y', $i))) {
                    $arrOptions[mktime(0, 0, 0, date('n', $i), 1, date('Y', $i))] = strftime(
                        $this->get('dateFormatPattern'),
                        mktime(0, 0, 0, date('n', $i), 1, date('Y', $i))
                    );
                }
            }
            ksort($arrOptions);

            // Remove empty values from list.
            foreach ($arrOptions as $mixKeyOption => $mixOption) {
                // Remove html/php tags.
                $mixOption = strip_tags($mixOption);
                $mixOption = trim($mixOption);

                if ($mixOption === '' || $mixOption === null) {
                    unset($arrOptions[$mixKeyOption]);
                }
            }
        }

        $arrLabel = array(
            ($this->get('label') ? $this->get('label') : $objAttribute->getName()),
            'GET: ' . $this->get('urlparam')
        );

        // split up our param so the widgets can use it again.
        $strParamName   = $this->getParamName();
        $arrMyFilterUrl = $arrFilterUrl;
        // If we have a value, we have to explode it by double underscore to have a valid value which the active checks
        // may cope with.
        if (array_key_exists($strParamName, $arrFilterUrl) && !empty($arrFilterUrl[$strParamName])) {
            if (is_array($arrFilterUrl[$strParamName])) {
                $arrParamValue = $arrFilterUrl[$strParamName];
            } else {
                // TODO: still unsure if double underscore is such a wise idea.
                $arrParamValue = explode('__', $arrFilterUrl[$strParamName], 2);
            }

            if ($arrParamValue && ($arrParamValue[0] || $arrParamValue[1])) {
                $arrMyFilterUrl[$strParamName] = $arrParamValue;
            } else {
                // no values given, clear the array.
                $arrParamValue = null;
            }
        }

        $GLOBALS['MM_FILTER_PARAMS'][] = $this->getParamName();

        if ($this->get('mode') == 'groups') {
            return array(
                $this->getParamName() => $this->prepareFrontendFilterWidget(
                    array
                    (
                        'label'     => $arrLabel,
                        'inputType' => 'select',
                        'options'   => $arrOptions,
                        'eval'      => array
                        (
                            'urlparam'           => $this->get('urlparam'),
                            'includeBlankOption' => ($this->get(
                                'blankoption'
                            ) && !$objFrontendFilterOptions->isHideClearFilter() ? true : false),
                            'blankOptionLabel'   => &$GLOBALS['TL_LANG']['metamodels_frontendfilter']['do_not_filter'],
                            'colname'            => $objAttribute->getColname(),
                            'onlypossible'       => 0,
                            'template'           => $this->get('template'),
                        ),
                        'urlvalue'  => !empty($arrParamValue) ? implode(',', $arrParamValue) : ''
                    ),
                    $arrMyFilterUrl,
                    $arrJumpTo,
                    $objFrontendFilterOptions
                )
            );
        }

        if ($this->get('mode') == 'datepicker') {

            return array(
                $this->getParamName() => $this->prepareFrontendFilterWidget(
                    array
                    (
                        'label'     => $arrLabel,
                        'inputType' => 'multical',
                        'options'   => $arrOptions,
                        'eval'      => array
                        (
                            'multiple'    => true,
                            'size'        => ($this->get('fromfield') && $this->get('tofield') ? 2 : 1),
                            'dateImage'   => 0,
                            'dateFormat'  => $GLOBALS['TL_CONFIG']['dateFormat'],
                            'urlparam'    => $this->get('urlparam'),
                            'template'    => $this->get('template'),
                            'placeholder' => array(
                                0 => $this->get('placeholderAttr1'),
                                1 => $this->get('placeholderAttr2')
                            )
                        ),
                        'urlvalue'  => !empty($arrParamValue) ? implode('__', $arrParamValue) : ''
                    ),
                    $arrMyFilterUrl,
                    $arrJumpTo,
                    $objFrontendFilterOptions
                )
            );
        }
    }

    /**
     * Retrieve the attribute name that is referenced in this filter setting.
     *
     * @return array
     */
    public function getReferencedAttributes()
    {
        if (!(
            $this->get('attr_id')
            && ($objAttribute = $this->getMetaModel()->getAttributeById($this->get('attr_id')))
        )) {
            return array();
        }

        return array($objAttribute->getColName());
    }

    /**
     * Create the filter rules for "datepicker" mode.
     *
     * @param IFilter    $filter     The filter to add the rules to.
     *
     * @param array      $value      The filter values.
     *
     * @param IAttribute $attribute  The first attribute.
     *
     * @param IAttribute $attribute2 The second attribute.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    private function handleDatePickerMode(IFilter $filter, $value, $attribute, $attribute2)
    {

		if(!$value[0])
			return;

		if (!isset($value[1])) {
			$value[1] = $value[0];
		}

		// timestamp aus date
		$objDate1 = new \Date($value[0], $GLOBALS['TL_CONFIG']['dateFormat']);
		$objDate2 = new \Date($value[1], $GLOBALS['TL_CONFIG']['dateFormat']);

		$strMore = $this->get('moreequal') ? '>=' : '>';
		$strLess = $this->get('lessequal') ? '<=' : '<';

		$arrQuery = array();
		$arrParams = array();

		if ($this->get('searchMode') == "fromto") {
			$filter->addFilterRule(
				new LessThan($attribute, $objDate1->tstamp, (bool) $this->get('moreequal'))
            );

			$filter->addFilterRule(
				new GreaterThan($attribute2, $objDate2->tstamp, (bool) $this->get('lessequal'))
			);

		} else {
			// Rangequery
			/*
			$arrQuery[] = sprintf("(((%s%s? AND %s%s?) OR (%s%s? AND %s%s?)) OR %s=0 OR %s=0)", $attribute->getColName(),$strMore, $attribute->getColName(), $strLess,$attribute2->getColName(),$strMore,$attribute2->getColName(),$strLess,$attribute->getColName(),$attribute2->getColName());							
			$arrParams[] = $objDate1->tstamp;
			$arrParams[] = $objDate2->tstamp;
			$arrParams[] = $objDate1->tstamp;
			$arrParams[] = $objDate2->tstamp;
			*/
			
			$arrQuery[] = sprintf(
				"%s %s ? OR %s %s ? OR %s=0 OR %s=0",
				$attribute->getColName(),
				$strLess,
				$attribute2->getColName(),
				$strMore,
				$attribute->getColName(),
				$attribute2->getColName()
			);							
			
			$arrParams[] = $objDate1->tstamp;
			$arrParams[] = $objDate2->tstamp;
			
			$arrParams[] = $attribute->getColName();
			$arrParams[] = $attribute2->getColName();


			$filter->addFilterRule(
				new SimpleQuery(
					sprintf(
						'SELECT id FROM %s WHERE ',
						$this->getMetaModel()->getTableName()
					) . 
					implode(
						' AND ',
						$arrQuery
					), 
					$arrParams
				)
			);

		}
    }

    /**
     * Create the filter rules for "group" mode.
     *
     * @param IFilter    $filter     The filter to add the rules to.
     *
     * @param array      $value      The filter values.
     *
     * @param IAttribute $attribute  The first attribute.
     *
     * @param IAttribute $attribute2 The second attribute.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    private function handleGroupMode(IFilter $filter, $value, $attribute, $attribute2)
    {
        if (empty($value[0])) {
            return;
        }

		$strMore = $this->get('moreequal') ? '>=' : '>';
		$strLess = $this->get('lessequal') ? '<=' : '<';

		$arrQuery = array();
		$arrParams = array();

		$arrQuery[] = sprintf('(EXTRACT(YEAR_MONTH FROM FROM_UNIXTIME(%s)) %s EXTRACT(YEAR_MONTH FROM FROM_UNIXTIME(?)))', $attribute->getColName(), $strLess);
		$arrParams[] = $value[0];
	
		if ($this->get('tofield')) {
			$arrQuery[] = sprintf('(EXTRACT(YEAR_MONTH FROM FROM_UNIXTIME(%s)) %s EXTRACT(YEAR_MONTH FROM FROM_UNIXTIME(?)))', $attribute2->getColName(), $strMore);
			$arrParams[] = $value[0];
		}

		$filter->addFilterRule(
			new SimpleQuery(
				sprintf('SELECT id FROM %s WHERE ', $this->getMetaModel()->getTableName()) . implode(' AND ', $arrQuery), $arrParams)
		);
		/*
        $date = new \Date($value[0]);
        $filter->addFilterRule(
            new LessThan($attribute, $date->monthBegin, (bool) $this->get('lessequal'))
        );

        if ($this->get('tofield')) {
            $filter->addFilterRule(
                new GreaterThan($attribute2, $date->monthEnd, (bool) $this->get('moreequal'))
            );
        }
		*/
    }
}
