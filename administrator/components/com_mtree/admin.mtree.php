<?php
/**
 * @version	$Id: admin.mtree.php 1805 2013-02-19 03:24:19Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

require_once(  JPATH_COMPONENT.DS.'config.mtree.class.php' );
global $mtconf;
$database =& JFactory::getDBO();
$mtconf = new mtConfig($database);

// global $task;
$task = JRequest::getCmd( 'task', '');
$option = JRequest::getCmd( 'option', 'com_mtree');
$format = JRequest::getCmd( 'format', '');

if($task != 'upgrade')
{
	require_once( JPATH_COMPONENT.DS.'admin.mtree.html.php' );
	require_once( JPATH_COMPONENT.DS.'admin.mtree.class.php' );
	require_once( JPATH_COMPONENT.DS.'tools.mtree.php' );
	require_once( JPATH_COMPONENT.DS.'mfields.class.php' );
	DEFINE( '_E_START_PUB', JText::_( 'COM_MTREE_START_PUBLISHING' ) );
	DEFINE( '_E_FINISH_PUB', JText::_( 'COM_MTREE_FINISH_PUBLISHING' ) );
	jimport('joomla.filesystem.file');
}

# Cache
$cache = &JFactory::getCache('com_mtree');

# Categories name cache
$cache_cat_names = array();

$id	= JRequest::getInt('id', 0);
$show	= JRequest::getCmd( 'show', '');

/* Cat ID 
 * Categories selected in category list
 */
$cat_id_fromurl	= JRequest::getInt('cat_id', 0);
if ($cat_id_fromurl == 0) {
	$cat_id = JRequest::getVar( 'cid', array(0), 'post');
	JArrayHelper::toInteger($cat_id, array(0));
} else {
	$cat_id = array( $cat_id_fromurl );
}

if( isset($cat_id) && $cat_id > 0 ) {
	$mtconf->setCategory( $cat_id[0] );
}

/* 
 * Link ID 
 * Listings selected in listing list
 */
$link_id_fromurl = JRequest::getInt('link_id', '');
if ($link_id_fromurl == '') {
	$link_id = JRequest::getVar('lid', array(), 'post');
	JArrayHelper::toInteger($link_id, array());
} else {
	$link_id = array( $link_id_fromurl );
}

/* Review ID */
$rev_id	= JRequest::getVar('rid', array(), 'post');
JArrayHelper::toInteger($rev_id, array());
if( empty($rev_id[0]) ) {
	$rev_id[0] = JRequest::getInt('rid', 0);
}

/* Custom Field ID */
$cf_id	= JRequest::getVar('cfid', array(), 'post');
JArrayHelper::toInteger($cf_id, array());
if( empty($cf_id[0]) ) {
	$cf_id[0] = JRequest::getInt('cfid', 0);
}

$cat_parent	= JRequest::getInt('cat_parent', 0);

/* Hide menu */
$hide_menu = JRequest::getInt('hide', 0);

/* Get Category ID for the Add Category/Listing links */
if ($task == 'newlink' || $task == 'newcat') {
	$parent_cat	= JRequest::getInt('cat_parent', 0);
} else {
	$parent_cat	= JRequest::getInt('cat_id', 0);
}

/* Start Left Navigation Menu */
if ( 
	$format != 'json'
	&&
	!$hide_menu 
	&& 
	!in_array($task,array('upgrade','spy','ajax', 'manageftattachments','editcat','editlink','newlink','editlink_for_approval','rebuild_thumbnails','import_images')) ) {
	HTML_mtree::print_startmenu( $task, $parent_cat );
}

switch ($task) {
	/***
	 * Ajax event
	 */
	 case 'ajax':
		 require_once($mtconf->getjconf('absolute_path') . '/administrator/components/com_mtree/admin.mtree.ajax.php');
		break;
	/***
	 * Spy
	 */
	 case 'spy':
		require_once($mtconf->getjconf('absolute_path') . '/administrator/components/com_mtree/spy.mtree.php');
		break;
	/***
	* Link Checker
	*/
	case 'linkchecker':
		require_once($mtconf->getjconf('absolute_path') . '/administrator/components/com_mtree/linkchecker.mtree.php');
		break;

	/***
	* Tools
	*/
	case 'tools':
		tools( $option );
		break;

	/***
	* Custom Fields
	*/
	case 'customfields':
	case "newcf":
	case "editcf":
	case "savecf":
	case "applycf":
	case 'cf_orderup':
	case 'cf_orderdown':
	case 'cancelcf':
	case 'cf_unpublish':
	case 'cf_publish':
	case 'removecf':
	case 'managefieldtypes':
		require_once($mtconf->getjconf('absolute_path') . '/administrator/components/com_mtree/customfields.mtree.php');
		switch( $task ) {
			case "newcf":
				editcf( 0, $option );
				break;
			case "editcf":
				editcf( $cf_id[0], $option );
				break;
			case "applycf":
			case "savecf":
				savecf( $option );
				break;
			case 'cf_orderup':
				ordercf( intval( $cf_id[0] ), -1, $option );
				break;
			case 'cf_orderdown':
				ordercf( intval( $cf_id[0] ), 1, $option );
				break;
			case 'cancelcf':
				cancelcf( $option );
				break;
			case 'cf_unpublish':
				cf_publish( $cf_id, 0, $option );
				break;
			case 'cf_publish':
				cf_publish( $cf_id, 1, $option );
				break;
			case 'removecf':
				removecf( $cf_id, $option );
				break;	
			case 'customfields':
				customfields( $option );
				break;	
			case 'managefieldtypes':
				managefieldtypes( $option );
				break;
		}
		break;

	/***
	 * Categories
	 */
	case "listcats":
		listcats( $cat_id[0], $cat_parent, $option );
		break;
	case "newcat":
		editcat( 0, $cat_parent, $show, $option );
		break;
	case "editcat":
		editcat( $cat_id[0], $cat_parent, $show, $option );
		break;
	case "editcat_browse_cat":
		editcat_browse_cat( $option, 0 );
		break;
	case "editcat_add_relcat":
		editcat_browse_cat( $option, 1 );
		break;
	case "editcat_remove_relcat":
		editcat_browse_cat( $option, -1 );
		break;
	case "applycat":
	case "savecat":
		$cache->clean();
		savecat( $option );
		break;
	case "cat_publish":
		$cache->clean();
		publishCats( $cat_id, 1, $option );
		break;
	case "cat_unpublish":
	$cache->clean();
		publishCats( $cat_id, 0, $option );
		break;
	case "cancelcat":
		cancelcat( $cat_parent, $option );
		break;
	case "removecats":
		$cache->clean();
		removecats( $cat_id, $option );
		break;
	case "removecats2":
		$cache->clean();
		removecats2( $cat_id, $option );
		break;
	
	case "fastadd":
		HTML_mtree::fastadd( $cat_parent, $option );
		break;

	case "fastadd_cat":
		$cache->clean();
		fastadd_cat( $cat_parent, $option );
		break;

	case "cat_featured":
		$cache->clean();
		featuredCats( $cat_id, 1, $option );
		break;
	case "cat_unfeatured":
		$cache->clean();
		featuredCats( $cat_id, 0, $option );
		break;
	case "cats_move":
		moveCats( $cat_id, $cat_parent, $option );
		break;
	case "cats_move2":
		$cache->clean();
		moveCats2( $cat_id, $option );
		break;
	case "cats_copy":
		copyCats( $cat_id, $cat_parent, $option );
		break;
	case "cats_copy2":
		$cache->clean();
		copyCats2( $cat_id, $option );
		break;
	case "cancelcats_move":
		cancelcats_move( $cat_id[0], $option );
		break;
	case "cat_orderup":
		cat_order( $cat_id[0], -1, $option );
		break;
	case "cat_orderdown":
		cat_order( $cat_id[0], 1, $option );
		break;

	/***
	 * Links
	 */
	case "newlink":
		editlink( 0, $cat_parent, false, $option );
		break;
	case "editlink":
		editlink( $link_id[0], $cat_parent, false, $option );
		break;
	case "editlink_for_approval":
		editlink( $link_id[0], $cat_parent, true, $option );
		break;
	/*
	case "editlink_browse_cat":
		editlink_browse_cat( $option, 0 );
		break;
	case "editlink_add_cat":
		editlink_browse_cat( $option, 1 );
		break;
	case "editlink_remove_cat":
		editlink_browse_cat( $option, -1 );
		*/
	case "openurl":
		openurl( $option );
		break;
	case "editlink_change_cat":
		editlink_change_cat( $option );
		break;
	case "savelink":
	case "applylink":
		$cache->clean();
		savelink( $option );
		break;
	case "next_link":
		$cache->clean();
		prev_next_link( "next", $option );
		break;
	case "prev_link":
		$cache->clean();
		prev_next_link( "prev", $option );
		break;
	case "link_publish":
		$cache->clean();
		publishLinks( $link_id, 1, $option );
		break;
	case "link_unpublish":
		$cache->clean();
		publishLinks( $link_id, 0, $option );
		break;
	case "removelinks":
		$cache->clean();
		removelinks( $link_id, $option );
		break;
	/*
	case "link_orderup":
		orderLinks( $link_id[0], -1, $option );
		break;
	case "link_orderdown":
		orderLinks( $link_id[0], 1, $option );
		break;
	*/
	case "link_featured":
		$cache->clean();
		featuredLinks( $link_id, 1, $option );
		break;
	case "link_unfeatured":
		$cache->clean();
		featuredLinks( $link_id, 0, $option );
		break;
	case "cancellink":
		cancellink( $link_id[0], $option );
		break;
	case "links_move":
		moveLinks( $link_id, $cat_parent, $option );
		break;
	case "links_move2":
		$cache->clean();
		moveLinks2( $link_id, $option );
		break;
	case "cancellinks_copy":
	case "cancellinks_move":
		cancellinks_move( $link_id[0], $option );
		break;
	case "links_copy":
		copyLinks( $link_id, $cat_parent, $option );
		break;
	case "links_copy2":
		$cache->clean();
		copyLinks2( $link_id, $option );
		break;
		
	/***
	* Approval / List Pending
	*/
	case "listpending_cats":
		listpending_cats( $option );
		break;
	case "approve_cats":
		$cache->clean();
		approve_cats( $cat_id, 0, $option );
		break;
	case "approve_publish_cats":
		$cache->clean();
		approve_cats( $cat_id, 1, $option );
		break;

	case "listpending_links":
		listpending_links( $option );
		break;
	case "approve_links":
		$cache->clean();
		approve_links( $link_id, 0, $option );
		break;
	case "approve_publish_links":
		$cache->clean();
		approve_links( $link_id, 1, $option );
		break;

	case "listpending_reviews":
		listpending_reviews( $option );
		break;
	case "save_pending_reviews":
		save_pending_reviews( $option );
		break;

	case "listpending_reports":
		listpending_reports( $option );
		break;
	case "save_reports":
		save_reports( $option );
		break;

	case "listpending_reviewsreports":
		listpending_reviewsreports( $option );
		break;
	case "save_reviewsreports":
		save_reviewsreports( $option );
		break;

	case "listpending_reviewsreply":
		listpending_reviewsreply( $option );
		break;
	case "save_reviewsreply":
		save_reviewsreply( $option );
		break;

	case "listpending_claims":
		listpending_claims( $option );
		break;
	case "save_claims":
		save_claims( $option );
		break;

	/***
	* Reviews
	*/
	case "reviews_list":
		list_reviews( $link_id[0], $option );
		break;
	case "newreview":
		editreview( 0, $link_id[0], $option );
		break;
	case "editreview":
		editreview( $rev_id[0], $cat_parent, $option );
		break;
	case "savereview":
		$cache->clean();
		savereview( $option );
		break;
	case "cancelreview":
		cancelreview( $link_id[0], $option );
		break;
	case "removereviews":
		$cache->clean();
		removereviews( $rev_id, $option );
		break;
	case "backreview":
		backreview( $link_id[0], $option );
		break;

	/***
	* Search
	*/
	case "search":
		search( $option );
		break;
	case "advsearch":
		advsearch( $option );
		break;
	case "advsearch2":
		require_once( $mtconf->getjconf('absolute_path') . '/administrator/components/com_mtree/mAdvancedSearch.class.php' );
		advsearch2( $option );
		break;

	/***
	* About Mosets Tree
	*/
	case "about":
		HTML_mtree::about( $option );
		break;

	/***
	* Tree Templates
	*/
	case "templates":
		// require_once( $mtconf->getjconf('absolute_path') .'/includes/domit/xml_domit_lite_include.php' );
		templates( $option );
		break;
	case "template_pages":
		// require_once( $mtconf->getjconf('absolute_path') .'/includes/domit/xml_domit_lite_include.php' );
		template_pages( $option );
		break;
	case "edit_templatepage":
		edit_templatepage( $option );
		break;
	case "save_templatepage":
	case 'apply_templatepage':
		$cache->clean();
		save_templatepage( $option );
		break;
	case "cancel_edittemplatepage":
		cancel_edittemplatepage( $option );
		break;
	case "cancel_templatepages":
		cancel_templatepages( $option );
		break;
	case "new_template":
		new_template( $option );
		break;
	case "install_template":
		install_template( $option );
		break;
	case "default_template":
		default_template( $option );
		break;
	case "copy_template":
		// require_once( $mtconf->getjconf('absolute_path') .'/includes/domit/xml_domit_lite_include.php' );
		copy_template( $option );
		break;
	case "copy_template2":
		copy_template2( $option );
		break;
	case "delete_template":
		delete_template( $option );
		break;
	case 'save_templateparams':
	case 'apply_templateparams':
		save_templateparam( $option );
		break;
		
	/***
	* Rebuild Thumbnails
	*/
	case "rebuild_thumbnails":
		$limitstart	= JRequest::getInt('limitstart', 0);
		$limit		= JRequest::getInt('limit', 5);
		rebuild_thumbnails( $option, $cat_id[0], $limitstart, $limit );
		break;

	/***
	* Import Images
	*/
	case "import_images":
		$limitstart	= JRequest::getInt('limitstart', 0);
		$limit		= JRequest::getInt('limit', 5);
		import_images( $option, $cf_id, $limitstart, $limit );
		break;
			
	/***
	* Configuration
	*/
	case "config":
		config( $option, $show );
		break;
	case "saveconfig":
		$cache->clean();
		saveconfig( $option );
		break;
	
	/***
	* Custom Fields
	*/
	case "customfields":
		customfields( $option );
		break;
	case "save_customfields":
		$cache->clean();
		save_customfields( $option );
		break;

	/***
	* License
	*/
	case "license":
		include( "license.mtree.php" );
		break;

	/***
	* CSV Import/Export
	*/
	case "csv":
		csv( $option );
		break;
	case "csv_export":
		csv_export( $option );
		break;

	/***
	* Upgrade routine
	*/
	case "upgrade":
		require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mtree'.DS.'upgrade.php' );
		break;

	/***
	* Diagnosis
	*/
	case "diagnosis":
		require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mtree'.DS.'diagnosis.php' );
		startprint( 0 );
		break;

	/***
	* Rebuild Tree
	*/
	case "rebuild_tree":
		$tree = new mtTree();
		$tree->rebuild( 0, 1);

		$database->setQuery( "SELECT MAX(rgt) FROM #__mt_cats" );
		$max_rgt = $database->loadResult();
		$database->setQuery( "UPDATE #__mt_cats SET rgt = ".($max_rgt +1).", lft=1 WHERE cat_id = 0 OR cat_parent = -1" );
		$database->query();

		JFactory::getApplication('site')->redirect( "index.php?option=$option&task=listcats&cat_id=0", JText::_( 'COM_MTREE_REBUILD_TREE_COMPLETED' ) );
		break;

	/***
	* Global Update
	*/
	case "globalupdate":
		$cache->clean();
		require_once( $mtconf->getjconf('absolute_path') . '/administrator/components/com_mtree/recount.mtree.php' );
		update_cats_and_links_count( 0, true, true );
		JFactory::getApplication('site')->redirect( "index.php?option=$option&task=listcats&cat_id=0", JText::_( 'COM_MTREE_CAT_AND_LISTING_COUNT_UPDATED' ) );
		break;

	/***
	* Geocode
	*/
	case "geocode":
		require_once( $mtconf->getjconf('absolute_path') . '/administrator/components/com_mtree/geocode.mtree.php' );
		break;

	/***
	* Recount
	*/
	case "fullrecount":
		require_once( $mtconf->getjconf('absolute_path') . '/administrator/components/com_mtree/recount.mtree.php' );
		recount( 'full', $cat_id[0] );
		break;
	
	case "fastrecount":
		require_once( $mtconf->getjconf('absolute_path') . '/administrator/components/com_mtree/recount.mtree.php' );
		recount( 'fast', $cat_id[0] );
		break;
		

	/***
	* Default List Category
	*/
	default:
		listcats( $cat_id[0], $cat_parent, $option );
		break;
}

/* End Left Navigation Menu */
// if ( !$hide_menu && !in_array($task,array('upgrade','spy','ajax','downloadft', 'manageftattachments')) ) {
if ( !$hide_menu && !in_array($task,array('upgrade','spy','ajax', 'manageftattachments')) ) {
	HTML_mtree::print_endmenu();
}


/***
* Link
*/

function editlink( $link_id, $cat_id, $for_approval=false, $option ) {
	global $mtconf;
	
	require_once( JPATH_COMPONENT_SITE.DS.'mtree.tools.php' );
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();
	$my		=& JFactory::getUser();
	$document	=& JFactory::getDocument();

	$row = new mtLinks( $database );
	$row->load( $link_id );

	$mtconf->setCategory( $row->cat_id );
	
	if ($row->link_id == 0) {
		$createdate =& JFactory::getDate();
		$row->cat_id = $cat_id;
		$row->alias = '';
		$row->link_hits = 0;
		$row->link_visited = 0;
		$row->link_votes = 0;
		$row->link_rating = 0.00;
		$row->link_featured = 0;
		$row->link_created = '';
		$row->publish_up = '';
		$row->link_published = 1;
		$row->link_approved = 1;
		$row->user_id = $my->id;
		$row->owner= $my->username;
	} else {
		if ($row->user_id > 0) {
			$database->setQuery( 'SELECT username FROM #__users WHERE id =' . $database->quote($row->user_id) );
			$row->owner = $database->loadResult();
		} else {
			$row->owner= $my->username;
		}
	}

	if ( $cat_id == 0 && $row->cat_id > 0 ) $cat_id = $row->cat_id;
	
	$document->addCustomTag("<link href=\"" .$mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_templates') . ((empty($row->link_template))?$mtconf->get('template'):$row->link_template) . '/editlisting.css' . "\" rel=\"stylesheet\" type=\"text/css\"/>");
	
	# Load images
	$database->setQuery( 'SELECT img_id, filename FROM #__mt_images WHERE link_id = ' . $database->quote($row->link_id) . ' ORDER BY ordering ASC' );
	$images = $database->loadObjectList();
	
	$lists = array();

	# Load all CORE, published and assigned custom fields
	$core_cf_ids = array(1,14,15,16,17,18,20,21,22,26,27);
	$assigned_cf_ids = getAssignedFieldsID($cat_id);

	$sql = "SELECT cf.*, " . ($row->link_id ? $row->link_id : 0) . " AS link_id, cfv.value, cfv.attachment, cfv.counter"
		.	"\nFROM #__mt_customfields AS cf "
		.	"\nLEFT JOIN #__mt_cfvalues AS cfv ON cf.cf_id=cfv.cf_id AND cfv.link_id = " . $database->quote($link_id)
		.	"\nWHERE"
		.	"\n("
		.	" cf.published='1'"
		.	((!empty($assigned_cf_ids))?"\nAND cf.cf_id IN (" . implode(',',$assigned_cf_ids). ") ":'')
		.	"\n)"
		.	((!empty($core_cf_ids))?"\nOR cf.cf_id IN (" . implode(',',$core_cf_ids). ") ":'')
		.	"\nORDER BY ordering ASC";
	$database->setQuery($sql);

	$fields = new mFields();
	$fields->setCoresValue( $row->link_name, $row->link_desc, $row->address, $row->city, $row->state, $row->country, $row->postcode, $row->telephone, $row->fax, $row->email, $row->website, $row->price, $row->link_hits, $row->link_votes, $row->link_rating, $row->link_featured, $row->link_created, $row->link_modified, $row->link_visited, $row->publish_up, $row->publish_down, $row->metakey, $row->metadesc, $row->user_id, $row->owner );
	$fields->loadFields($database->loadObjectList());
	$fields->setCatID($cat_id);
	
	$database->setQuery( 'SELECT * FROM #__mt_cats where cat_id = '.$cat_id.' LIMIT 1');
	$cat = $database->loadObject();
	
	# Get other categories
	$database->setQuery( 'SELECT cl.cat_id FROM #__mt_cl AS cl WHERE cl.link_id = ' . $database->quote($link_id) . ' AND cl.main = 0');
	$other_cats = $database->loadResultArray();

	# Get Pathway
	$pathWay = new mtPathWay( $cat_id );

	# Is this approval for modification?
	if ( $row->link_approved < 0 ) {
		$row->original_link_id = (-1 * $row->link_approved);
	} else {
		$row->original_link_id = '';
	}

	# Compile list of categories
	if ( $cat_id > 0 ) {
		// $database->setQuery( 'SELECT cat_parent FROM #__mt_cats WHERE cat_id = ' . $database->quote($cat_id) );
		// $browse_cat_parent = $database->loadResult();
		$browse_cat_parent = $cat->cat_parent;
	}
	$categories = array();
	if ( $cat_id > 0 ) {
		$categories[] = JHTML::_('select.option', $browse_cat_parent, JText::_( 'COM_MTREE_ARROW_BACK' ) );
	}
	
	$sql = 'SELECT cat_id AS value, cat_name AS text FROM #__mt_cats AS cat '
		. "\nWHERE cat_parent = " . $database->quote($cat_id) 
		. " AND cat_approved = '1' AND cat_published = '1'";
	if( $mtconf->get('first_cat_order1') != '' )
	{
		$sql .= ' ORDER BY ' . $mtconf->get('first_cat_order1') . ' ' . $mtconf->get('first_cat_order2');
		if( $mtconf->get('second_cat_order1') != '' )
		{
			$sql .= ', ' . $mtconf->get('second_cat_order1') . ' ' . $mtconf->get('second_cat_order2');
		}
	}

	$database->setQuery( $sql );
	$categories = array_merge( $categories, $database->loadObjectList() );
	$lists['cat_id'] = JHTML::_('select.genericlist', $categories, 'new_cat_id', 'size="8" class="text_area" style="display:block;width:50%;margin-top:6px;float:left"',	'value', 'text', $row->getCatID(), 'browsecat' );
	
	# Get Return task - Used by listpending_links
	$returntask	= JRequest::getCmd('returntask', '', 'post');
	
	# Get params definitions
	JForm::addFormPath(JPATH_COMPONENT.DS.'models');
	$form = JForm::getInstance('com_mtree.editlink', 'listing', array(), true, '/form/fields');

	$form->setValue('owner', 'publishing', $row->owner);
	$form->setValue('alias', 'publishing', $row->alias);
	$form->setValue('link_approved', 'publishing', $row->link_approved);
	$form->setValue('link_published', 'publishing', $row->link_published);
	$form->setValue('link_featured', 'publishing', ($fields->getFieldById(17)->getInputValue()?'1':'0'));
	$form->setValue('link_created', 'publishing', $fields->getFieldById(18)->getInputValue());
	$form->setValue('publish_up', 'publishing', $fields->getFieldById(21)->getInputValue());

	$publish_down = $fields->getFieldById(22)->getInputValue();
	if (JHTML::_('date', $publish_down, 'Y') <= 1969 || $publish_down == $database->getNullDate())
	{
		$form->setValue('publish_down', 'publishing', JText::_( 'COM_MTREE_NEVER' ));
	}
	else
	{
		$form->setValue('publish_down', 'publishing', $publish_down);
	}

	$form->setValue('link_template', 'publishing', $row->link_template);
	$form->setValue('metakey', 'publishing', $fields->getFieldById(26)->getInputValue());
	$form->setValue('metadesc', 'publishing', $fields->getFieldById(27)->getInputValue());
	$form->setValue('link_rating', 'publishing', $fields->getFieldById(16)->getInputValue());
	$form->setValue('link_votes', 'publishing', $fields->getFieldById(15)->getInputValue());
	$form->setValue('link_hits', 'publishing', $fields->getFieldById(14)->getInputValue());
	$form->setValue('link_visited', 'publishing', $fields->getFieldById(20)->getInputValue());
	$form->setValue('internal_notes', 'notes', $row->internal_notes);
		
	$params = new JRegistry;
	$params->loadString($row->attribs);
	$arrParams = $params->toArray();
	foreach( $arrParams AS $key => $value ) {
		$form->setValue($key, 'params', $value);
	}
	
	if ( $row->link_approved <= 0 ) {
		$database->setQuery( 'SELECT link_id FROM #__mt_links WHERE link_approved <= 0 ORDER BY link_created ASC, link_modified DESC' );
		$links = $database->loadResultArray();
		$number_of_prev = array_search($row->link_id,$links);
		$number_of_next = count($links) - 1 - $number_of_prev;
	} else {
		$number_of_prev = 0;
		$number_of_next = 0;
	}

	JText::script('COM_MTREE_SHOW_MAP', true);
	JText::script('COM_MTREE_REMOVE_MAP', true);
	JText::script('COM_MTREE_ENTER_AN_ADDRESS_AND_PRESS_LOCATE_IN_MAP_OR_MOVE_THE_RED_MARKER_TO_THE_LOCATION_IN_THE_MAP_BELOW');
	JText::script('COM_MTREE_LOCATE_IN_MAP', true);
	JText::script('COM_MTREE_LOCATING', true);
	JText::script('COM_MTREE_GEOCODER_NOT_OK', true);
	JText::script('COM_MTREE_ADD_AN_IMAGE', true);
	JText::script('COM_MTREE_REMOVE', true);

	HTML_mtree::editlink( $row, $fields, $images, $cat_id, $other_cats, $lists, $number_of_prev, $number_of_next, $pathWay, $returntask, $form, $option );
}

function openurl( $option ) {
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	$url = JRequest::getVar( 'url', '');

	if ( substr($url, 0, 7) <> "http://" && substr($url, 0, 8) <> "https://") {
		$url = "http://".$url;
	}

	$app->redirect( $url );
}

function prev_next_link( $prevnext, $option ) {
	global $mtconf;
	
	$database 	=& JFactory::getDBO();
	$jdate		= JFactory::getDate();

	$act		= JRequest::getCmd('act', '', 'post');
	$link_id	= JRequest::getInt('link_id', '', 'post');
	$post		= JRequest::get('post');
	
	$database->setQuery( 'SELECT link_id FROM #__mt_links WHERE link_approved <= 0 ORDER BY link_created ASC, link_modified DESC' );
	$links = $database->loadResultArray();
	if ( array_key_exists((array_search($link_id,$links) + 1),$links) ) {
		$next_link_id = $links[(array_search($link_id,$links) + 1)];
	} else {
		$next_link_id = 0;
	}
	
	if ( array_key_exists((array_search($link_id,$links) - 1),$links) ) {
		$prev_link_id = $links[(array_search($link_id,$links) - 1)];
	} else {
		$prev_link_id = 0;
	}

	if ( $prevnext == "next" ) {
		if ( $next_link_id > 0 ) {
			$post['returntask'] = "editlink&link_id=".$next_link_id;
		} else {
			$post['returntask'] = "listpending_links";
		}
	} elseif( $prevnext == "prev" ) {
		if ( $prev_link_id > 0 ) {
			$post['returntask'] = "editlink&link_id=".$prev_link_id;
		} else {
			$post['returntask'] = "listpending_links";
		}
	}

	switch( $act ) {

		case "ignore":
			savelink( $option, $post );
			break;

		case "discard":
			removeLinks( array($link_id), $option, $post );
			break;

		case "approve":
			$post['publishing']['link_approved'] = 1;
			$post['publishing']['link_published'] = 1;

			if( $mtconf->get('reset_created_date_upon_approval') ) {
				$post['publishing']['link_created'] = $jdate->toMySQL();
			}
			
			savelink( $option, $post );
			break;
	}

}

function savelink( $option, $post=null ) {
	global $mtconf;
	
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();
	$my		=& JFactory::getUser();
	$config 	=& JFactory::getConfig();
	$nullDate	= $database->getNullDate();
	$dispatcher	=& JDispatcher::getInstance();
	
	$stored = false;

	$row = new mtLinks( $database );

	if( is_null($post) ) {
		$post = JRequest::get( 'post' );
	}

	if (!$row->bind( $post )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	$original_link_id	= (int) $post['original_link_id'];
	$cat_id			= (int) $post['cat_id'];
	$row->cat_id		= $cat_id;
	$other_cats 		= explode(',', $post['other_cats']);

	$tzoffset = $config->getValue('config.offset');

	$publishing = $post['publishing'];
	$row->bind($publishing);

	# Is this a new listing?
	$new_link = false;
	$old_image = '';
	
	// Yes, new listing
	if ($row->link_id == 0) {
		$new_link = true;
		
		if ($row->link_created && strlen(trim( $row->link_created )) <= 10) {
			$row->link_created 	.= ' 00:00:00';
		}

		// $date =& JFactory::getDate($row->link_created, $tzoffset);
		// $row->link_created = $date->toMySQL();
		$row->link_created = JFactory::getDate()->toMySQL();

	// No, this listing has been saved to the database 
	// 1) Submission from visitor
	// 2) Modification request from listing owner
	} else {
		$row->link_modified = $row->getLinkModified( (empty($original_link_id)?$row->link_id:$original_link_id), $post );

		# Let's check if this link is on "pending approval" from an existing listing
		$database->setQuery( "SELECT link_approved FROM #__mt_links WHERE link_id = $row->link_id LIMIT 1" );
		$thislink = $database->loadObject(); // 1: approved; 0:unapproved/new listing; <-1: pending approval for update
		$link_approved = $thislink->link_approved;

		if ( $link_approved < 0 && $row->link_approved == 0 ) {
			$row->link_approved = $link_approved;
		}

	}

	// Append time if not added to publish date
	if (strlen(trim($row->publish_up)) <= 10) {
		$row->publish_up .= ' 00:00:00';
	}

	$date =& JFactory::getDate($row->publish_up, $tzoffset);
	$row->publish_up = $date->toMySQL();

	// Handle never unpublish date
	if (trim($row->publish_down) == JText::_( 'COM_MTREE_NEVER' ) || trim( $row->publish_down ) == '')
	{
		$row->publish_down = $nullDate;
	}
	else
	{
		if (strlen(trim( $row->publish_down )) <= 10) {
			$row->publish_down .= ' 00:00:00';
		}
		// $date =& JFactory::getDate($row->publish_down, $tzoffset);
		// $row->publish_down = $date->toMySQL();
		$row->publish_down = JFactory::getDate($row->publish_down)->toMySQL();
	}
	
	// @TODO // Is this still needed?
	// $date =& JFactory::getDate($row->link_created, $tzoffset);
	// $row->link_created = $date->toMySQL();
	// echo $row->link_created; jexit();
	// $row->link_created = JFactory::getDate($row->link_created)->toMySQL();

	$notes = $post['notes'];
	$row->bind($notes);
	
	# Lookup owner's userid. Return error if does not exists
	if ($publishing['owner'] == '') {
		// If owner field is left blank, assign the link to the current user
		$row->user_id = $my->id;
	} else {
		$database->setQuery( 'SELECT id FROM #__users WHERE username = ' . $database->quote($publishing['owner']) );
		$owner_id = $database->loadResult();
		if ($owner_id > 0) {
			$row->user_id = $owner_id;
		} else {
			echo "<script> alert('".JText::_( 'COM_MTREE_INVALID_OWNER_SELECT_AGAIN' )."'); window.history.go(-1); </script>\n";
			exit();
		}
	}
	
	# Listing alias
	if( empty($row->alias) )
	{
		$row->alias = JFilterOutput::stringURLSafe($row->link_name);
	}
	
	# Save parameters
	$params = $post['params'];

	if ( is_array( $params ) ) {
		$attribs = array();
		foreach ( $params as $k=>$v) {
			$attribs[] = "$k=$v";
		}
		$row->attribs = implode( "\n", $attribs );
	}

	# Publish the listing
	if ( $row->link_published && $row->link_id > 0 ) {
		$row->publishLink( 1 );
	} elseif ( !$row->link_published ) {
		$row->publishLink( 0 );
	}

	# Approve listing and send e-mail notification to the owner and admin
	if ( $row->link_approved == 1 && $row->link_id > 0 ) {
		# Get this actual link_approved value from DB
		$database->setQuery( 'SELECT link_approved FROM #__mt_links WHERE link_id = ' . $database->quote($row->link_id) );
		$link_approved = $database->loadResult();

		# This is a modification to the existing listing
		if ( $link_approved <= 0 ) {
			$row->updateLinkCount( 1 );
			$row->approveLink();
			// $stored = true;
		}
	}

	# Update the Link Counts for all cat_parent(s)
	if ($new_link) {
		$row->updateLinkCount( 1 );
	} else {
		// Get old state (approved, published)
		$database->setQuery( 'SELECT link_approved, link_published, cl.cat_id FROM (#__mt_links AS l, #__mt_cl AS cl) WHERE l.link_id = cl.link_id AND l.link_id = ' . $database->quote($row->link_id) . ' LIMIT 1' );
		$old_state = $database->loadObject();

		// From approved & published -to-> unapproved/unpublished
		if ( $old_state->link_approved == 1 && $old_state->link_published == 1 ) {
			if ( $row->link_published == 0 || $row->link_approved == 0) {
				$row->updateLinkCount( -1 );
			}

		// From unpublished/unapproved -to-> Published & Approved
		} elseif( $row->link_published == 1 && $row->link_approved == 1) {
			$row->updateLinkCount( 1 );
		}

		// Update link count if changing to a new category
		if ( $old_state->cat_id <> $cat_id && $old_state->link_approved <> 0 ) {
			$oldrow = new mtLinks( $database );
			$oldrow->cat_id = $old_state->cat_id;
			$oldrow->updateLinkCount( -1 );

			$newrow = new mtLinks( $database );
			$newrow->cat_id = $cat_id;
			$newrow->updateLinkCount( 1 );
		}
	}
	
	# Erase all listing associations 
	$database->setQuery(
		"DELETE FROM #__mt_links_associations "
		.	"\n WHERE link_id2 = " . $database->Quote($row->link_id)
		.	"\n LIMIT 1 "
		);
	$database->query();
	
	# Erase Previous Records, make way for the new data
	$sql = 'DELETE FROM #__mt_cfvalues WHERE link_id= ' . $database->quote($row->link_id) . ' AND attachment <= 0';
	$database->setQuery($sql);
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	# Load field type
	$database->setQuery( 'SELECT cf_id, field_type FROM #__mt_customfields' );
	$fieldtype = $database->loadObjectList('cf_id');
	
	if(count($fieldtype) > 0 ) {
		$load_ft = array();
		foreach( $fieldtype AS $ft ) {
			$class_name = 'mFieldType_' . $ft->field_type;
			if( !class_exists($class_name) ) {
				$fieldtype_file = JPATH_ROOT . $mtconf->get('relative_path_to_fieldtypes') . $ft->field_type . DS  . $ft->field_type . '.php';
				if( JFile::exists($fieldtype_file) )
				{
					require_once $fieldtype_file;
				}
			}
		}
	}
	
	# Collect all active custom field's id
	$active_cfs = array();
	$additional_cfs = array();
	$core_params = array();
	
	foreach($post AS $k => $v) {
		$v = JRequest::getVar( $k, '', 'post', '', 2);
		if ( substr($k,0,2) == "cf" && ( (!is_array($v) && (!empty($v) || $v == '0')) || (is_array($v) && !empty($v[0])) ) ) {
			if(strpos(substr($k,2),'_') === false && is_numeric(substr($k,2))) {
				// This custom field uses only one input. ie: cf17, cf23, cf2
				$active_cfs[intval(substr($k,2))] = $v;
			} else {
				// This custom field uses more than one input. The date field is an example of cf that uses this. ie: cf13_0, cf13_1, cf13_2
				$ids = explode('_',substr($k,2));
				if(count($ids) == 2 && is_numeric($ids[0]) && is_numeric($ids[1]) ) {
					$additional_cfs[intval($ids[0])][intval($ids[1])] = $v;
				}
			}
		} elseif( substr($k,0,7) == 'keep_cf' ) {
			$cf_id = intval(substr($k,7));
			$keep_att_ids[] = $cf_id;
			
	# Perform parseValue on Core Fields
		} elseif( substr($k,0,2) != "cf" && isset($row->{$k}) ) {
			if(strpos(strtolower($k),'link_') === false) {
				$core_field_type = 'core' . $k;
			} else {
				$core_field_type = 'core' . str_replace('link_','',$k);
			}
			$class = 'mFieldType_' . $core_field_type;
			
			if(class_exists($class)) {
				if(empty($core_params)) {
					$database->setQuery( 'SELECT field_type, params FROM #__mt_customfields WHERE iscore = 1' );
					$core_params = $database->loadObjectList('field_type');
				}
				$mFieldTypeObject = new $class(array('linkId'=>$row->link_id,'params'=>$core_params[$core_field_type]->params));
				$v = call_user_func(array(&$mFieldTypeObject, 'parseValue'),$v);
				$row->{$k} = $v;
			}
		}
	}

	if (!$stored) {
		# Save core values to database
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		} else {
		
			# If this is a newlink, rename the photo to listingID_photoName.jpg
			if ( $new_link ) {

				// Get last inserted listing ID
				$mysql_last_insert_cl_id = $database->insertid();

				$database->setQuery( 'SELECT link_id FROM #__mt_cl WHERE cl_id = ' . $database->quote($mysql_last_insert_cl_id) );
				$mysql_last_insert_id = $database->loadResult();

			}
		}

	}
	
	// $files_cfs is used to store attachment custom fields. 
	// This will be used in the next foreach loop to 
	// prevent it from storing it's value to #__mt_cfvalues 
	// table
	$file_cfs = array();
	
	// $file_values is used to store parsed data through 
	// mFieldType_* which will be done in the next foreach 
	// loop
	$file_values = array();
	$files = JRequest::get('files');
	
	foreach($files AS $k => $v) {
		if ( substr($k,0,2) == "cf" && is_numeric(substr($k,2)) && $v['error'] == 0) {
			$active_cfs[intval(substr($k,2))] = $v;
			$file_cfs[] = intval(substr($k,2));
		}
	}

	if(count($active_cfs)>0) {
		$database->setQuery('SELECT cf_id, params FROM #__mt_customfields WHERE iscore = 0 AND cf_id IN (\'' . implode('\',\'',array_keys($active_cfs)). '\') LIMIT ' . count($active_cfs));
		$params = $database->loadObjectList('cf_id');
		
		foreach($active_cfs AS $cf_id => $v) {
			if(class_exists('mFieldType_'.$fieldtype[$cf_id]->field_type)) {
				$class = 'mFieldType_'.$fieldtype[$cf_id]->field_type;
			} else {
				$class = 'mFieldType';
			}
		
			# Perform parseValue on Custom Fields
			
			$mFieldTypeObject = new $class(array('linkId'=>$row->link_id,'id'=>$cf_id,'params'=>$params[$cf_id]->params));
			
			if(array_key_exists($cf_id,$additional_cfs) && count($additional_cfs[$cf_id]) > 0) {
				$arr_v = $additional_cfs[$cf_id];
				array_unshift($arr_v, $v);
				$v = &$mFieldTypeObject->parseValue($arr_v);
			} else {
				$v = &$mFieldTypeObject->parseValue($v);
			}
			
			if(in_array($cf_id,$file_cfs)) {
				$file_values[$cf_id] = $v;
			}
			
			if( (!empty($v) || $v == '0') && !in_array($cf_id,$file_cfs)) {
				# -- Now add the row
				$sql = 'INSERT INTO #__mt_cfvalues (`cf_id`, `link_id`, `value`)'
					. "\nVALUES (" . $database->quote($cf_id) . ', ' . $database->quote($row->link_id) . ', ' . $database->quote((is_array($v)) ? implode('|',$v) : $v). ')';
				$database->setQuery($sql);
				if (!$database->query()) {
					echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
					exit();
				}
			}
			unset($mFieldTypeObject);
		} // End of foreach
	}

	# Remove all attachment except those that are kept
	$raw_filenames = array();
	
	if(isset($keep_att_ids) && count($keep_att_ids)>0) {
		$database->setQuery( 'SELECT CONCAT(' . $database->quote(JPATH_SITE.$mtconf->get('relative_path_to_attachments')) . ',raw_filename) FROM #__mt_cfvalues_att WHERE link_id = ' . $database->quote($row->link_id) . ' AND cf_id NOT IN (\'' . implode('\',\'',$keep_att_ids) . '\')' );
		$raw_filenames = $database->loadResultArray();
		
		$database->setQuery('DELETE FROM #__mt_cfvalues_att WHERE link_id = ' . $database->quote($row->link_id) . ' AND cf_id NOT IN (\'' . implode('\',\'',$keep_att_ids) . '\')' );
		$database->query();
		$database->setQuery('DELETE FROM #__mt_cfvalues WHERE link_id = ' . $database->quote($row->link_id) . ' AND cf_id NOT IN (\'' . implode('\',\'',$keep_att_ids) . '\') AND attachment > 0' );
		$database->query();
	} else {
		$database->setQuery( 'SELECT CONCAT(' . $database->quote(JPATH_SITE.$mtconf->get('relative_path_to_attachments')) . ',raw_filename) FROM #__mt_cfvalues_att WHERE link_id = ' . $database->quote($row->link_id) );
		$raw_filenames = $database->loadResultArray();
		
		$database->setQuery('DELETE FROM #__mt_cfvalues_att WHERE link_id = ' . $database->quote($row->link_id) );
		$database->query();
		$database->setQuery('DELETE FROM #__mt_cfvalues WHERE link_id = ' . $database->quote($row->link_id) . ' AND attachment > 0' );
		$database->query();
	}
	
	foreach($files AS $k => $v) {
		if ( substr($k,0,2) == "cf" && is_numeric(substr($k,2)) && $v['error'] == 0) {
			$cf_id = intval(substr($k,2));

			$file_extension = pathinfo($file_values[$cf_id]['name']);
			$file_extension = strtolower($file_extension['extension']);

			// Prevents certain file types from being uploaded. Defaults to prevent PHP file (php)
			if( in_array($file_extension,explode(',',$mtconf->get('banned_attachment_filetypes'))) ) {
				continue;
			}
			if(array_key_exists($cf_id,$file_values)) {
				$file = $file_values[$cf_id];
				if(!empty($file['data'])) {
					$data = $file['data'];
				} else {
					$fp = fopen($v['tmp_name'], "r");
					$data = fread($fp, $v['size']);
					fclose($fp);
				}
			} else {
				$file = $v;
				$fp = fopen($v['tmp_name'], "r");
				$data = fread($fp, $v['size']);
				fclose($fp);
			}
			
			$database->setQuery( 'SELECT CONCAT(' . $database->quote(JPATH_SITE.$mtconf->get('relative_path_to_attachments')) . ',raw_filename) FROM #__mt_cfvalues_att WHERE link_id = ' . $database->quote($row->link_id) . ' AND cf_id = ' . $database->quote($cf_id) );
			$raw_filenames = array_merge($raw_filenames, $database->loadResultArray());

			$database->setQuery('DELETE FROM #__mt_cfvalues_att WHERE link_id = ' . $database->quote($row->link_id) . ' AND cf_id = ' . $database->quote($cf_id));
			$database->query();
		
			$database->setQuery('DELETE FROM #__mt_cfvalues WHERE cf_id = ' . $database->quote($cf_id) . ' AND link_id = ' . $database->quote($row->link_id) . ' AND attachment > 0' );
			$database->query();

			$database->setQuery('INSERT INTO #__mt_cfvalues_att (link_id, cf_id, raw_filename, filename, filesize, extension) '
				.	'VALUES('
				.	$database->quote($row->link_id) . ', '
				.	$database->quote($cf_id) . ', '
				.	$database->quote($file['name']) . ', '
				.	$database->quote($file['name']) . ', '
				.	$database->quote($file['size']) . ', '
				.	$database->quote($file['type']) . ')'
				); 
				
			if($database->query() !== false) {
				$att_id = $database->insertid();
				
				$file_extension = strrchr($file['name'],'.');
				if( $file_extension === false ) {
					$file_extension = '';
				}
				
				if(JFile::write( JPATH_SITE.$mtconf->get('relative_path_to_attachments').$att_id.$file_extension, $data ))
				{
					$database->setQuery( 'UPDATE #__mt_cfvalues_att SET raw_filename = ' . $database->quote($att_id . $file_extension) . ' WHERE att_id = ' . $database->quote($att_id) . ' LIMIT 1' );
					$database->query();

					$sql = 'INSERT INTO #__mt_cfvalues (`cf_id`, `link_id`, `value`, `attachment`) '
						. 'VALUES (' . $database->quote($cf_id) . ', ' . $database->quote($row->link_id) . ', ' . $database->quote($file['name']) . ',1)';
					$database->setQuery($sql);
					$database->query();
				} else {
					// Move failed, remove record from previously INSERTed row in #__mt_cfvalues_att
					$database->setQuery('DELETE FROM #__mt_cfvalues_att WHERE att_id = ' . $database->quote($att_id) . ' LIMIT 1');
					$database->query();
				}
			}
		} 
	}

	if( !empty($raw_filenames) )
	{
		JFile::delete($raw_filenames);
	}
	
	# Remove all images except those that are kept
	$msg = '';
	if(is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_listing_small_image')) && is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_listing_medium_image')) && is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_listing_original_image'))) {
		
		if( isset($post['keep_img']) )
		{
			$keep_img_ids = $post['keep_img'];
			JArrayHelper::toInteger($keep_img_ids, array());
		}
		
		$image_filenames = array();
		if(isset($keep_img_ids) && count($keep_img_ids)>0) {
			$database->setQuery('SELECT filename FROM #__mt_images WHERE link_id = ' . $database->quote($row->link_id) . ' AND img_id NOT IN (\'' . implode('\',\'',$keep_img_ids) . '\')' );
			$image_filenames = $database->loadResultArray();
			$database->setQuery('DELETE FROM #__mt_images WHERE link_id = ' . $database->quote($row->link_id) . ' AND img_id NOT IN (\'' . implode('\',\'',$keep_img_ids) . '\')' );
			$database->query();
		} else {
			$database->setQuery('SELECT filename FROM #__mt_images WHERE link_id = ' . $database->quote($row->link_id) );
			$image_filenames = $database->loadResultArray();
			$database->setQuery('DELETE FROM #__mt_images WHERE link_id = ' . $database->quote($row->link_id) );
			$database->query();
		}
		if( count($image_filenames) ) {
			foreach($image_filenames AS $image_filename) {
				unlink($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $image_filename);
				unlink($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $image_filename);
				unlink($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $image_filename);
			}
		}
	}

	$images = new mtImages( $database );
	if( isset($files['image']) ) {
		if( !is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_listing_small_image')) || !is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_listing_medium_image')) ||  !is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_listing_original_image')) ) {
			$msg = JText::_( 'COM_MTREE_IMAGE_DIRECTORIES_NOT_WRITABLE' );
		} else {
			for($i=0;$i<count($files['image']['name']);$i++) {
				if ( !empty($files['image']['name'][$i]) && $files['image']['error'][$i] == 0 &&  $files['image']['size'][$i] > 0 ) {
					$file_extension = pathinfo($files['image']['name'][$i]);
					$file_extension = strtolower($file_extension['extension']);
					if( !in_array($file_extension,array('png','gif','jpg','jpeg')) ) {
						JError::raise(E_NOTICE, 0, JText::sprintf('COM_MTREE_ERROR_IMAGE_UNSUPPORTED_FILE_EXTENSION',$files['image']['name'][$i]) );
						continue;
					}
					$mtImage = new mtImage();
					$mtImage->setMethod( $mtconf->get('resize_method') );
					$mtImage->setQuality( $mtconf->get('resize_quality') );
					$mtImage->setSize( $mtconf->get('resize_small_listing_size') );
					$mtImage->setTmpFile( $files['image']['tmp_name'][$i] );
					$mtImage->setType( $files['image']['type'][$i] );
					$mtImage->setName( $files['image']['name'][$i] );
					$mtImage->setSquare( $mtconf->get('squared_thumbnail') );

					if( !$mtImage->resize() )
					{
						JError::raise(E_NOTICE, 0, JText::sprintf('COM_MTREE_ERROR_IMAGE_UNABLE_TO_PROCESS_IMAGE', $files['image']['name'][$i]));
						continue;
					}
					
					$mtImage->setDirectory( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') );
					$mtImage->saveToDirectory();
				
					$mtImage->setSize( $mtconf->get('resize_medium_listing_size') );
					$mtImage->setSquare(false);
					$mtImage->resize();
					$mtImage->setDirectory( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') );
					$mtImage->saveToDirectory();
					move_uploaded_file($files['image']['tmp_name'][$i], JPath::clean($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $files['image']['name'][$i]) );

					$database->setQuery( "INSERT INTO #__mt_images (link_id, filename, ordering) "
						.	'VALUES(' . $database->quote($row->link_id) . ', ' . $database->quote($files['image']['name'][$i]) . ', 9999)');
					$database->query();
					$img_id = intval($database->insertid());

					$old_small_image_path		= JPath::clean($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $files['image']['name'][$i]);
					$old_medium_image_path		= JPath::clean($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $files['image']['name'][$i]);
					$old_original_image_path	= JPath::clean($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $files['image']['name'][$i]);
					
					rename( $old_small_image_path, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') . $img_id . '.' . $file_extension);
					rename( $old_medium_image_path, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') . $img_id . '.' . $file_extension);
					rename( $old_original_image_path, $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_original_image') . $img_id . '.' . $file_extension);

					$database->setQuery('UPDATE #__mt_images SET filename = ' . $database->quote($img_id . '.' . $file_extension) . ' WHERE img_id = ' . $database->quote($img_id));
					$database->query();
				}
			}
		}
	}
	
	$img_sort_hash = $post['img_sort_hash'];
	
	if(!empty($img_sort_hash)) {
		parse_str($img_sort_hash,$arr_img_sort_hashes);
		$i=1;
		if(!empty($arr_img_sort_hashes['img']))
		{
			foreach($arr_img_sort_hashes['img'] AS $arr_img_sort_hash)
			{
				if(!empty($arr_img_sort_hash) && $arr_img_sort_hash > 0)
				{
					$database->setQuery( 'UPDATE #__mt_images SET ordering = ' . $database->quote($i) . ' WHERE img_id = ' . $database->quote(intval($arr_img_sort_hash)). ' LIMIT 1' );
					$database->query();
					$i++;
				}
			}	
		}
	}
	$images->reorder('link_id='.$row->link_id);
	
	# Update "Also appear in these categories" aka other categories
	$mtCL = new mtCL_main0( $database );
	$mtCL->load( $row->link_id );
	$mtCL->update( $other_cats );
	
	JPluginHelper::importPlugin('finder');
	$dispatcher->trigger('onFinderAfterSave', array('com_mtree.listing', new JObject(array('link_id' => $row->link_id)), $new_link));
	$returntask	= $post['returntask'];
	
// /*
	if ( $returntask <> '' ) {
		$app->redirect( "index.php?option=$option&task=$returntask", $msg );
	} else {

		$task = JFilterInput::clean($post['task'], 'cmd');
		
		if ( $task == "applylink" ) {
			$app->redirect( "index.php?option=$option&task=editlink&link_id=$row->link_id", $msg );
		} else {
			$app->redirect( "index.php?option=$option&task=listcats&cat_id=$cat_id", $msg );
		}
	}
// */
}

function publishLinks( $link_id=null, $publish=1,  $option ) {

	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	if (!is_array( $link_id ) || count( $link_id ) < 1) {
		$action = $publish ? strtolower(JText::_( 'COM_MTREE_PUBLISH' )) : strtolower(JText::_( 'COM_MTREE_UNPUBLISH' ));
		echo "<script> alert('".JText::sprintf( 'COM_MTREE_SELECT_AN_ITEM_TO', $action )."'); window.history.go(-1);</script>\n";
		exit;
	}

	$link_ids = implode( ',', $link_id );

	# Verify if these links is unpublished -> published OR published -> unpublished 
	foreach( $link_id AS $lid ) {
		$checklink = new mtLinks( $database );
		$checklink->load( $lid );

		if ( $checklink->link_published XOR $publish ) {
			$checklink->updateLinkCount( ( ($publish) ? 1 : -1 ) );
		}

	}

	# Publish/Unpublish Link
	$database->setQuery( 'UPDATE #__mt_links SET link_published = ' . $database->quote($publish)
		. "\nWHERE link_id IN ($link_ids)"
	);
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	} else {
		$dispatcher	=& JDispatcher::getInstance();
		JPluginHelper::importPlugin('finder');
		$dispatcher->trigger('onContentChangeState', array('com_mtree.listing', array($link_id), (int)$publish));
	}

	$row = new mtLinks( $database );
	$row->load( $link_id[0] );

	$app->redirect( "index.php?option=$option&task=listcats&cat_id=".$row->cat_id );
}

function removeLinks( $link_id, $option, $post=null ) {

	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$row = new mtLinks( $database );
	$row->load( $link_id[0] );
	
	if (!is_array( $link_id ) || count( $link_id ) < 1) {
		echo "<script> alert('".JText::_( 'COM_MTREE_SELECT_AN_ITEM_TO_DELETE' )."'); window.history.go(-1);</script>\n";
		exit;
	}
	if (count( $link_id )) {
		$link_ids = implode( ',', $link_id );
		
		$total = count( $link_id );

		# Locate all CL mapping and decrease the categories' link count
		foreach( $link_id AS $id ) {
			$database->setQuery( 'SELECT cat_id FROM #__mt_cl WHERE main = 0 AND link_id = ' . $database->quote($id) );
			$link_cls = $database->loadResultArray();
			
			if( count($link_cls) > 0 ) {
				foreach( $link_cls AS $link_cl ) {
					$row->updateLinkCount( -1, $link_cl );
				}
			}
		}

		# Delete the main records
		foreach( $link_id AS $id ) {
			$database->setQuery( 'SELECT link_approved FROM #__mt_links WHERE link_id = ' . $database->quote($id) );
			$link_approved = $database->loadResult();
			if ( $link_approved <= 0 ) {
				$total--;
			}
			$row->delLink( $id );
		}
		# Update link count for all category
		if ( $total > 0 ) {
			$row->updateLinkCount( (-1 * $total) );
		}
	}
	
	if( is_null($post) ) {
		$returntask	= JRequest::getCmd('returntask', '', 'post');
	} else {
		$returntask	= $post['returntask'];
	}

	if ( $returntask <> '' ) {
		$app->redirect( "index.php?option=$option&task=$returntask", JText::plural( 'COM_MTREE_N_LINKS_DELETED', count($link_id) ) );
	} else {
		$app->redirect( "index.php?option=$option&task=listcats&cat_id=".$row->cat_id, JText::plural( 'COM_MTREE_N_LINKS_DELETED', count($link_id) ) );
	}
}

function featuredLinks( $link_id, $featured=1, $option ) {
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$row = new mtLinks( $database );
	
	if (count( $link_id )) {
		foreach($link_id AS $lid) {
			$row->setFeaturedLink( $featured, $lid );
		}
	}
	$row->load( $lid );

	$app->redirect( "index.php?option=$option&task=listcats&cat_id=".$row->cat_id );
}

function orderLinks( $link_id, $inc, $option ) {
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	$row = new mtLinks( $database );
	$row->load( $link_id );
	$row->move( $inc, "cat_id = '$row->cat_id'" );
	
	$app->redirect( "index.php?option=$option&task=listcats&cat_id=".$row->cat_id );
}

function cancellink( $link_id, $option ) {
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();
	
	# Check return task - used to return to listpending_links
	$returntask	= JRequest::getCmd('returntask', '', 'post');
	
	if ( $returntask <> '' ) {
		$app->redirect( "index.php?option=$option&task=$returntask" );
	} else {
		$link = new mtLinks( $database );
		$link->load( $link_id );

		$app->redirect( "index.php?option=$option&task=listcats&cat_id=$link->cat_id" );
	}
}

function cancellinks_move( $link_id, $option ) {
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	$link = new mtLinks( $database );
	$link->load( $link_id );

	$app->redirect( "index.php?option=$option&task=listcats&cat_id=$link->cat_id" );
}

function moveLinks( $link_id, $cat_parent, $option ) {

	$database 	=& JFactory::getDBO();

	if (!is_array( $link_id ) || count( $link_id ) < 1) {
		echo "<script> alert('".JText::_( 'COM_MTREE_SELECT_AN_ITEM_TO_MOVE' )."'); window.history.go(-1);</script>\n";
		exit;
	}	

	# Get Pathway
	$pathWay = new mtPathWay( $cat_parent );

	# Get all category under cat_parent
	$database->setQuery( 'SELECT cat_id AS value, cat_name AS text FROM #__mt_cats WHERE cat_parent = ' . $database->quote($cat_parent) . ' ORDER BY cat_name ASC');
	$rows = $database->loadObjectList();

	# Get Parent's parent
	if ( $cat_parent > 0 ) {
		$database->setQuery( 'SELECT cat_parent FROM #__mt_cats WHERE cat_id = ' . $database->quote($cat_parent) );
		$cat_back = JHTML::_('select.option', $database->loadResult(), '&lt;--Back' );
		array_unshift( $rows, $cat_back );
	}
	
	$cats = $rows;
	$catList = JHTML::_('select.genericlist', $cats, 'cat_parent', 'class="text_area" size="8" style="width:30%"', 'value', 'text', null, 'browsecat' );

	HTML_mtree::move_links( $link_id, $cat_parent, $catList, $pathWay, $option );

}

function moveLinks2( $link_id, $option ) {
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$new_cat_parent	= JRequest::getInt( 'new_cat_parent', '', 'post');
	
	$row = new mtLinks( $database );

	if ( count( $link_id ) > 0 ) {
		foreach( $link_id AS $id ) {
			if ( $row->load( $id ) == true ) {
				if ( !isset($old_cat_parent) ) {
					$old_cat_parent = $row->cat_id;
				}
			} else {
				echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
				exit();
			}

			# Assign new cat_parent
			if ( $new_cat_parent >= 0 ) {
				$row->cat_id = $new_cat_parent;
			}

			if (!$row->store()) {
				echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
				exit();
			}
		} // End foreach
	} // End if
	
	# Update category, links count and update all ordering
	$result = $row->updateLinkCount( (count($link_id)*-1), $old_cat_parent );
	$result = $row->updateLinkCount( count($link_id), $new_cat_parent );

	$app->redirect( "index.php?option=$option&task=listcats&cat_id=$new_cat_parent" );
}

function copyLinks( $link_id, $cat_parent, $option ) {

	$database 	=& JFactory::getDBO();

	if (!is_array( $link_id ) || count( $link_id ) < 1) {
		echo "<script> alert('".JText::_( 'COM_MTREE_SELECT_AN_ITEM_TO_COPY' )."'); window.history.go(-1);</script>\n";
		exit;
	}	

	# Get Pathway
	$pathWay = new mtPathWay( $cat_parent );

	# Get all category under cat_parent
	$database->setQuery('SELECT cat_id AS value, cat_name AS text FROM #__mt_cats WHERE cat_parent = ' . $database->quote($cat_parent) . ' ORDER BY cat_name ASC');
	$rows = $database->loadObjectList();

	# Get Parent's parent
	if ( $cat_parent > 0 ) {
		$database->setQuery('SELECT cat_parent FROM #__mt_cats WHERE cat_id = ' . $database->quote($cat_parent));
		$cat_back = JHTML::_('select.option', $database->loadResult(), JText::_( 'COM_MTREE_ARROW_BACK' ) );
		array_unshift( $rows, $cat_back );
	}
	
	$cats = $rows;

	# Main Category list
	$lists['cat_id'] = JHTML::_('select.genericlist', $cats, 'cat_parent', 'class="text_area" size="8" style="width:30%"', 'value', 'text', null, 'browsecat' );

	# Copy Reviews?
	$copy_reviews					= JRequest::getInt( 'copy_reviews', 0, 'post');
	$lists['copy_reviews']			= JHTML::_('select.booleanlist', "copy_reviews", 'class="inputbox"', $copy_reviews);

	# Copy Secondary Categories?
	$copy_secondary_cats 			= JRequest::getInt( 'copy_secondary_cats', 0, 'post');
	$lists['copy_secondary_cats'] 	= JHTML::_('select.booleanlist', "copy_secondary_cats", 'class="inputbox"', $copy_secondary_cats);
	
	# Reset Hits?
	$reset_hits 					= JRequest::getInt( 'reset_hits', 1, 'post');
	$lists['reset_hits'] 			= JHTML::_('select.booleanlist', "reset_hits", 'class="inputbox"', $reset_hits);

	# Reset Rating & Votes?
	$reset_rating 					= JRequest::getInt( 'reset_rating', 1, 'post');
	$lists['reset_rating'] 			= JHTML::_('select.booleanlist', "reset_rating", 'class="inputbox"', $reset_rating);

	HTML_mtree::copy_links( $link_id, $cat_parent, $lists, $pathWay, $option );

}

function copyLinks2( $link_id, $option ) {
	
	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();
	
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );
	$new_cat_parent			= JRequest::getInt( 'new_cat_parent', '', 'post');
	$copy_reviews 			= JRequest::getInt( 'copy_reviews', 0, 'post');
	$copy_secondary_cats 	= JRequest::getInt( 'copy_secondary_cats', 0, 'post');
	$reset_hits 			= JRequest::getInt( 'reset_hits', 1, 'post');
	$reset_rating 			= JRequest::getInt( 'reset_rating', 1, 'post');

	$row = new mtLinks( $database );

	if ( count( $link_id ) > 0 ) {
		foreach( $link_id AS $id ) {
			$row->copyLink( $id, $new_cat_parent, $reset_hits, $reset_rating, $copy_reviews, $copy_secondary_cats);
			$row->cat_id = $new_cat_parent;
			$row->updateLinkCount( 1 );
		}
	}

	$app->redirect( "index.php?option=$option&task=listcats&cat_id=$new_cat_parent" );
}

/****
* Category
*/
function listcats( $cat_id, $cat_parent, $option ) {
	global $mtconf;

	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	$limit 		= $app->getUserStateFromRequest( "viewlistlimit", 'limit', $mtconf->getjconf('list_limit') );
	$limitstart 	= $app->getUserStateFromRequest( "viewcli-".$option."-".(($cat_id)?$cat_id:$cat_parent)."-limitstart", 'limitstart', 0 );

	if ( $cat_id == 0 && $cat_parent > 0 ) {
		$cat_id = $cat_parent;
	}

	# Creating db connection to #__mt_cats
	$mtCats = new mtCats( $database );
	$mtCats->load( $cat_id );

	# Page Navigation
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($mtCats->getNumOfLinks_NoRecursive( $cat_id ), $limitstart, $limit);
	
	# Main query - category
	$sql = 'SELECT cat.* FROM #__mt_cats AS cat '
		. 'WHERE cat_parent = ' . $database->quote($cat_id) . ' AND cat_approved = 1 ';
		
	if( $mtconf->get('first_cat_order1') != '' )
	{
		$sql .= ' ORDER BY ' . $mtconf->get('first_cat_order1') . ' ' . $mtconf->get('first_cat_order2');
		if( $mtconf->get('second_cat_order1') != '' )
		{
			$sql .= ', ' . $mtconf->get('second_cat_order1') . ' ' . $mtconf->get('second_cat_order2');
		}
	}

	$database->setQuery($sql);
	if(!$result = $database->query()) {
		echo $database->stderr();
		return false;
	}
	$cats = $database->loadObjectList();

	$catPageNav = new JPagination(count( $cats ), 0, 999999);

	# Get Pathway
	$pathWay = new mtPathWay( $cat_id );

	# Get Links for this category
	$sql = "SELECT l.*, COUNT(r.rev_id) AS reviews, cl.main AS main FROM (#__mt_links AS l, #__mt_cl AS cl)"
		."\nLEFT JOIN #__mt_reviews AS r ON (r.link_id = l.link_id)"
		."\nWHERE cl.cat_id = " . $database->quote($cat_id) . " AND link_approved = '1' AND (l.link_id = cl.link_id)"
		."\nGROUP BY l.link_id";
		
	if( $mtconf->get('min_votes_to_show_rating') > 0 && $mtconf->get('first_listing_order1') == 'link_rating' ) {
		$sql .= "\nORDER BY link_votes >= " . $mtconf->get('min_votes_to_show_rating') . " DESC, " . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2');
	} else {
		$sql .= "\nORDER BY " . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2');
	}
	$sql .= "\nLIMIT $pageNav->limitstart,$pageNav->limit";
		/*
		if( $mtconf->get('min_votes_to_show_rating') > 0 && $mtconf->get('first_listing_order1') == 'link_rating' ) {
			$sql .= "\n ORDER BY link_votes >= " . $mtconf->get('min_votes_to_show_rating') . ' DESC, ' . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2');
		} else {
			$sql .= "\n ORDER BY " . $mtconf->get('first_listing_order1') . ' ' . $mtconf->get('first_listing_order2') . ', ' . $mtconf->get('second_listing_order1') . ' ' . $mtconf->get('second_listing_order2');
		}
		*/
	$database->setQuery($sql);
	if(!$result = $database->query()) {
		echo $database->stderr();
		return false;
	}
	$links = $database->loadObjectList();
	
	# Get cat_ids for soft listing
	$softlinks = array();
	foreach( $links AS $link ) {
		if ( $link->main == 0 ) {
			$softlinks[] = $link->link_id;
		}
	}
	if ( !empty($softlinks) ) {
		$database->setQuery( "SELECT link_id, cat_id FROM #__mt_cl WHERE link_id IN (".implode(", ",$softlinks).") AND main = '1'" );
		$softlink_cat_ids = $database->loadObjectList( "link_id" );
	}

	HTML_mtree::listcats( $cats, $links, $softlink_cat_ids, $mtCats, $catPageNav, $pageNav, $pathWay, $option );
}

function editcat( $cat_id, $cat_parent, $show='', $option ) {
	global $mtconf;

	$database 	=& JFactory::getDBO();

	$row = new mtCats( $database );
	$row->load( $cat_id );

	if ($row->cat_id == 0) {
		$row->cat_id = 0;
		$row->cat_name = '';
		$row->cat_parent = $cat_parent;
		$row->cat_links = 0;
		$row->cat_cats = 0;
		$row->cat_featured = 0;
		$row->cat_published = 1;
		$row->cat_approved = 1;
		$row->cat_image = '';
		$row->cat_allow_submission = 1;
		$row->cat_show_listings = 1;
		$row->cat_image = '';
		$row->alias = '';
	} else {
		$cat_parent = $row->cat_parent;
	}

	$lists = array();
	$total_assoc_links = 0;

	$document	=& JFactory::getDocument();
	$document->addCustomTag("<link href=\"" .$mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_templates') . ((empty($row->link_template))?$mtconf->get('template'):$row->link_template) . '/editcategory.css' . "\" rel=\"stylesheet\" type=\"text/css\"/>");

	# Template select list
	// Decide if parent has a custom template assigned to it. If there is, select this template
	// by default.
	if ( $cat_parent > 0 && $cat_id == 0 ) {
		$database->setQuery( 'SELECT cat_template FROM #__mt_cats WHERE cat_id = ' . $database->quote($cat_parent) . ' LIMIT 1' );
		$parent_template = $database->loadResult();
	}
	$templateDirs	= JFolder::folders($mtconf->getjconf('absolute_path') . '/components/com_mtree/templates');
	$templates[] = JHTML::_('select.option', '', ( (!empty($parent_template)) ? 'Default ('.$parent_template.')' : 'Default' ) );

	foreach($templateDirs as $templateDir) {
		if ( $templateDir <> "index.html") $templates[] = JHTML::_('select.option', $templateDir, $templateDir );
	}

	$lists['templates'] = JHTML::_('select.genericlist', $templates, 'cat_template', 'class="inputbox" size="1"',
	'value', 'text', $row->cat_template );
	
	# Get related categories
	$database->setQuery( 'SELECT rel_id FROM #__mt_relcats WHERE cat_id = ' . $database->quote($cat_id) );
	$related_cats = $database->loadResultArray();

	# Compile list of categories - Related Categories
	$categories = array();
	$browse_cat = $row->getParent($cat_parent);
	// if ( $browse_cat > 0 ) {
	if ( $cat_id > 0 ) {
		$categories[] = JHTML::_('select.option', $row->cat_parent, '&lt;--Back' );
	}
	$database->setQuery( 'SELECT cat_id AS value, cat_name AS text FROM #__mt_cats '
	. 'WHERE cat_parent=' . $database->quote($cat_id) . ' ORDER BY cat_name ASC' );
	$categories = array_merge( $categories, $database->loadObjectList() );

	# new_related_cat
	$lists['new_related_cat'] = JHTML::_('select.genericlist', $categories, 'new_related_cat', 'size="8" class="text_area" style="display:block;width:50%;margin-top:6px;float:none"', 'value', 'text', ( ($row->cat_id == 0) ? $cat_parent : $row->cat_id ), 'browsecat' );

	# Yes/No select list for Approved Category
	$lists['cat_approved'] = JHTML::_('select.booleanlist', "cat_approved", 'class="inputbox"', (($row->cat_approved == 1) ? 1 : 0));

	# Yes/No select list for Featured Category
	$lists['cat_featured'] = JHTML::_('select.booleanlist', "cat_featured", 'class="inputbox"', $row->cat_featured);

	# Yes/No select list for "Published"
	$lists['cat_published'] = JHTML::_('select.booleanlist', "cat_published", 'class="inputbox"', $row->cat_published);

	# Yes/No select list for "Use Main Index"
	$lists['cat_usemainindex'] = JHTML::_('select.booleanlist', "cat_usemainindex", 'class="inputbox"', $row->cat_usemainindex);

	$lists['cat_allow_submission'] = JHTML::_('select.booleanlist', "cat_allow_submission", 'class="inputbox"', $row->cat_allow_submission);

	$lists['cat_show_listings'] = JHTML::_('select.booleanlist', "cat_show_listings", 'class="inputbox"', $row->cat_show_listings);

	# Get Pathway
	$pathWay = new mtPathWay( $cat_parent );

	# Get Return task - Used by listpending_cats
	$returntask	= JRequest::getCmd('returntask', '', 'post');

	# Get all fields for fields assigment
	$database->setQuery("SELECT * FROM #__mt_customfields ORDER BY ordering ASC");
	$customfields = $database->loadObjectList();
	
	if( $cat_parent == 0 )
	{
		# Get fields assigment
		$fields_map_cfs = array();
		
		// If this is a new category, assign all fields by default.
		if ($row->cat_id == 0) {
			foreach($customfields AS $customfield)
			{
				array_push($fields_map_cfs,$customfield->cf_id);
			}

		} else {
			$database->setQuery("SELECT cf_id FROM #__mt_fields_map WHERE cat_id = " . $row->cat_id);
			$fields_map_cfs = $database->loadResultArray();
		}
		
		# Get configuration groups
		$database->setQuery( 'SELECT * FROM #__mt_configgroup ' . (($show == 'all') ? '' : 'WHERE displayed = 1 AND overridable_by_category = 1 ') . 'ORDER BY ordering ASC' );
		$configgroups = $database->loadResultArray();

		// Get all configs
		$database->setQuery( 'SELECT c.* FROM (#__mt_config AS c, #__mt_configgroup AS cg) '
			. 'WHERE cg.groupname = c.groupname '
			. (($show == 'all') ? '' : 'AND c.displayed = \'1\' ')
			. 'AND c.overridable_by_category = 1 AND cg.overridable_by_category = 1 '
			. 'ORDER BY cg.ordering ASC, c.ordering' );
		$configs = $database->loadObjectList('varname');

		$cat_params = new JRegistry();
		$cat_params->loadJSON($row->metadata);

		# List of categories for associated category
		$database->setQuery( 'SELECT cat_id AS value, cat_name AS text FROM #__mt_cats '
			. 'WHERE cat_parent=' . $database->quote('0') . ' AND cat_id <> ' . $database->quote($row->cat_id)
			. ' ORDER BY cat_name ASC' 
		);
		$top_level_categories = $database->loadObjectList();
		array_unshift($top_level_categories,array("value"=>0,"text"=>JText::_('COM_MTREE_SELECT_A_CATEGORY')));
		
		$lists['cat_association'] = JHTML::_('select.genericlist', $top_level_categories, 'cat_association', '', 'value', 'text', $row->cat_association );
		
		// If this category is associated with another category, get
		// the total number of associated listings.
		if( $row->cat_association > 0 ) {
			$database->setQuery(
				'SELECT count(*)  FROM #__mt_links_associations AS lassoc '
				.	' LEFT JOIN #__mt_cl AS cl ON cl.link_id = lassoc.link_id2 AND main = 1 '
				.	' LEFT JOIN #__mt_cats AS cat ON cat.cat_id = cl.cat_id '
				.	' WHERE lft >= ' . $row->lft . ' AND rgt <= ' . $row->rgt
				);
			$total_assoc_links = $database->loadResult();
		}
	} else {
		$customfields 	= null;
		$fields_map_cfs	= null;
		$cat_params	= null;
		$configs	= null;
		$configgroups	= null;
	}

	HTML_mtree::editcat( $row, $cat_parent, $related_cats, $browse_cat, $customfields, $fields_map_cfs, $lists, $pathWay, $configs, $cat_params, $configgroups, $total_assoc_links, $returntask, $option );
}

function savecat( $option ) {
	global $mtconf;

	$app 		= JFactory::getApplication();
	$database 	=& JFactory::getDBO();
	$my		=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$template_all_subcats	= JRequest::getInt( 'template_all_subcats', 0, 'post');
	$related_cats 		= explode(',', JRequest::getVar( 'other_cats', '', 'post'));
	$remove_image		= JRequest::getInt( 'remove_image', 0);
	$cat_image		= JRequest::getVar( 'cat_image', null, 'files');
	
	if ( $related_cats[0] == '' ) {
		$related_cats = array();
	}

	$post = JRequest::get( 'post' );
	$post['cat_desc'] = JRequest::getVar('cat_desc', '', 'POST', 'string', JREQUEST_ALLOWRAW);
	
	$row = new mtCats( $database );
	if (!$row->bind( $post )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if( empty($row->alias) )
	{
		$row->alias = JFilterOutput::stringURLSafe($row->cat_name);
	}
	
	# Get the name of the old photo and category's association
	if ( $row->cat_id > 0 ) {
		$sql = 'SELECT cat_image, cat_association, lft, rgt FROM #__mt_cats WHERE cat_id = ' . $database->quote($row->cat_id);
		$database->setQuery($sql);
		$original_cat = $database->loadObject();
		$old_image = $original_cat->cat_image;
		$old_cat_association = $original_cat->cat_association;
	} else {
		$old_image = '';
		$old_cat_association = 0;
	}

	# Remove previous old image
	$msg = '';
	if ( $remove_image || ($old_image <> '' && array_key_exists('tmp_name',$cat_image) && !empty($cat_image['tmp_name'])) ) {
		$row->cat_image = '';

		if(file_exists($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_cat_original_image') . $old_image) && file_exists($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_cat_small_image') . $old_image) && is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_cat_small_image')) && is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_cat_original_image'))) {
			if(!unlink($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_cat_original_image') . $old_image) || !unlink($mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_cat_small_image') . $old_image)) {
				$msg .= JText::_( 'COM_MTREE_ERROR_DELETING_OLD_IMAGE' );
			}
		}
	}

	# Create Thumbnail
	if ( $cat_image['name'] <> '' ) {
		if(!is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_cat_small_image')) || !is_writable($mtconf->getjconf('absolute_path').$mtconf->get('relative_path_to_cat_original_image'))) {
			$msg .= JText::_( 'COM_MTREE_IMAGE_DIRECTORIES_NOT_WRITABLE' );
		} else {
			$mtImage = new mtImage();
			$mtImage->setDirectory( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_cat_small_image') );
			$mtImage->setMethod( $mtconf->get('resize_method') );
			$mtImage->setQuality( $mtconf->get('resize_quality') );
			$mtImage->setSize( $mtconf->get('resize_cat_size') );
			$mtImage->setTmpFile( $cat_image['tmp_name'] );
			$mtImage->setType( $cat_image['type'] );
			if($row->cat_id > 0) {
				$mtImage->setName( $row->cat_id . '_' . $cat_image['name'] );
				$row->cat_image = $row->cat_id . '_' . $cat_image['name'];
			} else {
				$mtImage->setName( $cat_image['name'] );
				$row->cat_image = $cat_image['name'];
			}
			$mtImage->setSquare(false);
			$mtImage->resize();
			$mtImage->saveToDirectory();
			move_uploaded_file($cat_image['tmp_name'],$mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_cat_original_image') . $row->cat_image);
		}
	}

	# Is this a new category?
	// Category created by conventional "Add Category" link
	if ($row->cat_id == 0) {
		$new_cat = true;
		$row->cat_created = $now;
	} else {

		$database->setQuery( 'SELECT cat_approved FROM #__mt_cats WHERE cat_id = ' . $database->quote($row->cat_id) );
		$cat_approved = $database->loadResult();
		// Approved new category submitted by users
		if ( $row->cat_approved == 1 && $cat_approved == 0 && $row->lft == 0 && $row->rgt == 0 ) {
			$new_cat = true;
			$row->cat_created = $now;
		} else {
			$new_cat = false;
		}
	}
	
	# Category association
	if( $old_cat_association == $row->cat_association )
	{
		// Category association remains unchanged. No action needed
	}
	elseif ($old_cat_association > 0 )
	{
		// Category association has been changed. 
		// Delete all current associated listings for new one to take place.

		// Get all listings (link_id2) that is associated inside this
		// category and its sub-categories. If any is found, remove 
		// them.
		$database->setQuery(
			'SELECT lassoc.link_id2 FROM #__mt_links_associations AS lassoc '
			.	' LEFT JOIN #__mt_cl AS cl ON cl.link_id = lassoc.link_id2 AND main = 1 '
			.	' LEFT JOIN #__mt_cats AS cat ON cat.cat_id = cl.cat_id '
			.	' WHERE lft >= ' . $original_cat->lft . ' AND rgt <= ' . $original_cat->rgt
			);
		$link_id2s = $database->loadResultArray();

		if( !empty($link_id2s) ) {
			$database->setQuery(
				'DELETE FROM #__mt_links_associations '
			.	' WHERE link_id2 IN ('.implode(',',$link_id2s).')'
			);
			$database->query();
		}
		
	}
	
	# Save config override
	$row->metadata = '';
	
	if( !empty($post['override']) )
	{
		$params = array();
		foreach($post['override'] AS $override => $value)
		{
			$params[$override] = $post['config'][$override];
		}
		$registry = new JRegistry();
		$registry->loadArray($params);
		$row->metadata = (string)$registry;
	}
	
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	# Change all subcats to use this template
	if ( $template_all_subcats == 1 ) {
		$row->updateSubCatsTemplate();
	}

	# Update the Category Counts for all cat_parent(s)
	if ($new_cat) {
		$row->updateLftRgt();
		$row->updateCatCount( 1 );
	}

	$row->reorder( "cat_parent='$row->cat_parent'" );

	# Update the related categories
	$mtRelCats = new mtRelCats( $database );
	$mtRelCats->setcatid( $row->cat_id );
	$mtRelCats->update( $related_cats );

	# Save fields map
	$database->setQuery("DELETE FROM #__mt_fields_map WHERE cat_id = " . $row->cat_id);
	$database->query();
	
	if( !empty($post['fields_map_cfs']) )
	{
		$fields_map_insert_values = implode(','.$row->cat_id.'),(',$post['fields_map_cfs']);
		$database->setQuery("INSERT INTO #__mt_fields_map (`cf_id`,`cat_id`) VALUES (".$fields_map_insert_values.",".$row->cat_id.")");
		$database->query();
	}
	
	$returntask	= JRequest::getCmd('returntask', '', 'post');
	
	// /*
	if ( $returntask <> '' ) {
		$app->redirect( "index.php?option=$option&task=$returntask", $msg );
	} else {
		$task = JRequest::getCmd( 'task', '', 'post');

		if ( $task == "applycat" ) {
			$app->redirect( "index.php?option=$option&task=editcat&cat_id=$row->cat_id", $msg );
		} else {
			$app->redirect( "index.php?option=$option&task=listcats&cat_id=$row->cat_parent", $msg );
		}
	}
	// */
}

function fastadd_cat( $cat_parent, $option ) {
	
	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();
	$jdate		= JFactory::getDate();
	$now		= $jdate->toMySQL();
	
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );
	$cat_names = preg_split('/\n/', JRequest::getVar( 'cat_names', '', 'post'));

	# Default Template
	// Decide if parent has a custom template assigned to it. If there is, use this template.
	if ( $cat_parent > 0 ) {
		$database->setQuery( 'SELECT cat_template FROM #__mt_cats WHERE cat_id = ' . $database->quote($cat_parent) . ' LIMIT 1' );
		$parent_template = $database->loadResult();
	}

	foreach( $cat_names AS $cat_name) {
		$cat_name = trim($cat_name);
		if ( !empty($cat_name) ) {
			
			$row = new mtCats( $database );
			$row->cat_name = stripslashes($cat_name);
			$row->alias = JFilterOutput::stringURLSafe($row->cat_name);
			$row->cat_created = $now;
			$row->cat_parent = $cat_parent;
			$row->cat_published = 1;
			$row->cat_approved = 1;
			if ( isset($parent_template) ) {
				$row->cat_template = $parent_template;
			}
			
			if (!$row->store()) {
				echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
				exit();
			}

			$row->updateLftRgt();
			# Update the Category Counts for all cat_parent(s)
			$row->updateCatCount( 1 );
			$row->updateFieldsMap();

			unset($row);
		}
	}
	?>
	<script type="text/javascript">
		window.addEventListener('load', function() {
			if (window.parent) {window.parent.location.reload(true);window.parent.SqueezeBox.close();}
		});
	</script>
	<?php
}

function publishCats( $cat_id=null, $publish=1,  $option ) {

	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	if (!is_array( $cat_id ) || count( $cat_id ) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script> alert('".JText::sprintf( 'COM_MTREE_SELECT_AN_ITEM_TO', $action )."'); window.history.go(-1);</script>\n";
		exit;
	}

	$cat_ids = implode( ',', $cat_id );

	$database->setQuery( "UPDATE #__mt_cats SET cat_published='$publish'"
		. "\nWHERE cat_id IN ($cat_ids)"
	);
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	} else {
		$dispatcher	=& JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$dispatcher->trigger('onCategoryChangeState', array($option, $cat_id, (int)$publish));
	}

	$row = new mtCats( $database );
	$row->load( $cat_id[0] );

	$app->redirect( "index.php?option=$option&task=listcats&cat_id=".$row->getParent() );
}

function removecats( $cat_id, $option ) {

	$database 	=& JFactory::getDBO();

	if (!is_array( $cat_id ) || count( $cat_id ) < 1) {
		echo "<script> alert('".JText::_( 'COM_MTREE_SELECT_AN_ITEM_TO_DELETE' )."'); window.history.go(-1);</script>\n";
		exit;
	}

	$database->setQuery( "SELECT * FROM #__mt_cats WHERE cat_id IN (".implode(", ",$cat_id).")" );
	$categories = $database->loadObjectList();

	$row = new mtCats( $database );
	$row->load( $cat_id[0] );

	HTML_mtree::removecats( $categories, $row->getParent(), $option );
	
}

function removecats2( $cat_id, $option ) {

	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$row = new mtCats( $database );
	$row->load( $cat_id[0] );
	
	$cat_parent = $row->getParent();

	if (!is_array( $cat_id ) || count( $cat_id ) < 1) {
		echo "<script> alert('".JText::_( 'COM_MTREE_SELECT_AN_ITEM_TO_DELETE' )."'); window.history.go(-1);</script>\n";
		exit;
	}
	if (count( $cat_id )) {
		$totalcats = 0;
		$totallinks = 0;
		foreach($cat_id AS $cid) {
			$row->load( $cid );
			$totalcats += ($row->cat_cats +1);
			$totallinks += $row->cat_links;
			$row->deleteCats( $cid );
		}
		# Update Cat & Link count
		smartCountUpdate( $cat_parent, (($totallinks)*-1), (($totalcats)*-1) );
	}

	$returntask	= JRequest::getCmd('returntask', '', 'post');

	if ( $returntask <> '' ) {
		$app->redirect( "index.php?option=$option&task=$returntask", JText::plural( 'COM_MTREE_N_CATS_DELETED', count($cat_id) ) );
	} else {
		$app->redirect( "index.php?option=$option&task=listcats&cat_id=".$cat_parent, JText::plural( 'COM_MTREE_N_CATS_DELETED', count($cat_id) ) );
	}

}

function featuredCats( $cat_id, $featured=1, $option ) {
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$row = new mtCats( $database );
	
	if (count( $cat_id )) {
		foreach($cat_id AS $cid) {
			$row->setFeaturedCat( $featured, $cid );
		}
	}

	$row->load( $cid );
	
	$app->redirect( "index.php?option=$option&task=listcats&cat_id=".$row->getParent() );
}

function orderCats( $cat_id, $inc, $option ) {
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );
	$row = new mtCats( $database );
	$row->load( $cat_id );
	$row->move( $inc, "cat_parent = '$row->cat_parent'" );

	$app->redirect( "index.php?option=$option&task=listcats&cat_id=$row->cat_parent" );
}

function cancelcat( $cat_parent, $option ) {
	
	$app	= JFactory::getApplication('site');

	# Check return task - used to return to listpending_cats
	$returntask	= JRequest::getCmd('returntask', '', 'post');
	
	if ( $returntask <> '' ) {
		$app->redirect( "index.php?option=$option&task=$returntask" );
	} else {
		$app->redirect( "index.php?option=$option&task=listcats&cat_id=$cat_parent" );
	}

}

function cancelcats_move( $cat_id, $option ) {
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	$cat = new mtCats( $database );
	$cat->load( $cat_id );

	$app->redirect( "index.php?option=$option&task=listcats&cat_id=$cat->cat_parent" );
}

function moveCats( $cat_id, $cat_parent, $option ) {

	$database 	=& JFactory::getDBO();

	if (!is_array( $cat_id ) || count( $cat_id ) < 1) {
		echo "<script> alert('".JText::_( 'COM_MTREE_SELECT_AN_ITEM_TO_MOVE' )."'); window.history.go(-1);</script>\n";
		exit;
	}	

	# Get Pathway
	$pathWay = new mtPathWay( $cat_parent );

	# Get all category under cat_parent except those which is moving
	$cat_ids = 	implode( ',', $cat_id );
	$database->setQuery('SELECT cat_id AS value, cat_name AS text FROM #__mt_cats WHERE cat_parent = ' . $database->quote($cat_parent) . ' AND cat_id NOT IN (' . $cat_ids . ') ORDER BY cat_name ASC');
	$rows = $database->loadObjectList();

	# Get Parent's parent
	if ( $cat_parent > 0 ) {
		$database->setQuery('SELECT cat_parent FROM #__mt_cats WHERE cat_id = ' . $database->quote($cat_parent));
		$cat_back = JHTML::_('select.option', $database->loadResult(), '&lt;--Back' );
		array_unshift( $rows, $cat_back );
	}
	
	$cats = $rows;
	$catList = JHTML::_('select.genericlist', $cats, 'cat_parent', 'class="text_area" size="8" style="width:30%"', 'value', 'text', null, 'browsecat' );

	HTML_mtree::move_cats( $cat_id, $cat_parent, $catList, $pathWay, $option );

}

function moveCats2( $cat_id, $option ) {
	
	$app		= JFactory::getApplication('site');
	$database =& JFactory::getDBO();
	
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$new_cat_parent_id = JRequest::getInt( 'new_cat_parent', '', 'post');
	
	if( $new_cat_parent_id == 0 ) {
		$database->setQuery( "SELECT cat_id, lft, rgt FROM #__mt_cats WHERE cat_parent = -1" );
		$new_cat_parent = $database->loadObject();
	} else {
		$database->setQuery( 'SELECT cat_id, lft, rgt FROM #__mt_cats WHERE cat_id = ' . $database->quote($new_cat_parent_id) );
		$new_cat_parent = $database->loadObject();
	}
	
	if( in_array($new_cat_parent_id,$cat_id) ) {
		$app->redirect( "index.php?option=$option", JText::_('COM_MTREE_YOU_CAN_NOT_MOVE_CATEGORIES_IN_TO_ITSELF') );
		return;
	}
	
	$row = new mtCats( $database );

	# Loop every moving categories 
	if ( count( $cat_id ) > 0 ) {

		$total_cats = 0;
		$total_links = 0;

		foreach( $cat_id AS $id ) {
			$row->load( $id );

			$total_cats++;
			$total_cats += $row->cat_cats;
			$total_links += $row->cat_links;
			
			# Assign new cat_parent
			$old_cat_parent = $row->cat_parent;
			if( $new_cat_parent_id == 0 ) {
				$row->cat_parent = 0;
			} else {
				$row->cat_parent = $new_cat_parent->cat_id;
			}

			if (!$row->store()) {
				echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
				exit();
			}
			
			$inc = $new_cat_parent->rgt - $row->lft;
			$original_row_lft = $row->lft;
			$original_row_rgt = $row->rgt;
			$subcats = $row->getSubCats_Recursive( $id );

			# Categories are moved to the right
			if ( $row->rgt < $new_cat_parent->rgt ) {
				# (1) Update all category's lft and rgt to the right of this new node to accommodate new categories
				$database->setQuery("UPDATE #__mt_cats SET lft = lft+".(2*count($subcats))." WHERE lft >= $new_cat_parent->rgt");
				$database->query();

				$database->setQuery("UPDATE #__mt_cats SET rgt = rgt+".(2*count($subcats))." WHERE rgt >= $new_cat_parent->rgt");
				$database->query();

				# (2) Update lft & rgt values of moving categories
				$database->setQuery( "UPDATE #__mt_cats SET lft = lft + $inc, rgt = rgt + $inc WHERE lft >= $row->lft AND rgt <= $row->rgt" );
				$database->query();

				# (3) Finally, update all lft & rgt from the old node
				$database->setQuery("UPDATE #__mt_cats SET lft = lft-".(2*count($subcats))." WHERE lft >= $original_row_lft");
				$database->query();

				$database->setQuery("UPDATE #__mt_cats SET rgt = rgt-".(2*count($subcats))." WHERE rgt >= $original_row_rgt");
				$database->query();

			# Categories are moved to the left
			} else {
				# (1) Update all category's lft and rgt to the right of this new node to accommodate new categories
				$database->setQuery("UPDATE #__mt_cats SET lft = lft+".(2*count($subcats))." WHERE lft >= $new_cat_parent->rgt");
				$database->query();

				$database->setQuery("UPDATE #__mt_cats SET rgt = rgt+".(2*count($subcats))." WHERE rgt >= $new_cat_parent->rgt");
				$database->query();

				# (2) Update lft & rgt values of moving categories
				$database->setQuery( "UPDATE #__mt_cats SET lft = lft +($inc - ".(2*count($subcats))."), rgt = rgt +($inc - ".(2*count($subcats)).") WHERE lft >= ($row->lft + ".(2*count($subcats)).") AND rgt <= ($row->rgt + ".(2*count($subcats)).")" );
				$database->query();

				# (3) Finally, update all lft & rgt from the old node
				$database->setQuery("UPDATE #__mt_cats SET lft = lft-".(2*count($subcats))." WHERE lft >= $original_row_lft + ".(2*count($subcats)));
				$database->query();

				$database->setQuery("UPDATE #__mt_cats SET rgt = rgt-".(2*count($subcats))." WHERE rgt >= $original_row_rgt + ".(2*count($subcats)) );
				$database->query();

			}

		} // End foreach

		smartCountUpdate_catMove( $old_cat_parent, $new_cat_parent->cat_id, $total_links, $total_cats );

	} // End if

	$app->redirect( "index.php?option=$option&task=listcats&cat_id=$row->cat_parent" );
}

function copyCats( $cat_id, $cat_parent, $option ) {
	
	$database =& JFactory::getDBO();
	
	if (!is_array( $cat_id ) || count( $cat_id ) < 1) {
		echo "<script> alert('".JText::_( 'COM_MTREE_SELECT_AN_ITEM_TO_COPY' )."'); window.history.go(-1);</script>\n";
		exit;
	}	

	# Get Pathway
	$pathWay = new mtPathWay( $cat_parent );

	# Get all category under cat_parent except those which is moving
	$cat_ids = 	implode( ',', $cat_id );
	$database->setQuery('SELECT cat_id AS value, cat_name AS text FROM #__mt_cats WHERE cat_parent = ' . $database->quote($cat_parent) . ' AND cat_id NOT IN (' . $cat_ids . ') ORDER BY cat_name ASC');
	$rows = $database->loadObjectList();

	# Get Parent's parent
	if ( $cat_parent > 0 ) {
		$database->setQuery('SELECT cat_parent FROM #__mt_cats WHERE cat_id = ' . $database->quote($cat_parent));
		$cat_back = JHTML::_('select.option', $database->loadResult(), JText::_( 'COM_MTREE_ARROW_BACK' ) );
		array_unshift( $rows, $cat_back );
	}
	
	$cats = $rows;

	# Copy Related Cats?
	$copy_relcats			= JRequest::getInt( 'copy_relcats', 0, 'post');
	$lists['copy_relcats'] 	= JHTML::_('select.booleanlist', "copy_relcats", 'class="inputbox"', $copy_relcats);

	# Copy subcats?
	$copy_subcats 			= JRequest::getInt( 'copy_subcats', 1, 'post');
	$lists['copy_subcats'] 	= JHTML::_('select.booleanlist', "copy_subcats", 'class="inputbox"', $copy_subcats);

	# Copy Listings?
	$copy_listings 			= JRequest::getInt( 'copy_listings', 1, 'post');
	$lists['copy_listings'] = JHTML::_('select.booleanlist', "copy_listings", 'class="inputbox"', $copy_listings);

	# Copy Reviews?
	$copy_reviews 			= JRequest::getInt( 'copy_reviews', 0, 'post');
	$lists['copy_reviews'] 	= JHTML::_('select.booleanlist', "copy_reviews", 'class="inputbox"', $copy_reviews);

	# Reset Hits?
	$reset_hits 			= JRequest::getInt( 'reset_hits', 1, 'post');
	$lists['reset_hits'] 	= JHTML::_('select.booleanlist', "reset_hits", 'class="inputbox"', $reset_hits);

	# Reset Rating & Votes?
	$reset_rating 			= JRequest::getInt( 'reset_rating', 1, 'post');
	$lists['reset_rating'] 	= JHTML::_('select.booleanlist', "reset_rating", 'class="inputbox"', $reset_rating);

	# Main Category list
	$lists['cat_id'] = JHTML::_('select.genericlist', $cats, 'cat_parent', 'class="text_area" size="8" style="width:30%"', 'value', 'text', null, 'browsecat' );
	
	HTML_mtree::copy_cats( $cat_id, $cat_parent, $lists, $pathWay, $option );
}

function copyCats2( $cat_id, $option ) {
	
	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$new_cat_parent_id 	= JRequest::getInt( 'new_cat_parent', '', 'post');
	$copy_subcats 		= JRequest::getInt( 'copy_subcats', 	1, 'post');
	$copy_relcats		= JRequest::getInt( 'copy_relcats', 	0, 'post');
	$copy_listings 		= JRequest::getInt( 'copy_listings', 	1, 'post');
	$copy_reviews 		= JRequest::getInt( 'copy_reviews', 	0, 'post');
	$reset_hits 		= JRequest::getInt( 'reset_hits', 		1, 'post');
	$reset_rating 		= JRequest::getInt( 'reset_rating', 	1, 'post');

	$total_cats = 0;
	$total_links = 0;

	$row = new mtCats( $database );

	if ( count( $cat_id ) > 0 ) {

		foreach( $cat_id AS $id ) {

			$database->setQuery( 'SELECT cat_id, lft, rgt FROM #__mt_cats WHERE cat_id = ' . $database->quote($new_cat_parent_id) );
			$new_cat_parent = $database->loadObject();

			$copied_cat_ids = $row->copyCategory( $id, $new_cat_parent->cat_id, $copy_subcats, $copy_relcats, $copy_listings, $copy_reviews, $reset_hits, $reset_rating, null );

			// Retrieve category's count
			$database->setQuery( 'SELECT cat_cats, cat_links FROM #__mt_cats WHERE cat_id = ' . $database->quote($id) . ' LIMIT 1' );
			$total = $database->loadObject();

			$total_cats++;
			$total_cats += $total->cat_cats;
			$total_links += $total->cat_links;

			//print_r( $copied_cat_ids );

			// Update all category's lft and rgt to the right of this new node to accommodate new categories
			$database->setQuery("UPDATE #__mt_cats SET lft = lft+".(2*count($copied_cat_ids))." WHERE lft >= $new_cat_parent->rgt AND cat_id NOT IN (".implode(",",$copied_cat_ids).")");
			$database->query();

			$database->setQuery("UPDATE #__mt_cats SET rgt = rgt+".(2*count($copied_cat_ids))." WHERE rgt >= $new_cat_parent->rgt AND cat_id NOT IN (".implode(",",$copied_cat_ids).")");
			$database->query();

		} // End foreach
	} // End if
	
	smartCountUpdate( $new_cat_parent_id, $total_links, $total_cats );

	$app->redirect( "index.php?option=$option&task=listcats&cat_id=$new_cat_parent->cat_id" );
}

function cat_order( $cat_id, $move, $option ) {
	
	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$row = new mtCats( $database );
	$row->load( $cat_id );
	$row->order($move);

	$app->redirect( "index.php?option=$option&task=listcats&cat_id=$row->cat_parent" );
}

/****
* Approval / Pending
*/
function listpending_links( $option ) {
	global $mtconf;
	
	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();

	# Get Pathway
	$pathWay = new mtPathWay();

	# Limits
	$limit = $app->getUserStateFromRequest( "viewlistlimit", 'limit', $mtconf->getjconf('list_limit') );
	$limitstart = $app->getUserStateFromRequest( "viewcli{$option}limitstart", 'limitstart', 0 );

	$database->setQuery('SELECT COUNT(*) FROM #__mt_links WHERE link_approved < 1');
	$total = $database->loadResult();

	# Page Navigation
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	# Get all pending links
	$sql = "SELECT * FROM #__mt_links"
		.	"\nWHERE link_approved < '1'"
		.	"\nORDER BY link_created ASC, link_modified DESC"
		.	"\nLIMIT $pageNav->limitstart,$pageNav->limit";
	$database->setQuery($sql);
	if(!$result = $database->query()) {
		echo $database->stderr();
		return false;
	}
	$links = $database->loadObjectList();

	HTML_mtree::listpending_links( $links, $pathWay, $pageNav, $option );
}

function approve_links( $link_id, $publish=0, $option ) {

	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	if (!is_array( $link_id ) || count( $link_id ) < 1) {
		echo "<script> alert('".JText::_( 'COM_MTREE_SELECT_AN_ITEM_TO_APPROVE' )."'); window.history.go(-1);</script>\n";
		exit;
	}
	
	if (count( $link_id )) {
		foreach( $link_id AS $lid ) {

			$mtLinks = new mtLinks( $database );
			$mtLinks->load( $lid );
			$mtLinks->publishLink( $publish );
			
			// Only increase Link count if this is an approval to a new listing
			if ( $mtLinks->link_approved == 0 ) {
				$mtLinks->updateLinkCount( 1 );
			} elseif( $mtLinks->link_approved < 0 ) {
				// Check if there is any category change during modification
				$database->setQuery( "SELECT cat_id FROM #__mt_cl WHERE link_id = ABS(".$mtLinks->link_approved.") AND main = '1'" );
				$ori_cat_id = $database->loadResult();
				if ( $ori_cat_id <> $mtLinks->cat_id ) {
					$mtLinks->updateLinkCount( 1 );
					mtUpdateLinkCount( $ori_cat_id, -1 );
				}			
			}
			$mtLinks->approveLink();
			unset($mtLinks);
		}
	}

	$app->redirect( "index.php?option=$option&task=listpending_links",JText::sprintf( 'COM_MTREE_LINKS_HAVE_BEEN_APRROVED',count( $link_id ) ) );	
}

function listpending_cats( $option ) {
	global $mtconf;
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	# Get Pathway
	$pathWay = new mtPathWay();

	# Limits
	$limit = $app->getUserStateFromRequest( "viewlistlimit", 'limit', $mtconf->getjconf('list_limit') );
	$limitstart = $app->getUserStateFromRequest( "viewcli{$option}limitstart", 'limitstart', 0 );

	$database->setQuery("SELECT COUNT(*) FROM #__mt_cats WHERE cat_approved <> '1'");
	$total = $database->loadResult();

	# Page Navigation
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	# Get all pending cats
	/*
	$sql = "SELECT cat.*, cimg.filename AS cat_image FROM #__mt_cats AS cat"
		. "\nLEFT JOIN #__mt_cats_images AS cimg ON cimg.cat_id = cat.cat_id"
	
	*/
	$sql = "SELECT cat.* FROM #__mt_cats AS cat"
		. "\nWHERE cat.cat_approved <> '1'"
		. "\nORDER BY cat.cat_created DESC"
		. "\nLIMIT $pageNav->limitstart,$pageNav->limit";
	$database->setQuery($sql);
	if(!$result = $database->query()) {
		echo $database->stderr();
		return false;
	}
	$cats = $database->loadObjectList();

	HTML_mtree::listpending_cats( $cats, $pathWay, $pageNav, $option );
}

function approve_cats( $cat_id, $publish=0, $option ) {

	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$mtCats = new mtCats( $database );

	if (!is_array( $cat_id ) || count( $cat_id ) < 1) {
		echo "<script> alert('".JText::_( 'COM_MTREE_SELECT_AN_ITEM_TO_APPROVE' )."'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count( $cat_id )) {
		foreach( $cat_id AS $cid ) {
			$mtCats->load( $cid );
			$mtCats->approveCat();
			$mtCats->publishCat( $publish );
			if( $mtCats->lft == 0 && $mtCats->rgt == 0 ) {
				$mtCats->updateLftRgt();
			}
			$mtCats->updateCatCount( 1 );
		}
	}

	$app->redirect( "index.php?option=$option&task=listpending_cats",sprintf(JText::_( 'COM_MTREE_CATS_HAVE_BEEN_APRROVED' ),count( $cat_id )) );	
}

function listpending_reviews( $option ) {
	global $mtconf;
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	# Get Pathway
	$pathWay = new mtPathWay();

	# Limits
	$limit = $app->getUserStateFromRequest( "viewlistlimit.pending_reviews", 'limit', $mtconf->getjconf('list_limit') );
	$limitstart = $app->getUserStateFromRequest( "viewcli{$option}limitstart.pending_reviews", 'limitstart', 0 );

	$database->setQuery("SELECT COUNT(*) FROM #__mt_reviews WHERE rev_approved <> '1'");
	$total = $database->loadResult();

	# Page Navigation
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	# Get all pending reviews
	$sql = "SELECT r.*, u.username AS username, u.email AS email, l.link_name, log.value FROM #__mt_reviews AS r"
		.	"\nLEFT JOIN #__users AS u ON u.id = r.user_id"
		.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
		.	"\nLEFT JOIN #__mt_log AS log ON log.link_id = r.link_id AND log.user_id = r.user_id AND log.log_type = 'vote' AND log.rev_id = r.rev_id"
		.	"\nWHERE r.rev_approved <> '1'"
		.	"\nORDER BY r.rev_date DESC"
		.	"\nLIMIT $pageNav->limitstart,$pageNav->limit";
		;

	$database->setQuery($sql);
	if(!$result = $database->query()) {
		echo $database->stderr();
		return false;
	}
	$reviews = $database->loadObjectList();

	HTML_mtree::listpending_reviews( $reviews, $pathWay, $pageNav, $option );
}

function save_pending_reviews( $option ) {
	global $mtconf;
	
	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();
	$mtReviews	= new mtReviews( $database );

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$reviews 		= JRequest::getVar( 'rev', 			'', 'post');
	$review_titles 	= JRequest::getVar( 'rev_title', 	'', 'post');
	$review_texts 	= JRequest::getVar( 'rev_text', 	'', 'post');
	$admin_notes 	= JRequest::getVar( 'admin_note', 	'', 'post');
	$email_message 	= JRequest::getVar( 'emailmsg', 	'', 'post');
	$send_email 	= JRequest::getVar( 'sendemail', 	'', 'post');

	foreach( $reviews AS $review_id => $action ) {
		
		$review_id = intval( $review_id );
		
		$database->setQuery( 'SELECT link_id, user_id FROM #__mt_reviews WHERE rev_id = ' . $database->quote($review_id) . ' LIMIT 1' );
		$rev = $database->loadObject();
		
		switch(intval($action)){
			// 1: Approve; 0: Ignore
			case '0';
			case '1';
				$database->setQuery( 'UPDATE #__mt_reviews SET rev_title = ' . $database->quote($review_titles[$review_id]) . ', rev_text = ' . $database->quote($review_texts[$review_id]) . ' WHERE rev_id = ' . $database->quote($review_id) );
				$database->query();

				if($action == 1) {
					$mtReviews->load( $review_id );
					$mtReviews->approveReview( 1 );
				} else if ($action == 0 ) {
					if(@isset($admin_notes) && @array_key_exists($review_id,$admin_notes)) {
						$database->setQuery( 'UPDATE #__mt_reviews SET admin_note = ' . $database->quote($admin_notes[$review_id]) . ' WHERE rev_id = ' . $database->quote($review_id) );
						$database->query();
					}
					if(@isset($send_email) && @array_key_exists($review_id,$send_email) && $send_email[$review_id] == 1) {
						$database->setQuery( 'UPDATE #__mt_reviews SET send_email = 1, email_message = ' . $database->quote($email_message[$review_id]) . ' WHERE rev_id = ' . $database->quote($review_id) . ' LIMIT 1' );
						$database->query();
					} else {
						$database->setQuery( 'UPDATE #__mt_reviews SET send_email = 0, email_message = \'\' WHERE rev_id = ' . $database->quote($review_id) . ' LIMIT 1' );
						$database->query();
					}
				}
				break;
			// -1: Reject; -2: Reject and remove vote
			case '-1':
			case '-2':
				if($action==-2){					
					$database->setQuery( 'SELECT * FROM #__mt_links WHERE link_id = ' . $database->quote($rev->link_id) . ' LIMIT 1' );
					$link = $database->loadObject();
					
					$database->setQuery( 'SELECT value FROM #__mt_log WHERE log_type = \'vote\' AND user_id = ' . $database->quote($rev->user_id) . ' AND link_id = ' . $database->quote($rev->link_id) . ' LIMIT 1' );
					$user_rating = $database->loadResult();
					
					if($link->link_votes == 1){
						$new_rating = 0;
					} elseif($link->link_rating > 0 && $link->link_votes > 0 && $user_rating > 0) {
						$new_rating = (($link->link_rating * $link->link_votes) - $user_rating) / ($link->link_votes -1);
					}
					$database->setQuery( 'UPDATE #__mt_links SET link_rating = ' . $database->quote($new_rating) . ', link_votes = ' . $database->quote($link->link_votes -1) . ' WHERE link_id = ' . $database->quote($link->link_id) );
					$database->query();
					unset($link);
					$database->setQuery( 'DELETE FROM #__mt_log WHERE log_type = \'vote\' AND rev_id = ' . $database->quote($review_id) . ' AND user_id = ' . $database->quote($rev->user_id) . ' LIMIT 1' );
					$database->query();
				}
				$database->setQuery( 'DELETE FROM #__mt_reviews WHERE rev_id = ' . $database->quote($review_id) . ' LIMIT 1' );
				$database->query();
				$database->setQuery( 'DELETE FROM #__mt_log WHERE log_type = \'review\' AND rev_id = ' . $database->quote($review_id) . ' AND user_id = ' . $database->quote($rev->user_id) . ' LIMIT 1' );
				$database->query();
				break;		
		}
		
		if($action <> 0 && !empty($email_message[$review_id])) {
			$subject = JText::sprintf( 'COM_MTREE_REJECTED_APPROVED_REVIEW_SUBJECT',$review_titles[$review_id] );
			
			$database->setQuery( 'SELECT email FROM #__users AS u WHERE u.id = ' . $database->quote($rev->user_id) . ' LIMIT 1' );
			$to_email = $database->loadResult();
			
			$from_name = $mtconf->get('predefined_reply_from_name');
			if(empty($from_name)) {
				$from_name = $mtconf->getjconf('fromname');
			}
			$from_email = $mtconf->get('predefined_reply_from_email');
			if(empty($from_email)) {
				$from_email = $mtconf->getjconf('mailfrom');
			}
			$bcc = $mtconf->get('predefined_reply_bcc');
			if(empty($bcc)) {
				$bcc = NULL;
			}			
			JFactory::getMailer()->sendMail( $from_email, $from_name, $to_email, $subject, $email_message[$review_id], 0, NULL, $bcc );
		}
		
	}
	$app->redirect( "index.php?option=$option&task=listpending_reviews" );

}

function listpending_reports( $option ) {
	global $mtconf;
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	# Get Pathway
	$pathWay = new mtPathWay();

	# Limits
	$limit = $app->getUserStateFromRequest( "viewlistlimit.pendingreports", 'limit', $mtconf->getjconf('list_limit') );
	$limitstart = $app->getUserStateFromRequest( "viewcli{$option}limitstart.pendingreports", 'limitstart', 0 );

	$database->setQuery("SELECT COUNT(*) FROM #__mt_reports WHERE rev_id = 0 && link_id > 0");
	$total = $database->loadResult();

	# Page Navigation
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);
	
	# Get all pending reports
	$sql = "SELECT r.*, u.username AS username, u.email AS email, l.link_name FROM #__mt_reports AS r"
		.	"\nLEFT JOIN #__users AS u ON u.id = r.user_id"
		.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
		.	"\nWHERE r.rev_id = 0 && r.link_id > 0"
		.	"\nORDER BY r.created DESC"
		.	"\nLIMIT $pageNav->limitstart,$pageNav->limit";

	$database->setQuery($sql);
	if(!$result = $database->query()) {
		echo $database->stderr();
		return false;
	}
	$reports = $database->loadObjectList();

	HTML_mtree::listpending_reports( $reports, $pathWay, $pageNav, $option );
}

function save_reports( $option ) {
	
	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$reports 		= JRequest::getVar( 'report', '', 'post');
	$admin_notes 	= JRequest::getVar( 'admin_note', '', 'post');

	foreach( $reports AS $report_id => $action ) {
		$report_id = intval($report_id);
		if($action == 1) {
			$database->setQuery( 'DELETE FROM #__mt_reports WHERE report_id = ' . $database->quote($report_id) );
			$database->query();
		} else {
			if( @isset($admin_notes) && @array_key_exists($report_id,$admin_notes) ) {
				$database->setQuery( 'UPDATE #__mt_reports SET admin_note = ' . $database->quote($admin_notes[$report_id]) . ' WHERE report_id = ' . $database->quote($report_id) );
				$database->query();
			}
		}
	}

	$app->redirect( "index.php?option=$option&task=listpending_reports" );

}

function listpending_reviewsreports( $option ) {
	global $mtconf;
	
	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();
	
	# Get Pathway
	$pathWay = new mtPathWay();

	# Limits
	$limit = $app->getUserStateFromRequest( "viewlistlimit.pending_reviews", 'limit', $mtconf->getjconf('list_limit') );
	$limitstart = $app->getUserStateFromRequest( "viewcli{$option}limitstart.pending_reviews", 'limitstart', 0 );

	$database->setQuery("SELECT COUNT(*) FROM #__mt_reports WHERE rev_id > 0 && link_id > 0");
	$total = $database->loadResult();

	# Page Navigation
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	# Get all pending reports
	$sql = "SELECT r.*, rev.rev_title, rev.rev_text, u2.username AS review_username, u2.id AS review_user_id, rev.rev_date, u.username AS username, u.email AS email, l.link_name FROM #__mt_reports AS r"
		.	"\nLEFT JOIN #__mt_reviews AS rev ON rev.rev_id = r.rev_id"
		.	"\nLEFT JOIN #__users AS u ON u.id = r.user_id"		// The person that made the report
		.	"\nLEFT JOIN #__users AS u2 ON u2.id = rev.user_id"	// The reviewer
		.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
		.	"\nWHERE r.rev_id > 0 && r.link_id > 0"
		.	"\nORDER BY r.created DESC";

	$database->setQuery($sql);
	if(!$result = $database->query()) {
		echo $database->stderr();
		return false;
	}
	$reports = $database->loadObjectList();

	HTML_mtree::listpending_reviewsreports( $reports, $pathWay, $pageNav, $option );
}

function save_reviewsreports( $option ) {
	
	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$reports 		= JRequest::getVar( 'report', '', 'post');
	$admin_notes 	= JRequest::getVar( 'admin_note', '', 'post');

	foreach( $reports AS $report_id => $action ) {
		$report_id = intval($report_id);
		if($action == 1) {
			$database->setQuery( 'DELETE FROM #__mt_reports WHERE report_id = ' . $database->quote($report_id) );
			$database->query();
		} else {
			if( @isset($admin_notes) && @array_key_exists($report_id,$admin_notes) ) {
				$database->setQuery( 'UPDATE #__mt_reports SET admin_note = ' . $database->quote($admin_notes[$report_id]) . ' WHERE report_id = ' . $database->quote($report_id) );
				$database->query();
			}
		}
	}

	$app->redirect( "index.php?option=$option&task=listpending_reviewsreports" );

}

function listpending_reviewsreply( $option ) {

	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();

	# Get Pathway
	$pathWay = new mtPathWay();

	# Get all pending owner's reply
	$sql = "SELECT r.*, u.username AS username, owner.username AS owner_username, owner.id AS owner_user_id, owner.email AS owner_email, u.email AS email, l.link_name FROM #__mt_reviews AS r"
		.	"\nLEFT JOIN #__users AS u ON u.id = r.user_id"
		.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
		.	"\nLEFT JOIN #__users AS owner ON owner.id = l.user_id"
		.	"\nWHERE r.ownersreply_approved = '0' AND r.ownersreply_text != ''"
		.	"\nORDER BY r.ownersreply_date DESC";

	$database->setQuery($sql);
	if(!$result = $database->query()) {
		echo $database->stderr();
		return false;
	}
	$reviewreplies = $database->loadObjectList();
	HTML_mtree::listpending_reviewsreply( $reviewreplies, $pathWay, $option );
}

function save_reviewsreply( $option ) {
	
	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();
	
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$ownersreplies 	= JRequest::getVar( 'or', 			'', 'post');
	$or_text 		= JRequest::getVar( 'or_text', 		'', 'post');
	$admin_notes 	= JRequest::getVar( 'admin_note', 	'', 'post');

	foreach( $ownersreplies AS $rev_id => $action ) {
		$rev_id = intval($rev_id);
		
		// 1: Approve; 0: Ignore; -1: Reject
		if ( $action == 1 || $action == 0 ) {

			$database->setQuery( 'UPDATE #__mt_reviews SET ownersreply_text = ' . $database->quote($or_text[$rev_id]) . ' WHERE rev_id = ' . $database->quote($rev_id) );
			$database->query();

			if($action == 1) {
				$database->setQuery( 'UPDATE #__mt_reviews SET ownersreply_approved = 1 WHERE rev_id = ' . $database->quote($rev_id) );
				$database->query();
			} else if ($action == 0 && @isset($admin_notes) && @array_key_exists($rev_id,$admin_notes) ) {
				$database->setQuery( 'UPDATE #__mt_reviews SET ownersreply_admin_note = ' . $database->quote($admin_notes[$rev_id]) . ' WHERE rev_id = ' . $database->quote($rev_id) );
				$database->query();
			}

		} else {
			$database->setQuery( 'UPDATE #__mt_reviews SET ownersreply_text = \'\', ownersreply_approved = \'0\', ownersreply_date = \'\', ownersreply_admin_note = \'\' WHERE rev_id = ' . $database->quote($rev_id) );
			$database->query();
		}

	}

	$app->redirect( "index.php?option=$option&task=listpending_reviewsreply" );

}

function listpending_claims( $option ) {

	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	# Get Pathway
	$pathWay = new mtPathWay();

	$database->setQuery("SELECT COUNT(*) FROM #__mt_claims");
	$total = $database->loadResult();

	# Get all pending claims
	$sql = "SELECT r.*, u.username AS username, u.name AS name, u.email AS email, l.link_name FROM #__mt_claims AS r"
		.	"\nLEFT JOIN #__users AS u ON u.id = r.user_id"
		.	"\nLEFT JOIN #__mt_links AS l ON l.link_id = r.link_id"
		.	"\nORDER BY r.created DESC";

	$database->setQuery($sql);
	if(!$result = $database->query()) {
		echo $database->stderr();
		return false;
	}
	$claims = $database->loadObjectList();

	HTML_mtree::listpending_claims( $claims, $pathWay, $option );
}

function save_claims( $option ) {
	global $mtconf;
	
	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();
	
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$claims 		= JRequest::getVar( 'claim', 		'', 'post');
	$admin_notes 	= JRequest::getVar( 'admin_note', 	'', 'post');

	foreach( $claims AS $claim_id => $user_id ) {
		$claim_id = intval($claim_id);
		$user_id = intval($user_id);
		
		if($user_id > 0) {
			
			$database->setQuery( 'SELECT c.link_id, l.link_name FROM (#__mt_claims AS c, #__mt_links AS l) WHERE claim_id = ' . $database->quote($claim_id) . ' AND c.link_id = l.link_id');
			$link = $database->loadObject();
			
			$database->setQuery( 'SELECT email FROM #__users WHERE id = ' . $database->quote($user_id) );
			$email = $database->loadResult();

			$database->setQuery( 'UPDATE #__mt_links SET user_id = ' . $database->quote($user_id) . ' WHERE link_id = ' . $database->quote($link->link_id) . ' LIMIT 1' );
			$database->query();

			$database->setQuery( 'DELETE FROM #__mt_claims WHERE claim_id = ' . $database->quote($claim_id) );
			$database->query();

			$subject = JText::_( 'COM_MTREE_CLAIM_APPROVED_SUBJECT' );
			$body = JText::sprintf( 'COM_MTREE_CLAIM_APPROVED_MSG', $link->link_name, $mtconf->getjconf('live_site')."/index.php?option=com_mtree&task=viewlink&link_id=$link->link_id" );

			JFactory::getMailer()->sendMail( $mtconf->getjconf('mailfrom'), $mtconf->getjconf('fromname'), $email, $subject, $body );

		} else if ( $user_id == -1) {
			$database->setQuery( 'DELETE FROM #__mt_claims WHERE claim_id = ' . $database->quote($claim_id) );
			$database->query();
		} else if ( $user_id == 0 ) {
			if( @isset($admin_notes) && @array_key_exists($claim_id,$admin_notes) ) {
				$database->setQuery( 'UPDATE #__mt_claims SET admin_note = ' . $database->quote($admin_notes[$claim_id]) . ' WHERE claim_id = ' . $database->quote($claim_id) );
				$database->query();
			}
		}

	}

	$app->redirect( "index.php?option=$option&task=listpending_claims" );

}

/****
* Reviews
*/
function list_reviews( $link_id, $option ) {
	global $mtconf;
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	# Get Link's info
	$link = new mtLinks( $database );
	$link->load( $link_id );

	# Get Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Limits
	$limit = $app->getUserStateFromRequest( "viewlistlimit", 'limit', $mtconf->getjconf('list_limit') );
	$limitstart = $app->getUserStateFromRequest( "viewcli{$option}limitstart", 'limitstart', 0 );

	$database->setQuery('SELECT COUNT(*) FROM #__mt_reviews WHERE rev_approved=1 && link_id = ' . $database->quote($link_id) );
	$total = $database->loadResult();

	# Page Navigation
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	# Get All the reviews
	$sql = "SELECT *, u.name AS username FROM #__mt_reviews AS r"
		. "\nLEFT JOIN #__users AS u ON u.id = r.user_id"
		. "\nWHERE r.rev_approved=1 && r.link_id = '".$link_id."'"
		. "\nLIMIT $pageNav->limitstart,$pageNav->limit";
	$database->setQuery($sql);
	if(!$result = $database->query()) {
		echo $database->stderr();
		return false;
	}
	$reviews = $database->loadObjectList();

	HTML_mtree::list_reviews( $reviews, $link, $pathWay, $pageNav, $option );

}

function editreview( $rev_id, $link_id, $option ) {
	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();

	$row = new mtReviews( $database );
	$row->load( $rev_id );

	if ($row->rev_id == 0) {
		$row->link_id = $link_id;
		$row->owner= $my->username;
		$row->rev_approved=1;
		$row->rev_date = $jdate->toMySQL();
		$row->not_registered = 0;
	} else {
		if ($row->user_id > 0) {
			$database->setQuery('SELECT username FROM #__users WHERE id =' . $database->quote($row->user_id) );
			$row->owner = $database->loadResult();
			$row->not_registered = 0;
		} else {
			$row->not_registered = 1;
		}
	}

	# Yes/No select list
	$lists['rev_approved'] = JHTML::_('select.booleanlist', "rev_approved", 'class="inputbox"', (($row->rev_approved == 1) ? 1 : 0));
	$lists['ownersreply_approved'] = JHTML::_('select.booleanlist', "ownersreply_approved", 'class="inputbox"', (($row->ownersreply_approved == 1) ? 1 : 0));

	# Lookup Cat ID
	$link = new mtLinks( $database );
	$link->load( $row->link_id );

	# Get Pathway
	$pathWay = new mtPathWay( $link->cat_id );

	# Get Return task - Used by listpending_links
	$returntask	= JRequest::getCmd('returntask', '', 'post');

	HTML_mtree::editreview( $row, $pathWay, $returntask, $lists, $option );
}

function savereview( $option ) {

	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();
	$my			=& JFactory::getUser();
	$jdate		= JFactory::getDate();
	
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$post = JRequest::get( 'post' );
	$row = new mtReviews( $database );
	if (!$row->bind( $post )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$owner = JRequest::getVar( 'owner', '', 'post');
	$not_registered	= JRequest::getInt( 'not_registered', 0);
	
	# Is this a new review?
	if ($row->rev_id == 0) {
		$row->rev_date = $jdate->toMySQL();	
	}

	# Lookup owner's userid. Return error if does not exists
	if ($owner == '') {
		// If owner field is left blank, assign the link to the current user
		$row->user_id = $my->id;
	} else {

		if ( $not_registered == 0 ) {
		
			$database->setQuery('SELECT id FROM #__users WHERE username = ' . $database->quote($owner) );
			$owner_id = $database->loadResult();
			if ($owner_id > 0) {
				$row->user_id = $owner_id;
			} else {
				echo "<script> alert('".JText::_( 'COM_MTREE_INVALID_OWNER_SELECT_AGAIN' )."'); window.history.go(-1); </script>\n";
				exit();
			}

		} else {
			$row->user_id = 0;
			$row->guest_name = $owner;
		}
	}

	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	# Check return task - used to return to listpending_links
	$returntask	= JRequest::getCmd('returntask', '', 'post');
	
	if ( $returntask <> '' ) {
		$app->redirect( "index.php?option=$option&task=$returntask" );
	} else {
		$app->redirect( "index.php?option=$option&task=reviews_list&link_id=$row->link_id" );
	}

}

function removeReviews( $rev_id, $option ) {

	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$row = new mtReviews( $database );
	$row->load( $rev_id[0] );

	if (!is_array( $rev_id ) || count( $rev_id ) < 1) {
		echo "<script> alert('".JText::_( 'COM_MTREE_SELECT_AN_ITEM_TO_DELETE' )."'); window.history.go(-1);</script>\n";
		exit;
	}
	if (count( $rev_id )) {
		$rev_ids = implode( ',', $rev_id );
		
		# Remove links
		$database->setQuery( "DELETE FROM #__mt_reviews WHERE rev_id IN ($rev_ids) LIMIT ".count( $rev_id ) );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}

		# Remove logs
		$database->setQuery( "DELETE FROM #__mt_log WHERE log_type = 'review' AND rev_id IN ($rev_ids) LIMIT ".count( $rev_id ) );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}

	$app->redirect( "index.php?option=$option&task=reviews_list&link_id=".$row->link_id, JText::plural('COM_MTREE_N_REVIEWS_DELETED', count($rev_id)) );
}

function cancelreview( $link_id, $option ) {
	
	$app		= JFactory::getApplication('site');

	# Check return task - used to return to listpending_links
	$returntask	= JRequest::getCmd('returntask', '', 'post');
	
	if ( $returntask <> '' ) {
		$app->redirect( "index.php?option=$option&task=$returntask" );
	} else {
		$app->redirect( "index.php?option=$option&task=reviews_list&link_id=$link_id" );
	}
}

function backreview( $link_id, $option ) {
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	$mtLinks = new mtLinks( $database );
	$mtLinks->load( $link_id );

	$app->redirect( "index.php?option=$option&task=listcats&cat_id=$mtLinks->cat_id" );
}

/***
* Search
*/
function search( $option ) {
	global $mtconf;
	
	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();

	$search_text 	= JRequest::getVar( 'search_text', '', 'post');
	$search_where	= JRequest::getInt( 'search_where', 0, 'post'); // 1: Listing, 2: Category

	$limit = $app->getUserStateFromRequest( "viewlistlimit", 'limit', $mtconf->getjconf('list_limit') );
	$limitstart = $app->getUserStateFromRequest( "viewcli{$option}limitstart", 'limitstart', 0 );
	# Detect search command
	
	# Quick Go
	$id_found = 0;
	if ( substr($search_text, 0, 3) == "id:" ) {
		$temp = explode(":", $search_text);
		if ( is_numeric($temp[1]) ) {
			$id_found = $temp[1];
		}
	}

	# Search query
	if ( $search_where == 1 ) {
		
		if ($id_found) {
			$link = new mtLinks( $database );
			$link->load( $id_found );
			
			if( !empty($link->link_name) ) {
				$app->redirect( "index.php?option=com_mtree&task=editlink&link_id=".$id_found );
			} else {
				$app->redirect( "index.php?option=com_mtree",JText::_( 'COM_MTREE_YOUR_SEARCH_DOES_NOT_RETURN_ANY_RESULT' ) );
			}

		} else {
			// Total Results
			$database->setQuery( 'SELECT COUNT(*) FROM #__mt_links '
				.	"\nWHERE link_name LIKE '%" . $database->getEscaped( $search_text, true ) . "%'"
				);
			$total = $database->loadResult();

			// Page Navigation
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($total, $limitstart, $limit);

			// Links
			$database->setQuery( "SELECT l.*, COUNT(r.rev_id) AS reviews FROM #__mt_links AS l"
				.	"\nLEFT JOIN #__mt_reviews AS r ON r.link_id = l.link_id"
				.	"\nWHERE l.link_name LIKE '%" . $database->getEscaped( $search_text, true ) . "%'"
				.	"\nGROUP BY l.link_id"
				.	"\nORDER BY l.link_name ASC"
				.	"\nLIMIT " . $pageNav->limitstart . ', ' . $pageNav->limit
				);

		}
		
	} else {

		if ($id_found) {
			$cat = new mtCats( $database );
			$cat->load( $id_found );
			
			if( !empty($cat->cat_name) ) {
				$app->redirect( "index.php?option=com_mtree&task=editcat&cat_id=".$id_found );
			} else {
				$app->redirect( "index.php?option=com_mtree",JText::_( 'COM_MTREE_YOUR_SEARCH_DOES_NOT_RETURN_ANY_RESULT' ) );
			}

		} else {

			// Total Results
			$database->setQuery( "SELECT COUNT(*) FROM #__mt_cats WHERE cat_name LIKE '%" . $database->getEscaped( $search_text, true ) ."%'" );
			$total = $database->loadResult();

			// Page Navigation
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($total, $limitstart, $limit);

			// Categories
			$database->setQuery( "SELECT * FROM #__mt_cats WHERE cat_name LIKE '%" . $database->getEscaped( $search_text, true ) . "%' ORDER BY cat_name ASC LIMIT $pageNav->limitstart, $pageNav->limit" );
		
		}

	}

	$results = $database->loadObjectList();

	# Get Pathway
	$pathWay = new mtPathWay();

	# Results Output
	if ( $search_where == 1 ) {
		// Links
		HTML_mtree::searchresults_links( $results, $pageNav, $pathWay, $search_where, $search_text, $option );
	} else {
		// Categories
		HTML_mtree::searchresults_categories( $results, $pageNav, $pathWay, $search_where, $search_text, $option );
	}

}

function advsearch( $option ) {
	global $mtconf;

	$database 	=& JFactory::getDBO();

	# Template select list
	$templateDirs	= JFolder::folders($mtconf->getjconf('absolute_path') . '/components/com_mtree/templates');
	$templates[] = JHTML::_('select.option', '', JText::_( 'COM_MTREE_DEFAULT' ) );

	foreach($templateDirs as $templateDir) {
		$templates[] = JHTML::_('select.option', $templateDir, $templateDir );
	}

	$lists['templates'] = JHTML::_('select.genericlist', $templates, 'link_template', 'class="inputbox" size="1"',
	'value', 'text', '' );
	
	# Publishing
	$publishing[] = JHTML::_('select.option', 1, JText::_( 'COM_MTREE_ALL' ) );
	$publishing[] = JHTML::_('select.option', 2, JText::_( 'COM_MTREE_PUBLISHED' ) );
	$publishing[] = JHTML::_('select.option', 3, JText::_( 'COM_MTREE_UNPUBLISHED' ) );
	$publishing[] = JHTML::_('select.option', 4, JText::_( 'COM_MTREE_PENDING' ) );
	$publishing[] = JHTML::_('select.option', 5, JText::_( 'COM_MTREE_EXPIRED' ) );
	$publishing[] = JHTML::_('select.option', 6, JText::_( 'COM_MTREE_PENDING_APPROVAL' ) );
	$lists['publishing'] = JHTML::_('select.genericlist', $publishing, 'publishing', 'class="inputbox" size="1"',	'value', 'text', '' );

	// Comparison option
	$comparison[] = JHTML::_('select.option', 1, JText::_( 'COM_MTREE_EXACTLY' ) );
	$comparison[] = JHTML::_('select.option', 2, JText::_( 'COM_MTREE_MORE_THAN' ) );
	$comparison[] = JHTML::_('select.option', 3, JText::_( 'COM_MTREE_LESS_THAN' ) );

	# Load all CORE and custom fields
	$database->setQuery( "SELECT cf.*, '0' AS link_id, '' AS value, '0' AS attachment FROM #__mt_customfields AS cf "
		.	"\nWHERE cf.published='1' ORDER BY ordering ASC" );
	$fields = new mFields($database->loadObjectList());

	# Search condition
	$searchConditions[] = JHTML::_('select.option', 1, strtolower(JText::_( 'COM_MTREE_ANY' )) );
	$searchConditions[] = JHTML::_('select.option', 2, strtolower(JText::_( 'COM_MTREE_ALL' )) );
	$lists['searchcondition'] = JHTML::_('select.genericlist', $searchConditions, 'searchcondition', 'class="inputbox" size="1"',
	'value', 'text', 1 );

	# Price
	$lists['price'] = JHTML::_('select.genericlist', $comparison, 'price_2', 'class="inputbox" size="1"',
	'value', 'text', 1 );

	# Rating
	$lists['rating'] = JHTML::_('select.genericlist', $comparison, 'rating_2', 'class="inputbox" size="1"',
	'value', 'text', 1 );

	# Votes
	$lists['votes'] = JHTML::_('select.genericlist', $comparison, 'votes_2', 'class="inputbox" size="1"',
	'value', 'text', 1 );

	# Hits
	$lists['hits'] = JHTML::_('select.genericlist', $comparison, 'hits_2', 'class="inputbox" size="1"',
	'value', 'text', 1 );

	# Reviews
	$lists['reviews'] = JHTML::_('select.genericlist', $comparison, 'reviews_2', 'class="inputbox" size="1"',
	'value', 'text', 1 );

	HTML_mtree::advsearch( $fields, $lists, $option );
}

function advsearch2( $option ) {
	global $mtconf;

	$database =& JFactory::getDBO();
	
	$post 			= JRequest::get( 'post' );
	$search_where 	= JRequest::getVar( 'search_where', '', 'post'); // 1: Listing, 2: Category
	$limit			= JRequest::getInt( 'limit', 15);
	$limitstart		= JRequest::getInt( 'limitstart', 0);
	$owner 			= JRequest::getVar( 'owner', '', 'post');

	$searchParams = array();
	
	# Load all published CORE & custom fields
	$database->setQuery( "SELECT cf.*, '0' AS link_id, '' AS value, '0' AS attachment FROM #__mt_customfields AS cf "
		.	"\nWHERE cf.published='1' ORDER BY ordering ASC" );
	$fields = new mFields($database->loadObjectList());

	$searchParams = $fields->loadSearchParams($post);

	foreach( array('publishing','link_template','link_rating','rating_2','link_votes','votes_2','link_hits','hits_2','reviews','reviews_2','internal_notes','metakey','metadesc', 'price_2', 'price') AS $otherField ) {
		$searchParams[$otherField] = JRequest::getVar( $otherField, '', 'post');
	}

	# Search query
	if ( $search_where == 1 ) {
		
		$where = array();
		$having = '';
		$advsearch = new mAdvancedSearch( $database );

		if( JRequest::getInt( 'searchcondition', 1, 'post') == '2' ) {
			$advsearch->useAndOperator();
		} else {
			$advsearch->useOrOperator();
		}

		$fields->resetPointer();
		while( $fields->hasNext() ) {
			$field = $fields->getField();
			$searchFields = $field->getSearchFields();
			
			if( array_key_exists(0,$searchFields) && isset($searchParams[$searchFields[0]]) && !empty($searchParams[$searchFields[0]]) ) {
				foreach( $searchFields AS $searchField ) {
					if( isset($searchParams[$searchField]) ) {
						$searchFieldValues[] = $searchParams[$searchField];
					}
				}
				if( count($searchFieldValues) > 0 && !empty($searchFieldValues[0]) ) {
					if( is_array($searchFieldValues[0]) && empty($searchFieldValues[0][0]) ) {
						// Do nothing
					} else {
						$tmp_where_cond = call_user_func_array(array($field, 'getWhereCondition'),$searchFieldValues);
						if( !is_null($tmp_where_cond) ) {
							$advsearch->addCondition( $field, $searchFieldValues );
						} 
					}
				}
				unset($searchFieldValues);
			}
			$fields->next();
		}

		if(!empty($searchParams['metadesc'])) {
			$advsearch->addRawCondition( 'metadesc LIKE \'%' . $database->getEscaped($searchParams['metadesc'], true) . '%\'');
		}

		if(!empty($searchParams['metakey'])) {
			$advsearch->addRawCondition( 'metakey LIKE \'%' . $database->getEscaped($searchParams['metakey'], true) . '%\'');
		}

		if(!empty($searchParams['internal_notes'])) {
			$advsearch->addRawCondition( 'internal_notes LIKE \'%' . $database->getEscaped($searchParams['internal_notes']) . '%\'');
		}

		if ( !empty($searchParams['link_template']) ) {
			$advsearch->addRawCondition( 'link_template = ' . $database->quote($searchParams['link_template']) );
		}

		if ( is_numeric($searchParams['link_rating']) && $searchParams['link_rating'] >= 0 && $searchParams['link_rating'] <= 5 ) {
			switch($searchParams['rating_2']) {
				case 1:
					$advsearch->addRawCondition( 'link_rating = ' . $database->quote($searchParams['link_rating']) );
					break;
				case 2:
					$advsearch->addRawCondition( 'link_rating > ' . $database->quote($searchParams['link_rating']) );
					break;
				case 3:
					$advsearch->addRawCondition( 'link_rating < ' . $database->quote($searchParams['link_rating']) );
					break;
			}
		}
		
		// votes
		if ( is_numeric($searchParams['link_votes']) && $searchParams['link_votes'] >= 0 ) {
			switch($searchParams['votes_2']) {
				case 1:
					$advsearch->addRawCondition( 'link_votes = ' . $database->quote($searchParams['link_votes']) );
					break;
				case 2:
					$advsearch->addRawCondition( 'link_votes > ' . $database->quote($searchParams['link_votes']) );
					break;
				case 3:
					$advsearch->addRawCondition( 'link_votes < ' . $database->quote($searchParams['link_votes']) );
					break;
			}
		}
		
		// hits
		if ( is_numeric($searchParams['link_hits']) && $searchParams['link_hits'] >= 0 ) {
			switch($searchParams['hits_2']) {
				case 1:
					$advsearch->addRawCondition( 'link_hits = ' . $database->quote($searchParams['link_hits']) );
					break;
				case 2:
					$advsearch->addRawCondition( 'link_hits > ' . $database->quote($searchParams['link_hits']) );
					break;
				case 3:
					$advsearch->addRawCondition( 'link_hits < ' . $database->quote($searchParams['link_hits']) );
					break;
			}
		}

		// price
		if ( is_numeric($searchParams['price']) && $searchParams['price'] >= 0 ) {
			switch($searchParams['price_2']) {
				case 1:
					$advsearch->addRawCondition( 'price = ' . $database->quote($searchParams['price']) );
					break;
				case 2:
					$advsearch->addRawCondition( 'price > ' . $database->quote($searchParams['price']) );
					break;
				case 3:
					$advsearch->addRawCondition( 'price < ' . $database->quote($searchParams['price']) );
					break;
			}
		}

		$jdate = JFactory::getDate();
		$now = $jdate->toMySQL();
		$nullDate	= $database->getNullDate();

		switch ($searchParams['publishing']) {
			case 2: // Published
				$advsearch->addRawCondition( "( (publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now')  AND "
				. "(publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now') AND "
				.	"link_published = '1' )" );
				break;
			case 3: // Unpublished
				$advsearch->addRawCondition( "link_published = '0'" );
				break;
			case 4: // Pending
				$advsearch->addRawCondition( "( (publish_up => '$now' OR publish_up = ".$database->Quote($nullDate).") AND link_published = '1' )" );
				break;
			case 5: // Expired
				$advsearch->addRawCondition( "( publish_down < '$now' AND publish_down <> '$nullDate' AND link_published = '1' )" );
				break;
			case 6: // Pending Listing, waiting for approval
				$advsearch->addRawCondition( "link_approved <= 0" );
				break;
		}

		# Check if this owner exists
		# Lookup owner's userid. Return error if does not exists
		if ( !empty($owner) ) {
			$database->setQuery('SELECT id FROM #__users WHERE username =' . $database->quote($owner));
			$owner_id = $database->loadResult();
			if ($owner_id > 0) {
				$advsearch->addRawCondition( 'l.user_id = ' . $database->quote($owner_id) );
			} else {
				echo "<script> alert('".JText::_( 'COM_MTREE_INVALID_OWNER_SELECT_AGAIN' )."'); window.history.go(-1); </script>\n";
				exit();
			}
		}
		
		$advsearch->search();

		// Total Results
		$total = $advsearch->getTotal();

		// Links
		$where[] = "cl.main = '1'";
		$where[] = "cl.link_id = l.link_id";

	} else {

		// Total Results
		$database->setQuery( "SELECT COUNT(*) FROM #__mt_cats WHERE cat_name LIKE '%" . $database->getEscaped( $search_text ,true ) . "%'" );
		$total = $database->loadResult();

		// Categories
		$database->setQuery( "SELECT * FROM #__mt_cats WHERE cat_name LIKE '%" . $database->getEscaped( $search_text, true ) . "%' ORDER BY cat_name ASC LIMIT $limitstart, $limit" );
	}

	$results = $advsearch->loadResultList( $limitstart, $limit );

	if ( $search_where == 1 && !is_null($results) ) {
	
		require_once( JPATH_COMPONENT_SITE.DS.'mtree.tools.php' );
		$reviews = getReviews($results);

		$i = 0;
		foreach( $results AS $link )
		{
			$results[$i]->reviews = 0;
			if( isset($reviews[$link->link_id]) )
			{
				$results[$i]->reviews = $reviews[$link->link_id]->total;
			}
			$i++;
		}
	}
	
	# Page Navigation
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	# Get Pathway
	$pathWay = new mtPathWay();
	
	# Results Output
// /*
	if ( $search_where == 1 ) {
		// Links
		HTML_mtree::advsearchresults_links( $results, $fields, $pageNav, $pathWay, $search_where, $option );
	} else {
		// Categories
		HTML_mtree::searchresults_categories( $results, $pageNav, $pathWay, $search_where, $option );
	}
// */
}

/***
* Tree Templates
*/
function templates( $option ) {
	global $mtconf;
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();

	$templateBaseDir = JPath::clean( $mtconf->getjconf('absolute_path').DS.'components'.DS.'com_mtree'.DS.'templates' );

	$rows = array();
	// Read the template dir to find templates
	$templateDirs = JFolder::folders($templateBaseDir, '.');

	$cur_template = $mtconf->get('template');

	$rowid = 0;

	// Check that the directory contains an xml file
	foreach($templateDirs as $templateDir) {
		if($templateDir == 'index.html') continue;
		$dirName = JPath::clean($templateBaseDir.DS.$templateDir);
		$xmlFilesInDir = JFolder::files($dirName,'.xml');

		foreach($xmlFilesInDir as $xmlfile) {
			$xml = & JFactory::getXMLParser('Simple');
			if (!$xml->loadFile( $dirName.DS.$xmlfile )) {
				return false;
			}

			if ($xml->document->name() != 'extension') {
				continue;
			}
			if ($xml->document->attributes('type') != 'template') {
				continue;
			}

			$row = new StdClass();
			$row->id = $rowid;
			$row->directory = $templateDir;
			$row->name = $xml->document->name[0]->data();
			$row->creationdate = $xml->document->creationdate[0]->data();
			$row->author = $xml->document->author[0]->data() ? $xml->document->author[0]->data() : 'Unknown';
			$row->copyright = $xml->document->copyright[0]->data();
			$row->authorEmail = $xml->document->authoremail[0]->data();
			$row->authorUrl = $xml->document->authorurl[0]->data();
			$row->version = $xml->document->version[0]->data();
			$row->description = $xml->document->description[0]->data();

			// Get info from db
			if ($cur_template == $templateDir) {
				$row->default	= 1;
			} else {
				$row->default = 0;
			}

			$row->checked_out = 0;
			$row->mosname = strtolower( str_replace( ' ', '_', $row->name ) );

			$rows[] = $row;
			$rowid++;
		}
	}

	HTML_mtree::list_templates( $rows, $option );
}

function template_pages( $option ) {
	global $mtconf;

	$database =& JFactory::getDBO();
	
	$template = JRequest::getCmd( 'template', '');
	
	$xmlfile = $mtconf->getjconf('absolute_path') . '/components/com_mtree/templates/' . $template . '/templatedetails.xml';
	$xml = JFactory::getXML($xmlfile);

	$template_name = $xml->name;
	
	$database->setQuery('SELECT params FROM #__mt_templates WHERE tem_name = ' . $database->quote($template) . ' LIMIT 1');
	$template_params = $database->loadResult();
	
	JForm::addFormPath($mtconf->getjconf('absolute_path') . '/components/com_mtree/templates/' . $template);
	$form = JForm::getInstance('com_mtree.edittemplate', 'templatedetails', array('control'=>'params'), true, '/extension/config/fields');
	$registry = new JRegistry;
	$registry->loadString($template_params);
	$form->bind($registry->toArray());

	// START
	jimport('joomla.filesystem.folder');
	$template_files = JFolder::files($mtconf->getjconf('absolute_path') . '/components/com_mtree/templates/' . $template, '\.tpl.php$', false, false);
	// END
	HTML_mtree::template_pages( $template, $template_files, $template_name, $form, $option );
}

function edit_templatepage( $option ) {
	global $mtconf;
	
	$app		= JFactory::getApplication('site');
	$page		= JRequest::getCmd( 'page', '');
	$template	= JRequest::getCmd( 'template', '');

	$file = JPath::clean($mtconf->getjconf('absolute_path') .'/components/com_mtree/templates/'. $template .'/'. $page .'.tpl.php');

	if ( $fp = fopen( $file, 'r' ) ) {
		$content = fread( $fp, filesize( $file ) );
		$content = htmlspecialchars( $content );
		fclose($fp);
		HTML_mtree::edit_templatepage( $page, $template, $content, $option );
	} else {
		$app->redirect( 'index.php?option='. $option .'&task=template_pages&template=' . $template, JText::sprintf( 'COM_MTREE_CANNOT_OPEN_FILE', $file ) );
	}

}

function copy_template( $option ) {
	global $mtconf;
	
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$template = JRequest::getCmd( 'template', '');
	
	if( !empty($template) ) {
		$xmlfile = $mtconf->getjconf('absolute_path') . '/components/com_mtree/templates/' . $template . '/templatedetails.xml';
		$xml = JFactory::getXML($xmlfile);
		$template_name = $xml->name;
		
		HTML_mtree::copy_template( $template, $template_name, $option );
	}
}

function copy_template2( $option ) {
	global $mtconf;
	
	$app		= JFactory::getApplication('site');

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$template = JRequest::getCmd( 'template', '');
	$new_template_name = JRequest::getString( 'new_template_name', '');
	$new_template_folder = JRequest::getCmd( 'new_template_folder', '');
	$new_template_creation_date = JRequest::getString( 'new_template_creation_date', '');
	$new_template_author = JRequest::getString( 'new_template_author', '');
	$new_template_author_email = JRequest::getString( 'new_template_author_email', '');
	$new_template_author_url = JRequest::getString( 'new_template_author_url', '');
	$new_template_version = JRequest::getString( 'new_template_version', '');
	$new_template_description = JRequest::getString( 'new_template_description', '');
	$new_template_copyright = JRequest::getString( 'new_template_copyright', '');

	jimport('joomla.filesystem.folder');
	
	$result = JFolder::copy($template, $new_template_folder, $mtconf->getjconf('absolute_path') . '/components/com_mtree/templates/');
	
	if( $result === true )
	{
		$new_template_xml = $mtconf->getjconf('absolute_path') . '/components/com_mtree/templates/' . $new_template_folder . DS . 'templatedetails.xml';

		jimport('joomla.utilities.simplexml');
		
		$xml = new JSimpleXML;
		$xml->loadFile($new_template_xml);

		$xml->document->name[0]->setData($new_template_name);
		$xml->document->creationdate[0]->setData($new_template_creation_date);
		$xml->document->author[0]->setData($new_template_author);
		$xml->document->authoremail[0]->setData($new_template_author_email);
		$xml->document->authorurl[0]->setData($new_template_author_url);
		$xml->document->version[0]->setData($new_template_version);
		$xml->document->description[0]->setData($new_template_description);
		$xml->document->copyright[0]->setData($new_template_copyright);
		
		JFile::write($new_template_xml,$xml->document->toString());
		
		$db =& JFactory::getDBO();
		$db->setQuery("INSERT INTO #__mt_templates (`tem_name`) VALUES(".$db->Quote($new_template_folder).")");
		$db->query();
		
		$app->redirect( 'index.php?option=com_mtree&task=templates', JText::_( 'COM_MTREE_TEMPLATE_SUCCESSFULLY_COPIED' ) );
	}
	
	return true;
}

function delete_template( $option ) {
	global $mtconf;

	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();
	
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$template = JRequest::getCmd( 'template', '');

	$path = JPath::clean($mtconf->getjconf('absolute_path') . '/components/com_mtree/templates/' . $template);
	$database->setQuery('DELETE FROM #__mt_templates WHERE tem_name = ' . $database->quote($template) . ' LIMIT 1');
	$database->query();
	if (is_dir( $path )) {
		rmdirr( JPath::clean( $path ) );
	}

	$app->redirect( 'index.php?option='. $option .'&task=templates' );
}

function save_templateparam( $option ) {
	
	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();
	
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$params = JRequest::getVar( 'params', array(), 'post', 'array');
	$template = JRequest::getCmd( 'template', '');
	if ( is_array( $params ) ) {
		$attribs = array();
		foreach ( $params as $k=>$v) {
			if( is_array($v) ) {
				$attribs[] = "$k=".implode('|',$v);
			} else {
				$attribs[] = "$k=$v";
			}
		}
		$str_params = implode( "\n", $attribs );
	}

	$database->setQuery('UPDATE #__mt_templates SET params = ' . $database->quote($str_params) . ' WHERE tem_name = ' . $database->quote($template) . ' LIMIT 1');
	$database->query();
	
	$task = JRequest::getCmd( 'task', '', 'post');
	if ( $task == "save_templateparams" ) {
		$app->redirect( 'index.php?option='. $option .'&task=templates' );
	} else {
		$app->redirect( 'index.php?option='. $option .'&task=template_pages&template=' . $template );
	}
}

function save_templatepage( $option ) {
	global $mtconf;
	
	$task = JRequest::getCmd( 'task', '');
	$app		= JFactory::getApplication('site');

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$template 	= JRequest::getCmd( 'template', '', 'post');
	$page		= JRequest::getCmd( 'page', '', 'post');
	
	$pagecontent = JRequest::getVar('pagecontent', '', 'post', 'string', JREQUEST_ALLOWRAW);

	if ( !$template ) {
		$app->redirect( 'index.php?option='. $option .'&task=templates' );
	}

	if ( !$pagecontent ) {
		$app->redirect( 'index.php?option='. $option .'&task=templates' );
	}

	$file = JPath::clean($mtconf->getjconf('absolute_path') .'/components/com_mtree/templates/'. $template .'/'.$page.'.tpl.php');
	if ( is_writable( $file ) == false ) {
		$app->redirect( "index.php?option=$option&task=templates" , JText::sprintf( 'COM_MTREE_FILE_NOT_WRITEABLE', $file ) );
	}
	
	if($task == 'apply_templatepage')
	{
		$return = "index.php?option=$option&task=edit_templatepage&template=$template&page=$page";
	}
	else
	{
		$return = "index.php?option=$option&task=template_pages&template=$template";
	}
	
	if ( $fp = fopen ($file, 'w' ) ) {
		fputs( $fp, $pagecontent, strlen( $pagecontent ) );
		fclose( $fp );
		$app->redirect( $return, JText::_( 'COM_MTREE_TEMPLATE_PAGE_SAVED' ) );
	} else {
		$app->redirect( $return, JText::sprintf( 'COM_MTREE_FILE_NOT_WRITEABLE', $file ) );
	}
}

function new_template( $option ) {
	HTML_mtree::new_template( $option );
}

function install_template( $option ) {
	global $mtconf;
	
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	jimport('joomla.filesystem.archive');
	jimport('joomla.filesystem.folder');
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();
	$config		= JFactory::getConfig();
	$files		= JRequest::get('files');
	$tmp_dest	= $config->get('tmp_path').DS.$files['template']['name'];
	$tmp_src	= $files['template']['tmp_name'];

	// Move uploaded file
	$moved = JFile::upload($tmp_src, $tmp_dest);
	
	if ($moved === false) {
		$app->redirect( 'index.php?option=com_mtree&task=templates', JText::_( 'COM_MTREE_TEMPLATE_INSTALLATION_FAILED' ) );
	}

	// Clean the paths to use for template.zip extraction
	$archivename = JPath::clean($tmp_dest);
	$extractdir = JPath::clean(dirname($archivename).DS.uniqid('install_'));

	// do the unpacking of the archive
	$result = JArchive::extract($archivename, $extractdir);

	if ($result === false) {
		$app->redirect( 'index.php?option=com_mtree&task=templates', JText::_( 'COM_MTREE_TEMPLATE_INSTALLATION_FAILED' ) );
	}

	$tmp_xml = $extractdir . '/templatedetails.xml';
	if( JFile::exists($tmp_xml) ) {

		$xmlDoc = & JFactory::getXMLParser('Simple');
		
		if (!$xmlDoc->loadFile( $tmp_xml )) {
			return false;
		}
		$template_name = $xmlDoc->document->name[0]->data();
		
		$database->setQuery('INSERT INTO #__mt_templates (tem_name) VALUES(' . $database->quote($template_name) . ')');
		$database->query();

	} else {
		JFolder::delete($extractdir);
		JFile::delete($archivename);
		$app->redirect( 'index.php?option=com_mtree&task=templates', JText::_( 'COM_MTREE_TEMPLATE_INSTALLATION_FAILED' ) );
	}
	
	$tmp_installdir = JPath::clean($mtconf->getjconf('absolute_path') . '/components/com_mtree/templates/' . $template_name);
	if(JFile::exists($tmp_installdir)) {
		JFolder::delete($extractdir);
		JFile::delete($archivename);
		$app->redirect( 'index.php?option=com_mtree&task=templates', JText::_( 'COM_MTREE_TEMPLATE_INSTALLATION_FAILED' ) );
	}

	JFolder::copy( $extractdir, $tmp_installdir);
	JFolder::delete($extractdir);
	JFile::delete($archivename);
	$app->redirect( 'index.php?option=com_mtree&task=templates', JText::_( 'COM_MTREE_TEMPLATE_INSTALLATION_SUCCESS' ) );
	
}

function rmdirr($path) {
    if($files = glob($path . "/*")){
        foreach($files AS $file) {
            is_dir($file)? rmdirr($file) : unlink($file);
        }
    }
    rmdir($path);
}

function default_template( $option ) {
	
	$app		= JFactory::getApplication('site');
	$database	=& JFactory::getDBO();
	$template 	= JRequest::getVar( 'template', '');
	
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	if(!empty($template)) {
		$database->setQuery("UPDATE #__mt_config SET value ='" . $database->getEscaped($template) . "' WHERE varname = 'template' AND groupname = 'main' LIMIT 1");
		$database->query();
	}
	$app->redirect('index.php?option=com_mtree&task=templates');	
}

function cancel_edittemplatepage( $option ) {
	
	$app		= JFactory::getApplication('site');
	$template = JRequest::getVar( 'template', '');
	$app->redirect( "index.php?option=$option&task=template_pages&template=" . $template );
}

function cancel_templatepages( $option ) {
	$app = JFactory::getApplication('site');
	$app->redirect( "index.php?option=$option&task=templates" );
}

/***
* CSV Import Export
*/
function csv( $option ) {
	global $mtconf;

	$database 	=& JFactory::getDBO();

	# Load all custom fields
	$sql = "SELECT cf.* FROM #__mt_customfields AS cf "
		.	"\nWHERE cf.iscore = 0 ORDER BY ordering ASC";
	$database->setQuery($sql);

	$fields = new mFields();
	// $fields->setCoresValue( $row->link_name, $row->link_desc, $row->address, $row->city, $row->state, $row->country, $row->postcode, $row->telephone, $row->fax, $row->email, $row->website, $row->price, $row->link_hits, $row->link_votes, $row->link_rating, $row->link_featured, $row->link_created, $row->link_modified, $row->link_visited, $row->publish_up, $row->publish_down, $row->user_id, $row->username );
	$fields->loadFields($database->loadObjectList());

	# Publishing
	$publishing[] = JHTML::_('select.option', 1, JText::_( 'COM_MTREE_ALL' ) );
	$publishing[] = JHTML::_('select.option', 2, JText::_( 'COM_MTREE_PUBLISHED' ) );
	$publishing[] = JHTML::_('select.option', 3, JText::_( 'COM_MTREE_UNPUBLISHED' ) );
	$publishing[] = JHTML::_('select.option', 4, JText::_( 'COM_MTREE_PENDING' ) );
	$publishing[] = JHTML::_('select.option', 5, JText::_( 'COM_MTREE_EXPIRED' ) );
	$publishing[] = JHTML::_('select.option', 6, JText::_( 'COM_MTREE_PENDING_APPROVAL' ) );
	$lists['publishing'] = JHTML::_('select.genericlist', $publishing, 'publishing', 'class="inputbox" size="1"',	'value', 'text', '' );

	HTML_mtree::csv( $fields, $lists, $option );
	
}

function csv_export( $option ) {
	global $mtconf;
	
	$database 	=& JFactory::getDBO();
	$fields 	= JRequest::getVar( 'fields', '', 'post');
	$publishing = JRequest::getVar( 'publishing', '', 'post');
	$nullDate	= $database->getNullDate();

	$jdate = JFactory::getDate();
	$now = $jdate->toMySQL();

	$custom_fields = array();
	$core_fields = array();
	foreach($fields AS $field) {
		if(substr($field,0,2) == 'cf') {
			$custom_fields[] =  substr($field,2);
		} elseif( $field == 'cat_id') {
			$core_fields[] = 'GROUP_CONCAT(DISTINCT cat_id ORDER BY cl.main DESC SEPARATOR \',\') AS cat_id';
		} else {
			$core_fields[] = $field;
		}
	}
	$where = array();
	switch ($publishing) {
		case 2: // Published
			$where[] = "( (publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now')  AND "
			. "(publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now') AND "
			.	"link_published = '1' )";
			break;
		case 3: // Unpublished
			$where[] = "link_published = '0'";
			break;
		case 4: // Pending
			$where[] =  "( (publish_up => '$now' OR publish_up = ".$database->Quote($nullDate).") AND link_published = '1' )";
			break;
		case 5: // Expired
			$where[] =  "( publish_down < '$now' AND publish_down != '0000-00-00 00:00:00' AND link_published = '1' )";
			break;
		case 6: // Pending Listing, waiting for approval
			$where[] = "link_approved <= 0";
			break;
	}
	
	# Get link_id(s) first
	if(count($where)>0) {
		$database->setQuery('SELECT link_id FROM #__mt_links WHERE '.implode(" AND ", $where));
	} else {
		$database->setQuery('SELECT link_id FROM #__mt_links');
	}
	$link_ids = $database->loadResultArray();

	$header = '';
	$data = '';
	if(count($link_ids) > 0) {
		# Get the core fields value
		unset($where);
		$where = array();
		$where[] = "l.link_id = cl.link_id";
		// $where[] = "cl.main = '1'";
		$where[] = "l.link_id IN (" . implode(',',$link_ids) . ")";
		if(in_array('l.link_id',$core_fields)) {
			$sql = "SELECT ".implode(", ",$core_fields)." FROM (#__mt_links AS l, #__mt_cl AS cl)";
		} else {
			$sql = "SELECT ".implode(", ",array_merge(array('l.link_id'),$core_fields))." FROM (#__mt_links AS l, #__mt_cl AS cl)";
		}
		if (count($where)) {
			$sql .= "\n WHERE ".implode(" AND ", $where);
		}
		
		if(in_array('cat_id',$fields)) {
			$sql .= "\n GROUP BY cl.link_id";
		}
		
		$database->setQuery( $sql );
		$rows = $database->loadObjectList('link_id');
	
		# Get the custom fields' value
		if(count($custom_fields)>0) {
			$database->setQuery('SELECT cf_id, link_id, value FROM #__mt_cfvalues WHERE cf_id IN (' . implode(',',$custom_fields) . ') AND link_id IN (' . implode(',',$link_ids) . ')');
			$cfvalues = $database->loadObjectList();
			foreach($cfvalues AS $cfvalue) {
				$rows[$cfvalue->link_id]->{'cf'.$cfvalue->cf_id} = $cfvalue->value;
			}
		}
		$seperator = ',';

		# Create the CSV data
		$header = '';
		$data='';
		$i=0;
		foreach ($fields AS $field) {
			$i++;
			if($field == 'l.link_id') {
				$header .= 'link_id';
			} elseif(substr($field,0,2) == 'cf') {
				$header .=  substr($field,2);
			} else {
				$header .= $field;
			}
			if($i<count($fields)) {
				$header .= $seperator;
			}
		}
		$header .= "\n";

		foreach($rows AS $row) {
			$line = '';
			$j = 0;
			foreach($fields as $field){
				if($field == 'l.link_id') {
					if( !in_array('l.link_id',$core_fields) ) {
						continue;
					} else {
						$field = 'link_id';
					}
				}
				if( isset($row->$field) ) {
					$value = $row->$field;
				} else {
					$value = '';
				}
				
				if ($j >= 0) {
					if( !empty($value) ) {
						$line .= '"' . str_replace('"', '""', $value) . '"';
					}
					if( ($j+1) < count($fields) ) {
						$line .= $seperator;
					}
				}
				
				$j++;

			}
			
			if( !empty($line) ) {
				$data .= trim($line)."\n";
			}
		}
	}
	
	# this line is needed because returns embedded in the data have "\r"
	# and this looks like a "box character" in Excel
	$data = str_replace("\r", "", $data);

	HTML_mtree::csv_export( $header, $data, $option );
}

/***
* Rebuild Thumbnails
*/
function rebuild_thumbnails( $option, $cat_id, $limitstart, $limit )
{
	global $mtconf;
	
	$db = JFactory::getDBO();

	// Rebuild all images
	if( $cat_id == 0 )
	{
		$db->setQuery( 'SELECT filename FROM #__mt_images LIMIT ' . $limitstart . ', ' . $limit );
		$filenames = $db->loadResultArray();
		
		$db->setQuery( 'SELECT COUNT(*) FROM #__mt_images' );
		$total = $db->loadResult();
		
		$remaining_total = $total - $limitstart;
	}
	// Rebuild images from one top level category
	else
	{
		$db->setQuery( 'SELECT * FROM #__mt_cats WHERE cat_id = ' . $cat_id . ' LIMIT 1' );
		$cat = 	$db->loadObject();

		$db->setQuery( "SHOW TABLES LIKE '".$db->getPrefix()."mt_tmp_rebuild_thumbnails'" );
		$result = $db->loadResult();
		
		// Table exists, which means a list of images is readily 
		// available and prepopolated for rebuild
		if( !is_null($result) )
		{
			// Do nothing. #__mt_tmp_rebuild_thumbnails is readily available.
		}
		// Table does not exists. Populate #__mt_tmp_rebuild_thumbnails 
		// table with a list of images to rebuild.
		// $limitstart must start at 0 so that the rebuild table is created at
		// start and not in the middle (to avoid abuse of URL change).
		elseif( $limitstart == 0 )
		{
			// Create a temporary table for storing a list of images
			// to rebuild.
			$db->setQuery( 'CREATE TABLE `#__mt_tmp_rebuild_thumbnails` (`img_id` int(11) NOT NULL, `filename` varchar(255) NOT NULL, `rebuilt` smallint(5) unsigned NOT NULL DEFAULT \'0\', PRIMARY KEY (`img_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8' );
			$db->query();

			$mtCats = new mtCats( $db );
			$subcats = $mtCats->getSubCats_Recursive( $cat_id, 0 );
			$subcats[] = $cat_id;

			$sql = "SELECT l.link_id FROM #__mt_links AS l "
				. "\n LEFT JOIN #__mt_cats AS cat ON cat.cat_id IN (".implode( ", ", $subcats ).")"
				. "\n LEFT JOIN #__mt_cl AS cl ON cl.cat_id = cat.cat_id AND cl.main = 1"
				. "\n WHERE l.link_id = cl.link_id";
			$db->setQuery( $sql );
			$link_ids = $db->loadResultArray();

			$sql = "INSERT #__mt_tmp_rebuild_thumbnails (`img_id`, `filename`) "
				. "\n SELECT img_id, filename FROM #__mt_images WHERE link_id IN (".implode(', ',$link_ids).")";
			$db->setQuery( $sql );
			$db->query();
		}

		// Get the total
		$db->setQuery( 'SELECT COUNT(*) FROM #__mt_tmp_rebuild_thumbnails' );
		$total = $db->loadResult();

		// Get the remaining images to be rebuilt
		$db->setQuery( 'SELECT COUNT(*) FROM #__mt_tmp_rebuild_thumbnails WHERE rebuilt = 0' );
		$remaining_total = $db->loadResult();
		
		if( $limitstart > $total )
		{
			$limitstart = ($total - $remaining_total);
		}
		
		$db->setQuery( 'SELECT filename FROM #__mt_tmp_rebuild_thumbnails LIMIT ' . $limitstart . ', ' . $limit );
		$filenames = $db->loadResultArray();
	}

	if( !empty($filenames) )
	{
		$mtconf->setCategory( $cat_id );

		foreach( $filenames AS $filename )
		{
			$file_fullpath = JPATH_ROOT . $mtconf->get('relative_path_to_listing_original_image') . $filename;
			
			echo '<br />'.$filename;

			$mtImage = new mtImage();
			$mtImage->setMethod( $mtconf->get('resize_method') );
			$mtImage->setQuality( $mtconf->get('resize_quality') );
			$mtImage->setSize( $mtconf->get('resize_small_listing_size') );
			$mtImage->setTmpFile( $file_fullpath );

			$mtImage->setName( $filename );
			$mtImage->setSquare( $mtconf->get('squared_thumbnail') );
				
			if( !$mtImage->resize() )
			{
				JError::raise(E_NOTICE, 0, JText::sprintf('COM_MTREE_ERROR_IMAGE_UNABLE_TO_PROCESS_IMAGE', $file_fullpath));
				continue;
			}
			
			$mtImage->setDirectory( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_small_image') );
			$mtImage->saveToDirectory();
					
			$mtImage->setSize( $mtconf->get('resize_medium_listing_size') );
			$mtImage->setSquare(false);
			$mtImage->resize();
			$mtImage->setDirectory( $mtconf->getjconf('absolute_path') . $mtconf->get('relative_path_to_listing_medium_image') );
			$mtImage->saveToDirectory();
		}
	}
	
	// If this uses #__mt_tmp_rebuild_thumbnails, remove images that has 
	// been rebuilt.
	if( $cat_id > 0 && !empty($filenames) )
	{
		$db->setQuery( 'UPDATE #__mt_tmp_rebuild_thumbnails SET rebuilt = \'1\' WHERE filename IN ("'.implode('", "',$filenames).'")' );
		$db->query();
	}

	// Generate the 'Next' link, or leave it empty to indicate Finish/Done 
	// state.
	$next_link = '';
	if( $remaining_total > 0 )
	{
		$next_link = "index.php?option=com_mtree&task=rebuild_thumbnails&tmpl=component&limit=".$limit."&limitstart=".($limitstart + $limit)."&cat_id=".$cat_id;
	}
	else
	{
		if( $cat_id > 0 )
		{
			$db->setQuery( 'DROP TABLE #__mt_tmp_rebuild_thumbnails' );
			$db->query();
		}
	}

	HTML_mtree::rebuild_thumbnails( $option, $cat_id, $next_link );
}

function import_images( $option, $cf_id, $limitstart, $limit )
{
	global $mtconf;
	
	if( is_array($cf_id) )
	{
		$cf_id = $cf_id[0];
	}

	jimport( 'joomla.filesystem.file' );
	$database = JFactory::getDBO();

	$database->setQuery( 'SELECT link_id, value FROM #__mt_cfvalues WHERE cf_id = '.$cf_id.' ORDER BY id LIMIT ' . $limitstart . ', ' . $limit );
	$images = $database->loadObjectList();

	$database->setQuery( 'SELECT COUNT(*) FROM #__mt_cfvalues WHERE cf_id = ' . $cf_id );
	$total = $database->loadResult();
	
	$remaining_total = $total - $limitstart;

	foreach( $images AS $image )
	{
		$remoteImageURL = $image->value;
		$imageName = basename($image->value);
		$storedImageName = $image->link_id . '_' . $imageName;
		
		// Check the header to make sure this is an image as indicated by Content-Type header
		// before loading the entire image for storage.
		$url = $image->value;

		if (!$fp = fopen($url, 'r')) {
			// trigger_error("Unable to open URL ($url)", E_USER_ERROR);
			continue;
		}

		$meta = stream_get_meta_data($fp);

		if( stripos( implode(' ', $meta['wrapper_data']), 'Content-Type: image/') !== false )
		{
			// Yes, this is an image
		} else {
			// No, this is not an image. Go to next image
			continue;
		}

		fclose($fp);

		// Copy the image
		copy( $image->value, JPATH_ROOT . $mtconf->get( 'relative_path_to_listing_small_image' ) . $storedImageName);
		copy( $image->value, JPATH_ROOT . $mtconf->get( 'relative_path_to_listing_medium_image' ) . $storedImageName);
		copy( $image->value, JPATH_ROOT . $mtconf->get( 'relative_path_to_listing_original_image' ) . $storedImageName);

		// The copy function above requires image file name to be unique. If there exists an image with the same file name, 
		// the existing image will be replace. With this behaviour, we need to remove any image entry in #__mt_image of the 
		// same file name.
		$database->setQuery( 'DELETE FROM #__mt_images WHERE link_id = ' . $image->link_id . ' AND filename = \''.$storedImageName.'\' LIMIT 1' );
		$database->query();
		
		// Get the max ordering of the current listing and set the image order so that it always ordered last.
		$database->setQuery( 'SELECT MAX(ordering) FROM #__mt_images WHERE link_id = ' . $image->link_id . ' LIMIT 1' );
		$ordering = $database->loadResult();
		
		if( is_null($ordering) )
		{
			$ordering = 0;
		}
		$ordering += 1;
		
		// Insert this new image to #__mt_images database table.
		$database->setQuery( 'INSERT INTO #__mt_images (link_id, filename, ordering) VALUES('.$image->link_id.', \''.$storedImageName.'\', '.$ordering.') ' );
		$database->query();

	}
	
	// Generate the 'Next' link, or leave it empty to indicate Finish/Done state.
	$next_link = '';
	if( $remaining_total > 0 )
	{
		$next_link = "index.php?option=com_mtree&task=import_images&tmpl=component&limit=".$limit."&limitstart=".($limitstart + $limit)."&cfid=".$cf_id;
	}

	HTML_mtree::import_images( $option, $cf_id, $next_link );
}

/***
* Configuration
*/
function config( $option, $show='' ) {
	global $mtconf;
	
	$database 	=& JFactory::getDBO();
	
	# Get all config groups
	$database->setQuery( 'SELECT * FROM #__mt_configgroup ' . (($show == 'all') ? '' : 'WHERE displayed = 1 ') . 'ORDER BY ordering ASC' );
	$configgroups = $database->loadResultArray();

	# Get all configs
	$database->setQuery( 'SELECT c.* FROM (#__mt_config AS c, #__mt_configgroup AS cg) '
		. 'WHERE cg.groupname = c.groupname '
		. (($show == 'all') ? '' : 'AND c.displayed = \'1\' ')
		. 'ORDER BY cg.ordering ASC, c.ordering' );
	$configs = $database->loadObjectList('varname');

	# Image Library list
	$imageLibs=array();
	$imageLibs=detect_ImageLibs();
	if(!empty($imageLibs['gd1'])) { $thumbcreator[] = JHTML::_('select.option', 'gd1', 'GD Library '.$imageLibs['gd1'] ); }
	$thumbcreator[] = JHTML::_('select.option', 'gd2', 'GD2 Library '.( (array_key_exists('gd2',$imageLibs)) ? $imageLibs['gd2'] : '') );
	$thumbcreator[] = JHTML::_('select.option', 'netpbm', (isset($imageLibs['netpbm'])) ? $imageLibs['netpbm'] : "Netpbm" );
	$thumbcreator[] = JHTML::_('select.option', 'imagemagick', (isset($imageLibs['imagemagick'])) ? $imageLibs['imagemagick'] : "Imagemagick" ); 
	$lists['resize_method'] = JHTML::_('select.genericlist', $thumbcreator, 'resize_method', 'class="text_area" size="3"', 'value', 'text', $configs['resize_method']->value );

	$hasGDLibrary = detect_GD_library();

	# Sort Direction
	$sort[] = JHTML::_('select.option', "asc", JText::_( 'COM_MTREE_ASCENDING' ) );
	$sort[] = JHTML::_('select.option', "desc", JText::_( 'COM_MTREE_DESCENDING' ) );
	$lists['sort_direction'] = $sort;

	# Category Order
	$cat_order = array();
	$cat_order[] = JHTML::_('select.option', '', JText::_( ' ' ) );
	$cat_order[] = JHTML::_('select.option', "lft", JText::_( 'COM_MTREE_CONFIG_CUSTOM_ORDER' ) );
	$cat_order[] = JHTML::_('select.option', "cat_name", JText::_( 'COM_MTREE_NAME' ) );
	$cat_order[] = JHTML::_('select.option', "cat_featured", JText::_( 'COM_MTREE_FEATURED' ) );
	$cat_order[] = JHTML::_('select.option', "cat_created", JText::_( 'COM_MTREE_CREATED' ) );
	$lists['cat_order'] = $cat_order;

	# Listing Order
	$listing_order = array();
	$listing_order[] = JHTML::_('select.option', "link_name", JText::_( 'COM_MTREE_NAME' ) );
	$listing_order[] = JHTML::_('select.option', "link_hits", JText::_( 'COM_MTREE_HITS' ) );
	$listing_order[] = JHTML::_('select.option', "link_votes", JText::_( 'COM_MTREE_VOTES' ) );
	$listing_order[] = JHTML::_('select.option', "link_rating", JText::_( 'COM_MTREE_RATING' ) );
	$listing_order[] = JHTML::_('select.option', "link_visited", JText::_( 'COM_MTREE_VISIT' ) );
	$listing_order[] = JHTML::_('select.option', "link_featured", JText::_( 'COM_MTREE_FEATURED' ) );
	$listing_order[] = JHTML::_('select.option', "link_created", JText::_( 'COM_MTREE_CREATED' ) );
	$listing_order[] = JHTML::_('select.option', "link_modified", JText::_( 'COM_MTREE_MODIFIED' ) );
	$listing_order[] = JHTML::_('select.option', "address", JText::_( 'COM_MTREE_ADDRESS' ) );
	$listing_order[] = JHTML::_('select.option', "city", JText::_( 'COM_MTREE_CITY' ) );
	$listing_order[] = JHTML::_('select.option', "state", JText::_( 'COM_MTREE_STATE' ) );
	$listing_order[] = JHTML::_('select.option', "country", JText::_( 'COM_MTREE_COUNTRY' ) );
	$listing_order[] = JHTML::_('select.option', "postcode", JText::_( 'COM_MTREE_POSTCODE' ) );
	$listing_order[] = JHTML::_('select.option', "telephone", JText::_( 'COM_MTREE_TELEPHONE' ) );
	$listing_order[] = JHTML::_('select.option', "fax", JText::_( 'COM_MTREE_FAX' ) );
	$listing_order[] = JHTML::_('select.option', "email", JText::_( 'COM_MTREE_EMAIL' ) );
	$listing_order[] = JHTML::_('select.option', "website", JText::_( 'COM_MTREE_WEBSITE' ) );
	$listing_order[] = JHTML::_('select.option', "price", JText::_( 'COM_MTREE_PRICE' ) );

	if( $show == 'all' || $configs['first_listing_order1']->value == 'ordering' || $configs['first_search_order1']->value == 'ordering' )
	{
		$listing_order[] = JHTML::_('select.option', "ordering", JText::_( 'COM_MTREE_ORDERING' ) );
	}
	
	$lists['listing_order'] = $listing_order;

	# Review Order
	$review_order[] = JHTML::_('select.option', '', JText::_( ' ' ) );
	$review_order[] = JHTML::_('select.option', "rev_date", JText::_( 'COM_MTREE_REVIEW_DATE' ) );
	$review_order[] = JHTML::_('select.option', "vote_helpful", JText::_( 'COM_MTREE_TOTAL_HELPFUL_VOTES' ) );
	$review_order[] = JHTML::_('select.option', "vote_total", JText::_( 'COM_MTREE_TOTAL_VOTES' ) );
	$lists['review_order'] = $review_order;

	# Sort
	$sort_by_options = array('-link_created', '-link_modified', '-link_hits', '-link_visited', '-link_rating', '-link_votes', 'link_name', '-price','price');
	
	foreach( $sort_by_options AS $sort_by_option ) {
		$sort_by[] = JHTML::_('select.option', $sort_by_option, JText::_( 'COM_MTREE_ALL_LISTINGS_SORT_OPTION_'.strtoupper($sort_by_option) ) );
	}
	$lists['sort'] = $sort_by;
	
	# Sort options
	$html = '';
	$sort_by_option_values = explode('|',$configs['all_listings_sort_by_options']->value);
	$html .= '<fieldset>';
	foreach( $sort_by_options AS $sort_by_option ) {
		$html .= '<input type="checkbox" name="all_listings_sort_by_options[]" value="'.$sort_by_option.'"';
		$html .= ' style="clear:left"';
		if( in_array($sort_by_option,$sort_by_option_values) ) {
			$html .= ' checked';
		}
		$html .= ' />';
		$html .= '<label style="clear:none;float:none;margin:3px 0 0 5px;float:left">';
		$html .= JText::_( 'COM_MTREE_ALL_LISTINGS_SORT_OPTION_'.strtoupper($sort_by_option) );
		$html .= '</label>';
	}
	$html .= '</fieldset>';
	$lists['sort_options'] = $html;
	
	# User Access
	$access = array();
	$access[] = JHTML::_('select.option', "-1", JText::_( 'JNONE' ) );
	$access[] = JHTML::_('select.option', "0", JText::_( 'COM_MTREE_PUBLIC' ) );
	$access[] = JHTML::_('select.option', "1", JText::_( 'COM_MTREE_REGISTERED_ONLY' ) );
	$lists['user_access'] = $access;

	# User Access2
	$lists['user_access2'] = array_merge($access,array(JHTML::_('select.option', "2", JText::_( 'COM_MTREE_REGISTERED_ONLY_EXCEPT_LISTING_OWNER' ) )));
	
	# SEF's link slug type
	$sef_link_slug_type = array();
	$sef_link_slug_type[] = JHTML::_('select.option', "1", JText::_( 'COM_MTREE_ALIAS' ) );
	$sef_link_slug_type[] = JHTML::_('select.option', "2", JText::_( 'COM_MTREE_LINK_ID' ) );
	$sef_link_slug_type[] = JHTML::_('select.option', "3", JText::_( 'COM_MTREE_LINK_ID_AND_ALIAS_HYBRID' ) );
	$lists['sef_link_slug_type'] = $sef_link_slug_type;
	
	# Default Owner's Listing page
	$default_owner_listing_page = array();
	$default_owner_listing_page[] = JHTML::_('select.option', "viewuserslisting", JText::_( 'COM_MTREE_DEFAULT_OWNER_LISTING_PAGE_OPTION_VIEWUSERSLISTING' ) );
	$default_owner_listing_page[] = JHTML::_('select.option', "viewusersfav", JText::_( 'COM_MTREE_DEFAULT_OWNER_LISTING_PAGE_OPTION_VIEWUSERSFAV' ) );
	$default_owner_listing_page[] = JHTML::_('select.option', "viewusersreview", JText::_( 'COM_MTREE_DEFAULT_OWNER_LISTING_PAGE_OPTION_VIEWUSERSREVIEW' ) );
	$lists['owner_default_page'] = $default_owner_listing_page;
	
	# Listings type in index
	$type_of_listings_in_index = array();
	$arr_tmp = array('listcurrent','listpopular', 'listmostrated', 'listtoprated', 'listmostreview', 'listnew', 'listupdated', 'listfavourite', 'listfeatured');
	
	foreach( $arr_tmp AS $tmp )
	{
		$type_of_listings_in_index[] = JHTML::_('select.option', $tmp, JText::_( 'COM_MTREE_TYPES_OF_LISTINGS_IN_INDEX_OPTION_'.strtoupper($tmp) ) );
	}
	$lists['type_of_listings_in_index'] = $type_of_listings_in_index;

	# Feature Locations
	$feature_locations = array();
	$feature_locations[] = JHTML::_('select.option', "1", JText::_( 'COM_MTREE_STANDALONE_PAGE' ) );
	$feature_locations[] = JHTML::_('select.option', "2", JText::_( 'COM_MTREE_LISTING_DETAILS_PAGE' ) );
	$lists['feature_locations'] = $feature_locations;
	
	HTML_mtree::config( $configs, $configgroups, $lists, $hasGDLibrary, $option );
}

function saveconfig($option) {
	
	$app		= JFactory::getApplication('site');
	$database 	=& JFactory::getDBO();
	$post 		= JRequest::get( 'post' );
	
	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	# This make sure the root entry has a cat_id equal to 0.
	$database->setQuery( "UPDATE #__mt_cats SET cat_id = 0 WHERE cat_parent = -1 LIMIT 1" );
	$database->query();
	
	# Save configs
	foreach( $post AS $key => $value ) {
		if( in_array($key,array('option','task')) ) continue;
		$sql = '';
		$sql .= 'UPDATE #__mt_config SET value = ';
		if( is_array($value) ) {
			$sql .= $database->quote(implode('|',$value));
		} else {
			$sql .= $database->quote($value);
		}
		$sql .= ' WHERE varname = ' . $database->quote($key);
		$sql .= ' LIMIT 1';
		$database->setQuery($sql);
		$database->query();
	}
	$app->redirect( "index.php?option=$option&task=config", JText::_( 'COM_MTREE_CONFIG_HAVE_BEEN_UPDATED' ) );
}

function tools( $option )
{
	global $mtconf;
	
	$db = JFactory::getDBO();
	
	# Get top level categories for fields assignment
	$sql = "SELECT * FROM #__mt_cats WHERE cat_approved = 1 AND cat_parent = 0";
	if( $mtconf->get('first_cat_order1') != '' )
	{
		$sql .= ' ORDER BY ' . $mtconf->get('first_cat_order1') . ' ' . $mtconf->get('first_cat_order2');
		if( $mtconf->get('second_cat_order1') != '' )
		{
			$sql .= ', ' . $mtconf->get('second_cat_order1') . ' ' . $mtconf->get('second_cat_order2');
		}
	}
	$db->setQuery($sql);
	$top_level_cats = $db->loadObjectList();

	array_unshift(
		$top_level_cats, 
		(object) array(
			'cat_name' => JText::_('COM_MTREE_ALL_CATEGORIES'),
			'cat_id' => 0 
		) 
	);
	
	$lists['top_level_cats'] = JHTML::_('select.genericlist', $top_level_cats, 'resize_category', '', 'cat_id', 'cat_name', 0, 'rebuild_thumbnails_cat_id' );

	# Get URL based field type for 'Move Images' tool
	$db->setQuery( 'SELECT cf_id, caption FROM #__mt_customfields WHERE field_type = \'mweblink\'' );
	$mweblinks = $db->loadObjectList();

	$lists['mweblinks'] = JHTML::_('select.genericlist', $mweblinks, 'mweblinks', '', 'cf_id', 'caption', 0, 'cfid' );
	
	HTML_mtree::tools( $lists, $option );
}

function getItemId()
{
	$items	= JMenu::getInstance('Site')->getItems('component','com_mtree');
	if( !empty($items) )
	{
		return $items[0]->id;
	}
	else
	{
		return '';
	}
}

function getMTUrl( $query )
{
	jimport('joomla.application.router');
	$router = JRouter::getInstance('Site', array('mode'=>JROUTER_MODE_SEF));
	$Itemid = getItemId();
	
	$url = $query;
	if( !empty($Itemid) )
	{
		$url = $query.'&Itemid='.$Itemid;
	}

	$sefurl = $router->build($url);

	// Replace spaces, /administrator.
	$sefurl = preg_replace(array('/\s/u','/\/administrator/u'), array('%20',''), $sefurl);
	
	return JURI::getInstance()->toString(array('scheme', 'host', 'port')).$sefurl;
}
?>