<?php
/**
 * @version	$Id: init.php 1614 2012-10-27 03:49:35Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die;

global $mtconf;

if(!isset($mtconf))
{
	if( !isset($database) )
	{
		$database = JFactory::getDBO();
	}
	
	require( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_mtree'.DS.'config.mtree.class.php');
	$mtconf	= new mtConfig($database);

}

$cat_id	= JRequest::getInt('cat_id', 0);
if( $cat_id != 0 )
{ 
	$mtconf->setCategory($cat_id);
}
?>