<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package    MetaModels
 * @subpackage Filtertimestamp
 * @author     Henry Lamorski <henry.lamorski@mailbox.org>
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Filter\Setting;

use MetaModels\Filter\IFilter;
use MetaModels\Filter\Rules\SimpleQuery;
use MetaModels\Filter\Rules\StaticIdList;
use MetaModels\FrontendIntegration\FrontendFilterOptions;


class Timestamp extends SimpleLookup
{
	


	protected function getParamName()
	{
		if ($this->get('urlparam'))
		{
			return $this->get('urlparam');
		}

		$objAttribute = $this->getMetaModel()->getAttributeById($this->get('attr_id'));
		if ($objAttribute)
		{
			return $objAttribute->getColName();
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function prepareRules(IFilter $objFilter, $arrFilterUrl)
	{
	
		$objMetaModel = $this->getMetaModel();
		$objAttribute = $objMetaModel->getAttributeById($this->get('attr_id')); 
		$objAttribute2 = $objMetaModel->getAttributeById($this->get('attr_id2')); 
		$strParamName = $this->getParamName(); 
		$strColname = $objAttribute->getColName(); 

		if (!$objAttribute2)
		{
			$objAttribute2 = $objAttribute;
		}

		$arrParamValue = NULL;
		if (array_key_exists($strParamName, $arrFilterUrl) && !empty($arrFilterUrl[$strParamName]))
		{
			if (is_array($arrFilterUrl[$strParamName]))
				$arrParamValue = $arrFilterUrl[$strParamName];
			else 
				$arrParamValue = explode('__', $arrFilterUrl[$strParamName]);
		}

		if ($objAttribute && $strParamName && $arrParamValue && ($arrParamValue[0] || $arrParamValue[1]))
		{
			$strMore = $this->get('moreequal') ? '>=' : '>';
			$strLess = $this->get('lessequal') ? '<=' : '<';

			$arrQuery = array();
			$arrParams = array();
	
			if($this->get('mode') == "groups")
			{
				if ($arrParamValue[0]) {
					$arrQuery[] = sprintf('(EXTRACT(YEAR_MONTH FROM FROM_UNIXTIME(%s)) %s EXTRACT(YEAR_MONTH FROM FROM_UNIXTIME(?)))', $objAttribute->getColName(), $strLess);
					$arrParams[] = $arrParamValue[0];
	
					if($this->get('tofield'))
					{
						$arrQuery[] = sprintf('(EXTRACT(YEAR_MONTH FROM FROM_UNIXTIME(%s)) %s EXTRACT(YEAR_MONTH FROM FROM_UNIXTIME(?)))', $objAttribute2->getColName(), $strMore);
						$arrParams[] = $arrParamValue[0];
					}
				}
			}

			if($this->get('mode') == "datepicker")
			{
				if ($arrParamValue[0]) {
					// timestamp aus date
					$objDate = new \Date($arrParamValue[0], $GLOBALS['TL_CONFIG']['dateFormat']); 
					$arrQuery[] = sprintf("(%s%s?)", $objAttribute->getColName(), $strMore);						
					$arrParams[] = $objDate->tstamp;
				}

				if ($arrParamValue[1]) {
					// timestamp aus date
					$objDate = new \Date($arrParamValue[1], $GLOBALS['TL_CONFIG']['dateFormat']); 
					$arrQuery[] = sprintf("(%s%s?)", $objAttribute2->getColName(), $strLess);						
					$arrParams[] = $objDate->tstamp;
				}
			}


		
			// $arrQuery: (reisedatum_von>=?)
			// SELECT id FROM mm_wricke_touristik WHERE (reisedatum_von>=?)
			// $arrParams: 1433116800

			$objFilter->addFilterRule(new SimpleQuery(
				sprintf('SELECT id FROM %s WHERE ', $this->getMetaModel()->getTableName()) . implode(' AND ', $arrQuery), $arrParams));
			return;
		} 
		$objFilter->addFilterRule(new StaticIdList(NULL));
	}

	
	/**
	 * {@inheritdoc}
	 */
	public function getParameterFilterWidgets($arrIds, $arrFilterUrl, $arrJumpTo, FrontendFilterOptions $objFrontendFilterOptions)
	{


		$objAttribute = $this->getMetaModel()->getAttributeById($this->get('attr_id'));
		$objAttribute2 = $this->getMetaModel()->getAttributeById($this->get('attr_id2'));

			

		if($this->get('mode') == "groups")
		{
			$arrOptions = array();

		
			foreach ($arrIds as $strId)
			{
				$objMm = $this->getMetaModel()->findById($strId);

				$start= $objMm->get($objAttribute->getColName());
	
				if(!$objAttribute2) {
					$arrOptions[mktime(0,0,0,date("n",$start),1,date("Y",$start))] = date($this->get("dateFormatPattern"),mktime(0,0,0,date("n",$start),1,date("Y",$start)));
					continue;
				}

				$end  = $objMm->get($objAttribute2->getColName());
	
				for($i=$start;$i<$end;$i=mktime(0,0,0,date("n",$i)+1,1,date("Y",$i)))
					$arrOptions[mktime(0,0,0,date("n",$i),1,date("Y",$i))] = date($this->get("dateFormatPattern"),mktime(0,0,0,date("n",$i),1,date("Y",$i)));
			}
			ksort($arrOptions);


			// Remove empty values from list.
			foreach ($arrOptions as $mixKeyOption => $mixOption)
			{
			// Remove html/php tags.
				$mixOption = strip_tags($mixOption);
				$mixOption = trim($mixOption);
	
				if($mixOption === '' || $mixOption === null)
				{
					unset($arrOptions[$mixKeyOption]);
				}
			}
	
		}

		$arrLabel = array(
			($this->get('label') ? $this->get('label') : $objAttribute->getName()),
			'GET: '.$this->get('urlparam')
		);



		$arrUrlValue = $arrFilterUrl[$this->getParamName()];

		// split up our param so the widgets can use it again.
		$strParamName = $this->getParamName();
		$arrMyFilterUrl = $arrFilterUrl;
		// if we have a value, we have to explode it by double underscore to have a valid value which the active checks may cope with.
		if (array_key_exists($strParamName, $arrFilterUrl) && !empty($arrFilterUrl[$strParamName]))
		{
			if (is_array($arrFilterUrl[$strParamName]))
			{
				$arrParamValue = $arrFilterUrl[$strParamName];
			} else {
				// TODO: still unsure if double underscore is such a wise idea.
				$arrParamValue = explode('__', $arrFilterUrl[$strParamName], 2);
			}

			if ($arrParamValue && ($arrParamValue[0] || $arrParamValue[1]))
			{
				$arrMyFilterUrl[$strParamName] = $arrParamValue;
			} else {
				// no values given, clear the array.
				$arrParamValue = NULL;
			}
		}

		$GLOBALS['MM_FILTER_PARAMS'][] = $this->getParamName();

		if($this->get('mode') == "groups")
		{
			return array(
				$this->getParamName() => $this->prepareFrontendFilterWidget(
					array
					(
						'label'     => $arrLabel,
						'inputType' => 'select',
						'options'   => $arrOptions,
						'eval'      => array
						(
							'urlparam'  => $this->get('urlparam'),
							'includeBlankOption' => ($this->get('blankoption') && !$objFrontendFilterOptions->isHideClearFilter() ? true : false),
							'blankOptionLabel'   => &$GLOBALS['TL_LANG']['metamodels_frontendfilter']['do_not_filter'],
							'colname'      => $objAttribute->getColname(),
							'onlypossible' => 0,
							'template'  => $this->get('template'),
						),
						'urlvalue' => !empty($arrParamValue) ? implode(',', $arrParamValue) : ''
						),
					$arrMyFilterUrl,
					$arrJumpTo,
					$objFrontendFilterOptions
				)
			);
		}

		if($this->get('mode') == "datepicker")
		{

			return array(
				$this->getParamName() => $this->prepareFrontendFilterWidget(
					array
					(
						'label'     => $arrLabel,
						'inputType' => 'multical',
						'options'   => $arrOptions,
						'eval'      => array
						(
							'multiple'  => true,
							'size'      => ($this->get('fromfield') && $this->get('tofield') ? 2 : 1),
							'dateImage' => 1,
							'dateFormat' => $GLOBALS['TL_CONFIG']['dateFormat'],
							'urlparam'  => $this->get('urlparam'),
							'template'  => $this->get('template'),
							'placeholder' => array(0=>$this->get('placeholderAttr1'),1=>$this->get('placeholderAttr2'))
						),
						'urlvalue' => !empty($arrParamValue) ? implode('__', $arrParamValue) : ''
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
		if (!($this->get('attr_id') && ($objAttribute = $this->getMetaModel()->getAttributeById($this->get('attr_id')))))
		{
			return array();
		}

		return array($objAttribute->getColName());
	}
}
