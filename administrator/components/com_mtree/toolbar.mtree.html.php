<?php
/**
 * @version	$Id: toolbar.mtree.html.php 1724 2012-12-31 02:57:17Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */


defined('_JEXEC') or die('Restricted access');

class TOOLBAR_mtree {

	/***
	 * Link
	 */
	function EDITLINK_MENU() {
		
		$task	= JRequest::getCmd( 'task', '');
		$bar 	= & JToolBar::getInstance('toolbar');
		
		JToolBarHelper::title(  ($task=='newlink') ? JText::_( 'COM_MTREE_ADD_LISTING' ) : JText::_( 'COM_MTREE_EDIT_LISTING' ), 'article.png' );

		if($task == 'editlink_for_approval') {
			$bar->appendButton( 'Custom', '<a href="#" onclick="javascript:Joomla.submitbutton(\'savelink\');" class="toolbar"><span class="icon-32-save" title="' . JText::_( 'COM_MTREE_SAVE_CHANGES' ) . '"></span>' . JText::_( 'COM_MTREE_SAVE_CHANGES' ) . '</a>', 'savelink' );
		} else {
			$bar->appendButton( 'Custom', '<a href="#" onclick="javascript:Joomla.submitbutton(\'savelink\');" class="toolbar"><span class="icon-32-save" title="' . JText::_( 'JSAVE' ) . '"></span>' . JText::_( 'JSAVE' ) . '</a>', 'savelink' );
			$bar->appendButton( 'Custom', '<a href="#" onclick="javascript:Joomla.submitbutton(\'applylink\');" class="toolbar"><span class="icon-32-apply" title="' . JText::_( 'JAPPLY' ) . '"></span>' . JText::_( 'JAPPLY' ) . '</a>', 'applylink' );
			
		}
		JToolBarHelper::cancel( 'cancellink' );
	}

	function MOVELINKS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_MOVE_LINK' ), 'article.png' );
		JToolBarHelper::save( 'links_move2' );
		JToolBarHelper::custom( 'cancellinks_move', 'cancel.png', 'cancel_f2.png', 'JCANCEL', false );
	}

	function COPYLINKS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_COPY_LINK' ), 'article.png' );
		JToolBarHelper::save( 'links_copy2' );
		JToolBarHelper::custom( 'cancellinks_copy', 'cancel.png', 'cancel_f2.png', 'JCANCEL', false );
	}

	/***
	 * Category
	 */
	function EDITCAT_MENU() {
		$task = JRequest::getCmd( 'task', '');

		JToolBarHelper::title( ( ($task=='newcat') ? JText::_( 'COM_MTREE_ADD_CATEGORY' ) : JText::_( 'COM_MTREE_EDIT_CATEGORY' )), 'categories.png' );
		JToolBarHelper::save( 'savecat' );
		JToolBarHelper::apply( 'applycat' );
		JToolBarHelper::cancel( 'cancelcat' );
	}

	function MOVECATS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_MOVE_CATEGORY' ), 'move_f2.png' );
		JToolBarHelper::save( 'cats_move2' );
		JToolBarHelper::custom( 'cancelcats_move', 'cancel.png', 'cancel_f2.png', 'JCANCEL', false );
	}

	function COPYCATS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_COPY_CATEGORY' ), 'copy_f2.png' );
		JToolBarHelper::save( 'cats_copy2' );
		JToolBarHelper::custom( 'cancelcats_copy', 'cancel.png', 'cancel_f2.png', 'JCANCEL', false );
	}

	function REMOVECATS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_DELETE' ), 'trash.png' );
		JToolBarHelper::custom( 'removecats2', 'delete.png', 'delete_f2.png', 'JTOOLBAR_DELETE', false );
		JToolBarHelper::custom( 'cancelcat', 'cancel.png', 'cancel_f2.png', 'JCANCEL', false );
	}

	function LISTCATS_MENU() {
		JToolBarHelper::title( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 'mosetstree' );
		JToolBarHelper::deleteList('','removecats', 'COM_MTREE_DELETE_CATEGORIES');
		JToolBarHelper::customX( 'cats_copy', 'copy.png', 'copy_f2.png', 'COM_MTREE_COPY_CATEGORIES' );
		JToolBarHelper::customX( 'cats_move', 'move.png', 'move_f2.png', 'COM_MTREE_MOVE_CATEGORIES' );
		JToolBarHelper::divider();
		$bar = & JToolBar::getInstance('toolbar');
		$bar->appendButton( 'Custom', '<a href="#" onclick="javascript:if(document.adminForm.link_boxchecked.value==0){alert(\'' . JText::_( 'COM_MTREE_DELETE_LISTINGS_MSG', true ) . '\');}else{  submitbutton(\'removelinks\')}" class="toolbar"><span class="icon-32-delete" title="' . JText::_( 'COM_MTREE_DELETE_LISTINGS' ) . '"></span>' . JText::_( 'COM_MTREE_DELETE_LISTINGS' ) . '</a>', 'delete-links' );
		$bar->appendButton( 'Custom', '<a href="#" onclick="javascript:if(document.adminForm.link_boxchecked.value==0){alert(\'' . JText::_( 'COM_MTREE_COPY_LISTINGS_MSG', true ) . '\');}else{  submitbutton(\'links_copy\')}" class="toolbar"><span class="icon-32-copy" title="' . JText::_( 'COM_MTREE_COPY_LISTINGS' ) . '"></span>' . JText::_( 'COM_MTREE_COPY_LISTINGS' ) . '</a>', 'copy-links' );
		$bar->appendButton( 'Custom', '<a href="#" onclick="javascript:if(document.adminForm.link_boxchecked.value==0){alert(\'' . JText::_( 'COM_MTREE_MOVE_LISTINGS_MSG', true ) . '\');}else{  submitbutton(\'links_move\')}" class="toolbar"><span class="icon-32-move" title="' . JText::_( 'COM_MTREE_MOVE_LISTINGS' ) . '"></span>' . JText::_( 'COM_MTREE_MOVE_LISTINGS' ) . '</a>', 'move-links' );
	}

	/***
	 * Approval
	 */
	function LISTPENDING_LINKS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_PENDING_LISTING' ), 'article.png' );
		$bar = & JToolBar::getInstance('toolbar');
		$bar->appendButton( 'Custom', '<a href="#" onclick="javascript:if(document.adminForm.link_boxchecked.value==0){alert(\'' . JText::_( 'COM_MTREE_APPROVE_AND_PUBLISH_LISTING_MSG', true ) . '\');}else{  submitbutton(\'approve_publish_links\')}" class="toolbar"><span class="icon-32-publish" title="' . JText::_( 'COM_MTREE_APPROVE_AND_PUBLISH_LISTING' ) . '"></span>' . JText::_( 'COM_MTREE_APPROVE_AND_PUBLISH_LISTING' ) . '</a>', 'approve-links' );
		$bar->appendButton( 'Custom', '<a href="#" onclick="javascript:if(document.adminForm.link_boxchecked.value==0){alert(\'' . JText::_( 'COM_MTREE_DELETE_LISTINGS_MSG', true ) . '\');}else{  submitbutton(\'removelinks\')}" class="toolbar"><span class="icon-32-delete" title="' . JText::_( 'COM_MTREE_DELETE_LISTINGS' ) . '"></span>' . JText::_( 'COM_MTREE_DELETE_LISTINGS' ) . '</a>', 'delete-links' );
	}

	function LISTPENDING_CATS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_PENDING_CATEGORIES' ), 'categories.png' );
		JToolBarHelper::custom( 'approve_publish_cats', 'publish.png', 'publish_f2.png',JText::_( 'COM_MTREE_APPROVE_AND_PUBLISH' ), true );
		JToolBarHelper::custom( 'approve_cats', 'publish.png', 'publish_f2.png', JText::_( 'COM_MTREE_APPROVE_CATEGORIES' ), true );
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('', 'removecats');
	}

	function LISTPENDING_REVIEWS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_PENDING_REVIEWS' ), 'article.png' );
		JToolBarHelper::apply( 'save_pending_reviews' );
	}

	function LISTPENDING_REPORTS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_PENDING_REPORTS' ), 'article.png' );
		JToolBarHelper::apply( 'save_reports' );
	}

	function LISTPENDING_REVIEWSREPORTS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_PENDING_REVIEWS_REPORTS' ), 'article.png' );
		JToolBarHelper::apply( 'save_reviewsreports' );
	}

	function LISTPENDING_REVIEWSREPLY_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_PENDING_REVIEWS_REPLIES' ), 'article.png' );
		JToolBarHelper::apply( 'save_reviewsreply' );
	}

	function LISTPENDING_CLAIMS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_PENDING_CLAIMS' ), 'article.png' );
		JToolBarHelper::apply( 'save_claims' );
	}

	/***
	 * Reviews
	 */
	function LISTREVIEWS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_REVIEWS' ), 'article.png' );
		JToolBarHelper::custom( 'newreview', 'new.png', 'new_f2.png', 'JTOOLBAR_NEW', false );
		JToolBarHelper::editList( 'editreview' );
		JToolBarHelper::deleteList( '', 'removereviews' );
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'backreview', 'back.png', 'back_f2.png', 'JTOOLBAR_BACK', false );
	}

	function EDITREVIEW_MENU() {
		$task = JRequest::getCmd( 'task', '');
		
		JToolBarHelper::title(  (($task=='newreview') ? JText::_( 'COM_MTREE_ADD' ) : JText::_( 'COM_MTREE_EDIT' )) . ' ' . JText::_( 'COM_MTREE_REVIEW' ), 'article.png' );
		JToolBarHelper::save( 'savereview' );
		JToolBarHelper::cancel( 'cancelreview' );
	}

	/***
	*	Search Results
	*/
	function SEARCH_LISTINGS() {
		JHtml::_('behavior.framework');
		JToolBarHelper::title( JText::_( 'COM_MTREE_SEARCH_RESULTS' ) . ' - ' . JText::_( 'COM_MTREE_LISTINGS' ) , 'article.png' );
		$bar = & JToolBar::getInstance('toolbar');
		$bar->appendButton( 'Custom', '<a href="#" onclick="javascript:if(document.adminForm.link_boxchecked.value==0){alert(\'' . JText::_( 'COM_MTREE_DELETE_LISTINGS_MSG' ) . '\');}else{  submitbutton(\'removelinks\')}" class="toolbar"><span class="icon-32-delete" title="' . JText::_( 'COM_MTREE_DELETE_LISTINGS' ) . '"></span>' . JText::_( 'COM_MTREE_DELETE_LISTINGS' ) . '</a>', 'delete-links' );
		$bar->appendButton( 'Custom', '<a href="#" onclick="javascript:if(document.adminForm.link_boxchecked.value==0){alert(\'' . JText::_( 'COM_MTREE_COPY_LISTINGS_MSG' ) . '\');}else{  submitbutton(\'links_copy\')}" class="toolbar"><span class="icon-32-copy" title="' . JText::_( 'COM_MTREE_COPY_LISTINGS' ) . '"></span>' . JText::_( 'COM_MTREE_COPY_LISTINGS' ) . '</a>', 'copy-links' );
		$bar->appendButton( 'Custom', '<a href="#" onclick="javascript:if(document.adminForm.link_boxchecked.value==0){alert(\'' . JText::_( 'COM_MTREE_MOVE_LISTINGS_MSG' ) . '\');}else{  submitbutton(\'links_move\')}" class="toolbar"><span class="icon-32-move" title="' . JText::_( 'COM_MTREE_MOVE_LISTINGS' ) . '"></span>' . JText::_( 'COM_MTREE_MOVE_LISTINGS' ) . '</a>', 'move-links' );
	}

	function SEARCH_CATEGORIES() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_SEARCH_RESULTS' ) . ' - ' . JText::_( 'COM_MTREE_CATEGORIES' ) , 'article.png' );
		JToolBarHelper::custom( 'editcat', 'edit.png', 'edit_f2.png', 'COM_MTREE_EDIT_CATEGORY', true );
		JToolBarHelper::custom( 'removecats', 'delete.png', 'delete_f2.png', 'COM_MTREE_DELETE_CATEGORIES', true );
		JToolBarHelper::custom( 'cats_move', 'move.png', 'move_f2.png', 'COM_MTREE_MOVE_CATEGORIES', true );
	}

	/***
	* Tree Templates
	*/
	function TREE_TEMPLATES() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_TREE_TEMPLATES' ), 'thememanager' );
		JToolBarHelper::addNew('new_template');
		JToolBarHelper::makeDefault('default_template');
		JToolBarHelper::editList( 'template_pages' );
		JToolBarHelper::custom( 'copy_template', 'copy.png', 'copy_f2.png', 'COM_MTREE_COPY_TEMPLATE', true );
		JToolBarHelper::deleteList( '','delete_template' );
	}
	
	function TREE_TEMPLATEPAGES() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_TREE_TEMPLATES' ), 'thememanager' );
		JToolBarHelper::save( 'save_templateparams' );
		JToolBarHelper::apply( 'apply_templateparams' );
		JToolBarHelper::editList( 'edit_templatepage' );
		JToolBarHelper::cancel( 'cancel_templatepages' );
	}

	function TREE_EDITTEMPLATEPAGE() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_TEMPLATE_PAGE_EDITOR' ), 'thememanager' );
		JToolBarHelper::save( 'save_templatepage' );
		JToolBarHelper::apply( 'apply_templatepage' );
		JToolBarHelper::cancel( 'cancel_edittemplatepage' );
	}
	
	function TREE_NEWTEMPLATE() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_UPLOAD_NEW_TEMPLATE' ), 'thememanager' );
		JToolBarHelper::cancel( 'cancel_templatepages' );
	}
	
	function TREE_COPYTEMPLATE() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_COPY_TEMPLATE' ), 'thememanager' );
		JToolBarHelper::save( 'copy_template2' );
		JToolBarHelper::cancel( 'cancel_templatepages' );
	}
	
	
	/***
	* Advanced Search
	*/
	function ADVSEARCH() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_ADVANCED_SEARCH' ) );
	}
	
	function ADVSEARCH2() {
		JHtml::_('behavior.framework');
		JToolBarHelper::title( JText::_( 'COM_MTREE_ADVANCED_SEARCH_RESULTS' ) );
		$bar = & JToolBar::getInstance('toolbar');
		$bar->appendButton( 'Custom', '<a href="#" onclick="javascript:if(document.adminForm.link_boxchecked.value==0){alert(\'' . JText::_( 'COM_MTREE_DELETE_LISTINGS_MSG' ) . '\');}else{  submitbutton(\'removelinks\')}" class="toolbar"><span class="icon-32-delete" title="' . JText::_( 'COM_MTREE_DELETE_LISTINGS' ) . '"></span>' . JText::_( 'COM_MTREE_DELETE_LISTINGS' ) . '</a>', 'delete-links' );
		$bar->appendButton( 'Custom', '<a href="#" onclick="javascript:if(document.adminForm.link_boxchecked.value==0){alert(\'' . JText::_( 'COM_MTREE_COPY_LISTINGS_MSG' ) . '\');}else{  submitbutton(\'links_copy\')}" class="toolbar"><span class="icon-32-copy" title="' . JText::_( 'COM_MTREE_COPY_LISTINGS' ) . '"></span>' . JText::_( 'COM_MTREE_COPY_LISTINGS' ) . '</a>', 'copy-links' );
		$bar->appendButton( 'Custom', '<a href="#" onclick="javascript:if(document.adminForm.link_boxchecked.value==0){alert(\'' . JText::_( 'COM_MTREE_MOVE_LISTINGS_MSG' ) . '\');}else{  submitbutton(\'links_move\')}" class="toolbar"><span class="icon-32-move" title="' . JText::_( 'COM_MTREE_MOVE LISTINGS' ) . '"></span>' . JText::_( 'COM_MTREE_MOVE_LISTINGS' ) . '</a>', 'move-links' );
	}
	
	/***
	* Configuration
	*/
	function CONFIG_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_CONFIGURATION' ), 'config.png' );
		JToolBarHelper::apply('saveconfig');
		JToolBarHelper::back();
	}
	
	/***
	* Custom Fields
	*/
	function CUSTOM_FIELDS() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_CUSTOM_FIELDS' ), 'module' );
		JToolBarHelper::publishList('cf_publish');
		JToolBarHelper::unpublishList('cf_unpublish');
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'newcf', 'new.png', 'new_f2.png', 'JTOOLBAR_NEW', false );
		JToolBarHelper::deleteList( '', 'removecf' );
	}
	
	function EDIT_CUSTOM_FIELDS() {
		$cf_id = JRequest::getInt( 'cfid' );
		JToolBarHelper::title( JText::_( 'COM_MTREE_CUSTOM_FIELD' ) . ': ' . (($cf_id)?JText::_('JTOOLBAR_EDIT') : JText::_('JTOOLBAR_NEW'))  , 'module' );
		JToolBarHelper::save( 'savecf' );
		JToolBarHelper::apply( 'applycf' );
		JToolBarHelper::cancel( 'cancelcf' );
	}
	
	function MANAGE_FIELD_TYPES() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_INSTALLED_FIELD_TYPES' ), 'install.png' );
	}

	/***
	* Link Checker
	*/
	function LINKCHECKER_MENU() {
		JToolBarHelper::save('linkchecker');
	}
	
	/***
	* Spy
	*/
	function SPY_VIEWUSER_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_USER' ), 'user' );
		JToolBarHelper::deleteList();
	}
}
?>