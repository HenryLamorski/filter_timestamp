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

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'MetaModels\Filter\Setting\Timestamp' => 'system/modules/metamodelsfilter_timestamp/MetaModels/Filter/Setting/Timestamp.php',
	'MetaModels\Filter\Widgets\MultiCalendarWidget' => 'system/modules/metamodelsfilter_timestamp/MetaModels/Filter/Widgets/MultiCalendarWidget.php',

	'MetaModelFilterSettingTimestamp'     => 'system/modules/metamodelsfilter_timestamp/deprecated/MetaModelFilterSettingTimestamp.php',
));
