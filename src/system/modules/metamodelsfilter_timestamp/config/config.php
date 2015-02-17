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
 * Frontend widgets
 */
$GLOBALS['TL_FFL']['multical'] = 'MetaModels\Filter\Widgets\MultiCalendarWidget';

/**
 * Frontend filter
 */
$GLOBALS['METAMODELS']['filters']['timestamp']['class'] = 'MetaModels\Filter\Setting\Timestamp';
$GLOBALS['METAMODELS']['filters']['timestamp']['image'] = 'system/modules/metamodelsfilter_timestamp/html/filter_timestamp.png';
$GLOBALS['METAMODELS']['filters']['timestamp']['info_callback'] = array('MetaModels\Dca\Filter', 'infoCallback');
$GLOBALS['METAMODELS']['filters']['timestamp']['attr_filter'][] = 'timestamp';

// non composerized Contao 2.X autoload support.
$GLOBALS['MM_AUTOLOAD'][] = dirname(__DIR__);
$GLOBALS['MM_AUTOLOAD'][] = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'deprecated';
