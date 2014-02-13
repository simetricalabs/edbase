<?php
/**
 * @version	$Id: mod_mt_filter.php 1281 2011-12-02 10:24:06Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2011-2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

require( JPATH_ROOT . DS.'components'.DS.'com_mtree'.DS.'init.module.php');
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mtree'.DS.'mfields.class.php' );
require_once( dirname(__FILE__).DS.'helper.php' );

if( !$moduleHelper->isModuleShown() ) { return; }

$moduleclass_sfx= $params->get( 'moduleclass_sfx' );
$filter_button	= intval( $params->get( 'filter_button', 1 ) );
$reset_button	= intval( $params->get( 'reset_button', 1 ) );
$cat_id		= intval( $params->get( 'cat_id', 0 ) );
$cf_ids		= $params->get( 'fields' );
$itemid		= MTModuleHelper::getItemid();
$intItemid	= str_replace('&Itemid=','',$itemid);

$db 		= JFactory::getDBO();
$post 		= JRequest::get( 'request' );
$search_params	= $post;

# Load all CORE and custom fields
$db->setQuery( "SELECT cf.*, '0' AS link_id, '' AS value, '0' AS attachment, '".$cat_id."' AS cat_id FROM #__mt_customfields AS cf "
	.	"\nWHERE cf.hidden ='0' AND cf.published='1' && filter_search = '1'"
	.	((!empty($cf_ids))?"\nAND cf.cf_id IN (" . implode(',',$cf_ids). ") ":'')
	.	" ORDER BY ordering ASC" 
	);
$filter_fields = new mFields($db->loadObjectList());
$searchParams = $filter_fields->loadSearchParams($search_params);
$hasSearchParams = true;

global $mtconf;
if( $mtconf->get('load_js_framework_frontend') && !JFactory::getApplication()->get('jquery') )
{
	$document	=& JFactory::getDocument();
	$document->addCustomTag(' <script src="'.$mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js_library') . '" type="text/javascript"></script>');
	JFactory::getApplication()->set('jquery',true);
}

require(JModuleHelper::getLayoutPath('mod_mt_filter'));
?>