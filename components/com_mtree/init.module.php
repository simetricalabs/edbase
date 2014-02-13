<?php
/**
 * @version	$Id: init.module.php 1717 2012-12-29 08:36:14Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die;

require( JPATH_ROOT . DS.'components'.DS.'com_mtree'.DS.'init.php');
require_once( JPATH_ROOT . DS.'components'.DS.'com_mtree'.DS.'modulehelper.php');

$moduleHelper = new MTModuleHelper;

$moduleHelper->setMtConf($mtconf);
if( isset($params) )
{
	$moduleHelper->setParams($params);
}
?>