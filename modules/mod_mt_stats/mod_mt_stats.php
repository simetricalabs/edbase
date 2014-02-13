<?php
/**
 * @version	$Id: mod_mt_stats.php 1621 2012-11-08 07:26:35Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

require( JPATH_ROOT . DS.'components'.DS.'com_mtree'.DS.'init.module.php');
require_once( dirname(__FILE__).DS.'helper.php' );

if( !$moduleHelper->isModuleShown() ) { return; }

# Get params
$moduleclass_sfx 	= $params->get( 'moduleclass_sfx' );
$cache			= $params->get( 'cache', 1 );

$cache 			=& JFactory::getCache('mod_mt_stats');
$total_links		= $cache->call(array('modMTStatsHelper','getTotalLinks'));
$total_categories	= $cache->call(array('modMTStatsHelper','getTotalCategories'));

require(JModuleHelper::getLayoutPath('mod_mt_stats'));