<?php
/**
 * @version	$Id: mod_mt_lastupdate.php 1621 2012-11-08 07:26:35Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

require( JPATH_ROOT . DS.'components'.DS.'com_mtree'.DS.'init.module.php');
require_once (dirname(__FILE__).DS.'helper.php');

if( !$moduleHelper->isModuleShown() ) { return; }

# Get params
$moduleclass_sfx	= $params->get( 'moduleclass_sfx' );
$caption		= $params->get( 'caption', '%s' );

$last_update		= modMTLastupdateHelper::getLastUpdate( $params );

require(JModuleHelper::getLayoutPath('mod_mt_lastupdate'));