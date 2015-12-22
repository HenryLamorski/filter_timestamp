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
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @license    LGPL.
 * @filesource
 */

/**
 * palettes
 */
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['timestamp extends _attribute_']['+config'][] = 'attr_id2';

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['timestamp extends _attribute_']['+config'][] = 'placeholderAttr1';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['timestamp extends _attribute_']['+config'][] = 'placeholderAttr2';

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['timestamp extends _attribute_']['+config'][] = 'urlparam';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['timestamp extends _attribute_']['+fefilter'][] = 'label';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['timestamp extends _attribute_']['+fefilter'][] = 'template';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['timestamp extends _attribute_']['+fefilter'][] = 'moreequal';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['timestamp extends _attribute_']['+fefilter'][] = 'lessequal';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['timestamp extends _attribute_']['+fefilter'][] = 'fromfield';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['timestamp extends _attribute_']['+fefilter'][] = 'tofield';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['timestamp extends _attribute_']['+config'][] = 'mode';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['timestamp extends _attribute_']['+config'][] = 'dateFormatPattern';




/**
 * fields
 */
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['dateFormatPattern'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['dateFormatPattern'],
    'exclude'                 => true,
    'inputType'               => 'text',
    'eval'                    => array
    (
        'tl_class'            => 'w50'
    )
);

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['placeholderAttr1'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['placeholderAttr1'],
    'exclude'                 => true,
    'inputType'               => 'text',
    'eval'                    => array
    (
        'tl_class'            => 'w50'
    )
);
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['placeholderAttr2'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['placeholderAttr2'],
    'exclude'                 => true,
    'inputType'               => 'text',
    'eval'                    => array
    (
        'tl_class'            => 'w50'
    )
);


$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['moreequal'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['moreequal'],
    'exclude'                 => true,
    'default'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array
    (
        'tl_class'            => 'w50'
    )
);

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['lessequal'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['lessequal'],
    'exclude'                 => true,
    'default'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array
    (
        'tl_class'            => 'w50'
    )
);

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['fromfield'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['fromfield'],
    'exclude'                 => true,
    'default'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array
    (
        'tl_class'            => 'w50 clr'
    )
);

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['tofield'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['tofield'],
    'exclude'                 => true,
    'default'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array
    (
        'tl_class'            => 'w50'
    )
);

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['attr_id2'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['attr_id2'],
    'exclude'                 => true,
    'inputType'               => 'select',
    'eval'                    => array
    (
        'doNotSaveEmpty'      => false,
        'alwaysSave'          => true,
        'submitOnChange'      => true,
        'includeBlankOption'  => true,
        'tl_class'            => 'w50',
    ),
);

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['mode'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['mode'],
    'exclude'                 => true,
    'inputType'               => 'select',
    'options'          => array('datepicker' => 'datepicker', 'groups' => 'groups'),
    'eval'                    => array
    (
        'doNotSaveEmpty'      => true,
        'alwaysSave'          => true,
        'submitOnChange'      => true,
        'includeBlankOption'  => false,
        'tl_class'            => 'w50',
    ),
);
