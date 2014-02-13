<?php
/**
 * @version	$Id: admin.mtree.html.php 1751 2013-01-05 02:02:11Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

class HTML_mtree {

	/***
	* Left Navigation
	*/
	function print_style() {
		global $mtconf;
	?>
	<style type="text/css">
		a.mt_menu {
			font-weight: bold;
			text-decoration: none;
		}
		a.mt_menu:hover {
			font-weight: bold;
			text-decoration: underline;
		}
		a.mt_menu_selected {
			font-weight: bold;
			color: #515151;
			text-decoration: none;
			font-size: 12px;
		}
		a.mt_menu_selected:hover {
			text-decoration: underline;
			font-weight: bold;
			color: #515151;
			font-size: 12px;
		}
		ul.linkcats{
			margin:0px;
			padding:0;
		}
		ul.linkcats > li:first-child
		{
		font-weight:bold;
		}
		ul.linkcats li {
			margin:0;
			padding:0;
			list-style:none;
			padding:0 0 3px 0;
		}
		ul.linkcats img {margin-right:4px;}
		ul.linkcats a {text-decoration:underline;margin-right:3px;}
		.icon-48-mosetstree {background: url(..<?php echo $mtconf->get('relative_path_to_images'); ?>mosetstree-icon.png) no-repeat left;}
/*		fieldset input, fieldset select, fieldset label, fieldset img {
			float:none;
			display:inline;
		}
*/	</style>
	<?php
	}

	function print_startmenu( $task, $cat_parent ) {
		global $mtconf;
		
		$app		= JFactory::getApplication();
		$database	=& JFactory::getDBO();
		$template	= $app->getTemplate(true)->template;
		
		# Count the number of pending links/cats/reviews/reports/claims
		$database->setQuery( "SELECT COUNT(*) FROM #__mt_cats WHERE cat_approved='0'" );
		$pending_cats = $database->loadResult();

		$database->setQuery( "SELECT COUNT(*) FROM #__mt_links WHERE link_approved <= 0" );
		$pending_links = $database->loadResult();
	
		$database->setQuery( "SELECT COUNT(*) FROM #__mt_reviews WHERE rev_approved='0'" );
		$pending_reviews = $database->loadResult();
	
		$database->setQuery( "SELECT COUNT(*) FROM #__mt_reports WHERE rev_id = 0 && link_id > 0" );
		$pending_reports = $database->loadResult();

		$database->setQuery( "SELECT COUNT(*) FROM #__mt_reviews WHERE ownersreply_text != '' AND ownersreply_approved = '0'" );
		$pending_reviewsreply = $database->loadResult();

		$database->setQuery( "SELECT COUNT(*) FROM #__mt_reports WHERE rev_id > 0 && link_id > 0" );
		$pending_reviewsreports = $database->loadResult();

		$database->setQuery( "SELECT COUNT(*) FROM #__mt_claims" );
		$pending_claims = $database->loadResult();

		HTML_mtree::print_style();

	?>
	<table cellpadding="3" cellspacing="0" border="0" width="100%">
	<tr>
		<td align="left" valign="top" width="160" height="0">

			<table cellpadding="2" cellspacing="0" border="0" width="160" height="100%" align="left" style="border: 1px solid #cccccc;">
				<tr><td colspan="2" style="background: #DDE1E6; border-bottom: 1px solid #cccccc;font-weight:bold;"><?php echo JText::_( 'COM_MTREE_TITLE' ) ?></td></tr>
				
				<?php
				if (!$mtconf->get('admin_use_explorer')) {
				?>
				<tr>
					<td width="20" align="center" style="background-color:#DDE1E6"><img src="..<?php echo $mtconf->get('relative_path_to_images'); ?>house.png" width="16" height="16" /></td>
					<td width="100%" style="background-color:#F1F3F5"> <a class="mt_menu<?php echo ($task=="listcats" || $task=="editcat" || $task=="") ? "_selected": ""; ?>" href="index.php?option=com_mtree&task=listcats"><?php echo JText::_( 'COM_MTREE_NAVIGATE_TREE' ) ?></a></td>
				</tr>
				<?php } ?>
				<tr>
					<td align="center" style="background-color:#DDE1E6"><img src="..<?php echo $mtconf->get('relative_path_to_images'); ?>page_white_add.png" width="16" height="16" /></td>
					<td style="background-color:#F1F3F5"> <a class="mt_menu<?php echo ($task=="newlink") ? "_selected": ""; ?>" href="index.php?option=com_mtree&amp;task=newlink&amp;cat_parent=<?php echo $cat_parent ?>"><?php echo JText::_( 'COM_MTREE_ADD_LISTING' ) ?></a></td>
				</tr>

				<tr>
					<td align="center" style="background-color:#DDE1E6"><img src="..<?php echo $mtconf->get('relative_path_to_images'); ?>folder_add.png" width="16" height="16" /></td>
					<td style="background-color:#F1F3F5"> <a class="mt_menu<?php echo ($task=="newcat") ? "_selected": ""; ?>" href="index.php?option=com_mtree&amp;task=newcat&amp;cat_parent=<?php echo $cat_parent ?>"><?php echo JText::_( 'COM_MTREE_ADD_CAT' ) ?></a></td>
				</tr>
				<?php 
					# Pending Approvals
					if ( 
							($pending_links > 0)
							OR
							($pending_cats > 0)
							OR
							($pending_reviews > 0)
							OR
							($pending_reports > 0)
							OR
							($pending_reviewsreply > 0)
							OR
							($pending_reviewsreports > 0)
							OR
							($pending_claims > 0)
						 ) { 
				?>
				<tr><td colspan="2" style="background: #DDE1E6; border-bottom: 1px solid #cccccc;border-top: 1px solid #cccccc;font-weight:bold;"><?php echo JText::_( 'COM_MTREE_PENDING_APPROVAL' ) ?></td></tr>
					
				<?php if ( $pending_cats > 0 ) { ?>
				<tr>
					<td style="background-color:#DDE1E6"><img src="..<?php echo $mtconf->get('relative_path_to_images'); ?>folder.png" width="18" height="18" /></td>
					<td style="background-color:#F1F3F5">&nbsp;<a class="mt_menu<?php echo ($task=="listpending_cats") ? "_selected": ""; ?>" href="index.php?option=com_mtree&task=listpending_cats"><?php echo JText::_( 'COM_MTREE_CATEGORIES' ) ?> (<?php echo $pending_cats; ?>)</a></td>
				</tr>
					<?php 
					}

					if ( $pending_links > 0 ) { ?>
				<tr>
					<td style="background-color:#DDE1E6"><img src="..<?php echo $mtconf->get('relative_path_to_images'); ?>page_white.png" width="16" height="16" /></td>
					<td style="background-color:#F1F3F5">&nbsp;<a class="mt_menu<?php echo ($task=="listpending_links") ? "_selected": ""; ?>" href="index.php?option=com_mtree&task=listpending_links"><?php echo JText::_( 'COM_MTREE_LISTINGS' ) ?> (<?php echo $pending_links; ?>)</a></td>
				</tr>
				<?php 
					}	

					if ( $pending_reviews > 0 ) { ?>
				<tr>
					<td style="background-color:#DDE1E6"><img src="..<?php echo $mtconf->get('relative_path_to_images'); ?>comment.png" width="16" height="16" /></td>
					<td style="background-color:#F1F3F5">&nbsp;<a class="mt_menu<?php echo ($task=="listpending_reviews") ? "_selected": ""; ?>" href="index.php?option=com_mtree&task=listpending_reviews"><?php echo JText::_( 'COM_MTREE_REVIEWS' ) ?> (<?php echo $pending_reviews; ?>)</a></td>
				</tr>
				<?php 
					}	

					if ( $pending_reports > 0 ) { ?>
				<tr>
					<td style="background-color:#DDE1E6"><img src="..<?php echo $mtconf->get('relative_path_to_images'); ?>error.png" width="16" height="16" /></td>
					<td style="background-color:#F1F3F5">&nbsp;<a class="mt_menu<?php echo ($task=="listpending_reports") ? "_selected": ""; ?>" href="index.php?option=com_mtree&task=listpending_reports"><?php echo JText::_( 'COM_MTREE_REPORTS' ) ?> (<?php echo $pending_reports; ?>)</a></td>
				</tr>
				<?php 
					}	

					if ( $pending_reviewsreply > 0 ) { ?>
				<tr>
					<td style="background-color:#DDE1E6"><img src="..<?php echo $mtconf->get('relative_path_to_images'); ?>user_comment.png" width="16" height="16" /></td>
					<td style="background-color:#F1F3F5">&nbsp;<a class="mt_menu<?php echo ($task=="listpending_reviewsreply") ? "_selected": ""; ?>" href="index.php?option=com_mtree&task=listpending_reviewsreply"><?php echo JText::_( 'COM_MTREE_OWNERS_REPLIES' ) ?> (<?php echo $pending_reviewsreply; ?>)</a></td>
				</tr>
				<?php 
					}	

					if ( $pending_reviewsreports > 0 ) { ?>
				<tr>
					<td style="background-color:#DDE1E6"><img src="..<?php echo $mtconf->get('relative_path_to_images'); ?>error.png" width="16" height="16" /></td>
					<td style="background-color:#F1F3F5">&nbsp;<a class="mt_menu<?php echo ($task=="listpending_reviewsreports") ? "_selected": ""; ?>" href="index.php?option=com_mtree&task=listpending_reviewsreports"><?php echo JText::_( 'COM_MTREE_REVIEWS_REPORTS' ) ?> (<?php echo $pending_reviewsreports; ?>)</a></td>
				</tr>
				<?php 
					}	

					if ( $pending_claims > 0 ) { ?>
				<tr>
					<td style="background-color:#DDE1E6"><img src="..<?php echo $mtconf->get('relative_path_to_images'); ?>user_green.png" width="16" height="16" /></td>
					<td style="background-color:#F1F3F5">&nbsp;<a class="mt_menu<?php echo ($task=="listpending_claims") ? "_selected": ""; ?>" href="index.php?option=com_mtree&task=listpending_claims"><?php echo JText::_( 'COM_MTREE_CLAIMS' ) ?> (<?php echo $pending_claims; ?>)</a></td>
				</tr>
				<?php 
					}	

				} 
				 # End of Pending Approvals
				
				 # dTree
				if ($mtconf->get('admin_use_explorer')) {
				?>
				<tr><td colspan="2" style="background: #DDE1E6; border-bottom: 1px solid #cccccc;border-top: 1px solid #cccccc;font-weight:bold;"><?php echo JText::_( 'COM_MTREE_EXPLORER' ) ?></td></tr>
				<tr><td colspan="2" style="background-color:#F1F3F5;">
				<?php

				$cats = HTML_mtree::getChildren( 0, $mtconf->get('explorer_tree_level') );
				?>
				<link rel="StyleSheet" href="components/com_mtree/dtree.css" type="text/css" />
				<script type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>dtree.js"></script>

				<script type="text/javascript">
					<!--
					
					fpath = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folder.gif';
					d = new dTree('d');

					d.config.closeSameLevel = true; 

					d.icon.root = '..<?php echo $mtconf->get('relative_path_to_images'); ?>house.png',
					d.icon.folder = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folder.gif',
					d.icon.folderOpen = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folderopen.gif',
					d.icon.node = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/page.gif',
					d.icon.empty = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/empty.gif',
					d.icon.line = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/line.png',
					d.icon.join = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/join.png',
					d.icon.joinBottom = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/joinbottom.png',
					d.icon.plus = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/plus.png',
					d.icon.plusBottom = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/plusbottom.png',
					d.icon.minus = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/minus.gif',
					d.icon.minusBottom = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/minusbottom.gif',
					d.icon.nlPlus = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/nolines_plus.gif',
					d.icon.nlMinus = '..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/nolines_minus.gif'

					d.add(0,-1,'<?php echo JText::_( 'COM_MTREE_ROOT' ) ?>', 'index.php?option=com_mtree');
					<?php
					foreach( $cats AS $cat ) {
							echo "\nd.add(";
							echo $cat->cat_id.",";
							echo $cat->cat_parent.",";
							
							// Print Category Name
							echo "'".addslashes(htmlspecialchars($cat->cat_name, ENT_QUOTES ));
							echo "',";

							echo "pp(".$cat->cat_id."),";
							echo "'','',";
							echo "fpath";
							echo ");";
					}
					?>
					document.write(d);
					
					function pp(cid) {
						return 'index.php?option=com_mtree&task=listcats&cat_id='+cid;
					}
					//-->
				</script>

				</td></tr>
				<?php
					}

				# End of  dTree

				 # This Directory
				if ( $task == 'listcats' || $task == 'editcat' || $task == 'editcat_browse_cat' || $task == 'editcat_add_relcat' || $task == 'editcat_remove_relcat' ) {
					if($cat_parent > 0) {
						# Lookup all information about this directory
						$thiscat = new mtCats( $database );
						$thiscat->load( $cat_parent );

				?>
				<tr><td colspan="2" align="left" style="color: black; padding-left: 20px;font-weight:bold;background: #DDE1E6 url(..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folderopen.gif) no-repeat center left; border-bottom: 1px solid #cccccc;border-top: 1px solid #cccccc;"><?php echo JText::_( 'COM_MTREE_THIS_CATEGORY' ) ?></td></tr>
				<tr class="row0"><td colspan="2" style="background-color:#F1F3F5">
					<?php
						$tcat = new mtDisplay();
						$tcat->add(JText::_( 'COM_MTREE_NAME' ), '<a href="index.php?option=com_mtree&task=editcat&cat_id=' . $thiscat->cat_id . '&cat_parent=' . $thiscat->cat_parent . '">' . $thiscat->cat_name . '</a>');
						$tcat->add( JText::_( 'COM_MTREE_CAT_ID' ), $thiscat->cat_id );
						$tcat->add( JText::_( 'COM_MTREE_LISTINGS' ), $thiscat->cat_links);
						$tcat->add( JText::_( 'COM_MTREE_CATEGORIES' ), $thiscat->cat_cats);
						$tcat->add( JText::_( 'COM_MTREE_RELATED_CATEGORIES2' ), $thiscat->getNumOfRelCats() );
						$tcat->add( JText::_( 'COM_MTREE_PUBLISHED' ), JHtml::_('jgrid.published', $thiscat->cat_published, '', '', false) );
						$tcat->add( JText::_( 'COM_MTREE_FEATURED' ), JHtml::_('mtree.featured', $thiscat->cat_featured, '', '', false) );
						$tcat->display();
					?>
				</td></tr>

				<?php
					}

				# This Listing
				} elseif( $task == 'editlink' || $task == 'editlink_change_cat' || $task == 'reviews_list' || $task == 'newreview' || $task == 'editreview' || $task == 'editlink_browse_cat' || $task == 'editlink_add_cat' || $task == 'editlink_remove_cat' ) {
					global $link_id;

					if ( $link_id[0] > 0 ) {
						$thislink = new mtLinks( $database );
						$thislink->load( $link_id[0] );

						$database->setQuery( 'SELECT COUNT(*) FROM #__mt_reviews WHERE link_id = ' . $database->quote($link_id[0]) . ' AND rev_approved = 1' );
						$reviews = $database->loadResult();
						?>
				<tr><td colspan="2" align="left" style="color: black; padding-left: 20px;font-weight:bold;background: #DDE1E6 url(../includes/js/ThemeOffice/document.png) no-repeat center left; border-bottom: 1px solid #cccccc;border-top: 1px solid #cccccc;"><?php echo JText::_( 'COM_MTREE_THIS_LISTING' ) ?></td></tr>
				<tr class="row0"><td colspan="2" style="background-color:#F1F3F5">
					<?php
						$tlisting = new mtDisplay();
						$tlisting->add(JText::_( 'COM_MTREE_NAME' ), '<a href="index.php?option=com_mtree&task=editlink&link_id=' . $thislink->link_id . '">' . $thislink->link_name . '</a>');
						$tlisting->add( JText::_( 'COM_MTREE_LISTING_ID' ), $thislink->link_id );
						$tlisting->add( JText::_( 'COM_MTREE_CATEGORY' ), '<a href="index.php?option=com_mtree&task=listcats&cat_id=' . $thislink->cat_id . '">' . $thislink->getCatName() . '</a>');
						$tlisting->add( JText::_( 'COM_MTREE_REVIEWS' ), '<a href="index.php?option=com_mtree&task=reviews_list&link_id=' . $thislink->link_id . '">' . $reviews . '</a>');
						$tlisting->add( JText::_( 'COM_MTREE_HITS' ), $thislink->link_hits );
						$tlisting->add( JText::_( 'COM_MTREE_MODIFIED2' ), tellDateTime($thislink->link_modified) );
						$tlisting->display();
					?>
				</td></tr>
						<?php
					}
				}
				
				// Search
				$search_text 	= JRequest::getVar( 'search_text', '', 'post');
				$search_where	= JRequest::getInt( 'search_where', 0, 'post'); // 1: Listing, 2: Category
				
				?>

				<tr><td colspan="2" style="background: #DDE1E6; border-bottom: 1px solid #cccccc;border-top: 1px solid #cccccc;font-weight:bold;"><?php echo JText::_( 'COM_MTREE_SEARCH' ) ?></td></tr>
				<tr><td colspan="2" align="left" style="background-color:#F1F3F5">
					<form action="index.php" method="post">
					<input class="text_area" type="text" name="search_text" size="10" maxlength="250" value="<?php echo $search_text; ?>" /> <input type="submit" value="<?php echo JText::_( 'COM_MTREE_SEARCH' ) ?>" class="button" />
					<select name="search_where" class="inputbox" size="1">
						<option value="1"<?php echo ($search_where == 1)?' selected':''?>><?php echo JText::_( 'COM_MTREE_LISTINGS' ) ?></option>
						<option value="2"<?php echo ($search_where == 2)?' selected':''?>><?php echo JText::_( 'COM_MTREE_CATEGORIES' ) ?></option>
					</select>
					<a href="index.php?option=com_mtree&task=advsearch"><?php echo JText::_( 'COM_MTREE_ADVANCED_SEARCH_SHORT' ) ?></a>
					<input type="hidden" name="option" value="com_mtree" />
					<input type="hidden" name="task" value="search" />
					<input type="hidden" name="limitstart" value="0" />
					</form>
				</td></tr>

				<tr><td colspan="2" style="background: #DDE1E6; border-bottom: 1px solid #cccccc;border-top: 1px solid #cccccc;font-weight:bold;"><?php echo JText::_( 'COM_MTREE_MORE' ) ?></td></tr>
				<tr>
					<td style="background: #DDE1E6;">
						<img src="templates/<?php echo $template; ?>/images/menu/icon-16-search.png" width="16" height="16" />
					</td>
					<td style="background-color:#F1F3F5">
						&nbsp;<a class="mt_menu" href="index.php?option=com_mtree&task=spy"><?php echo JText::_( 'COM_MTREE_SPY_DIRECTORY' );?></a>
					</td>
				</tr>
				<tr>
					<td style="background: #DDE1E6;">
						<img src="templates/<?php echo $template; ?>/images/menu/icon-16-config.png" width="16" height="16" />
					</td>
					<td style="background-color:#F1F3F5">&nbsp;<a class="mt_menu<?php echo ($task=="config") ? "_selected": ""; ?>" href="index.php?option=com_mtree&task=config"><?php echo JText::_( 'COM_MTREE_CONFIGURATION' ) ?></a></td>
				</tr>
				<tr>
					<td style="background: #DDE1E6;">
						<img src="templates/<?php echo $template; ?>/images/menu/icon-16-themes.png" width="16" height="16" />
					</td>
					<td style="background-color:#F1F3F5">&nbsp;<a class="mt_menu<?php echo ($task=="templates") ? "_selected": ""; ?>" href="index.php?option=com_mtree&task=templates"><?php echo JText::_( 'COM_MTREE_TEMPLATES' ) ?></a></td>
				</tr>
				<tr>
					<td style="background: #DDE1E6;">
						<img src="templates/<?php echo $template; ?>/images/menu/icon-16-plugin.png" width="16" height="16" />
					</td>
					<td style="background-color:#F1F3F5">&nbsp;<a class="mt_menu<?php echo ($task=="customfields") ? "_selected": ""; ?>" href="index.php?option=com_mtree&task=customfields"><?php echo JText::_( 'COM_MTREE_CUSTOM_FIELDS' ) ?></a></td>
				</tr>

				<tr>
					<td style="background: #DDE1E6;">
						<img src="templates/<?php echo $template; ?>/images/menu/icon-16-component.png" width="16" height="16" />
					</td>
					<td style="background-color:#F1F3F5">&nbsp;<a class="mt_menu<?php echo ($task=="tools") ? "_selected": ""; ?>" href="index.php?option=com_mtree&task=tools"><?php echo JText::_( 'COM_MTREE_TOOLS' ) ?></a></td>
				</tr>
				<tr>
					<td style="background: #DDE1E6;">
						<img src="templates/<?php echo $template; ?>/images/menu/icon-16-info.png" width="16" height="16" />
					</td>
					<td style="background-color:#F1F3F5">&nbsp;<a class="mt_menu<?php echo ($task=="about") ? "_selected": ""; ?>" href="index.php?option=com_mtree&task=about"><?php echo JText::_( 'COM_MTREE_ABOUT_MOSETS_TREE' ) ?></a></td>
				</tr>

			</table>		
		</td>
		<td valign="top">
		<?php 
	}

	function print_endmenu() {	
	?></td>
		</tr>
	</table>
	<?php
	}

	function getChildren( $cat_id, $cat_level ) {
		global $mtconf;

		$database	=& JFactory::getDBO();
		$cat_ids	= array();

		if ( $cat_level > 0  ) {

			$sql = "SELECT cat_id, cat_name, cat_parent, cat_cats, cat_links FROM #__mt_cats AS cat WHERE cat_published=1 && cat_approved=1 && cat_parent= " . $database->quote($cat_id) . ' ';
			
			if ( !$mtconf->get('display_empty_cat') ) { 
				$sql .= "&& ( cat_cats > 0 || cat_links > 0 ) ";	
			}

			if( $mtconf->get('first_cat_order1') != '' )
			{
				$sql .= ' ORDER BY ' . $mtconf->get('first_cat_order1') . ' ' . $mtconf->get('first_cat_order2');
				if( $mtconf->get('second_cat_order1') != '' )
				{
					$sql .= ', ' . $mtconf->get('second_cat_order1') . ' ' . $mtconf->get('second_cat_order2');
				}
			}

			$database->setQuery( $sql );
			$cat_ids = $database->loadObjectList();

			if ( count($cat_ids) ) {
				foreach( $cat_ids AS $cid ) {
					$children_ids = HTML_mtree::getChildren( $cid->cat_id, ($cat_level-1) );
					$cat_ids = array_merge( $cat_ids, $children_ids );
				}
			}
		}

		return $cat_ids;

	}

	/***
	* Link
	*/
	function editLink( &$row, $fields, $images, $cat_id, $other_cats, &$lists, $number_of_prev, $number_of_next, &$pathWay, $returntask, &$form, $option, $activetab=0 ) {
		global $mtconf;

		JFilterOutput::objectHTMLSafe( $row );
		$editor = &JFactory::getEditor();
		?>
		<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js_library'); ?>"></script>
		<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>category.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>addlisting.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>jquery-ui-1.8.24.custom.min.js"></script>
		<?php if( $mtconf->get( 'use_map' ) ) { 
		?><script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>map.js"></script><?php
		}
		?>
		<script language="javascript" type="text/javascript">
		jQuery.noConflict();
		var mosConfig_live_site=document.location.protocol+'//' + location.hostname + '<?php echo ($_SERVER["SERVER_PORT"] == 80) ? "":":".$_SERVER["SERVER_PORT"] ?><?php echo $_SERVER["PHP_SELF"]; ?>';
		
		var active_cat=<?php echo $cat_id; ?>;
		var attCount=0;
		var validations=[];
		var cachedFields;
		<?php
		$fields->resetPointer();
		while( $fields->hasNext() ) {
			$field = $fields->getField();
			if($field->hasJSValidation() && $field->hasInputField()) {
				echo "\n";
				echo 'validations[\''.$field->getInputFieldID().'\']='.$field->getJSValidation().';';
			}
			$fields->next();
		}
		?>
		jQuery(document).ready(function(){
			<?php
			$fields->resetPointer();
			while( $fields->hasNext() ) {
				$field = $fields->getField();
				if($field->hasJSOnInit()) {
					echo "\n";
					echo $field->getJSOnInit().';';
				}
				$fields->next();
			}
			?>	
		});
		function addAtt() {
			attCount++;
			var newLi = document.createElement("LI");
			newLi.id="att"+attCount;
			newLi.style.marginRight="5px";
			newLi.style.position="relative";
			newLi.style.listStyleType="none";
			newLi.style.left="17px";
			var newFile=document.createElement("INPUT");
			newFile.className="text_area";
			newFile.name="image[]";
			newFile.type="file";
			newFile.size="28";
			newFile.multiple=true;
			newLi.appendChild(newFile);
			var newLink=document.createElement("A");
			newLink.href="javascript:remAtt("+attCount+")";
			removeText=document.createTextNode("<?php echo JText::_('Remove') ?>");
			newLink.appendChild(removeText);
			newLi.appendChild(newLink);
			gebid('upload_att').appendChild(newLi);
		}
		function remAtt(id) {gebid('upload_att').removeChild(gebid('att'+id));attCount--;}
		Joomla.submitbutton = function(task)
		{
			var form = document.mtForm;
			var scroll = new Fx.SmoothScroll({links:'mtForm',wheelStops:false})
			if (task == 'cancellink') {
				Joomla.submitform(task, document.getElementById('mtForm'));
				return;
			}
			
			<?php
			$fields->resetPointer();
			while( $fields->hasNext() ) {
				$field = $fields->getField();
				if($field->hasJSOnSave()) {
					echo "\n";
					echo $field->getJSOnSave().';';
				}
				$fields->next();
			}
			?>
			
			var validation_failed = false;
			var validation_fields = jQuery('#mtfields .mtinput input, #mtfields .mtinput textarea');
			if(validation_fields.length>0)
			{
				for(var index=0;index<validation_fields.length;index++)
				{
					var id=validation_fields[index].id;

					// Validate required fields
					if(
						(validation_fields[index].required && !mtValidateIsEmpty(validation_fields[index]))
						||
						!mtValidate(validation_fields[index])
					){
						validation_failed=true;
						addValidationErrorHighlight(id.slice(2).split('_').shift().toInt());
						scroll.toElement(id);
						jQuery('#validate-advice-'+id).fadeToggle('fast').fadeToggle('slow');
						validation_fields[index].focus();
					}else{
						removeValidationErrorHighlight(validation_fields[index].id.slice(2).split('_').shift().toInt());
					}
				}
			}

			if(validation_failed) {return false;}

			if(jQuery('#mapcon').css('display') == 'none') {
				document.mtForm.lat.value=0;
				document.mtForm.lng.value=0;
				document.mtForm.zoom.value=0;
			}

			var hash = jQuery("#sortableimages").sortable('serialize');
			if(hash != ''){document.mtForm.img_sort_hash.value=hash;}
			if(attCount>0 && checkImgExt(attCount,jQuery("input:file[name|='image[]']"))==false) {
				alert('<?php echo addslashes(JText::_( 'COM_MTREE_PLEASE_SELECT_A_JPG_PNG_OR_GIF_FILE_FOR_THE_IMAGES' )) ?>');
				return;
			} else {
				Joomla.submitform(task, document.getElementById('mtForm'));
			}
			return;
		}
		</script>
	<form action="index.php" method="post" name="mtForm" id="mtForm" enctype="multipart/form-data">
	<center>
	<?php
		if ( $row->link_approved <= 0 ) {

			?>
			<table cellpadding="0" cellspacing="0" border="0" class="toolbar">
			<tr height="60" valign="middle" align="center">
			<?php

			if ( $number_of_prev > 0 ) {
			?>
			<td class="button">
				<div class="toolbar-list">
				<a class="toolbar" style="border:none" href="javascript:submitbutton('prev_link');">
					<span class="icon-32-back" title="<?php echo JText::_( 'COM_MTREE_PREVIOUS' ) ?>"></span>
					<b> (<?php echo $number_of_prev ?>) <?php echo JText::_( 'COM_MTREE_PREVIOUS' ) ?></b>
				</a>
				</div>
			</td>
			<?php
			} else {
			?>
			<td class="button">
				<div class="toolbar-list">
				<span class="icon-32-back" title="<?php echo JText::_( 'COM_MTREE_PREVIOUS' ) ?>"></span>
				<b><font color="#C0C0C0"> (0) <?php echo JText::_( 'COM_MTREE_PREVIOUS' ) ?></font></b>
				</div>
			</td>
			<?php
			}
			?>
			<td>
				<fieldset style="padding: 5px; border: 1px solid #c0c0c0">
					<input style="float:none;display:inline" type="radio" name="act" id="act_ignore" value="ignore" checked="checked" /><label style="float:none;display:inline" for="act_ignore"><?php echo JText::_( 'COM_MTREE_IGNORE' ) ?></label>
					<input style="float:none;display:inline" type="radio" name="act" id="act_approve" value="approve" /><label style="float:none;display:inline" for="act_approve"><?php echo JText::_( 'COM_MTREE_APPROVE' ) ?></label>
					<input style="float:none;display:inline" type="radio" name="act" id="act_discard" value="discard" /><label style="float:none;display:inline" for="act_discard"><?php echo JText::_( 'COM_MTREE_REJECT' ) ?></label>
				</fieldset>
			</td>
			<?php 

			if ( $number_of_next > 0 ) {
			?>
			<td class="button">
				<div class="toolbar-list">
				<a class="toolbar" style="border:none" href="javascript:submitbutton('next_link');">
					<span class="icon-32-forward" title="<?php echo JText::_( 'COM_MTREE_NEXT' ) ?>"></span>
					<b><?php echo JText::_( 'COM_MTREE_NEXT' ) ?> (<?php echo $number_of_next ?>) </b>
				</a>
				</div>
			</td>
			<?php
			} else {
			?>
			<td>
				<div class="toolbar-list">
				<a class="toolbar" style="border:none" href="javascript:submitbutton('next_link');">
					<span class="icon-32-save" title="<?php echo JText::_( 'COM_MTREE_SAVE' ) ?>"></span>
					<strong><?php echo JText::_( 'COM_MTREE_SAVE' ) ?></strong>
				</a>
				</div>
			</td>
			<?php
			}
			?>
			</tr>
			</table>
			<?php
		}
	?>
	</center>

	<table width="100%"><tr>
		<th align="left" style="background: url(..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folderopen.gif) no-repeat center left"><div style="margin-left: 18px"><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $cat_id, 'index.php?option=com_mtree&task=listcats' ); ?></div></th>
	</tr></table>



















	<!-- <table width="100%">
		<tr>
			<td valign="top"> -->
	<div class="width-60 fltlft">
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'COM_MTREE_TEM_LISTING_DETAILS' ); ?></legend>
					<ul class="adminformlist" id="mtfields">
						<li>
							<label for="browsecat"><?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?></label>
							<ul class="linkcats" id="linkcats">
							<li id="lc<?php echo $cat_id; ?>"><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $cat_id, '' ); ?></li>
							<?php
							if ( !empty($other_cats) ) {
								foreach( $other_cats AS $other_cat ) {
									if ( is_numeric( $other_cat ) ) {
										echo '<li id="lc' . $other_cat . '"><a href="javascript:remSecCat('.$other_cat.')">'.JText::_( 'COM_MTREE_REMOVE' ).'</a>'. $pathWay->printPathWayFromCat_withCurrentCat( $other_cat, '' ) . '</li>';
									}
								}
							}
							?>
							</ul>
							<a href="#" onclick="javascript:togglemc();return false;" id="lcmanage"><?php echo JText::_( 'COM_MTREE_MANAGE' ); ?></a>
							<div id="mc_con" style="display:none">
							<span id="mc_active_pathway" style="float:left;padding: 1px 0pt 1px 3px; background-color: white; width: 98%;position:relative;top:4px;height:13px;color:black"><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $cat_id, '' ); ?></span>
							<?php echo $lists["cat_id"]; ?>
							<input type="button" class="button" value="<?php echo JText::_( 'COM_MTREE_UPDATE_CATEGORY' ) ?>" id="mcbut1" onclick="updateMainCat()" style="float:left;clear:left"/>
							<input type="button" class="button" value="<?php echo JText::_( 'COM_MTREE_ALSO_APPEAR_IN_THIS_CATEGORIES' ) ?>" id="mcbut2" onclick="addSecCat()" style="float:left"/>
							</div>
						</li>
					<?php
					$field_link_desc = $fields->getFieldById(2);
					$fields->resetPointer();
					while( $fields->hasNext() ) {
						$field = $fields->getField();
						if($field->hasInputField() && !in_array($field->name,array('metakey','metadesc'))) {
							echo '<li id="field_'.$field->getId().'" class="'.$field->getFieldTypeClassName().'">';
							if($field->hasCaption()) {
								echo '<span id="caption_'.$field->getId().'" class="mtcaption">';
								echo '<label for="'.$field->getInputFieldName().'">';
								echo $field->getCaption();
								if($field->isRequired()) {
									echo '<span class="star">&#160;*</span>';
								}
								echo '</label>';
								echo '</span>';
							}
							echo '<span id="input_'.$field->getId().'" class="mtinput">';
							echo $field->getModPrefixText();
							echo $field->getInputHTML();
							echo $field->getModSuffixText();
							echo '</span>';
							echo '</li>';
						}
						$fields->next();
					}
					?>
					</ul>
				</fieldset>
	</div>
	<div class="width-40 fltrt">
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'COM_MTREE_IMAGES' ); ?></legend>
					<table class="admintable" width="100%">
						<tr>
							<td valign="top">
								<p style="color:#666"><?php echo JText::_( 'COM_MTREE_DRAG_TO_SORT_IMAGES_DESELECT_CHECKBOX_TO_REMOVE' ); ?></p>

								<ul style="list-style-type: none; 
							margin: 10px 0 0 0;
							padding: 0;
							width: 350px;
							overflow: visible;" id="sortableimages"><?php
							foreach( $images AS $image ) {
								echo '<li style="
								position:relative;
								left:-13px;
								margin: 0 0 13px 0;
								padding: 0px; 
								float: left; 
								list-style-position: outside;
								line-height: 100%;" id="img_' . $image->img_id . '">';
								echo '<input style="position:relative;
								left: 20px;
								top: 10px;
								vertical-align: top;
								z-index: 1;
								margin: 0;
								padding: 0;" type="checkbox" name="keep_img[]" value="' . $image->img_id . '" checked />';
								echo '<a href="' . $mtconf->getjconf('live_site');
								switch( $mtconf->get('small_image_click_target_size','o') )
								{
									case 'm':
										echo $mtconf->get('relative_path_to_listing_medium_image');
										break;
									default:
									case 'o':
										echo $mtconf->get('relative_path_to_listing_original_image');
										break;
									case 's':
										echo $mtconf->get('relative_path_to_listing_small_image');
										break;
								}
								echo $image->filename . '" target="_blank">';
								echo '<img border="0" style="position:relative;border:1px solid black;" align="middle" src="' . $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_listing_small_image') . $image->filename . '" alt="' . $image->filename . '" />';
								echo '</a>';
								echo '</li>';
							}
							?>
							</ul>
							<ol id="upload_att" style="overflow:hidden;
							clear: both;
							list-style-type: none;
							margin: 0;
							padding: 0;">
							</ol>
							<div style="margin: 10px 0 10px 2px;">
							<a href="javascript:addAtt();" id="add_att"><?php echo JText::_( 'COM_MTREE_ADD_AN_IMAGE' ) ?></a>
							</div>
							</td>
						</tr>
					</table>
				</fieldset>
				
				<?php if( $mtconf->get( 'use_map' ) ) { ?>
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'COM_MTREE_MAP' ); ?></legend>
					<div id="mapcon">
					<?php
					$width = '100%';
					$height = '200px';
					?>
					<script src="http://maps.googleapis.com/maps/api/js?v=3.6&amp;sensor=false" type="text/javascript"></script>
					<script type="text/javascript">
						var map = null;
					    	var geocoder = null;
						var marker = null;
						var infowindow = null;
						var defaultCountry = '<?php echo addslashes($mtconf->get( 'map_default_country' )); ?>';
						var defaultState = '<?php echo addslashes($mtconf->get( 'map_default_state' )); ?>';
						var defaultCity = '<?php echo addslashes($mtconf->get( 'map_default_city' )); ?>';
						var defaultLat = '<?php echo addslashes($mtconf->get('map_default_lat')); ?>';
						var defaultLng = '<?php echo addslashes($mtconf->get('map_default_lng')); ?>';
						var defaultZoom = '<?php echo addslashes($mtconf->get('map_default_zoom')); ?>';
						var linkValLat = '<?php echo $row->lat; ?>';
						var linkValLng = '<?php echo $row->lng; ?>';
						var linkValZoom = '<?php echo $row->zoom; ?>';
					</script>
					<div id="mapContainer">
					<div style="padding:4px 0; width:95%"><input style="float:none" type="button" onclick="locateInMap()" value="<?php echo JText::_( 'COM_MTREE_LOCATE_IN_MAP' ); ?>" name="locateButton" id="locateButton" /><span style="padding:0px; margin:3px" id="map-msg"></span></div>
					<div id="map" style="width:<?php echo $width; ?>;height:<?php echo $height; ?>"></div>
					</div>
					<input type="hidden" id="lat" name="lat" value="<?php echo $row->lat; ?>" />
					<input type="hidden" id="lng" name="lng" value="<?php echo $row->lng; ?>" />
					<input type="hidden" id="zoom" name="zoom" value="<?php echo $row->zoom; ?>" />
					<input type="hidden" id="show_map" name="show_map" value="<?php echo $row->show_map; ?>" />
					</div>
					<a id="togglemap" href="#" onclick="javascript:toggleMap();"><? echo JText::_('COM_MTREE_REMOVE_MAP'); ?></a>
				</fieldset>
				<?php } 
				
				$document	= &JFactory::getDocument();
				$renderer	= $document->loadRenderer('module');

				$position	= 'mt-editlink-right';
				$contents	= '';

				foreach (JModuleHelper::getModules($position) as $mod)  {
					$params = new JParameter( $mod->params );
					$output = $renderer->render($mod);
					if( !empty( $output ))
					{
						$contents .= '<fieldset class="adminform">';
						$contents .= '<legend>'.$mod->title.'</legend>';
						$contents .= $output;
						$contents .= '</fieldset>';
					}
				}
			
				echo $contents;
				
				echo JHTML::_('sliders.start','content-sliders-'.$row->link_id, array('useCookie'=>1));
				
				foreach ($form->getFieldsets() as $fieldset): 
					echo JHTML::_('sliders.panel',JText::_( 'COM_MTREE_'.$fieldset->name ), $fieldset->name);
					echo '<fieldset class="panelform">';
					echo '<table class=paramlist cellpadding=0 cellspacing=1>';
					foreach($form->getFieldset($fieldset->name) AS $field):
					?>
			                    <?php if ($field->hidden): ?>
			                         <?php echo $field->input;?>
			                    <?php else:?>
						<tr>
					<td class="paramlist_key" width="40%">
			                            <?php echo $field->label; ?>
					</td>
		                         <td class="paramlist_value"><?php echo $field->input;?></td>
					</tr>
			                    <?php endif;?>
			               <?php endforeach;
					echo '</table>';
					echo '</fieldset>';
			     	endforeach;
			
				echo JHTML::_('sliders.end');
				?>
	</div>

	<input type="hidden" name="img_sort_hash" value="" />
	<input type="hidden" name="link_id" value="<?php echo $row->link_id; ?>" />
	<input type="hidden" name="original_link_id" value="<?php echo $row->original_link_id; ?>" />
	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="task" value="editlink" />
	<input type="hidden" name="returntask" value="<?php echo ($row->link_approved <= 0)?"listpending_links" : $returntask ?>" />
	<input type="hidden" name="cat_id" value="<?php echo $cat_id; ?>" />
	<input type="hidden" name="other_cats" id="other_cats" value="<?php echo ( ( !empty($other_cats) ) ? implode(', ', $other_cats) : '' ) ?>" />
	<input type="hidden" name="is_admin" value="1" />
	<?php echo JHTML::_( 'form.token' ); ?>
	</form>
	<?php
	}
	
	function move_links( $link_id, $cat_parent, $catList, $pathWay, $option ) {
		global $mtconf;
?>
<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>category.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js_library'); ?>"></script>
<script language="javascript" type="text/javascript">
	jQuery.noConflict();
	var mosConfig_live_site=document.location.protocol+'//' + location.hostname + '<?php echo ($_SERVER["SERVER_PORT"] == 80) ? "":":".$_SERVER["SERVER_PORT"] ?><?php echo $_SERVER["PHP_SELF"]; ?>';
	var active_cat=<?php echo $cat_parent; ?>;
	jQuery(document).ready(function(){
		jQuery('#browsecat').click(function(){
			cc(jQuery(this).val());
		});
	});
	Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task, document.getElementById('mtForm'));
	}
</script>

<form action="index.php" method="post" name="adminForm" id="mtForm">

<table cellpadding="5" cellspacing="0" border="0" width="100%" class="adminform">
	<tr>
		<td width="20%" align="right"><?php echo JText::_( 'COM_MTREE_NUMBER_OF_ITEMS' ) ?>:</td>
		<td align="left"><?php echo count( $link_id );?></td>
	</tr>
	<tr>
		<td align="right" valign="top"><?php echo JText::_( 'COM_MTREE_CURRENT_CATEGORY' ) ?>:</td>
		<td align="left"><strong><?php echo $pathWay->printPathWayFromLink( 0, 'index.php?option=com_mtree&task=listcats' );?></strong></td>
	</tr>
	<tr>
		<td align="right" valign="top"><?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?>:</td>
		<td align="left">
			<div id="mc_active_pathway" style="border: 1px solid #C0C0C0; padding: 1px 0pt 1px 3px;margin-bottom:4px; background-color: white; width: 30%;color:black"><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $cat_parent, '' ); ?></div>
			<?php echo $catList;?>
		</td>
	</tr>
</table>

<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="new_cat_parent" value="<?php echo $cat_parent;?>" />
<input type="hidden" name="task" value="links_move" />
<input type="hidden" name="boxchecked" value="1" />
<?php echo JHTML::_( 'form.token' ); ?>
<?php
		foreach ($link_id as $id) {
			echo "\n<input type=\"hidden\" name=\"lid[]\" value=\"$id\" />";
		}
?>
</form>

<?php
	}

	function copy_links( $link_id, $cat_parent, $lists, $pathWay, $option ) {
		global $mtconf;
?>
<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>category.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js_library'); ?>"></script>
<script language="javascript" type="text/javascript">
	jQuery.noConflict();
	var mosConfig_live_site=document.location.protocol+'//' + location.hostname + '<?php echo ($_SERVER["SERVER_PORT"] == 80) ? "":":".$_SERVER["SERVER_PORT"] ?><?php echo $_SERVER["PHP_SELF"]; ?>';
	var active_cat=<?php echo $cat_parent; ?>;
	jQuery(document).ready(function(){
		jQuery('#browsecat').click(function(){
			cc(jQuery(this).val());
		});
	});
	Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task, document.getElementById('mtForm'));
	}
</script>

<form action="index.php" method="post" name="adminForm" id="mtForm">

<table cellpadding="5" cellspacing="0" border="0" width="100%" class="adminform">
	<tr>
		<td width="20%" align="right"><?php echo JText::_( 'COM_MTREE_NUMBER_OF_ITEMS' ) ?>:</td>
		<td align="left"><?php echo count( $link_id );?></td>
	</tr>
	<tr>
		<td align="right" valign="top"><?php echo JText::_( 'COM_MTREE_CURRENT_CATEGORY' ) ?>:</td>
		<td align="left"><strong><?php echo $pathWay->printPathWayFromLink( 0, 'index.php?option=com_mtree&task=listcats' );?></strong></td>
	</tr>
	<tr>
		<td align="right" valign="top"><?php echo JText::_( 'COM_MTREE_COPY_TO' ) ?>:</td>
		<td align="left">
		<div id="mc_active_pathway" style="border: 1px solid #C0C0C0; padding: 1px 0pt 1px 3px;margin-bottom:4px; background-color: white; width: 30%;color:black"><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $cat_parent, '' ); ?></div>
		<?php echo $lists['cat_id'];?></td>
	</tr>

	<tr><td colspan="2" height="10px"></td></tr>

	<tr>
		<th colspan="2"><?php echo JText::_( 'COM_MTREE_OPTIONS' ) ?></th>
	</tr>
	<tr>
		<td align="right"><?php echo JText::_( 'COM_MTREE_COPY_REVIEWS' ) ?>:</td>
		<td align="left"><?php echo $lists['copy_reviews'] ;?></td>
	</tr>
	<tr>
		<td align="right"><?php echo JText::_( 'COM_MTREE_COPY_SECONDARY_CATEGORIES' ) ?>:</td>
		<td align="left"><?php echo $lists['copy_secondary_cats'] ;?></td>
	</tr>
	<tr>
		<td align="right"><?php echo JText::_( 'COM_MTREE_RESET_HITS' ) ?>:</td>
		<td align="left"><?php echo $lists['reset_hits'] ;?></td>
	</tr>
	<tr>
		<td align="right"><?php echo JText::_( 'COM_MTREE_RESET_RATINGS_AND_VOTES' ) ?>:</td>
		<td align="left"><?php echo $lists['reset_rating'] ;?></td>
	</tr>
</table>

<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="new_cat_parent" value="<?php echo $cat_parent;?>" />
<input type="hidden" name="task" value="links_copy" />
<input type="hidden" name="boxchecked" value="1" />
<?php echo JHTML::_( 'form.token' ); ?>
<?php
		foreach ($link_id as $id) {
			echo "\n<input type=\"hidden\" name=\"lid[]\" value=\"$id\" />";
		}
?>
</form>

<?php
	}

	/**
	* Category
	*/
	function listcats( &$rows, &$links, &$softlink_cat_ids, &$parent, $catPageNav, &$pageNav, &$pathWay, $option ) {
		global $mtconf;
		
		$app		= JFactory::getApplication();
		$database	=& JFactory::getDBO();
		$nullDate	= $database->getNullDate();

		JHTML::_('behavior.tooltip');
		
		$max_char = 80;

		?>
		<script language="javascript" type="text/javascript">
			function link_listItemTask( id, task ) {
				var f = document.adminForm;
				lb = eval( 'f.' + id );
				if (lb) {
					lb.checked = true;
					submitbutton(task);
				}
				return false;
			}

			function link_isChecked(isitchecked){
				if (isitchecked == true){
					document.adminForm.link_boxchecked.value++;
				}
				else {
					document.adminForm.link_boxchecked.value--;
				}
			}

			function link_checkAll( n ) {
				var f = document.adminForm;
				var c = f.link_toggle.checked;
				var n2 = 0;
				for (i=0; i < n; i++) {
					lb = eval( 'f.lb' + i );
					if (lb) {
						lb.checked = c;
						n2++;
					}
				}
				if (c) {
					document.adminForm.link_boxchecked.value = n2;
				} else {
					document.adminForm.link_boxchecked.value = 0;
				}
			}

		</script>
		
		<form action="index.php" method="post" name="adminForm">
		<script language="Javascript">
		<?php
		if ( $mtconf->get('admin_use_explorer') ) { ?>
		// Open Explorer
		d.openTo(<?php echo ( (isset($parent->cat_id)) ? $parent->cat_id : '0'); ?>, true);
		<?php } ?>
		</script>

		<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<th align="left" style="background: url(..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folderopen.gif) no-repeat center left"><div style="margin-left: 18px"><?php echo $pathWay->printPathWayFromLink( 0, 'index.php?option=com_mtree&task=listcats' ); ?></div>
				</th>
				<th align="right">
					<?php JHtml::_('behavior.modal'); ?>
					<a rel="{handler: 'iframe', size: {x: 500, y: 450}, onClose: function() {}}" href="index.php?option=com_mtree&amp;task=fastadd&amp;cat_parent=<?php echo $parent->cat_id; ?>&amp;hide=1&amp;tmpl=component" class="modal"><?php echo JText::_("COM_MTREE_FAST_ADD"); ?></a>
				</th>
			</tr>
		</table>
		<?php
		
		$app		= JFactory::getApplication('site');
		$database 	=& JFactory::getDBO();
		$listOrder	= $app->getUserStateFromRequest("listcats{$parent->cat_id}listordering", 'list.ordering', 'cat.lft');
		$listDirn	= $app->getUserStateFromRequest("listcats{$parent->cat_id}listdirection", 'list.direction', 'asc');
		$saveOrder 	= ($listOrder == 'cat.lft' && $listDirn == 'asc');
		$ordering 	= ($listOrder == 'cat.lft');
		$colspanAdd	= 0;
		
		?>
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<thead>
			<tr>
				<th width=6% style="min-width:45px;padding-left:8px;text-align:left" align="left">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
				</th>
				<th width="64%" align="left" style="text-align:left" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?></th>
				<th width="5%"><?php echo JText::_( 'COM_MTREE_CATEGORIES' ) ?></th>
				<th width="5%"><?php echo JText::_( 'COM_MTREE_LISTINGS' ) ?></th>
				<th width="10%"><?php echo JText::_( 'COM_MTREE_FEATURED' ) ?></th>
				<th width="10%"><?php echo JText::_( 'COM_MTREE_PUBLISHED' ) ?></th>
				<?php if( $mtconf->get('first_cat_order1') == 'lft' ): ?>
				<th width="6%">
					<?php echo JText::_( 'JGRID_HEADING_ORDERING' ); ?>
					<?php // echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.lft', $listDirn, $listOrder); ?>
					<?php /* if ($saveOrder) :?>
						<?php echo JHtml::_('grid.order',  $rows, 'filesave.png', 'categories.saveorder'); ?>
					<?php endif; */ ?>
				</th>
				<?php 
					$colspanAdd++;
				endif; 
				?>
			</tr>
			</thead>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i]; ?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input style=\"float:left\" type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->cat_id; ?>" onclick="isChecked(this.checked);" />
					<a href="index.php?option=com_mtree&amp;task=listcats&amp;cat_id=<?php echo $row->cat_id; ?>"><?php 
				if ($row->cat_image) {
					echo "<img style=\"float:right\" border=\"0\" src=\".." . $mtconf->get('relative_path_to_images') . "dtree/imgfolder2.gif\" width=\"18\" height=\"18\" onmouseover=\"this.src='.." . $mtconf->get('relative_path_to_images') . "dtree/imgfolder.gif'\" onmouseout=\"this.src='.." . $mtconf->get('relative_path_to_images') . "dtree/imgfolder2.gif'; return nd(); \" />";
				} else {
					echo "<img style=\"float:right\" border=\"0\" src=\".." . $mtconf->get('relative_path_to_images') . "dtree/folder.gif\" width=\"18\" height=\"18\" name=\"img".$i."\" onmouseover=\"this.src='.." . $mtconf->get('relative_path_to_images') . "dtree/folderopen.gif'\" onmouseout=\"this.src='.." . $mtconf->get('relative_path_to_images') . "dtree/folder.gif'\" />"; 
				}
				?></a>
				</td>
				<td align="left"><a href="index.php?option=com_mtree&amp;task=editcat&amp;cat_id=<?php echo $row->cat_id; ?>"><?php echo htmlspecialchars($row->cat_name); ?></a></td>
				<td align="center"><?php echo $row->cat_cats; ?></td>
				<td align="center"><?php echo $row->cat_links; ?></td>
				<td align="center">
					<?php echo JHtml::_('mtree.featured', $row->cat_featured, $i,'cat_'); ?>
				</td>
				 <td align="center">
					<?php echo JHtml::_('jgrid.published', $row->cat_published, $i, 'cat_', true); ?>
				</td>
				<?php if( $mtconf->get('first_cat_order1') == 'lft' ): ?>
				<td class="order" align=center>
					<?php if ($saveOrder) : ?>
						<span><?php 
							echo $catPageNav->orderUpIcon(
								$i, 
								isset($rows[$i -1]), 
								(($mtconf->get('first_cat_order2') == 'asc')?'cat_orderup':'cat_orderdown'), 
								'JLIB_HTML_MOVE_UP', 
								$ordering
								); 
						?></span>
						<span><?php 
							echo $catPageNav->orderDownIcon(
								$i, 
								$catPageNav->total, 
								isset($rows[$i +1]), 
								(($mtconf->get('first_cat_order2') == 'asc')?'cat_orderdown':'cat_orderup'), 
								'JLIB_HTML_MOVE_DOWN', 
								$ordering
								); 
						?></span>
					<?php endif; ?>
					<?php /* $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5" value="<?php echo $i + 1;//echo $orderkey + 1;?>" <?php echo $disabled ?> class="text-area-order" />
					<?php */ ?>
				</td>
				<?php endif; ?>
			</tr>
			<?php $k = 1 - $k; } ?>
			<tr style="background-color:#F7F7F7;">
				<th style="padding-left:9px;text-align:left;border-bottom:1px solid #CCCCCC;">
					<input type="checkbox" name="link_toggle" value="" onclick="link_checkAll(<?php echo count( $links ); ?>);" />
				</th>
				<th width="75%" colspan="<?php echo 2 + $colspanAdd; ?>" style="text-align:left;border-bottom:1px solid #CCCCCC;" class="title" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_LISTING' ) ?></th>
				<th width="5%" style="border-bottom:1px solid #CCCCCC;"><?php echo JText::_( 'COM_MTREE_REVIEWS' ) ?></th>
				<th width="10%" style="text-align:center;border-bottom:1px solid #CCCCCC;"><?php echo JText::_( 'COM_MTREE_FEATURED' ) ?></th>
				<th width="10%" style="text-align:center;border-bottom:1px solid #CCCCCC;"><?php echo JText::_( 'COM_MTREE_PUBLISHED' ) ?></th>
			</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $links ); $i < $n; $i++) {
			$row = &$links[$i]; ?>
			<tr class="<?php echo "row$k"; ?>">
				<?php if ( $row->main == 1 ) { ?>
				<td>
					<input type="checkbox" id="lb<?php echo $i;?>" name="lid[]" value="<?php echo $row->link_id; ?>" onclick="link_isChecked(this.checked);" />
					<?php
					echo "<img style=\"float:right\" src=\".." . $mtconf->get('relative_path_to_images') . "page_white.png\" width=\"16\" height=\"16\" />" ?></td>
				<td colspan="<?php echo 2 + $colspanAdd; ?>" align="left">
					<?php
					if ($row->internal_notes) {
						$intnotes = preg_replace('/\s+/', ' ', nl2br($row->internal_notes));
						echo JHTML::_('tooltip', $intnotes, '', 'new.png' );
					}
					?>
					<a href="index.php?option=com_mtree&amp;task=editlink&amp;link_id=<?php echo $row->link_id; ?>"><?php echo htmlspecialchars($row->link_name); ?></a>
				</td>
				<?php } else { ?>
				<td></td>
				<td colspan="<?php echo 2 + $colspanAdd; ?>" align="left">
					<a href="index.php?option=com_mtree&task=listcats&cat_id=<?php echo $softlink_cat_ids[$row->link_id]->cat_id ?>"> <?php echo $pathWay->printPathWayFromLink( $row->link_id ); ?></a> <?php echo JText::_( 'COM_MTREE_ARROW' ) ?> <a href="index.php?option=com_mtree&task=editlink&link_id=<?php echo $row->link_id ?>"><?php echo htmlspecialchars($row->link_name); ?></a>
				</td>
				<?php } ?>
				<td align="center"><a href="index.php?option=com_mtree&task=reviews_list&link_id=<?php echo $row->link_id; ?>"><?php echo $row->reviews; ?></a></td>
			  <td align="center">
				<?php echo JHtml::_('mtree.featured', $row->link_featured, $i,'link_', true, 'lb'); ?>
			</td>
			  <td align="center">
				<?php echo JHtml::_('jgrid.published', $row->link_published, $i, 'link_', true, 'lb', $row->publish_up, $row->publish_down); ?>
				</td>
			</tr><?php

				$k = 1 - $k;
			}
			?>
			<tfoot>
			<tr>
				<td colspan="<?php echo 6 + $colspanAdd; ?>">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="cat_parent" value="<?php echo $parent->cat_id; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="link_boxchecked" value="0" />
		<input type="hidden" name="cat_names" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
<?php
	}
	
	/**
	* Fast Add Category
	*/
	function fastadd( $cat_parent, $option ) {
		?>
		<h2><?php echo JText::_( 'COM_MTREE_FAST_ADD_TITLE' ) ?></h2>
		
		<p><?php echo JText::_( 'COM_MTREE_FAST_ADD_INSTRUCTIONS' ) ?></p>

		<form action="index.php" method="post" name="adminForm">
			<p><textarea name="cat_names" cols=60 rows=20></textarea></p>
			<p><input type="submit" value="<?php echo JText::_( 'COM_MTREE_FAST_ADD_SUBMIT_BUTTON' ) ?>"/></p>

			<input type="hidden" name="task" value="fastadd_cat" />
			<input type="hidden" name="cat_parent" value="<?php echo $cat_parent;?>" />
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="tmpl" value="component" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}
	
	/**
	*
	* Writes the edit form for new and existing category
	*
	*/
	function editCat( &$row, $cat_parent, $related_cats, $browse_cat, $customfields, $fields_map_cfs, &$lists, &$pathWay, $configs, $cat_params, $configgroups, $total_assoc_links, $returntask, $option, $activetab=0, $template_all_subcats='' ) {
		global $mtconf;

		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'cat_desc' );
		
		jimport('joomla.html.pane');
		
		$pane	= &JPane::getInstance('sliders', array('allowAllClose' => true));
		$tabs	= &JPane::getInstance('tabs');
		$editor = &JFactory::getEditor();
		?>
		<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js_library'); ?>"></script>
		<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>category.js"></script>
		<script language="javascript" type="text/javascript">
		jQuery.noConflict();
		var txtRemove = '<?php echo addslashes(JText::_( 'COM_MTREE_REMOVE' )) ?>';
		var mosConfig_live_site=document.location.protocol+'//' + location.hostname + '<?php echo ($_SERVER["SERVER_PORT"] == 80) ? "":":".$_SERVER["SERVER_PORT"] ?><?php echo $_SERVER["PHP_SELF"]; ?>';
		var active_cat=<?php echo $row->cat_id; ?>;
		jQuery(document).ready(function(){
			toggleMcBut(active_cat);			
			jQuery('#browsecat').click(function(){
				cc(jQuery(this).val());
			});
			jQuery('input[type=checkbox][name^=override]').click(function(){
				toggleOverridableConfig(this);
			});
			jQuery('li[id^=config_]').each(function(){
				var li=this;
				var config_name = /config_([0-9A-Za-z_]+)/i.exec(li.id);
				config_name = config_name[1];
				jQuery('span select[name=config\\['+config_name+'\\]], span input[name=config\\['+config_name+'\\]], span textarea[name=config\\['+config_name+'\\]]').each(function(){
					if(li.className=='default') {
						jQuery(this).attr('disabled',true);
					} else {
						jQuery(this).attr('disabled',false)
					}
				});
			});
		});
		function toggleOverridableConfig(obj){
			var config_name = /override\[([0-9A-Za-z_]+)\]/i.exec(obj.name)
			config_name = config_name[1];
			var config = jQuery('#config_'+config_name);
			if(config.attr('class') == 'default') {
				jQuery('#config_'+config_name).attr('class','override');
				jQuery('#config_'+config_name+' span input').removeAttr('disabled');
				jQuery('#config_'+config_name+' span select').removeAttr('disabled');
				jQuery('#config_'+config_name+' span textarea').removeAttr('disabled');
			} else {
				jQuery('#config_'+config_name).attr('class','default');
				jQuery('#config_'+config_name+' span input').attr('disabled','true');
				jQuery('#config_'+config_name+' span select').attr('disabled','true');
				jQuery('#config_'+config_name+' span textarea').attr('disabled','true');
			}
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancelcat' || pressbutton == 'editcat_add_relcat' || pressbutton == 'editcat_browse_cat') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (form.cat_name.value == ""){
				alert( "<?php echo JText::_( 'COM_MTREE_CATEGORY_MUST_HAVE_NAME' ) ?>" );
			} else {
				<?php echo $editor->save( 'cat_desc' );	?>
				submitform( pressbutton );
			}
		}
		</script>
		<style type="text/css">
		ul.linkcats a {text-decoration:underline;margin-right:3px;}
		table.paramlist tr {
		}
		table.paramlist td {
			background-color:#F6F6F6;
			border-bottom:1px solid #E9E9E9;
			padding:5px 3px;
			
		}
		fieldset input, fieldset select, fieldset label, fieldset img {
			float:none;
			display:inline;
		}
		fieldset.fields-assignment {font-size:1.091em}
		</style>
		<form action="index.php" method="post" name="adminForm" id="mtForm" enctype="multipart/form-data">
		
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
    			<tr><th colspan="5" align="left" style="background: url(..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folderopen.gif) no-repeat center left"><div style="margin-left: 18px"><?php echo $pathWay->printPathWayFromLink( 0, 'index.php?option=com_mtree&task=listcats' ); ?></div></th></tr>
  		</table>

		<div class="width-60 fltlft">

		<fieldset>
		<legend><?php echo JText::_( 'COM_MTREE_CATEGORY_DETAILS' ); ?></legend>

		<table cellpadding="4" cellspacing="1" border="0" width="100%" class="admintable">
			<tr>
				<td width="15%" align="right" class="key"><?php echo JText::_( 'COM_MTREE_NAME' ) ?>:</td>
				<td width="85%" align="left">
					<input class="text_area required" type="text" name="cat_name" size="50" maxlength="250" value="<?php echo $row->cat_name;?>" />
				</td>
			</tr>
			<tr valign="bottom">
				<td width="20%" align="right" valign="top" class="key"><?php echo JText::_( 'COM_MTREE_RELATED_CATEGORIES' ) ?>:</td>
				<td width="80%" align="left" colspan="2">
					<ul class="linkcats" id="linkcats">
					<li><input style="float:none" type="button" class="button" name="lcmanage" value="<?php echo JText::_( 'COM_MTREE_ADD_RELATED_CATEGORIES' ); ?>" onclick="javascript:togglemc();return false;" /></li>
					<?php
					if ( !empty($related_cats) ) {
						foreach( $related_cats AS $related_cat ) {
							if ( is_numeric( $related_cat ) ) {
								echo '<li id="lc' . $related_cat . '"><a href="javascript:remSecCat('.$related_cat.')">'.JText::_( 'COM_MTREE_REMOVE' ).'</a>'. $pathWay->printPathWayFromCat_withCurrentCat( $related_cat, '' ) . '</li>';
							}
						}
					}
					?>
					</ul>
					<div id="mc_con" style="display:none">
					<div id="mc_active_pathway" style="border: 1px solid #C0C0C0; padding: 1px 0pt 1px 3px; background-color: white; width: 98%;position:relative;top:4px;height:13px;color:black"><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $row->cat_id, '' ); ?></div>
					<?php echo $lists["new_related_cat"]; ?>
					<input style="float:none" type="button" class="button" value="<?php echo JText::_( 'COM_MTREE_ADD' ) ?>" id="mcbut1" onclick="addSecCat()"/>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top" align="right" class="key"><?php echo JText::_( 'COM_MTREE_DESCRIPTION' ) ?>:</td>
				<td>
				<?php 
				echo $editor->display( 'cat_desc',  $row->cat_desc , '100%', '550', '75', '20' ) ;
				?></td>
			</tr>

			<tr>
				<td valign="top" align="right" class="key"><?php echo JText::_( 'COM_MTREE_IMAGE' ) ?>:</td>
				<td valign="top" align="left">
					<input style="float:none" class="text_area" type="file" name="cat_image" />
					<?php if ($row->cat_image != "") { ?>
					<p />
					<img style="border: 5px solid #c0c0c0;" src="<?php echo $mtconf->getjconf('live_site').$mtconf->get('relative_path_to_cat_small_image') . $row->cat_image ?>" />
					<input style="float:none" type="checkbox" name="remove_image" value="1" /> <?php echo JText::_( 'COM_MTREE_REMOVE_THIS_IMAGE' ) ?>
					<?php } ?>
				</td>
			</tr>
		</table>
		</fieldset>
		
		<?php
		
		if( !empty($configgroups) ):
		echo $tabs->startPane("content-pane");
		$configgroup = '';
		$j=0;

		foreach( $configgroups AS $configgroup ) {

			if( $j > 0 ) {
				echo $tabs->endPanel();
			}
			echo $tabs->startPanel( JText::_('COM_MTREE_'.$configgroup), $configgroup.'-page');

		?>
		<ul class="config">
		<?php
			$i = 0;
			foreach( $configs AS $config )
			{ 
				if( $config->groupname == $configgroup )
				{
					echo '<li';
					echo ' id="config_'.$config->varname.'"';
					$override_value = $cat_params->get($config->varname);
					if( $override_value != '' ) {
						echo ' class="override"';
					} else {
						echo ' class="default"';
					}
					echo '>';
					if( $config->configcode == 'note' ) {
						echo '<span style="border-bottom: 1px solid #C0C0C0;border-top: 1px solid #C0C0C0; background-color: #FFFFFF">';
					} elseif( !in_array($config->configcode, array('sort_direction','predefined_reply')) ) {
						echo '<label';
						if($i<=1) {
							// echo ' style="width:295px"';
						}
						echo '>';
						
						echo MTConfigHtml::overrideCheckbox(
							array(
								array(
									'varname'	=>	$config->varname,
									'value'		=>	$config->value,
									'override'	=>	$cat_params->get($config->varname)
								)
							),
							array('namespace'=>'config', 'class'=>'override')
						);
						
						$langcode = 'COM_MTREE_CONFIGNAME_'.strtoupper($config->varname);
						if( JText::_( 'COM_MTREE_CONFIGNAME_'.strtoupper($config->varname) ) == $langcode ) {
							echo $config->varname;
						} else {
							echo JText::_( 'COM_MTREE_CONFIGNAME_'.strtoupper($config->varname) );
						}

						if( substr($config->varname,0,4) == 'rss_' ) {
							if( $config->varname == 'rss_custom_fields') {
								echo ' (cust_#)';
							} else {
								echo ' ('.substr($config->varname,4).')';
							}
						}
						echo ':</label><span';
						if($i<=1) {
							echo ' width="75%"';
						}
						echo '>';
					}
					switch( $config->configcode ) {
						case 'text':
						case 'user_access':
						case 'user_access2':
						case 'sef_link_slug_type':
						default:
							echo MTConfigHtml::_(
								$config->configcode, 
								array(
									array(
										'varname'	=>	$config->varname,
										'value'		=>	$config->value,
										'override'	=>	$cat_params->get($config->varname)
									)
								),
								array('namespace'=>'config')
							);

							break;
						case 'sort_direction':
							continue;
							break;
						case 'cat_order':
						case 'listing_order':
						case 'review_order':
							$tmp_varname = substr($config->varname,0,-1);
							echo MTConfigHtml::_(
								$config->configcode, 
								array(
									array(
										'varname'	=>	$config->varname,
										'value'		=>	$config->value,
										'override'	=>	$cat_params->get($config->varname)
									),
									array(
										'varname'	=>	$tmp_varname.'2',
										'value'		=>	$configs[$tmp_varname.'2']->value,
										'override'	=>	$cat_params->get($tmp_varname.'2')
									)
								),
								array('namespace'=>'config')
							);
							if( substr($config->varname,-1) == '1' ) {
								unset($configs[$tmp_varname.'2']);
							} else {
								unset($configs[$tmp_varname.'1']);
							}
							break;
						case 'predefined_reply':
							continue;
							break;
						case 'predefined_reply_title':
							$tmp_varname = substr($config->varname,17,1);
							echo MTConfigHtml::_(
								$config->configcode, 
								array(
									array(
										'varname'	=>	$tmp_varname.'_title',
										'value'		=>	$configs['predefined_reply_'.$tmp_varname.'_title']->value,
										'override'	=>	$cat_params->get('predefined_reply_'.$tmp_varname.'_title')
									),
									array(
										'varname'	=>	$tmp_varname.'_message',
										'value'		=>	$configs['predefined_reply_'.$tmp_varname.'_message']->value,
										'override'	=>	$cat_params->get('predefined_reply_'.$tmp_varname.'_message')
									)
								),
								array('namespace'=>'config')
							);
							if( substr($config->varname,19) == 'title' ) {
								unset($configs['predefined_reply_'.$tmp_varname.'_message']);
							} else {
								unset($configs['predefined_reply_'.$tmp_varname.'_title']);
							}						
							break;
						case 'note':
							echo JText::_( 'COM_MTREE_CONFIGNOTE_'.strtoupper($config->varname) );
							break;
					}
					if( JText::_( 'COM_MTREE_CONFIGDESC_'.strtoupper($config->varname) ) != 'COM_MTREE_CONFIGDESC_'.strtoupper($config->varname) ) {
						echo '<span style="background-color:white;padding:0 0 3px 10px;">' . JText::_( 'COM_MTREE_CONFIGDESC_'.strtoupper($config->varname) ) . '</span>';
					}

				?>
				</span>
			</li>
		<?php 
					unset($configs[$config->varname]);
					$i++;
				}
			}
			echo '</ul>';
			$j++;
		}
		echo $tabs->endPanel();
		echo $tabs->endPane();
		endif;
		?>
		
		<?php if( !empty($customfields) ): ?>
		<p />
		<fieldset class="fields-assignment">
		<legend><?php echo JText::_( 'COM_MTREE_EDIT_CATEGORY_FIELDS_ASSIGNMENT' ); ?></legend>
		<?php echo JText::_( 'COM_MTREE_EDIT_CATEGORY_FIELDS_ASSIGNMENT_INSTRUCTIONS' ); ?>
		<button type="button" id="jform_toggle" class="jform-rightbtn" onclick="jQuery('.chk-cf').each(function() { this.checked = !this.checked; });jQuery('#cf-1').attr('checked',true);">
			<?php echo JText::_('JGLOBAL_SELECTION_INVERT'); ?>
		</button>
		<ul style="overflow:hidden;width:100%">
		<?php
		
		foreach( $customfields AS $cf ) 
		{
			switch($cf->cf_id)
			{
				case 1:
					$checked = ' checked="checked" disabled';
					break;
				default:
					$checked = (in_array($cf->cf_id,$fields_map_cfs) ? ' checked="checked"' : '');
					break;
			}
			echo '<li style="width:31%;float:left">';
			echo '<input type="checkbox" id="cf-'.$cf->cf_id.'" name="fields_map_cfs[]"' .
					' value="'.$cf->cf_id.'" class="chk-cf"'
					.$checked.'/>';
			echo '<label for="cf-'.$cf->cf_id.'">'.$cf->caption.'</label>';
			echo '</li>';
		}
		?>
		</ul>
		</fieldset>
		<?php endif; ?>
		
		</div>
		<div class="width-40 fltrt">
		<?php
		
		jimport('joomla.html.pane');
		$pane	=& JPane::getInstance('sliders');
	
		echo $pane->startPane("content-pane");
		echo $pane->startPanel( JText::_( 'COM_MTREE_PUBLISHING_INFO' ), "publishing-page" );
		?>
		<table width="100%" class="paramlist admintable" cellspacing="1">
			<?php if ( $row->cat_approved == 0 || $row->cat_id == 0 ) { ?>
			<tr>
				<td valign="top" align="right" class="paramlist_key"><?php echo JText::_( 'COM_MTREE_APPROVED' ) ?>:</td>
				<td align="left" class="paramlist_value"><?php echo $lists['cat_approved'] ?></td>
			</tr>
			<?php } else { ?>
			<input type="hidden" name="cat_approved" value="1" />
			<?php } ?>
			<tr>
				<td valign="top" align="right" class="paramlist_key"><?php echo JText::_( 'COM_MTREE_PUBLISHED' ) ?>:</td>
				<td align="left" class="paramlist_value"><?php echo $lists['cat_published'] ?></td>
			</tr>

			<tr>
				<td valign="top" align="right" class="paramlist_key"><?php echo JText::_( 'COM_MTREE_FEATURED' ) ?>:</td>
				<td align="left" class="paramlist_value"><?php echo $lists['cat_featured'] ?></td>
			</tr>

			<tr>
				<td valign="top" align="right" class="paramlist_key"><?php echo JText::_( 'COM_MTREE_ALLOW_SUBMISSION' ) ?>:</td>
				<td align="left" class="paramlist_value"><?php echo $lists['cat_allow_submission'] ?></td>
			</tr>
			<tr>
				<td valign="top" align="right" class="paramlist_key"><?php echo JText::_( 'COM_MTREE_SHOW_LISTINGS' ) ?>:</td>
				<td align="left" class="paramlist_value"><?php echo $lists['cat_show_listings'] ?></td>
			</tr>
			<tr>
				<td valign="top" align="right" class="paramlist_key"><?php echo JText::_( 'COM_MTREE_ALIAS' ) ?>:</td>
				<td align="left" class="paramlist_value"><input style="width:96%" type="text" name="alias" value="<?php echo $row->alias; ?>" class="inputbox" /></td>
			</tr>
			<tr>
				<td valign="top" align="right" class="paramlist_key"><?php echo JText::_( 'COM_MTREE_CUSTOM_TITLE' ) ?>:</td>
				<td align="left" class="paramlist_value"><input style="width:96%" type="text" name="title" value="<?php echo $row->title; ?>" class="inputbox" /></td>
			</tr>
			<tr>
				<td valign="top" align="right" class="paramlist_key"><?php echo JText::_( 'COM_MTREE_TEMPLATE' ) ?>:</td>
				<td align="left" class="paramlist_value">
					<?php echo $lists['templates']; ?>
				</td>
			</tr>
			<tr>
				<td valign="top" align="right" class="paramlist_key">&nbsp;</td>
				<td align="left" class="paramlist_value">
					<input type="checkbox" name="template_all_subcats" id="template_all_subcats" value="1"<?php echo (($template_all_subcats == 1) ? ' checked="on"' : '' ) ?> /><label for="template_all_subcats"><?php echo JText::_( 'COM_MTREE_CHANGE_ALL_SUBCATS_TO_THIS_TEMPLATE' ) ?></label>
				</td>
			</tr>
			<tr>
				<td valign="top" align="right" class="paramlist_key">&nbsp;</td>
				<td align="left" class="paramlist_value">
					<?php echo JText::_( 'COM_MTREE_USE_MAIN_INDEX_TEMPLATE_PAGE' ) ?><br />
				  <?php echo $lists['cat_usemainindex'] ?>
				</td>
			</tr>

			<tr>
				<td valign="top" class="paramlist_key"><?php echo JText::_( 'COM_MTREE_META_KEYWORDS' ) ?>:</td>
				<td class="paramlist_value"><textarea class="text_area" cols="30" rows="3" style="width:96%; height:80px" name="metakey" width="500"><?php
				echo str_replace('&','&amp;',$row->metakey); 
				?></textarea>
				</td>
			</tr>

			<tr>
				<td valign="top" class="paramlist_key"><?php echo JText::_( 'COM_MTREE_META_DESCRIPTION' ) ?>:</td>
				<td class="paramlist_value"><textarea class="text_area" cols="30" rows="3" style="width:96%; height:80px" name="metadesc" width="500"><?php echo str_replace('&','&amp;',$row->metadesc); ?></textarea>
				</td>
			</tr>
		</table>
		<?php

		echo $pane->endPanel();
		
		if( $row->cat_parent == 0 )
		{
		echo $pane->startPanel( JText::_( 'COM_MTREE_EDIT_CATEGORY_ASSOCIATION' ), "association-page" );

		?>
		<table width="100%" class="paramlist admintable" cellspacing="1">
			<tr>
				<td colspan=2>
					<?php echo JText::_( 'COM_MTREE_EDIT_CATEGORY_ASSOCIATION_DESC' ) ?>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left" class="paramlist_key"><?php echo JText::_( 'COM_MTREE_EDIT_CATEGORY_ASSOCIATED_CATEGORY' ) ?></td>
				<td class="paramlist_value">
					<?php echo $lists['cat_association']; ?>
				</td>
			</tr>
			<?php if( $total_assoc_links > 0 ) { ?>
			<tr><td colspan=2><strong><?php echo JText::_( 'COM_MTREE_EDIT_CATEGORY_CHANGE_ASSOCIATED_CATEGORY_EXPLAIN' ) ?></strong></td></tr>
			<tr>
				<td valign="top" align="left" class="paramlist_key"><?php echo JText::_( 'COM_MTREE_EDIT_CATEGORY_TOTAL_ASSOCIATED_LISTINGS' ) ?></td>
				<td class="paramlist_value">
					<?php echo $total_assoc_links; ?>
				</td>
			</tr>
			<?php } ?>
		</table>
		<?php
		
		echo $pane->endPanel();
		}
		echo $pane->startPanel( JText::_( 'COM_MTREE_OPERATIONS' ), "operations-page" );

		?>
		<table width="100%" class="paramlist admintable" cellspacing="1">
			<tr>
				<td><strong><?php echo JText::_( 'COM_MTREE_FULL_RECOUNT' ) ?></strong></td>
			</tr>
			<tr>
				<td valign="top" align="left"><?php echo JText::_( 'COM_MTREE_FULL_RECOUNT_EXPLAIN' ) ?><input type="button" class="button" value="<?php echo JText::_( 'COM_MTREE_PERFORM_FULL_RECOUNT' ) ?>" onClick="window.open('index.php?option=com_mtree&task=fullrecount&hide=1&cat_id=<?php echo $row->cat_id ?>&tmpl=component','recount','width=300,height=150')" /><p /></td>
			</tr>
			<tr>
				<td><strong><?php echo JText::_( 'COM_MTREE_FAST_RECOUNT' ) ?></strong></td>
			</tr>
			<tr>
				<td valign="top" align="left"><?php echo JText::_( 'COM_MTREE_FAST_RECOUNT_EXPLAIN' ) ?><input type="button" class="button" value="<?php echo JText::_( 'COM_MTREE_PERFORM_FAST_RECOUNT' ) ?>" onClick="window.open('index.php?option=com_mtree&task=fastrecount&hide=1&cat_id=<?php echo $row->cat_id ?>&tmpl=component','recount','width=300,height=150')" /><p /></td>
			</tr>
		</table>
		
		</div>
		<?php
		echo $pane->endPanel();
		echo $pane->endPane();
		?>
		<input type="hidden" name="cat_id" value="<?php echo $row->cat_id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="editcat" />
		<input type="hidden" name="returntask" value="<?php echo $returntask ?>" />
		<input type="hidden" name="cat_parent" value="<?php echo $cat_parent; ?>" />
		<input type="hidden" name="other_cats" id="other_cats" value="<?php echo ( ( !empty($related_cats) ) ? implode(', ', $related_cats) : '' ) ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
<?php
	}

	/***
	* Move Category
	*/
	function move_cats( $cat_id, $cat_parent, $catList, $pathWay, $option ) {
		global $mtconf;
?>
<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js_library'); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>category.js"></script>
<script language="javascript" type="text/javascript">
	jQuery.noConflict();
	var mosConfig_live_site=document.location.protocol+'//' + location.hostname + '<?php echo ($_SERVER["SERVER_PORT"] == 80) ? "":":".$_SERVER["SERVER_PORT"] ?><?php echo $_SERVER["PHP_SELF"]; ?>';
	var active_cat=<?php echo $cat_id; ?>;
	jQuery(document).ready(function(){
		jQuery('#browsecat').click(function(){cc(jQuery(this).val());});
	});
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancelcats_move') {
			submitform( pressbutton );
			return;
		}
		submitform( pressbutton );
	}
</script>

<form action="index.php" method="post" name="adminForm" id="mtForm">
<table cellpadding="5" cellspacing="0" border="0" width="100%" class="adminform">
	<tr>
		<td width="20%" align="right"><?php echo JText::_( 'COM_MTREE_NUMBER_OF_ITEMS' ) ?>:</td>
		<td align="left"><?php echo count( $cat_id );?></td>
	</tr>
	<tr>
		<td align="right" valign="top"><?php echo JText::_( 'COM_MTREE_CURRENT_CATEGORY' ) ?>:</td>
		<td align="left"><strong><?php echo $pathWay->printPathWayFromLink( 0, 'index.php?option=com_mtree&task=listcats' );?></strong></td>
	</tr>	
	<tr>
		<td align="right" valign="top"><?php echo JText::_( 'COM_MTREE_MOVE_TO' ) ?>:</td>
		<td align="left">
		<div id="mc_active_pathway" style="border: 1px solid #C0C0C0; padding: 1px 0pt 1px 3px;margin-bottom:4px; background-color: white; width: 40%;color:black"><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $cat_parent, '' ); ?></div>
		<?php echo $catList;?></td>
	</tr>
</table>

<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="new_cat_parent" value="<?php echo $cat_parent;?>" />
<input type="hidden" name="task" value="cats_move" />
<input type="hidden" name="boxchecked" value="1" />
<?php echo JHTML::_( 'form.token' ); ?>
<?php
		foreach ($cat_id as $id) {
			echo "\n<input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
		}
?>
</form>

<?php
	}
	
	/***
	* Copy Category
	*/
	function copy_cats( $cat_id, $cat_parent, $lists, $pathWay, $option ) {
		global $mtconf;
?>
<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js'); ?>category.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js_library'); ?>"></script>
<script language="javascript" type="text/javascript">
	jQuery.noConflict();
	var mosConfig_live_site=document.location.protocol+'//' + location.hostname + '<?php echo ($_SERVER["SERVER_PORT"] == 80) ? "":":".$_SERVER["SERVER_PORT"] ?><?php echo $_SERVER["PHP_SELF"]; ?>';
	var active_cat=<?php echo $cat_id; ?>;
	jQuery(document).ready(function(){
		//toggleMcBut(active_cat);			
		jQuery('#browsecat').click(function(){
			cc(jQuery(this).val());
		});
	});
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancelcats_copy') {
			submitform( pressbutton );
			return;
		}
		submitform( pressbutton );
	}
</script>

<form action="index.php" method="post" name="adminForm" id="mtForm">
<table cellpadding="5" cellspacing="0" border="0" width="100%" class="adminform">
	<tr>
		<td width="17%" align="right"><?php echo JText::_( 'COM_MTREE_NUMBER_OF_ITEMS' ) ?>:</td>
		<td align="left"><?php echo count( $cat_id );?></td>
	</tr>
	<tr>
		<td align="right" valign="top"><?php echo JText::_( 'COM_MTREE_CURRENT_CATEGORY' ) ?>:</td>
		<td align="left"><strong><?php echo $pathWay->printPathWayFromLink( 0, 'index.php?option=com_mtree&task=listcats' );?></strong></td>
	</tr>
	<tr>
		<td align="right" valign="top"><?php echo JText::_( 'COM_MTREE_COPY_TO' ) ?>:</td>
		<td align="left">
		<div id="mc_active_pathway" style="border: 1px solid #C0C0C0; padding: 1px 0pt 1px 3px;margin-bottom:4px; background-color: white; width: 40%;color:black"><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $cat_parent, '' ); ?></div>
		<?php echo $lists['cat_id'] ;?></td>
	</tr>

	<tr><td colspan="2" height="10px"></td></tr>

	<tr>
		<th colspan="2"><?php echo JText::_( 'COM_MTREE_OPTIONS' ) ?></th>
	</tr>
	<tr>
		<td align="right"><?php echo JText::_( 'COM_MTREE_COPY_SUBCATS' ) ?>:</td>
		<td align="left"><?php echo $lists['copy_subcats'] ;?></td>
	</tr>	<tr>
		<td align="right"><?php echo JText::_( 'COM_MTREE_COPY_RELCATS' ) ?>:</td>
		<td align="left"><?php echo $lists['copy_relcats'] ;?></td>
	</tr>
	<tr>
		<td align="right"><?php echo JText::_( 'COM_MTREE_COPY_LISTINGS' ) ?>:</td>
		<td align="left"><?php echo $lists['copy_listings'] ;?></td>
	</tr>
	<tr>
		<td align="right"><?php echo JText::_( 'COM_MTREE_COPY_REVIEWS' ) ?>:</td>
		<td align="left"><?php echo $lists['copy_reviews'] ;?></td>
	</tr>
	<tr>
		<td align="right"><?php echo JText::_( 'COM_MTREE_RESET_HITS' ) ?>:</td>
		<td align="left"><?php echo $lists['reset_hits'] ;?></td>
	</tr>
	<tr>
		<td align="right"><?php echo JText::_( 'COM_MTREE_RESET_RATINGS_AND_VOTES' ) ?>:</td>
		<td align="left"><?php echo $lists['reset_rating'] ;?></td>
	</tr>
		
</table>

<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="new_cat_parent" value="<?php echo $cat_parent;?>" />
<input type="hidden" name="task" value="cats_copy" />
<input type="hidden" name="boxchecked" value="1" />
<?php echo JHTML::_( 'form.token' ); ?>
<?php
		foreach ($cat_id as $id) {
			echo "\n<input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
		}
?>
</form>

<?php
	}

	function removecats( $categories, $cat_parent, $option ) {
		global $mtconf;
	?>

		<strong><?php echo JText::_( 'COM_MTREE_CONFIRM_DELETE_CATS' ) ?></strong>
		<p />

		<form action="index.php" method="post" name="adminForm">

		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<thead>
			<tr>
				<th width="18px" nowrap="nowrap">&nbsp;</th>
				<th width="80%" nowrap="nowrap" style="text-align:left" align="left"><?php echo JText::_( 'COM_MTREE_NAME' ) ?></th>
				<th width="10%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_CATEGORIES' ) ?></th>
				<th width="10%" nowrap="nowrap" align="center"><?php echo JText::_( 'COM_MTREE_LISTINGS' ) ?></th>
			</tr>
			</thead>
		<?php
		$k = 0;
		for ($i=0, $n=count( $categories ); $i < $n; $i++) {
			$row = &$categories[$i]; ?>
			<tr class="<?php echo "row$k"; ?>" align="left">
				<td width="18px"><img src="..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folder.gif" width="18" height="18" /><input type="hidden" name="cid[]" value="<?php echo $row->cat_id ?>" /></td>
				<td align="left" width="80%"><?php echo $row->cat_name; ?></td>
				<td><?php echo $row->cat_cats; ?></td>
				<td><?php echo $row->cat_links; ?></td>
			</tr>
			<?php		$k = 1 - $k; } ?>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="cat_parent" value="<?php echo $cat_parent;?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	<?php

	}
	
	/***
	* Approval
	*/
	function listpending_links( $links, $pathWay, $pageNav, $option ) {
		global $mtconf;
		JHTML::_('behavior.tooltip');
		?>
		<script language="javascript" type="text/javascript">
			function link_listItemTask( id, task ) {
				var f = document.adminForm;
				lb = eval( 'f.' + id );
				if (lb) {
					lb.checked = true;
					submitbutton(task);
				}
				return false;
			}

			function link_isChecked(isitchecked){
				if (isitchecked == true){
					document.adminForm.link_boxchecked.value++;
				}
				else {
					document.adminForm.link_boxchecked.value--;
				}
			}

			function link_checkAll( n ) {
				var f = document.adminForm;
				var c = f.link_toggle.checked;
				var n2 = 0;
				for (i=0; i < n; i++) {
					lb = eval( 'f.lb' + i );
					if (lb) {
						lb.checked = c;
						n2++;
					}
				}
				if (c) {
					document.adminForm.link_boxchecked.value = n2;
				} else {
					document.adminForm.link_boxchecked.value = 0;
				}
			}
		</script>
		<form action="index.php" method="post" name="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<thead>
			<tr>
				<th width="38" style="min-width:43px;text-align:right" align="right">
					<input type="checkbox" name="link_toggle" value="" onclick="link_checkAll(<?php echo count( $links ); ?>);" />
				</th>
				<th width="30%" style="text-align:left" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_LISTING' ) ?></th>
				<th width="60%" style="text-align:left" align="left" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?></th>
				<th width="10%" style="text-align:left"><?php echo JText::_( 'COM_MTREE_CREATED' ) ?></th>
			</tr>
			</thead>
		<?php
		$k = 0;
		for ($i=0, $n=count( $links ); $i < $n; $i++) {
			$row = &$links[$i]; ?>
			<tr class="<?php echo "row$k"; ?>" align="left">
				<td>
					<?php
					echo "<img style=\"float:left\" src=\".." . $mtconf->get('relative_path_to_images') . "page_white.png\" width=\"16\" height=\"16\">"; ?>
					<input type="checkbox" id="lb<?php echo $i;?>" name="lid[]" value="<?php echo $row->link_id; ?>" onclick="link_isChecked(this.checked);" />
				</td>
				<td><?php
					if ($row->internal_notes) {
						$intnotes = preg_replace('/\s+/', ' ', nl2br($row->internal_notes));
						echo JHTML::_('tooltip', $intnotes, '', 'messaging.png' );
						echo '&nbsp;';
					}
					echo (($row->link_approved < 0 ) ? '': '<b>' ); ?><a href="#edit" onclick="return link_listItemTask('lb<?php echo $i;?>','editlink_for_approval')"><?php echo $row->link_name; ?></a><?php echo (($row->link_approved < 0 ) ? '': '<b>' ); ?></td>
				<td><?php $pathWay->printPathWayFromLink( $row->link_id, '' ); ?></td>
				<td><?php echo tellDateTime($row->link_created); ?></td>
			</tr><?php

				$k = 1 - $k;
			}
			?>
			<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="listpending_links" />
		<input type="hidden" name="returntask" value="listpending_links" />
		<input type="hidden" name="link_boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

	function listpending_cats( $cats, $pathWay, $pageNav, $option ) {
		global $mtconf;
		?>
		<form action="index.php" method="post" name="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<thead>
			<tr>
				<th style="min-width: 43px; " align="right"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $cats ); ?>);" /></th>
				<th width="30%" style="text-align:left" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_CATEGORIES' ) ?></th>
				<th width="55%" style="text-align:left" align="left" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_PARENT' ) ?></th>
				<th width="10%" style="text-align:left" ><?php echo JText::_( 'COM_MTREE_CREATED' ) ?></th>
			</tr>
			</thead>
		<?php
		$k = 0;
		for ($i=0, $n=count( $cats ); $i < $n; $i++) {
			$row = &$cats[$i]; ?>
			<tr class="<?php echo "row$k"; ?>" align="left">
				<td><a href="#go" onclick="return listItemTask('cb<?php echo $i;?>','listcats')"><?php 
					
				if ($row->cat_image) {
					echo "<img style=\"float:left\" border=\"0\" src=\"..".$mtconf->get('relative_path_to_images')."dtree/imgfolder2.gif\" width=\"18\" height=\"18\" onmouseover=\"showInfo('" .$row->cat_name ."', '".$row->cat_image."', 'cat'); this.src='..".$mtconf->get('relative_path_to_images')."dtree/imgfolder.gif'\" onmouseout=\"this.src='..".$mtconf->get('relative_path_to_images')."dtree/imgfolder2.gif'; return nd(); \" />";
				} else {
					echo "<img style=\"float:left\" border=\"0\" src=\"..".$mtconf->get('relative_path_to_images')."dtree/folder.gif\" width=\"18\" height=\"18\" name=\"img".$i."\" onmouseover=\"this.src='..".$mtconf->get('relative_path_to_images')."dtree/folderopen.gif'\" onmouseout=\"this.src='..".$mtconf->get('relative_path_to_images')."dtree/folder.gif'\" />"; 
				}
				?></a><input style=\"float:right\" type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->cat_id; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editcat')"><?php echo $row->cat_name; ?></a></td>
				<td><?php echo $pathWay->printPathWayFromCat_withCurrentCat( $row->cat_parent, 0 ); ?></td>
				<td><?php echo tellDateTime($row->cat_created); ?></td>
			</tr><?php

				$k = 1 - $k;
			}
			?>

			<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="listpending_cats" />
		<input type="hidden" name="returntask" value="listpending_cats" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

	function listpending_reviews( $reviews, $pathWay, $pageNav, $option ) {
		global $mtconf;
		require_once( $mtconf->getjconf('absolute_path') . '/administrator/components/com_mtree/spy.mtree.html.php' );
		?>
		<script language="javascript" type="text/javascript" src="<?php echo $mtconf->getjconf('live_site') . $mtconf->get('relative_path_to_js_library'); ?>"></script>
		<script language="javascript" type="text/javascript">
		jQuery.noConflict();
		var predefined_reply=new Array();
		<?php
		$num_of_predefined_reply=0;
		for ( $j=1; $j <= 5; $j++ )
		{ 
			if( $mtconf->get( 'predefined_reply_'.$j.'_title' ) <> '' && $mtconf->get( 'predefined_reply_'.$j.'_message' ) <> '') {
				echo 'predefined_reply['.$j.']="'.str_replace("'","\\'",str_replace('"','\\"',str_replace("\t","\\t",str_replace("\r\n","\\n",str_replace("\\","\\\\",$mtconf->get( 'predefined_reply_'.$j.'_message' ))))))."\";\n";
				$num_of_predefined_reply++;
			}
		}
		?>
		function selectreply(value,rev_id){
			jQuery('#emailmsg_'+rev_id).val( predefined_reply[value] );
		}
		function toggleemaileditor(rev_id){
			jQuery('#emaileditor_'+rev_id).slideToggle('fast');
		}
		</script>
		<form action="index.php" method="post" name="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		<?php
		if ( count($reviews) <= 0 ) {
			?>
			<tr><th align="left">&nbsp;</th></tr>
			<tr class="row0"><td><?php echo JText::_( 'COM_MTREE_NO_REVIEW_FOUND' ) ?></td></tr>
			<?php
		} else {
			?>
			<thead>
			<tr>
				<td colspan="2">
					<div class="pagination">
					<?php echo $pageNav->getPagesLinks(); ?>
					<div class="limit"><?php echo $pageNav->getResultsCounter(); ?></div>
					</div>
				</td>
			</tr>
			</thead>
			<?php
			$k = 0;
			for ($i=0, $n=count( $reviews ); $i < $n; $i++) {
				$row = &$reviews[$i]; ?>
				<thead><tr><th style="text-align:left" align="left"<?php echo ( ($mtconf->get('use_internal_notes')) ? ' colspan="2"': '' ) ?>><?php 
					echo mtfHTML::rating($row->value);
				?>&nbsp;<a href="index.php?option=com_mtree&amp;task=editlink&amp;link_id=<?php echo $row->link_id; ?>"><?php echo $row->link_name ?></a> by <?php
				if($row->user_id > 0) {
					echo '<a href="index.php?option=com_mtree&task=spy&task2=viewuser&id='.$row->user_id.'">' . $row->username . '</a>';
				} elseif(!empty($row->email)) {
					echo '<a href="mailto:' . $row->email . '">' . $row->guest_name . '</a>';
				} else {
					echo $row->guest_name;
				}
				?>, <?php echo $row->rev_date ?> - <a href="<?php echo $mtconf->getjconf('live_site'). "/index.php?option=com_mtree&task=viewlink&link_id=$row->link_id"; ?>" target="_blank"><?php echo JText::_( 'COM_MTREE_VIEW_LISTING' ) ?></a></th></tr></thead>
				<tr align="left">
					<td<?php echo ( ($mtconf->get('use_internal_notes')) ? ' width="65%"': '' ) ?> valign="top" style="border-bottom:0px"><?php echo JText::_( 'COM_MTREE_REVIEW_TITLE' ) ?>: <input class="text_area" type="text" name="rev_title[<?php echo $row->rev_id; ?>]" value="<?php echo htmlspecialchars($row->rev_title); ?>" size="60" /></td>
					<?php if ( $mtconf->get('use_internal_notes') ) { ?><td valign="middle" width="35%" style="border-bottom:0px"><?php echo JText::_( 'COM_MTREE_INTERNAL_NOTES' ) ?>:</td><?php } ?>
				</tr>
				<tr align="left">
					<td<?php echo ( ($mtconf->get('use_internal_notes')) ? ' width="65%"': '' ) ?>>
						<textarea class="text_area" style="width:100%;height:150px" name="rev_text[<?php echo $row->rev_id ?>]"><?php echo htmlspecialchars($row->rev_text) ?></textarea>
						<p />
						<label for="app_<?php echo $row->rev_id ?>"><input type="radio" name="rev[<?php echo $row->rev_id ?>]" value="1" id="app_<?php echo $row->rev_id ?>" /><?php echo JText::_( 'COM_MTREE_APPROVE' ) ?></label>
						<label for="ign_<?php echo $row->rev_id ?>"><input type="radio" name="rev[<?php echo $row->rev_id ?>]" value="0" id="ign_<?php echo $row->rev_id ?>" checked="checked" /><?php echo JText::_( 'COM_MTREE_IGNORE' ) ?></label>
						<label for="rej_<?php echo $row->rev_id ?>"><input type="radio" name="rev[<?php echo $row->rev_id ?>]" value="-1" id="rej_<?php echo $row->rev_id ?>" /><?php echo JText::_( 'COM_MTREE_REJECT' ) ?></label>
						<?php if($row->value > 0) { ?>
						<label for="rejrv_<?php echo $row->rev_id ?>"><input type="radio" name="rev[<?php echo $row->rev_id ?>]" value="-2" id="rejrv_<?php echo $row->rev_id ?>" /><?php echo JText::_( 'COM_MTREE_REJECT_AND_REMOVE_VOTE' ) ?></label>
						<?php } 
						
						if( !empty($row->email) ) {
						?>						
						<span style="margin-top:2px;display:block;clear:left;"><input type="checkbox"<?php echo (($row->send_email)?' checked':''); ?> name="sendemail[<?php echo $row->rev_id ?>]" value="1" id="sendemail_<?php echo $row->rev_id ?>" onclick="toggleemaileditor(<?php echo $row->rev_id ?>)" /> <label for="sendemail_<?php echo $row->rev_id ?>"><?php echo JText::_( 'COM_MTREE_SEND_EMAIL_TO_REVIEWER_UPON_APPROVAL_OR_REJECTION' ) ?></label></span>
						<div id="emaileditor_<?php echo $row->rev_id ?>"<?php echo ((!$row->send_email)?' style="display:none"':''); ?>>
							<select onchange="selectreply(this.value,<?php echo $row->rev_id ?>)"<?php echo (($num_of_predefined_reply==0)?' disabled':''); ?>>
								<option><?php echo JText::_( 'COM_MTREE_SELECT_A_PRE_DEFINED_REPLY' ) ?></option>
								<?php
								for ( $k=1; $k <= 5; $k++ )
								{ 
									if( $mtconf->get( 'predefined_reply_'.$k.'_title' ) <> '') {
										echo '<option value="'.$k.'">'.$mtconf->get( 'predefined_reply_'.$k.'_title' ).'</option>';
									}
								}
								?>
							</select>&nbsp;<?php echo JText::_( 'COM_MTREE_OR_ENTER_THE_EMAIL_MESSAGE' ) ?>
							<p />
							<textarea name="emailmsg[<?php echo $row->rev_id ?>]" id="emailmsg_<?php echo $row->rev_id ?>" class="text_area" style="width:100%;height:110px"><?php echo $row->email_message ?></textarea>
						</div>
						<?php } ?>
					</td>
					<td valign="top"><textarea class="text_area" style="width:100%;height:150px;" name="admin_note[<?php echo $row->rev_id ?>]"><?php echo htmlspecialchars($row->admin_note) ?></textarea></td>
				</tr>				
				<?php		$k = 1 - $k; } 
				
			} ?>
			<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="listpending_reviews" />
		<input type="hidden" name="returntask" value="listpending_reviews" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

	function listpending_reports( $reports, $pathWay, $pageNav, $option ) {
		global $mtconf;
		?>
		<form action="index.php" method="post" name="adminForm">

		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		<?php
		if ( count($reports) <= 0 ) {
			?>
			<tr><th align="left">&nbsp;</th></tr>
			<tr class="row0"><td><?php echo JText::_( 'COM_MTREE_NO_REPORT_FOUND' ) ?></td></tr>
			<?php
		} else {
			?>
			<thead>
			<tr>
				<td colspan="2" align=right>
					<div class="pagination">
					<?php echo $pageNav->getPagesLinks(); ?>
					<div class="limit"><?php echo $pageNav->getResultsCounter(); ?></div>
					</div>
				</td>
			</tr>
			</thead>
			<?php
		$k = 0;
		for ($i=0, $n=count( $reports ); $i < $n; $i++) {
			$row = &$reports[$i]; ?>
			<thead><tr><th style="text-align:left" align="left"<?php echo ( ($mtconf->get('use_internal_notes')) ? ' colspan="2"': '' ) ?>><a href="index.php?option=com_mtree&task=editlink&link_id=<?php echo $row->link_id; ?>"><?php echo $row->link_name ?></a> - <a href="<?php echo $mtconf->getjconf('live_site') . "/index.php?option=com_mtree&task=viewlink&link_id=$row->link_id"; ?>" target="_blank"><?php echo JText::_( 'COM_MTREE_VIEW_LISTING' ) ?></a></th></tr></thead>
			<tr align="left">
				<td<?php echo ( ($mtconf->get('use_internal_notes')) ? ' width="65%"': '' ) ?> valign="top">
					<u><?php echo $row->subject . "</u>, " . ( (empty($row->username))? $row->guest_name : '<a href="index.php?option=com_mtree&task=spy&task2=viewuser&id='.$row->user_id.'">' . $row->username . '</a> ' ) ." ". $row->created ?>
					<p />
					<?php echo nl2br($row->comment) ?>
					<p />
					<label for="res_<?php echo $row->report_id ?>"><input type="radio" name="report[<?php echo $row->report_id ?>]" value="1" id="res_<?php echo $row->report_id ?>" /><?php echo JText::_( 'COM_MTREE_RESOLVED' ) ?></label>

					<label for="ign_<?php echo $row->report_id ?>"><input type="radio" name="report[<?php echo $row->report_id ?>]" value="0" id="ign_<?php echo $row->report_id ?>" checked="checked" /><?php echo JText::_( 'COM_MTREE_IGNORE' ) ?></label>
				</td>
				<?php if( $mtconf->get('use_internal_notes') ) { ?>
				<td style="height:100px;" valign="top" width="35%">
				<?php echo JText::_( 'COM_MTREE_INTERNAL_NOTES' ) ?>:<br />
				<textarea class="text_area" style="width:100%;height:80px;" name="admin_note[<?php echo $row->report_id ?>]"><?php echo htmlspecialchars($row->admin_note) ?></textarea>
				</td>
				<?php } ?>
			</tr>
			<?php		$k = 1 - $k; } } ?>
			<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="listpending_reports" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

	function listpending_reviewsreports( $reports, $pathWay, $pageNav, $option ) {
		global $mtconf;
		?>
		<form action="index.php" method="post" name="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		<?php
		if ( count($reports) <= 0 ) {
			?>
			<tr><th align="left">&nbsp;</th></tr>
			<tr class="row0"><td><?php echo JText::_( 'COM_MTREE_NO_REPORT_FOUND' ) ?></td></tr>
			<?php
		} else {
		?>
		<thead>
		<tr>
			<td align=right<?php echo ( ($mtconf->get('use_internal_notes')) ? ' colspan="2"': '' ) ?>>
				<div class="pagination">
				<?php echo $pageNav->getPagesLinks(); ?>
				<div class="limit"><?php echo $pageNav->getResultsCounter(); ?></div>
				</div>
			</td>
		</tr>
		</thead>
		<?php
		$k = 0;
		for ($i=0, $n=count( $reports ); $i < $n; $i++) {
			$row = &$reports[$i]; ?>
			<thead><tr><th style="text-align:left" align="left"<?php echo ( ($mtconf->get('use_internal_notes')) ? ' colspan="2"': '' ) ?>><a href="<?php echo $mtconf->getjconf('live_site') . "/index.php?option=com_mtree&task=viewlink&link_id=$row->link_id"; ?>" target="_blank"><?php echo $row->link_name ?></a></th></tr></thead>
			<tr align="left">
				<td<?php echo ( ($mtconf->get('use_internal_notes')) ? ' width="65%"': '' ) ?>>
					<blockquote style="margin:3px 0 10px 2px;background-color:#F3F3F3;padding:6px;border: 1px solid #e1e1e1;border-left:6px solid #E1E1E1;">
					<?php echo '<strong>' . $row->rev_title . '</strong>';
					echo ' - <a href="index.php?option=com_mtree&task=editreview&rid=' . $row->rev_id . '">' . JText::_( 'COM_MTREE_EDIT_REVIEW' ) . '</a>';
					 echo '<br />' . JText::_( 'COM_MTREE_REVIEWED_BY' ) . ' <a href="index.php?option=com_mtree&task=spy&task2=viewuser&id='.$row->review_user_id.'">' . $row->review_username . '</a>, ' . $row->rev_date ?>
					<p />
					<?php echo nl2br($row->rev_text); ?>
					</blockquote>
					<?php echo '</pre>'; echo ( (empty($row->username))? $row->guest_name : '<a href="index.php?option=com_mtree&task=spy&task2=viewuser&id='.$row->user_id.'">'.$row->username."</a> " ) ." ". $row->created ?>
					<p />
					<?php echo nl2br($row->comment) ?>
					<p />
					<label for="res_<?php echo $row->report_id ?>"><input type="radio" name="report[<?php echo $row->report_id ?>]" value="1" id="res_<?php echo $row->report_id ?>" /><?php echo JText::_( 'COM_MTREE_RESOLVED' ) ?></label>

					<label for="ign_<?php echo $row->report_id ?>"><input type="radio" name="report[<?php echo $row->report_id ?>]" value="0" id="ign_<?php echo $row->report_id ?>" checked="checked" /><?php echo JText::_( 'COM_MTREE_IGNORE' ) ?></label>
				</td>
				<?php if( $mtconf->get('use_internal_notes') ) { ?>
				<td style="height:100px;" valign="top" width="35%">
				<?php echo JText::_( 'COM_MTREE_INTERNAL_NOTES' ) ?>:<br />
				<textarea class="text_area" style="width:100%;height:200px" name="admin_note[<?php echo $row->report_id ?>]"><?php echo htmlspecialchars($row->admin_note) ?></textarea>
				</td>
				<?php } ?>
			</tr>
			<?php		$k = 1 - $k; } } ?>
			<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="listpending_reviewsreports" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

	function listpending_reviewsreply( $reviewsreply, $pathWay, $option ) {
		global $mtconf;
		?>
		<form action="index.php" method="post" name="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		<?php
		if ( count($reviewsreply) <= 0 ) {
			?>
			<tr><th align="left">&nbsp;</th></tr>
			<tr class="row0"><td><?php echo JText::_( 'COM_MTREE_NO_REPLY_FOUND' ) ?></td></tr>
			<?php
		} else {

		$k = 0;
		for ($i=0, $n=count( $reviewsreply ); $i < $n; $i++) {
			$row = &$reviewsreply[$i]; ?>
			<thead><tr><th style="text-align:left" align="left"<?php echo ( ($mtconf->get('use_internal_notes')) ? ' colspan="2"': '' ) ?>><a href="<?php echo $mtconf->getjconf('live_site') . "/index.php?option=com_mtree&task=viewlink&link_id=$row->link_id"; ?>" target="_blank"><?php echo $row->link_name ?></a></th></tr></thead>
			<tr align="left">
				<td<?php echo ( ($mtconf->get('use_internal_notes')) ? ' width="65%"': '' ) ?>>
					<blockquote style="margin:3px 0 10px 2px;background-color:#F3F3F3;padding:6px;border: 1px solid #e1e1e1;border-left:6px solid #E1E1E1;">
					<?php echo '<strong>' . $row->rev_title . '</strong>';
					echo ' - <a href="index.php?option=com_mtree&task=editreview&rid=' . $row->rev_id . '">' . JText::_( 'COM_MTREE_EDIT_REVIEW' ) . '</a>';
					echo '<br />' . JText::_( 'COM_MTREE_REVIEWED_BY' ) . ' <a href="index.php?option=com_mtree&task=spy&task2=viewuser&id='.$row->user_id.'">' . $row->username . '</a>, ' . $row->rev_date ?>
					<p />
					<?php echo nl2br($row->rev_text); ?>
					</blockquote>
					<?php 
						if( !empty($row->owner_username) ) {
							echo '<a href="index.php?option=com_mtree&task=spy&task2=viewuser&id='.$row->owner_user_id.'">'.$row->owner_username."</a>  ";
						}
						echo $row->ownersreply_date;
					?>
					<p />
					<textarea class="text_area" style="width:100%;height:150px" name="or_text[<?php echo $row->rev_id ?>]"><?php echo htmlspecialchars($row->ownersreply_text) ?></textarea>
					<p />

					<label for="app_<?php echo $row->rev_id ?>"><input type="radio" name="or[<?php echo $row->rev_id ?>]" value="1" id="app_<?php echo $row->rev_id ?>" /><?php echo JText::_( 'COM_MTREE_APPROVE' ) ?></label>
					<label for="ign_<?php echo $row->rev_id ?>"><input type="radio" name="or[<?php echo $row->rev_id ?>]" value="0" id="ign_<?php echo $row->rev_id ?>" checked="checked" /><?php echo JText::_( 'COM_MTREE_IGNORE' ) ?></label>
					<label for="rej_<?php echo $row->rev_id ?>"><input type="radio" name="or[<?php echo $row->rev_id ?>]" value="-1" id="rej_<?php echo $row->rev_id ?>" /><?php echo JText::_( 'COM_MTREE_REJECT' ) ?></label>
				</td>
				<?php if( $mtconf->get('use_internal_notes') ) { ?>
				<td style="height:100px;" valign="top" width="35%">
				<?php echo JText::_( 'COM_MTREE_INTERNAL_NOTES' ) ?>:<br />
				<textarea class="text_area" style="width:100%;height:200px" name="admin_note[<?php echo $row->rev_id ?>]"><?php echo htmlspecialchars($row->ownersreply_admin_note) ?></textarea>
				</td>
				<?php } ?>
			</tr>
			<?php		$k = 1 - $k; } } ?>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="save_reviewsreply" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

	function listpending_claims( $claims, $pathWay, $option ) {
		global $mtconf;
		?>
		<form action="index.php" method="post" name="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		<?php
		if ( count($claims) <= 0 ) {
			?>
			<tr><th align="left">&nbsp;</th></tr>
			<tr class="row0"><td><?php echo JText::_( 'COM_MTREE_NO_CLAIM_FOUND' ) ?></td></tr>
			<?php
		} else {

		$k = 0;
		for ($i=0, $n=count( $claims ); $i < $n; $i++) {
			$row = &$claims[$i]; ?>
			<thead><tr><th style="text-align:left" align="left"<?php echo ( ($mtconf->get('use_internal_notes')) ? ' colspan="2"': '' ) ?>><a href="index.php?option=com_mtree&task=editlink&link_id=<?php echo $row->link_id; ?>"><?php echo $row->link_name ?></a> by <a href="mailto:<?php echo $row->email ?>"><?php echo $row->name ?></a> (<?php echo $row->username ?>), <?php echo $row->created ?> - <a href="<?php echo $mtconf->getjconf('live_site') . "/index.php?option=com_mtree&task=viewlink&link_id=$row->link_id"; ?>" target="_blank"><?php echo JText::_( 'COM_MTREE_VIEW_LISTING' ) ?></a></th></tr></thead>
			<tr align="left">
				<td <?php echo ( ($mtconf->get('use_internal_notes')) ? 'width="65%" ': '' ) ?>valign="top">
					<?php echo nl2br(htmlspecialchars($row->comment)) ?>
					<p />
					<label for="app_<?php echo $row->claim_id ?>"><input type="radio" name="claim[<?php echo $row->claim_id ?>]" value="<?php echo $row->user_id ?>" id="app_<?php echo $row->claim_id ?>" /><?php echo JText::_( 'COM_MTREE_APPROVE' ) ?></label>
					<label for="ign_<?php echo $row->claim_id ?>"><input type="radio" name="claim[<?php echo $row->claim_id ?>]" value="0" id="ign_<?php echo $row->claim_id ?>" checked="checked" /><?php echo JText::_( 'COM_MTREE_IGNORE' ) ?></label>
					<label for="rej_<?php echo $row->claim_id ?>"><input type="radio" name="claim[<?php echo $row->claim_id ?>]" value="-1" id="rej_<?php echo $row->claim_id ?>" /><?php echo JText::_( 'COM_MTREE_REJECT' ) ?></label>
				</td>
				<?php if ( $mtconf->get('use_internal_notes') ) { ?>
				<td style="height:100px;" valign="top" width="35%">
				<?php echo JText::_( 'COM_MTREE_INTERNAL_NOTES' ) ?>:<br />
				<textarea style="width:100%;height:100%" name="admin_note[<?php echo $row->claim_id ?>]"><?php echo htmlspecialchars($row->admin_note) ?></textarea>
				</td>
				<?php } ?>
			</tr>
			<?php		$k = 1 - $k; } } ?>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="save_claims" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

	/***
	* Reviews
	*/
	function list_reviews( &$reviews, &$link, &$pathWay, &$pageNav, $option ) {
		global $mtconf;
	?>
		<form action="index.php" method="post" name="adminForm">
		
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<th width="100%" align="left" style="background: url(..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folderopen.gif) no-repeat center left"><div style="margin-left: 18px"><?php echo $pathWay->printPathWayFromLink( $link->link_id, 'index.php?option=com_mtree&task=listcats' ); ?></div></th>
			</tr>
			<tr>
				<th colspan="5" style="text-align:left"><?php echo $link->link_name; ?></th>
			</tr>
	  </table>

		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<thead>
			<tr>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $reviews ); ?>);" />
				</th>
				<th width="60%" style="text-align:left" align="left" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_REVIEW_TITLE' ) ?></th>
				<th width="15%" style="text-align:left" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_USER' ) ?></th>
				<th width="10%"><?php echo JText::_( 'COM_MTREE_HELPFULS' ) ?></th>
				<th width="15%"><?php echo JText::_( 'COM_MTREE_CREATED' ) ?></th>
			</tr>
			</thead>
<?php
		$k = 0;
		for ($i=0, $n=count( $reviews ); $i < $n; $i++) {
			$row = &$reviews[$i]; ?>
			<tr class="<?php echo "row$k"; ?>">
				<td width="20">
					<input type="checkbox" id="cb<?php echo $i;?>" name="rid[]" value="<?php echo $row->rev_id; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td align="left"><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editreview')"><?php echo $row->rev_title; ?></a></td>
				<td align="left"><?php echo (($row->user_id) ? $row->username : $row->guest_name); ?></td>
				<td align="center"><?php if( $row->vote_total > 0 ) { 
					echo $row->vote_helpful.' '.JText::_( 'COM_MTREE_OF' ).' '.$row->vote_total; 
				} else {
					echo '-';
				}
				?></td>
				<td align="center"><?php echo $row->rev_date; ?></td>
			</tr><?php

				$k = 1 - $k;
			}
			?>

			<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="reviews_list" />
		<input type="hidden" name="link_id" value="<?php echo $link->link_id; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

	function editreview( &$row, &$pathWay, $returntask, $lists, $option ) {
		global $mtconf;
		
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'rev_text' );
		JHTML::_( 'behavior.calendar' );
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancelreview') {
				submitform( pressbutton );
				return;
			}
			if (form.rev_text.value == ""){
				alert( "<?php echo JText::_( 'COM_MTREE_PLEASE_ENTER_REVIEW_TEXT' ) ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<th align="left" style="background: url(..<?php echo $mtconf->get('relative_path_to_images'); ?>dtree/folderopen.gif) no-repeat center left"><div style="margin-left: 18px"><?php echo $pathWay->printPathWayFromLink( $row->link_id, 'index.php?option=com_mtree&task=listcats' ); ?></div></th>
			</tr>
	  </table>

		<fieldset>
		
		<table cellpadding="4" cellspacing="1" border="0" width="100%" class="admintable">
		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<tr>
				<td width="15%" align="right" class="key">User:</td>
				<td width="85%" align="left">
					<input class="text_area" type="text" name="owner" size="20" maxlength="250" value="<?php echo (($row->not_registered) ? $row->guest_name : $row->owner );?>" /> <input type="checkbox" name="not_registered" id="not_registered" value="1" <?php echo (($row->not_registered) ? 'checked ' : '' ); ?>/> <label for="not_registered"><?php echo JText::_( 'COM_MTREE_THIS_IS_NOT_A_REGISTERED_USER' ) ?></label>
				</td>
			</tr>
			<tr>
				<td width="15%" align="right" class="key"><?php echo JText::_( 'COM_MTREE_REVIEW_TITLE' ) ?>:</td>
				<td width="85%" align="left">
					<input class="text_area" type="text" name="rev_title" size="60" maxlength="250" value="<?php echo $row->rev_title;?>" />
				</td>
			</tr>
			<tr>
				<td valign="top" align="right" class="key"><?php echo JText::_( 'COM_MTREE_REVIEW' ) ?>:</td>
				<td align="left"><textarea name="rev_text" cols="70" rows="15" class="text_area"><?php echo $row->rev_text; ?></textarea></td>
			</tr>
			<tr>
				<td valign="top" align="right" class="key"><?php echo JText::_( 'COM_MTREE_APPROVED' ) ?>:</td>
				<td align="left"><?php echo $lists['rev_approved'] ?></td>
			</tr>
			<tr>
				<td valign="top" align="right" class="key"><?php echo JText::_( 'COM_MTREE_HELPFULS' ) ?>:</td>
				<td align="left"><input class="text_area" type="text" name="vote_helpful" size="3" maxlength="4" value="<?php echo $row->vote_helpful;?>" /> <?php echo JText::_( 'COM_MTREE_OF' ) ?> <input class="text_area" type="text" name="vote_total" size="3" maxlength="4" value="<?php echo $row->vote_total;?>" /></td>
			</tr>
			<tr>
			  <td valign="top" align="right" class="key"><?php echo JText::_( 'COM_MTREE_OVERRIDE_CREATED_DATE' ) ?> </td>
			  <td align="left"><?php echo JHTML::_('calendar', $row->rev_date, 'rev_date', 'rev_date', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); ?></td>
			</tr>
			<tr>
				<td valign="top" align="right" class="key"><?php echo JText::_( 'COM_MTREE_OWNERS_REPLY' ) ?>:</td>
				<td align="left"><textarea name="ownersreply_text" cols="70" rows="8" class="text_area"><?php echo $row->ownersreply_text; ?></textarea></td>
			</tr>
			<tr>
				<td valign="top" align="right" class="key"><?php echo JText::_( 'COM_MTREE_APPROVED' ) ?>:</td>
				<td align="left"><?php echo $lists['ownersreply_approved'] ?></td>
			</tr>
		</table>
		
		</fieldset>
		<input type="hidden" name="rev_id" value="<?php echo $row->rev_id; ?>" />
		<input type="hidden" name="link_id" value="<?php echo $row->link_id; ?>" />
		<input type="hidden" name="returntask" value="<?php echo $returntask ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
<?php
	}

	/***
	* Search
	*/
	function searchresults_links( &$links, &$pageNav, &$pathWay, $search_where, $search_text, $option ) {
		$database	=& JFactory::getDBO();
		$nullDate	= $database->getNullDate();
	?>
		<script language="javascript" type="text/javascript">
			function listItemTask( id, task ) {
				var f = document.adminForm;
				lb = eval( 'f.' + id );
				if (lb) {
					lb.checked = true;
					submitbutton(task);
				}
				return false;
			}

			function link_isChecked(isitchecked){
				if (isitchecked == true){
					document.adminForm.link_boxchecked.value++;
				}
				else {
					document.adminForm.link_boxchecked.value--;
				}
			}

			function link_checkAll( n ) {
				var f = document.adminForm;
				var c = f.link_toggle.checked;
				var n2 = 0;
				for (i=0; i < n; i++) {
					lb = eval( 'f.lb' + i );
					if (lb) {
						lb.checked = c;
						n2++;
					}
				}
				if (c) {
					document.adminForm.link_boxchecked.value = n2;
				} else {
					document.adminForm.link_boxchecked.value = 0;
				}
			}

		</script>
		<form action="index.php" method="post" name="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<thead>
				<th width="20">
					<input type="checkbox" name="link_toggle" value="" onclick="link_checkAll(<?php echo count( $links ); ?>);" />
				</th>
				<th class="title" width="20%" nowrap="nowrap" style="text-align:left"><?php echo JText::_( 'COM_MTREE_LISTING' ) ?></th>
				<th width="65%" align="left" nowrap="nowrap" style="text-align:left"><?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?></th>
				<th width="100"><?php echo JText::_( 'COM_MTREE_REVIEWS' ) ?></th>
				<th><?php echo JText::_( 'COM_MTREE_FEATURED' ) ?></th>
				<th><?php echo JText::_( 'COM_MTREE_PUBLISHED' ) ?></th>
			</thead>
<?php
		$k = 0;
		for ($i=0, $n=count( $links ); $i < $n; $i++) {
			$row = &$links[$i]; ?>
			<tr class="<?php echo "row$k"; ?>" align="left">
				<td width="20">
					<input type="checkbox" id="lb<?php echo $i;?>" name="lid[]" value="<?php echo $row->link_id; ?>" onclick="link_isChecked(this.checked);" />
				</td>
				<td><a href="index.php?option=com_mtree&amp;task=editlink&amp;link_id=<?php echo $row->link_id; ?>"><?php echo htmlspecialchars($row->link_name); ?></a></td>
				<td><?php echo $pathWay->printPathWayFromLink( $row->link_id, '' ); ?></td>
				<td align="center"><a href="index.php?option=com_mtree&task=reviews_list&link_id=<?php echo $row->link_id; ?>"><?php echo $row->reviews; ?></a></td>
			  	<td width="10%" align="center">
					<?php echo JHtml::_('mtree.featured', $row->link_featured, $i,'link_', true, 'lb'); ?>
				</td>
			  	<td width="10%" align="center">
					<?php echo JHtml::_('jgrid.published', $row->link_published, $i, 'link_', true, 'lb', $row->publish_up, $row->publish_down); ?>
				</td>
			</tr><?php

				$k = 1 - $k;
			}
			?>
			<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="search" />
		<input type="hidden" name="search_where" value="<?php echo $search_where ?>" />
		<input type="hidden" name="search_text" value="<?php echo $search_text ?>" />
		<input type="hidden" name="link_boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	<?php
	}

	function searchresults_categories( &$rows, &$pageNav, &$pathWay, $search_where, $search_text, $option ) {
?>
		<form action="index.php" method="post" name="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<thead>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
				</th>
				<th style="text-align:left" class="title" width="25%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?></th>
				<th style="text-align:left" align="left" width="65%"><?php echo JText::_( 'COM_MTREE_PARENT' ) ?></th>
				<th><?php echo JText::_( 'COM_MTREE_CATEGORIES' ) ?></th>
				<th><?php echo JText::_( 'COM_MTREE_LISTINGS' ) ?></th>
				<th><?php echo JText::_( 'COM_MTREE_FEATURED' ) ?></th>
				<th><?php echo JText::_( 'COM_MTREE_PUBLISHED' ) ?></th>
			</thead>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i]; ?>
			<tr class="<?php echo "row$k"; ?>">
				<td width="20">
					<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->cat_id; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td width="50%" align="left">
					<a href="#go" onclick="return listItemTask('cb<?php echo $i;?>','listcats')"><?php 
						echo $row->cat_name; ?></a>
				</td>
				<td align="left"><?php echo $pathWay->printPathWayFromCat( $row->cat_id, 0 ); ?></td>
				<td align="center"><?php echo $row->cat_cats; ?></td>
				<td align="center"><?php echo $row->cat_links; ?></td>
				<td width="10%" align="center">
					<?php echo JHtml::_('mtree.featured', $row->cat_featured, $i,'cat_'); ?>
				</td>
			  	<td width="10%" align="center">
					<?php  echo JHtml::_('jgrid.published', $row->cat_published, $i, 'cat_', true); ?>
				</td>
			</tr><?php

				$k = 1 - $k;
			}
			?>
			<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="search" />
		<input type="hidden" name="search_where" value="<?php echo $search_where ?>" />
		<input type="hidden" name="search_text" value="<?php echo $search_text ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	<?php
	}

	/***
	* Tree Template
	*/
	function list_templates( $rows, $option ) {
	?>
	<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function(task)
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	</script>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<thead>
			<tr>
				<th class="title" width="30%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_NAME' ) ?></th>
				<th class="title" width="60%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_DESCRIPTION' ) ?></th>
				<th class="title" width="10%" nowrap="nowrap" align="center"><?php echo JText::_( 'COM_MTREE_DEFAULT' ) ?></th>
			</tr>
			</thead>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i]; ?>
			<tr class="<?php echo "row$k"; ?>" align="left">
				<td><input type="radio" id="cb<?php echo $i ?>" name="template" value="<?php echo $row->directory; ?>" onClick="isChecked(this.checked);" /> <a href="" onClick="return listItemTask('cb<?php echo $i ?>','template_pages')"><?php echo $row->name; ?></a></td>
				<td><?php echo $row->description; ?></td>
				<td align="center"><?php echo ($row->default) ? '<img src="templates/bluestork/images/menu/icon-16-default.png">' : '&nbsp;' ; ?></td>
			</tr>
			<?php		$k = 1 - $k; } ?>
			<tfoot>
			<tr><th colspan="3">&nbsp;</th></tr>
			</tfoot>
		</table>

		<p />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}
	
	function template_pages( $template, $template_files, $template_name, $form, $option ) {
	?>
	<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function(task)
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	</script>
  	<style type="text/css">
		fieldset.adminform.long label {min-width:240px;max-width:240px}
	</style>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminheading">
		<tr>
			<th class="templates"><?php echo JText::_( 'COM_MTREE_TREE_TEMPLATES' ) ?>: <? echo $template_name ?></th>
		</tr>
	</table>

	<?php if(!is_null($form)) { JHTML::_('behavior.tooltip');	?>
	<!-- <div class="width-55 fltrt"> -->
	<div class="width-55 fltlft">
	<?php
	echo '<fieldset class="adminform long" style="min-width:240px">';
	?>
	<legend><?php echo JText::_( 'COM_MTREE_PARAMETERS' ) ?></legend>
	<?php
	foreach ($form->getFieldsets() as $fieldset): 
		echo '<ul class="adminformlist">';
		foreach($form->getFieldset($fieldset->name) AS $field):
		?>
	            <?php if ($field->hidden): ?>
	                 <?php echo $field->input;?>
	            <?php else:?>
			<li>
	                	<label><?php echo $field->label; ?></label>
				<?php echo $field->input;?>
			</li>
	            <?php endif;?>
	       <?php endforeach;
		echo '</ul>';

	endforeach;
	echo '</fieldset>';
	?>	
	</div>
	<?php } ?>
	
	<div class="width-45 fltrt">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_MTREE_SELECT_TEMPLATE_FILE_TO_EDIT' ) ?></legend>
			<ul>
				<li><strong><?php echo JText::_( 'COM_MTREE_POPULAR_TEMPLATE_FILES' ); ?></strong></li>
				<?php
				$popular_template_files = array(
					'sub_listingDetails.tpl.php',
					'page_index.tpl.php',
					'sub_listingSummary.tpl.php',
					'page_advSearch.tpl.php',
					'page_subCatIndex.tpl.php',
					'sub_map.tpl.php',
					'page_addListing.tpl.php'
					);
				
				sort($popular_template_files);
				
				foreach( $popular_template_files AS $popular_template_file )
				{
					if( in_array($popular_template_file,$template_files) )
					{
						echo '<li>';
						echo '<a href="'
							. JRoute::_(
								"index.php?option=$option&task=edit_templatepage&template=$template&page=".str_replace('.tpl.php','',$popular_template_file)
							)
							. '">';
						echo $popular_template_file;
						echo '</a>';
						echo '</li>';
					}
				}
				
				echo '<li style="border-bottom:1px solid #ccc"></li>';
				echo '<li><strong>' . JText::_( 'COM_MTREE_OTHER_TEMPLATE_FILES' ) . '</strong></li>';

				foreach( $template_files AS $template_file )
				{
					if( in_array($template_file,$popular_template_files) )
					{
						continue;
					}
					
					echo '<li>';
					echo '<a href="'
						. JRoute::_(
							"index.php?option=$option&task=edit_templatepage&template=$template&page=".str_replace('.tpl.php','',$template_file)
						)
						. '">';
					echo $template_file;
					echo '</a>';
					echo '</li>';
				}
				?>
			</ul>
		</fieldset>
	</div>
	
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="template" value="<?php echo $template ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
	</form>
	<?php
	}
	
	function edit_templatepage( $page, $template, $content, $option ) {
		global $mtconf;
		?>
		<script language="javascript" type="text/javascript">
			Joomla.submitbutton = function(task)
			{
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
		</script>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<fieldset>
			<legend>
				/components/com_mtree/templates/<?php echo $template; ?>/<?php echo $page; ?>.tpl.php
	      		<?php
	      		$template_path = $mtconf->getjconf('absolute_path') . '/components/com_mtree/templates/' . $template . '/'.$page.'.tpl.php';
	      		echo is_writable( $template_path ) ? '<b><font color="orange"> - '.JText::_( 'COM_MTREE_WRITEABLE' ).'</font></b>' : '<b><font color="red"> - '.JText::_( 'COM_MTREE_UNWRITEABLE' ).'</font></b>';
	      		?>
			</legend>
		<table class="admintable" width="100%">
		<tr>
			<td>
			<textarea cols="90" rows="50" name="pagecontent" class="inputbox" style="width:100%"><?php echo $content; ?></textarea>
			</td>
		</tr>
		</table>
		</fieldset>
		
		<input type="hidden" name="template" value="<?php echo $template; ?>" />
		<input type="hidden" name="page" value="<?php echo $page; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}
	
	function new_template( $option ) {
		global $mtconf;
	?>
	<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function(task)
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	</script>
	<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminform">
	<tr><th><?php echo JText::_( 'COM_MTREE_UPLOAD_PACKAGE_FILE' ) ?></th></tr>
	<tr>
		<td align="left">
		<?php echo JText::_( 'COM_MTREE_PACKAGE_FILE' ) ?>:
		<input class="text_area" name="template" type="file" size="70"/>
		<input class="button" type="submit" value="<?php echo JText::_( 'COM_MTREE_UPLOAD_FILE_AND_INSTALL' ) ?>" />
		</td>
	</tr>
	</table>
	<input type="hidden" name="option" value="<?php echo $option ?>" />
	<input type="hidden" name="task" value="install_template" />
	<?php echo JHTML::_( 'form.token' ); ?>
	</form>
	
	<p />
	
	<table class="content">
	<?php
		echo '<td class="item">';
		echo '<strong>/components/com_mtree/templates</strong>';
		echo '</td><td align="left">';
		if( is_writable( $mtconf->getjconf('absolute_path') . '/components/com_mtree/templates' ) ) {
			echo '<b><font color="green">Writeable</font></b>';
		} else {
			echo '<b><font color="red">Unwriteable</font></b>';
		} 
	?></td></tr>
		
	</table>
	<?php
	}
	
	function copy_template( $template, $template_name, $option )
	{
		JHTML::_('behavior.tooltip');
	?>
	<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function(task)
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	</script>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_MTREE_NEW_TEMPLATE' ); ?></legend>
		<table cellspacing="1" class="admintable">
			<tbody><tr>
				<td valign="top" class="key">
					<?php echo JText::_( 'COM_MTREE_ORIGINAL_TEMPLATE' ); ?>
				</td>
				<td>
					<strong>
						<em><?php echo $template_name; ?> (<?php echo $template; ?>)</em>
					</strong>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_name">
						<?php echo JText::_( 'COM_MTREE_TEMPLATE_NAME' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="" size="35" id="new_template_name" name="new_template_name" class="text_area"/>
					<?php echo JHTML::_('tooltip',  JText::_( 'COM_MTREE_THE_NAME_OF_THE_NEW_TEMPLATE' ) ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_folder">
						<?php echo JText::_( 'COM_MTREE_FOLDER_NAME' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="" size="35" id="new_template_folder" name="new_template_folder" class="text_area"/>
					<?php echo JHTML::_('tooltip',  JText::_( "COM_MTREE_THE_NAME_OF_THE_NEW_TEMPLATE_S_FOLDER_ENTER_ONLY_ALPHA_NUMERIC_VALUES_AND_UNDERSCORE" ) ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_creation_date">
						<?php echo JText::_( 'COM_MTREE_CREATION_DATE' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="<?php echo strftime('%e %B %Y'); ?>" size="35" id="new_template_creation_date" name="new_template_creation_date" class="text_area"/>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_author">
						<?php echo JText::_( 'COM_MTREE_AUTHOR' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="<?php $my =& JFactory::getUser(); echo $my->name; ?>" size="35" id="new_template_author" name="new_template_author" class="text_area"/>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_author_email">
						<?php echo JText::_( 'COM_MTREE_AUTHOR_EMAIL' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="<?php $my =& JFactory::getUser(); echo $my->email; ?>" size="35" id="new_template_author_email" name="new_template_author_email" class="text_area"/>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_author_url">
						<?php echo JText::_( 'COM_MTREE_AUTHOR_URL' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="" size="35" id="new_template_author_url" name="new_template_author_url" class="text_area"/>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_copyright">
						<?php echo JText::_( 'COM_MTREE_COPYRIGHT' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="" size="35" id="new_template_copyright" name="new_template_copyright" class="text_area"/>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_version">
						<?php echo JText::_( 'COM_MTREE_VERSION' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="" size="35" id="new_template_version" name="new_template_version" class="text_area"/>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="new_template_description">
						<?php echo JText::_( 'COM_MTREE_TEMPLATE_DESCRIPTION' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" value="" size="35" id="new_template_description" name="new_template_description" class="text_area"/>
				</td>
			</tr>			
		</tbody></table>
	</fieldset>
	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="template" value="<?php echo $template; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
	</form>
	<?php
	}
	
	/***
	* Advanced Back-end Search
	*/
	function advsearch( $fields, $lists, $option ) {
		global $mtconf;
		?>
		<style type="text/css">
		fieldset input, fieldset textarea, fieldset select, fieldset img, fieldset button {
			float: none;
		}
		fieldset label, fieldset span.faux-label {
		float: none;
		clear: none;
		display: inline;
		}
		</style>

		<form action="index.php" method="post" name="adminForm">
		<fieldset>
		<legend><?php echo JText::_( 'COM_MTREE_SEARCH_LISTINGS' ) ?></legend>
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<td valign="top">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<td colspan="2"><?php echo JText::sprintf( 'COM_MTREE_RETURN_RESULTS_IF_X_OF_THE_FOLLOWING_CONDITIONS_ARE_MET',$lists['searchcondition'] ); ?></td>
						</tr>
						<tr>
							<td colspan="2"><input type="submit" value="<?php echo JText::_( 'COM_MTREE_SEARCH' ) ?>" class="button" /> &nbsp; <input type="reset" value="<?php echo JText::_( 'COM_MTREE_RESET' ) ?>" class="button" /></td>
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<?php
						while( $fields->hasNext() ) {
							$field = $fields->getField();
							if($field->hasSearchField()) {
								echo '<tr>';
								echo '<td valign="top" align="left" class="key" width="180px">' . $field->caption . ':' . '</td>';
								echo '<td align="left">';
								echo $field->getSearchHTML();
								echo '</td>';
								echo '</tr>';
							}
							$fields->next();
						}
						?>
						<tr>
							<td align="left" class="key"><?php echo JText::_( 'COM_MTREE_OWNER' )?>:</td>
							<td align="left"><input name="owner" type="text" class="text_area" size="20" /></td>
						</tr>
						<tr>
							<td align="left" class="key"><?php echo JText::_( 'COM_MTREE_PUBLISHING' )?>:</td>
							<td align="left"><?php echo $lists['publishing'] ?></td>
						</tr>
						<tr>
							<td align="left" class="key"><?php echo JText::_( 'COM_MTREE_TEMPLATE' )?>:</td>
							<td align="left"><?php echo $lists['templates'] ?></td>
						</tr>
						<tr>
							<td align="left" class="key"><?php echo JText::_( 'COM_MTREE_NOTES' )?>:</td>
							<td align="left" colspan="3"><input name="internal_notes" type="text" class="text_area" size="20" /></td>
						</tr>
					</table>	
				</td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td colspan="2"><input type="submit" value="<?php echo JText::_( 'COM_MTREE_SEARCH' ) ?>" class="button" /> &nbsp; <input type="reset" value="<?php echo JText::_( 'COM_MTREE_RESET' ) ?>" class="button" />
			</tr>
		</table>
		</fieldset>
		
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="advsearch2" />
		<input type="hidden" name="search_where" value="1" />
		</form>
		<?php		
	}

	function advsearchresults_links( &$links, &$fields, &$pageNav, &$pathWay, $search_where, $option ) {
		$database	=& JFactory::getDBO();
		$nullDate	= $database->getNullDate();
	?>
		<script language="javascript" type="text/javascript">
			function listItemTask( id, task ) {
				var f = document.adminForm;
				lb = eval( 'f.' + id );
				if (lb) {
					lb.checked = true;
					submitbutton(task);
				}
				return false;
			}

			function link_isChecked(isitchecked){
				if (isitchecked == true){
					document.adminForm.link_boxchecked.value++;
				}
				else {
					document.adminForm.link_boxchecked.value--;
				}
			}

			function link_checkAll( n ) {
				var f = document.adminForm;
				var c = f.link_toggle.checked;
				var n2 = 0;
				for (i=0; i < n; i++) {
					lb = eval( 'f.lb' + i );
					if (lb) {
						lb.checked = c;
						n2++;
					}
				}
				if (c) {
					document.adminForm.link_boxchecked.value = n2;
				} else {
					document.adminForm.link_boxchecked.value = 0;
				}
			}

		</script>
		<form action="index.php" method="post" name="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<thead>
				<th width="20"><input type="checkbox" name="link_toggle" value="" onclick="link_checkAll(<?php echo count( $links ); ?>);" /></th>
				<th style="text-align:left" class="title" width="20%" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_LISTING' ) ?></th>
				<th style="text-align:left" width="65%" align="left" nowrap="nowrap"><?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?></th>
				<th width="100"><?php echo JText::_( 'COM_MTREE_REVIEWS' ) ?></th>
				<th><?php echo JText::_( 'COM_MTREE_FEATURED' ) ?></th>
				<th><?php echo JText::_( 'COM_MTREE_PUBLISHED' ) ?></th>
			</thead>
<?php
		$k = 0;
		for ($i=0, $n=count( $links ); $i < $n; $i++) {
			$row = &$links[$i]; ?>
			<tr class="<?php echo "row$k"; ?>" align="left">
				<td width="20"><input type="checkbox" id="lb<?php echo $i;?>" name="lid[]" value="<?php echo $row->link_id; ?>" onclick="link_isChecked(this.checked);" /></td>
				<td><a href="index.php?option=com_mtree&amp;task=editlink&amp;link_id=<?php echo $row->link_id; ?>"><?php echo htmlspecialchars($row->link_name); ?></a></td>
				<td><?php echo '<a href="index.php?option=com_mtree&task=listcats&cat_id='.$row->cat_id.'">'.$pathWay->getCatName( $row->cat_id ).'</a>'; ?></td>
				<td align="center"><a href="index.php?option=com_mtree&task=reviews_list&link_id=<?php echo $row->link_id; ?>"><?php echo $row->reviews; ?></a></td>
			  	<td width="10%" align="center">
					<?php echo JHtml::_('mtree.featured', $row->link_featured, $i,'link_', true, 'lb'); ?>
				</td>
			  	<td width="10%" align="center">
					<?php echo JHtml::_('jgrid.published', $row->link_published, $i, 'link_', true, 'lb', $row->publish_up, $row->publish_down); ?>
				</td>
			</tr><?php

				$k = 1 - $k;
			}
			?>
			<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="advsearch2" />
		<input type="hidden" name="link_boxchecked" value="0" />
		<input type="hidden" name="search_where" value="<?php echo $search_where ?>" />
		<input type="hidden" name="searchcondition" value="<?php echo JRequest::getInt( 'searchcondition', 1, 'post'); ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		<?php
		$post = JRequest::get('post');
		$core_fields = array('link_name', 'link_desc', 'website', 'address', 'city', 'state', 'country', 'postcode', 'telephone', 'fax', 'email', 'publishing', 'link_template', 'price', 'price_2', 'link_rating', 'link_featured', 'rating_2', 'link_votes', 'votes_2', 'link_hits', 'hits_2', 'internal_notes', 'metakey', 'metadesc', 'link_created', 'link_created_2', 'link_created_3', 'link_created_4', 'link_created_5', 'link_created_6', 'link_created_7', 'link_created_8', 'link_created_9', 'link_created_10', 'link_modified', 'link_modified_2', 'link_modified_3', 'link_modified_4', 'link_modified_5', 'link_modified_6', 'link_modified_7', 'link_modified_8', 'link_modified_9', 'link_modified_10');
		foreach($core_fields AS $core_field) {
			echo '<input type="hidden" name="' . $core_field . '" value="';
			if(isset($post[$core_field])) {
				echo $post[$core_field];
			}
			echo '" />';
		}

		$fields->resetPointer();
		while( $fields->hasNext() ) {
			$field = $fields->getField();
			if( array_key_exists('cf'.$field->id, $post) && !empty($post['cf'.$field->id]) ) {
				
				if( is_array($post['cf'.$field->id]) )
				{
					$array = $post['cf'.$field->id];
					foreach( $array AS $items[0]['value'] )
					{
						?>
						<input type="hidden" name="cf<?php echo $field->id ?>[]" value="<?php echo $items[0]['value']; ?>" /><?php
					}
				} else {
				?>
					<input type="hidden" name="cf<?php echo $field->id ?>" value="<?php echo $post['cf'.$field->id] ?>" /><?php
					if( $field->numOfSearchFields > 1 )
					{
						for($i=2; $i<=$field->numOfSearchFields; $i++)
						{
							?>
								<input type="hidden" name="cf<?php echo $field->id ?>_<?php echo $i; ?>" value="<?php echo $post['cf'.$field->id.'_'.$i] ?>" /><?php
						}
					}
				}
			}
			$fields->next();
		}

		?>
		</form>
	<?php
	}

	/***
	* CSV Import/Export
	*/
	function csv( $fields, $lists, $option ) {
		JHtml::_('behavior.framework', false);
	?>
  <script type="text/javascript" language="javascript">
		function submitbutton( pressbutton ) {
			var form = document.adminForm;

			// do field validation
			var temp = false;
			if(pressbutton=='csv_export') {
				var elts      = document.adminForm.elements['fields[]'];
				var elts_cnt  = (typeof(elts.length) != 'undefined')
											? elts.length
											: 0;

				for (var i = 0; i < elts_cnt; i++) {
						if (elts[i].checked == true) temp = true;
				} 
			} else {
				temp = true;
			}
			if (temp == true) {
				submitform( pressbutton );
			} else {
				alert('<?php echo JText::_( 'COM_MTREE_PLEASE_SELECT_AT_LEAST_ONE_FIELD' ) ?>');
			}
		}

		function setCheckboxes(the_form, do_check)
		{
				var elts      = document.forms[the_form].elements['fields[]'];
				var elts_cnt  = (typeof(elts.length) != 'undefined')
											? elts.length
											: 0;

				if (elts_cnt) {
						for (var i = 0; i < elts_cnt; i++) {
								elts[i].checked = do_check;
						}
				} else {
						elts.checked        = do_check;
				}

				return true;
		}
		</script>
		
		<style type="text/css">
		table td label {clear:none;}
		</style>
		
		<form action="index.php" method="post" name="adminForm">

		<fieldset>
		<legend><?php echo JText::_( 'COM_MTREE_FIELDS' ); ?></legend>

		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="admintable">
			<tr>
				<td width="33%" valign="top" align="left">
					<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="l.link_id" id="link_id" /><label for="link_id"><?php echo JText::_( 'COM_MTREE_LISTING_ID' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="link_name" id="link_name" /><label for="link_name"><?php echo JText::_( 'COM_MTREE_NAME' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="link_desc" id="link_desc" /><label for="link_desc"><?php echo JText::_( 'COM_MTREE_DESCRIPTION' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="website" id="website" /><label for="website"><?php echo JText::_( 'COM_MTREE_WEBSITE' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="address" id="address" /><label for="address"><?php echo JText::_( 'COM_MTREE_ADDRESS' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="city" id="city" /><label for="city"><?php echo JText::_( 'COM_MTREE_CITY' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="state" id="state" /><label for="state"><?php echo JText::_( 'COM_MTREE_STATE' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="country" id="country" /><label for="country"><?php echo JText::_( 'COM_MTREE_COUNTRY' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="postcode" id="postcode" /><label for="postcode"><?php echo JText::_( 'COM_MTREE_POSTCODE' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="telephone" id="telephone" /><label for="telephone"><?php echo JText::_( 'COM_MTREE_TELEPHONE' ) ?></label>
						</td></tr>
					</table>

				</td>
				<td width="33%" valign="top">
					<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="fax" id="fax" /><label for="fax"><?php echo JText::_( 'COM_MTREE_FAX' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="email" id="email" /><label for="email"><?php echo JText::_( 'COM_MTREE_EMAIL' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="cat_id" id="cat_id" /><label for="cat_id"><?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="user_id" id="user_id" /><label for="user_id"><?php echo JText::_( 'COM_MTREE_OWNER' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="link_created" id="link_created" /><label for="link_created"><?php echo JText::_( 'COM_MTREE_CREATED' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="link_modified" id="link_modified" /><label for="link_modified"><?php echo JText::_( 'COM_MTREE_MODIFIED' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="publish_up" id="publish_up" /><label for="publish_up"><?php echo JText::_( 'COM_MTREE_PUBLISH_UP' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="publish_down" id="publish_down" /><label for="publish_down"><?php echo JText::_( 'COM_MTREE_PUBLISH_DOWN' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="link_hits" id="link_hits" /><label for="link_hits"><?php echo JText::_( 'COM_MTREE_HITS' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="price" id="price" /><label for="price"><?php echo JText::_( 'COM_MTREE_PRICE' ) ?></label>
						</td></tr>
					</table>
				</td>
				<td width="33%" valign="top">
					<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="link_rating" id="link_rating" /><label for="link_rating"><?php echo JText::_( 'COM_MTREE_RATING' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="link_votes" id="link_votes" /><label for="link_votes"><?php echo JText::_( 'COM_MTREE_VOTES' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="link_visited" id="link_visited" /><label for="link_visited"><?php echo JText::_( 'COM_MTREE_VISITED' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="internal_notes" id="internal_notes" /><label for="internal_notes"><?php echo JText::_( 'COM_MTREE_INTERNAL_NOTES' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="metakey" id="metakey" /><label for="metakey"><?php echo JText::_( 'COM_MTREE_META_KEYWORDS' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="metadesc" id="metadesc" /><label for="metadesc"><?php echo JText::_( 'COM_MTREE_META_DESCRIPTION' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="lat" id="lat" /><label for="lat"><?php echo JText::_( 'COM_MTREE_LATITUDE' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="lng" id="lng" /><label for="lng"><?php echo JText::_( 'COM_MTREE_LONGITUDE' ) ?></label>
						</td></tr>
						<tr><td align="left">
							<input type="checkbox" name="fields[]" value="zoom" id="zoom" /><label for="zoom"><?php echo JText::_( 'COM_MTREE_ZOOM' ) ?></label>
						</td></tr>
					</table>
				</td>
			</tr>
			<tr>
			<?php
			$fields->resetPointer();
			$count=0;
			for($i=0;$i<3;$i++) {
				echo '<td align="left" width="33%" valign="top">';
				echo '<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">';
				for($j=$count;$j<(ceil($fields->getTotal()/3)*($i+1)) && $fields->hasNext();$j++) {
					$field = $fields->getField();
					?>
					<tr><td align="left">
						<input type="checkbox" name="fields[]" value="<?php echo $field->getInputFieldName(1) ?>" id="<?php echo $field->getInputFieldName(1) ?>" /> <label for="<?php echo $field->getInputFieldName(1) ?>"><?php echo $field->getCaption(true) ?></label>
					</td></tr>
					<?php
					$count++;
					$fields->next();
					if($count>=(ceil($fields->getTotal()/3)*($i+1))) {
						break;
					}
				}
				echo '</table>';
				if($i==0) {
					echo '<p />';
					echo '<a href="#" onclick="setCheckboxes(\'adminForm\', true); return false;">' . JText::_( 'COM_MTREE_SELECT_ALL' ) . '</a> / <a href="#" onclick="setCheckboxes(\'adminForm\', false); return false;">' . JText::_( 'COM_MTREE_UNSELECT_ALL' ) . '</a>';
				}
				echo '</td>';
			}
			?>
			</tr>
			<tr><td colspan="3"></td></tr>

			<tr class="row0"><td colspan="3" align="left"><b><?php echo JText::_( 'COM_MTREE_PUBLISHING' ) ?></b></td></tr>
			<tr><td align="left" colspan="3">
				<?php echo $lists['publishing'] ?>
				<p />
				<input type="button" class="button" value="<?php echo JText::_( 'COM_MTREE_EXPORT' ) ?>" onClick="javascript:submitbutton('csv_export')" />
			</td></tr>
			
		</table>
		</fieldset>
		
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		</form>
	<?php
	}

	function csv_export( $header, $data, $option ) {
	?>
	<script language="Javascript">
	<!--
	/*
	Select and Copy form element script- By Dynamicdrive.com
	For full source, Terms of service, and 100s DTHML scripts
	Visit http://www.dynamicdrive.com
	*/

	//specify whether contents should be auto copied to clipboard (memory)
	//Applies only to IE 4+
	//0=no, 1=yes
	var copytoclip=1

	function HighlightAll(theField) {
	var tempval=eval("document."+theField)
	tempval.focus()
	tempval.select()
	if (document.all&&copytoclip==1){
	therange=tempval.createTextRange()
	therange.execCommand("Copy")
	window.status="Contents highlighted and copied to clipboard!"
	setTimeout("window.status=''",1800)
	}
	}
	//-->
	</script>

	<center>
	<form action="index.php" method="POST" name="adminForm">
	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
		<tr class="row0">
			<td>
			<p />
			<a href="javascript:HighlightAll('adminForm.csv_excel')"><?php echo JText::_( 'COM_MTREE_SELECT_ALL' ) ?></a>
			<p />
			<textarea name="csv_excel" rows="30" cols="80" style="width:100%"><?php 
				echo $header; 
				echo $data;
			?></textarea>
			<p />
			<a href="javascript:HighlightAll('adminForm.csv_excel')"><?php echo JText::_( 'COM_MTREE_SELECT_ALL' ) ?></a>
			</td>
		</tr>
	</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="doreport" />
	</form>
	</center>
	<?php
	}

	/***
	* Configuration
	*/
	function config( $configs, $configgroups, $lists, $hasGDLibrary, $option ) {
		global $mtconf;

		JHtml::_('behavior.formvalidation');
		jimport('joomla.html.pane');
		$pane	= &JPane::getInstance('tabs');
	?>
<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'application.cancel' || document.formvalidator.isValid(document.id('application-form'))) {
			Joomla.submitform(task, document.getElementById('application-form'));
		}
	}
</script>

<form action="index.php" method="POST" name="adminForm" id="application-form">
	<?php
	echo $pane->startPane("content-pane");
	$configgroup = '';
	$j=0;
	
	foreach( $configgroups AS $configgroup ) {
		
		if( $j > 0 ) {
			echo $pane->endPanel();
		}
		echo $pane->startPanel( JText::_( 'COM_MTREE_'.$configgroup), $configgroup.'-page');
		
	?>
	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
	<?php 
		$i = 0;
		foreach( $configs AS $config ) { 
			if( $config->groupname == $configgroup ) {

				echo '<tr>';
				if( in_array($config->configcode,array('note','resize_method')) ) {
					echo '<td colspan="2" align="left" style="border-bottom: 1px solid #C0C0C0;border-top: 1px solid #C0C0C0; background-color: #FFFFFF">';
				} elseif( !in_array($config->configcode, array('sort_direction','predefined_reply')) ) {
					echo '<td align="left" valign="top"';
					if($i<=1) {
						echo ' width="295"';
					}
					echo '>';
					$langcode = 'CONFIGNAME_'.strtoupper($config->varname);
					if( JText::_( 'COM_MTREE_CONFIGNAME_'.strtoupper($config->varname) ) == $langcode ) {
						echo $config->varname;
					} else {
						echo JText::_( 'COM_MTREE_CONFIGNAME_'.strtoupper($config->varname) );
					}
					
					if( substr($config->varname,0,4) == 'rss_' ) {
						if( $config->varname == 'rss_custom_fields') {
							echo ' (cust_#)';
						} else {
							echo ' ('.substr($config->varname,4).')';
						}
					}
					echo ':</td><td align="left"';
					if($i<=1) {
						echo ' width="85%"';
					}
					echo '>';
				}
				switch( $config->configcode ) {
					case 'text':
					default:
						echo '<input name="'.$config->varname.'" value="'.$config->value.'" size="30" class="text_area" />';
						break;
					case 'resize_method':
						if( $hasGDLibrary !== false ) {
							echo '<strong>'.$hasGDLibrary.'</strong>';
						} else {
							echo JText::_( 'COM_MTREE_GD_LIBRARY_NOT_AVAILABLE_ERROR' );
						}
						break;
					case 'template':
					case 'map':
					// case 'resize_method':
					case 'sort_options':
						echo $lists[$config->configcode];
						break;
					case 'yesno':
						echo '<fieldset class="radio" style="margin-bottom:0">';
						echo JHTML::_('select.booleanlist', $config->varname,'class="text_area"',$config->value);
						echo '</fieldset>';
						break;
					case 'sort_direction':
						continue;
						break;
					case 'cat_order':
					case 'listing_order':
					case 'review_order':
						$tmp_varname = substr($config->varname,0,-1);
						echo JHTML::_('select.genericlist', $lists[$configs[$tmp_varname.'1']->configcode], $tmp_varname.'1', 'class="inputbox" size="1"',	'value', 'text', $configs[$tmp_varname.'1']->value );
						echo JHTML::_('select.genericlist', $lists[$configs[$tmp_varname.'2']->configcode], $tmp_varname.'2', 'class="inputbox" size="1"',	'value', 'text', $configs[$tmp_varname.'2']->value );
						if( substr($config->varname,-1) == '1' ) {
							unset($configs[$tmp_varname.'2']);
						} else {
							unset($configs[$tmp_varname.'1']);
						}
						break;
					case 'predefined_reply':
						continue;
						break;
					case 'predefined_reply_title':
						$tmp_varname = substr($config->varname,17,1);
						echo '<input name="predefined_reply_'.$tmp_varname.'_title" value="'.$configs['predefined_reply_'.$tmp_varname.'_title']->value.'" size="60" class="text_area" />';
						echo '<br />';
						echo '<textarea style="margin-top:5px" name="predefined_reply_'.$tmp_varname.'_message" class="text_area" cols="80" rows="8" />'.$configs['predefined_reply_'.$tmp_varname.'_message']->value.'</textarea>';
						if( substr($config->varname,19) == 'title' ) {
							unset($configs['predefined_reply_'.$tmp_varname.'_message']);
						} else {
							unset($configs['predefined_reply_'.$tmp_varname.'_title']);
						}						
						break;
					case 'user_access':
					case 'user_access2':
					case 'sef_link_slug_type':
					case 'sort':
					case 'owner_default_page':
						echo JHTML::_('select.genericlist', $lists[$config->configcode], $config->varname, 'class="inputbox" size="1"',	'value', 'text', $config->value );
						break;
					case 'type_of_listings_in_index':
						echo JHTML::_('select.genericlist', $lists[$config->configcode], $config->varname, 'class="inputbox" size="1"',	'value', 'text', $config->value );
						break;
					case 'feature_locations':
						echo JHTML::_('select.genericlist', $lists[$config->configcode], $config->varname, 'class="inputbox" size="1"',	'value', 'text', $config->value );
						break;
					case 'note':
						echo JText::_( 'COM_MTREE_CONFIGNOTE_'.strtoupper($config->varname) );
						break;
				}
				
				if( 
					JText::_( ('COM_MTREE_CONFIGDESC_'.strtoupper($config->varname)) ) 
					!= 
					('COM_MTREE_CONFIGDESC_'.strtoupper($config->varname)) 
				) {
					echo '<span style="background-color:white;padding:0 0 3px 10px;">';
					echo JText::_( 'COM_MTREE_CONFIGDESC_'.strtoupper($config->varname) );
					echo '</span>';
				}

			?></td>
		</tr>
	<?php 
				unset($configs[$config->varname]);
				$i++;
			}
		}
		echo '</table>';
		$j++;
	}
	echo $pane->endPanel();
	echo $pane->endPane();
	?>
  	<input type="hidden" name="option" value="<?php echo $option; ?>">
  	<input type="hidden" name="task" value="saveconfig">
	<?php echo JHTML::_( 'form.token' ); ?>
	</form>
	<?php
	}

	/***
	* Tools
	*/
	function tools( $lists, $option ) {
		global $mtconf;
		JHtml::_('behavior.modal');
		
	?>
	<div id="config-document">
		<div id="page-general" class="tab">
			<div class="noshow">
				<div class="width-100">
					<table class="adminlist">
						<thead>
							<tr>
								<th width="140px">
									<?php echo JText::_( 'COM_MTREE_TOOL' ); ?>
								</th>
								<th colspan=2>
									<?php echo JText::_( 'COM_MTREE_DESCRIPTION' ); ?>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th colspan="3">&nbsp;</th>
							</tr>
						</tfoot>
						<tbody>
							<tr>
								<td><a href="index.php?option=com_mtree&task=csv"><?php echo JText::_( 'COM_MTREE_EXPORT' ) ?></a></td>
								<td colspan=2><?php echo JText::_( 'COM_MTREE_EXPORT_DESC' ) ?></td>
							</tr>
							<tr>
								<td><a href="index.php?option=com_mtree&task=geocode"><?php echo JText::_( 'COM_MTREE_LOCATE_LISTINGS_IN_MAP' ) ?></a></td>
								<td colspan=2><?php echo JText::_( 'COM_MTREE_LOCATE_LISTINGS_IN_MAP_DESC' ) ?></td>
							</tr>
							<tr>
								<td><a href="index.php?option=com_mtree&task=globalupdate"><?php echo JText::_( 'COM_MTREE_RECOUNT_CATEGORIES_LISTINGS' ) ?></a></td>
								<td colspan=2><?php echo JText::_( 'COM_MTREE_RECOUNT_CATEGORIES_LISTINGS_DESC' ) ?></td>
							</tr>
							<tr>
								<td><a href="index.php?option=com_mtree&task=rebuild_tree"><?php echo JText::_( 'COM_MTREE_REBUILD_TREE' ) ?></a></td>
								<td colspan=2><?php echo JText::_( 'COM_MTREE_REBUILD_TREE_DESC' ) ?></td>
							</tr>

							<tr>
								<td rowspan=2><?php echo JText::_( 'COM_MTREE_IMPORT_IMAGES' ) ?></td>
								<td colspan=2>
								<?php echo JText::_( 'COM_MTREE_IMPORT_IMAGES_DESC' ) ?>
								</td>
							</tr>
							<tr>
								<td width=100px><?php echo JText::_('COM_MTREE_CUSTOM_FIELD'); ?></td>
								<td>
									<?php echo $lists['mweblinks'] ?>
									&nbsp;
									<a href="" class="modal" onclick="javascript:this.href='index.php?option=com_mtree&task=import_images&tmpl=component&limit=100&limitstart=0&cfid='+escape(document.getElementById('cfid').value)" rel="{handler: 'iframe', size: {x: 500, y: 210}, onClose: function() {}}">
								<?php echo JText::_('COM_MTREE_IMPORT_IMAGES'); ?>
									</a>
								</td>
							</tr>
							
							<tr>
								<td rowspan=2><?php echo JText::_( 'COM_MTREE_REBUILD_THUMBNAILS' ) ?></td>
								<td colspan=2>
								<?php echo JText::_( 'COM_MTREE_REBUILD_THUMBNAILS_DESC' ) ?>
								</td>
							</tr>
							<tr>
								<td><?php echo JText::_('COM_MTREE_CATEGORY'); ?></td>
								<td>
									<?php echo $lists['top_level_cats'] ?>
									&nbsp;
									<a href="" class="modal" onclick="javascript:this.href='index.php?option=com_mtree&task=rebuild_thumbnails&tmpl=component&limit=50&limitstart=0&cat_id='+escape(document.getElementById('rebuild_thumbnails_cat_id').value)" rel="{handler: 'iframe', size: {x: 500, y: 210}, onClose: function() {}}">
								<?php echo JText::_('COM_MTREE_REBUILD_THUMBNAILS'); ?>
									</a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php
	}

	/***
	* Rebuild thumbnails
	*/
	function rebuild_thumbnails( $option, $cat_id, $next_link )
	{
		if( !empty($next_link) )
		{
		?>
		<h1><?php echo JText::_( 'COM_MTREE_REBUILDING_THUMBNAILS' ); ?></h1>
		
		<a href="<?php echo $next_link; ?>"><?php echo JText::_( 'COM_MTREE_NEXT' ); ?> &gt;</a>
		<?php
		JFactory::getApplication('site')->redirect( $next_link );
		
		}
		else
		{
		?>
		<h1 style="text-align:center"><?php echo JText::_( 'COM_MTREE_REBUILDING_THUMBNAILS_DONE' ); ?></h1>
		<p style="text-align:center"><?php echo JText::_( 'COM_MTREE_YOU_CAN_SAFELY_CLOSE_THIS_WINDOW_NOW' ); ?></p>
		<?php
		}
	}
	
	/***
	* Import images
	*/
	function import_images( $option, $cf_id, $next_link )
	{
		if( !empty($next_link) )
		{
		?>
		<h1><?php echo JText::_( 'COM_MTREE_IMPORT_IMAGES' ); ?></h1>
		
		<a href="<?php echo $next_link; ?>"><?php echo JText::_( 'COM_MTREE_NEXT' ); ?> &gt;</a>
		<?php
		JFactory::getApplication('site')->redirect( $next_link );
		
		}
		else
		{
		?>
		<h1 style="text-align:center"><?php echo JText::_( 'COM_MTREE_IMPORT_IMAGES_DONE' ); ?></h1>
		<p style="text-align:center"><?php echo JText::_( 'COM_MTREE_YOU_CAN_SAFELY_CLOSE_THIS_WINDOW_NOW' ); ?></p>
		<?php
		}
	}
	
	/***
	* About Mosets Tree
	*/
	function about() {
	global $mtconf;
	
	JHTML::_('behavior.switcher');
	?>
	<div id="submenu-box">
		<div class="t"><div class="t"><div class="t"></div></div></div>
		<div class="m">
			<div class="submenu-box">
				<div class="submenu-pad">
					<ul id="submenu" class="information">
						<li>
							<a href="#" onclick="return false;" id="general" class="active">
								<?php echo JText::_( 'COM_MTREE_GENERAL_INFORMATION' ); ?></a>
						</li>
						<li>
							<a href="#" onclick="return false;" id="license">
								<?php echo JText::_( 'COM_MTREE_LICENSE_AGREEMENT' ); ?></a>
						</li>
					</ul>
					<div class="clr"></div>
				</div>
			</div>
			<div class="clr"></div>
		</div>
		<div class="b"><div class="b"><div class="b"></div></div></div>
	</div>
	<div id="config-document">
		<div id="page-general" class="tab">
			<div class="noshow">
				<div class="width-100">
					<table class="adminlist">
						<thead>
							<tr>
								<th colspan="3">
									<?php echo JText::_( 'COM_MTREE_GENERAL_INFORMATION' ); ?>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th colspan="3">&nbsp;</th>
							</tr>
						</tfoot>
						<tbody>
							<tr>
								<td rowspan="5" width="300px">
									<center>
										<a href="http://www.mosets.com/tree/"><img width="260" height="62" src="..<?php echo $mtconf->get('relative_path_to_images'); ?>logo.png" alt="<?php echo $mtconf->get('name') ?>"></a>
									</center>
								</td>
								<td width="100">
									<strong><?php echo JText::_( 'COM_MTREE_VERSION' ); ?></strong>
								</td>
								<td>
									<?php echo $mtconf->get('version') ; ?>
								</td>
							</tr>
							<tr>
								<td>
									<strong><?php echo JText::_( 'COM_MTREE_WEBSITE' ); ?></strong>
								</td>
								<td>
									<a href="http://www.mosets.com">www.mosets.com</a>
								</td>
							</tr>
							<tr>
								<td>
									<strong><?php echo JText::_( 'COM_MTREE_AUTHOR'); ?></strong>
								</td>
								<td>
									C.Y. Lee at Mosets Consulting
								</td>
							</tr>
							<tr>
								<td>
									<strong><?php echo JText::_( 'COM_MTREE_EMAIL' ); ?></strong>
								</td>
								<td>
									<a href="mailto:mtree@mosets.com">mtree@mosets.com</a>
								</td>
							</tr>
							<tr>
								<td>
									<strong><?php echo JText::_( 'COM_MTREE_SUPPORT' ); ?></strong>
								</td>
								<td>
									<a href="http://forum.mosets.com/forumdisplay.php?f=25" target="_blank">Mosets Tree Priority Support</a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="page-license" class="tab">
			<div class="noshow">
					<table class="adminlist">
						<thead>
							<tr>
								<th>
									<?php echo JText::_( 'COM_MTREE_LICENSE_AGREEMENT' ); ?>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>&nbsp;</th>
							</tr>
						</tfoot>
						<tbody>
							<tr>
								<td>

									<h3><a href="index.php?option=com_mtree&amp;task=about">GNU GENERAL PUBLIC LICENSE</a></h3>
									<p>
									Version 2, June 1991
									</p>
									Copyright (C) 1989, 1991 Free Software Foundation, Inc.<br />
									51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA<br />
									<p />
									Everyone is permitted to copy and distribute verbatim copies of this license document, but changing it is not allowed.

									<h3>Preamble</h3>

									<p>
									  The licenses for most software are designed to take away your
									freedom to share and change it.  By contrast, the GNU General Public
									License is intended to guarantee your freedom to share and change free
									software--to make sure the software is free for all its users.  This
									General Public License applies to most of the Free Software
									Foundation's software and to any other program whose authors commit to
									using it.  (Some other Free Software Foundation software is covered by
									the GNU Lesser General Public License instead.)  You can apply it to
									your programs, too.
									</p>

									<p>
									  When we speak of free software, we are referring to freedom, not
									price.  Our General Public Licenses are designed to make sure that you
									have the freedom to distribute copies of free software (and charge for
									this service if you wish), that you receive source code or can get it
									if you want it, that you can change the software or use pieces of it
									in new free programs; and that you know you can do these things.
									</p>

									<p>
									  To protect your rights, we need to make restrictions that forbid
									anyone to deny you these rights or to ask you to surrender the rights.
									These restrictions translate to certain responsibilities for you if you
									distribute copies of the software, or if you modify it.
									</p>

									<p>
									  For example, if you distribute copies of such a program, whether
									gratis or for a fee, you must give the recipients all the rights that
									you have.  You must make sure that they, too, receive or can get the
									source code.  And you must show them these terms so they know their
									rights.
									</p>

									<p>
									  We protect your rights with two steps: (1) copyright the software, and
									(2) offer you this license which gives you legal permission to copy,
									distribute and/or modify the software.
									</p>

									<p>
									  Also, for each author's protection and ours, we want to make certain
									that everyone understands that there is no warranty for this free
									software.  If the software is modified by someone else and passed on, we
									want its recipients to know that what they have is not the original, so
									that any problems introduced by others will not reflect on the original
									authors' reputations.
									</p>

									<p>
									  Finally, any free program is threatened constantly by software
									patents.  We wish to avoid the danger that redistributors of a free
									program will individually obtain patent licenses, in effect making the
									program proprietary.  To prevent this, we have made it clear that any
									patent must be licensed for everyone's free use or not licensed at all.
									</p>

									<p>
									  The precise terms and conditions for copying, distribution and
									modification follow.
									</p>


									<h3>TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION</h3>


									<a name="section0"></a><p>
									<strong>0.</strong>
									 This License applies to any program or other work which contains
									a notice placed by the copyright holder saying it may be distributed
									under the terms of this General Public License.  The "Program", below,
									refers to any such program or work, and a "work based on the Program"
									means either the Program or any derivative work under copyright law:
									that is to say, a work containing the Program or a portion of it,
									either verbatim or with modifications and/or translated into another
									language.  (Hereinafter, translation is included without limitation in
									the term "modification".)  Each licensee is addressed as "you".
									</p>

									<p>
									Activities other than copying, distribution and modification are not
									covered by this License; they are outside its scope.  The act of
									running the Program is not restricted, and the output from the Program
									is covered only if its contents constitute a work based on the
									Program (independent of having been made by running the Program).
									Whether that is true depends on what the Program does.
									</p>

									<a name="section1"></a><p>
									<strong>1.</strong>
									 You may copy and distribute verbatim copies of the Program's
									source code as you receive it, in any medium, provided that you
									conspicuously and appropriately publish on each copy an appropriate
									copyright notice and disclaimer of warranty; keep intact all the
									notices that refer to this License and to the absence of any warranty;
									and give any other recipients of the Program a copy of this License
									along with the Program.
									</p>

									<p>
									You may charge a fee for the physical act of transferring a copy, and
									you may at your option offer warranty protection in exchange for a fee.
									</p>

									<a name="section2"></a><p>
									<strong>2.</strong>
									 You may modify your copy or copies of the Program or any portion
									of it, thus forming a work based on the Program, and copy and
									distribute such modifications or work under the terms of Section 1
									above, provided that you also meet all of these conditions:
									</p>

									<dl>
									  <dt></dt>
									    <dd>
									      <strong>a)</strong>
									      You must cause the modified files to carry prominent notices
									      stating that you changed the files and the date of any change.
									    </dd>
									  <dt></dt>
									    <dd>
									      <strong>b)</strong>
									      You must cause any work that you distribute or publish, that in
									      whole or in part contains or is derived from the Program or any
									      part thereof, to be licensed as a whole at no charge to all third
									      parties under the terms of this License.
									    </dd>
									  <dt></dt>
									    <dd>
									      <strong>c)</strong>
									      If the modified program normally reads commands interactively
									      when run, you must cause it, when started running for such
									      interactive use in the most ordinary way, to print or display an
									      announcement including an appropriate copyright notice and a
									      notice that there is no warranty (or else, saying that you provide
									      a warranty) and that users may redistribute the program under
									      these conditions, and telling the user how to view a copy of this
									      License.  (Exception: if the Program itself is interactive but
									      does not normally print such an announcement, your work based on
									      the Program is not required to print an announcement.)
									    </dd>
									</dl>

									<p>
									These requirements apply to the modified work as a whole.  If
									identifiable sections of that work are not derived from the Program,
									and can be reasonably considered independent and separate works in
									themselves, then this License, and its terms, do not apply to those
									sections when you distribute them as separate works.  But when you
									distribute the same sections as part of a whole which is a work based
									on the Program, the distribution of the whole must be on the terms of
									this License, whose permissions for other licensees extend to the
									entire whole, and thus to each and every part regardless of who wrote it.
									</p>

									<p>
									Thus, it is not the intent of this section to claim rights or contest
									your rights to work written entirely by you; rather, the intent is to
									exercise the right to control the distribution of derivative or
									collective works based on the Program.
									</p>

									<p>
									In addition, mere aggregation of another work not based on the Program
									with the Program (or with a work based on the Program) on a volume of
									a storage or distribution medium does not bring the other work under
									the scope of this License.
									</p>

									<a name="section3"></a><p>
									<strong>3.</strong>
									 You may copy and distribute the Program (or a work based on it,
									under Section 2) in object code or executable form under the terms of
									Sections 1 and 2 above provided that you also do one of the following:
									</p>

									<!-- we use this doubled UL to get the sub-sections indented, -->
									<!-- while making the bullets as unobvious as possible. -->

									<dl>
									  <dt></dt>
									    <dd>
									      <strong>a)</strong>
									      Accompany it with the complete corresponding machine-readable
									      source code, which must be distributed under the terms of Sections
									      1 and 2 above on a medium customarily used for software interchange; or,
									    </dd>
									  <dt></dt>
									    <dd>
									      <strong>b)</strong>
									      Accompany it with a written offer, valid for at least three
									      years, to give any third party, for a charge no more than your
									      cost of physically performing source distribution, a complete
									      machine-readable copy of the corresponding source code, to be
									      distributed under the terms of Sections 1 and 2 above on a medium
									      customarily used for software interchange; or,
									    </dd>
									  <dt></dt>
									    <dd>
									      <strong>c)</strong>
									      Accompany it with the information you received as to the offer
									      to distribute corresponding source code.  (This alternative is
									      allowed only for noncommercial distribution and only if you
									      received the program in object code or executable form with such
									      an offer, in accord with Subsection b above.)
									    </dd>
									</dl>

									<p>
									The source code for a work means the preferred form of the work for
									making modifications to it.  For an executable work, complete source
									code means all the source code for all modules it contains, plus any
									associated interface definition files, plus the scripts used to
									control compilation and installation of the executable.  However, as a
									special exception, the source code distributed need not include
									anything that is normally distributed (in either source or binary
									form) with the major components (compiler, kernel, and so on) of the
									operating system on which the executable runs, unless that component
									itself accompanies the executable.
									</p>

									<p>
									If distribution of executable or object code is made by offering
									access to copy from a designated place, then offering equivalent
									access to copy the source code from the same place counts as
									distribution of the source code, even though third parties are not
									compelled to copy the source along with the object code.
									</p>

									<a name="section4"></a><p>
									<strong>4.</strong>
									 You may not copy, modify, sublicense, or distribute the Program
									except as expressly provided under this License.  Any attempt
									otherwise to copy, modify, sublicense or distribute the Program is
									void, and will automatically terminate your rights under this License.
									However, parties who have received copies, or rights, from you under
									this License will not have their licenses terminated so long as such
									parties remain in full compliance.
									</p>

									<a name="section5"></a><p>
									<strong>5.</strong>
									 You are not required to accept this License, since you have not
									signed it.  However, nothing else grants you permission to modify or
									distribute the Program or its derivative works.  These actions are
									prohibited by law if you do not accept this License.  Therefore, by
									modifying or distributing the Program (or any work based on the
									Program), you indicate your acceptance of this License to do so, and
									all its terms and conditions for copying, distributing or modifying
									the Program or works based on it.
									</p>

									<a name="section6"></a><p>
									<strong>6.</strong>
									 Each time you redistribute the Program (or any work based on the
									Program), the recipient automatically receives a license from the
									original licensor to copy, distribute or modify the Program subject to
									these terms and conditions.  You may not impose any further
									restrictions on the recipients' exercise of the rights granted herein.
									You are not responsible for enforcing compliance by third parties to
									this License.
									</p>

									<a name="section7"></a><p>
									<strong>7.</strong>
									 If, as a consequence of a court judgment or allegation of patent
									infringement or for any other reason (not limited to patent issues),
									conditions are imposed on you (whether by court order, agreement or
									otherwise) that contradict the conditions of this License, they do not
									excuse you from the conditions of this License.  If you cannot
									distribute so as to satisfy simultaneously your obligations under this
									License and any other pertinent obligations, then as a consequence you
									may not distribute the Program at all.  For example, if a patent
									license would not permit royalty-free redistribution of the Program by
									all those who receive copies directly or indirectly through you, then
									the only way you could satisfy both it and this License would be to
									refrain entirely from distribution of the Program.
									</p>

									<p>
									If any portion of this section is held invalid or unenforceable under
									any particular circumstance, the balance of the section is intended to
									apply and the section as a whole is intended to apply in other
									circumstances.
									</p>

									<p>
									It is not the purpose of this section to induce you to infringe any
									patents or other property right claims or to contest validity of any
									such claims; this section has the sole purpose of protecting the
									integrity of the free software distribution system, which is
									implemented by public license practices.  Many people have made
									generous contributions to the wide range of software distributed
									through that system in reliance on consistent application of that
									system; it is up to the author/donor to decide if he or she is willing
									to distribute software through any other system and a licensee cannot
									impose that choice.
									</p>

									<p>
									This section is intended to make thoroughly clear what is believed to
									be a consequence of the rest of this License.
									</p>

									<a name="section8"></a><p>
									<strong>8.</strong>
									 If the distribution and/or use of the Program is restricted in
									certain countries either by patents or by copyrighted interfaces, the
									original copyright holder who places the Program under this License
									may add an explicit geographical distribution limitation excluding
									those countries, so that distribution is permitted only in or among
									countries not thus excluded.  In such case, this License incorporates
									the limitation as if written in the body of this License.
									</p>

									<a name="section9"></a><p>
									<strong>9.</strong>
									 The Free Software Foundation may publish revised and/or new versions
									of the General Public License from time to time.  Such new versions will
									be similar in spirit to the present version, but may differ in detail to
									address new problems or concerns.
									</p>

									<p>
									Each version is given a distinguishing version number.  If the Program
									specifies a version number of this License which applies to it and "any
									later version", you have the option of following the terms and conditions
									either of that version or of any later version published by the Free
									Software Foundation.  If the Program does not specify a version number of
									this License, you may choose any version ever published by the Free Software
									Foundation.
									</p>

									<a name="section10"></a><p>
									<strong>10.</strong>
									 If you wish to incorporate parts of the Program into other free
									programs whose distribution conditions are different, write to the author
									to ask for permission.  For software which is copyrighted by the Free
									Software Foundation, write to the Free Software Foundation; we sometimes
									make exceptions for this.  Our decision will be guided by the two goals
									of preserving the free status of all derivatives of our free software and
									of promoting the sharing and reuse of software generally.
									</p>

									<a name="section11"></a><p><strong>NO WARRANTY</strong></p>

									<p>
									<strong>11.</strong>
									 BECAUSE THE PROGRAM IS LICENSED FREE OF CHARGE, THERE IS NO WARRANTY
									FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE LAW.  EXCEPT WHEN
									OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR OTHER PARTIES
									PROVIDE THE PROGRAM "AS IS" WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESSED
									OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
									MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.  THE ENTIRE RISK AS
									TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU.  SHOULD THE
									PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY SERVICING,
									REPAIR OR CORRECTION.
									</p>

									<a name="section12"></a><p>
									<strong>12.</strong>
									 IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN WRITING
									WILL ANY COPYRIGHT HOLDER, OR ANY OTHER PARTY WHO MAY MODIFY AND/OR
									REDISTRIBUTE THE PROGRAM AS PERMITTED ABOVE, BE LIABLE TO YOU FOR DAMAGES,
									INCLUDING ANY GENERAL, SPECIAL, INCIDENTAL OR CONSEQUENTIAL DAMAGES ARISING
									OUT OF THE USE OR INABILITY TO USE THE PROGRAM (INCLUDING BUT NOT LIMITED
									TO LOSS OF DATA OR DATA BEING RENDERED INACCURATE OR LOSSES SUSTAINED BY
									YOU OR THIRD PARTIES OR A FAILURE OF THE PROGRAM TO OPERATE WITH ANY OTHER
									PROGRAMS), EVEN IF SUCH HOLDER OR OTHER PARTY HAS BEEN ADVISED OF THE
									POSSIBILITY OF SUCH DAMAGES.
									</p>
								</td>
							</tr>
						</tbody>
					</table>
			</div>
		</div>
	</div>
	<?php
	}

}

class MTConfigHtml {
	
	public static function _($function, $items=array(), $config=null)
	{
		$args = func_get_args();
		array_shift($args);
		$i = 0;
		
		foreach( $items AS $item )
		{
			if( !is_null($item['override']) ) {
				// echo '<p />[override '.$item['varname'].' : '.$item['override'].']';
			}
			
			if( !empty($config['namespace']) ) {
				$args[0][$i]['varname'] = $config['namespace'] . '[' . $args[0][$i]['varname'] . ']';
			}
			$i++;
		}
		return call_user_func_array(array('MTConfigHtml','self::'.$function), $args);
	}
	
	public static function overrideCheckbox($items=array(), $config=null)
	{
		$args = func_get_args();
		$i = 0;

		foreach( $items AS $item )
		{
			if( !empty($config['namespace']) ) {
				$args[0][$i]['varname'] = $config['namespace'] . '[' . $args[0][$i]['varname'] . ']';
			}
			$i++;
		}
		
		$checked = ($item['override'] != ''?true:false);
		$class = (!empty($config['class'])?'class="'.$config['class'].'" ':'');
		return '<input type="checkbox" name="override['.$item['varname'].']" value="1" '.($checked?'checked ':'').$class.'onclick="" />';
	}
	
	public static function text($items, $config=null)
	{
		return '<input name="'.$items[0]['varname'].'" value="'.self::getValue($items[0]).'" size="30" class="text_area" />';
	}

	public static function type_of_listings_in_index($items, $config=null)
	{
		# Listings type in index
		$type_of_listings_in_index = array();
		$arr_tmp = array('listcurrent','listpopular', 'listmostrated', 'listtoprated', 'listmostreview', 'listnew', 'listupdated', 'listfavourite', 'listfeatured');

		foreach( $arr_tmp AS $tmp )
		{
			$type_of_listings_in_index[] = JHTML::_('select.option', $tmp, JText::_( 'COM_MTREE_TYPES_OF_LISTINGS_IN_INDEX_OPTION_'.strtoupper($tmp) ) );
		}

		return JHTML::_('select.genericlist', $type_of_listings_in_index, $items[0]['varname'], 'class="inputbox" size="1"', 'value', 'text', self::getValue($items[0]) );
	}

	public static function feature_locations($items, $config=null)
	{
		$feature_locations = array();
		$feature_locations[] = JHTML::_('select.option', "1", JText::_( 'COM_MTREE_STANDALONE_PAGE' ) );
		$feature_locations[] = JHTML::_('select.option', "2", JText::_( 'COM_MTREE_LISTING_DETAILS_PAGE' ) );
		return JHTML::_('select.genericlist', $feature_locations, $items[0]['varname'], 'class="inputbox" size="1"', 'value', 'text', self::getValue($items[0]) );
	}

	public static function user_access($items, $config=null)
	{
		$access = array();
		$access[] = JHTML::_('select.option', "-1", JText::_( 'JNONE' ) );
		$access[] = JHTML::_('select.option', "0", JText::_( 'COM_MTREE_PUBLIC' ) );
		$access[] = JHTML::_('select.option', "1", JText::_( 'COM_MTREE_REGISTERED_ONLY' ) );
		return JHTML::_('select.genericlist', $access, $items[0]['varname'], 'class="inputbox" size="1"', 'value', 'text', self::getValue($items[0]) );
	}
	
	public static function user_access2($items, $config=null)
	{
		$access = array();
		$access[] = JHTML::_('select.option', "-1", JText::_( 'JNONE' ) );
		$access[] = JHTML::_('select.option', "0", JText::_( 'COM_MTREE_PUBLIC' ) );
		$access[] = JHTML::_('select.option', "1", JText::_( 'COM_MTREE_REGISTERED_ONLY' ) );
		$access[] = JHTML::_('select.option', "2", JText::_( 'COM_MTREE_REGISTERED_ONLY_EXCEPT_LISTING_OWNER' ) );
		return JHTML::_('select.genericlist', $access, $items[0]['varname'], 'class="inputbox" size="1"', 'value', 'text', self::getValue($items[0]) );
	}

	public static function sef_link_slug_type($items, $config=null)
	{
		$sef_link_slug_type = array();
		$sef_link_slug_type[] = JHTML::_('select.option', "1", JText::_( 'COM_MTREE_ALIAS' ) );
		$sef_link_slug_type[] = JHTML::_('select.option', "2", JText::_( 'COM_MTREE_LINK_ID' ) );
		$sef_link_slug_type[] = JHTML::_('select.option', "3", JText::_( 'COM_MTREE_LINK_ID_AND_ALIAS_HYBRID' ) );
		return JHTML::_('select.genericlist', $sef_link_slug_type, $items[0]['varname'], 'class="inputbox" size="1"', 'value', 'text', self::getValue($items[0]) );
	}

	public static function resize_method($items, $config=null)
	{
		$imageLibs=array();
		$imageLibs=detect_ImageLibs();
		if(!empty($imageLibs['gd1'])) { $thumbcreator[] = JHTML::_('select.option', 'gd1', 'GD Library '.$imageLibs['gd1'] ); }
		$thumbcreator[] = JHTML::_('select.option', 'gd2', 'GD2 Library '.( (array_key_exists('gd2',$imageLibs)) ? $imageLibs['gd2'] : '') );
		$thumbcreator[] = JHTML::_('select.option', 'netpbm', (isset($imageLibs['netpbm'])) ? $imageLibs['netpbm'] : "Netpbm" );
		$thumbcreator[] = JHTML::_('select.option', 'imagemagick', (isset($imageLibs['imagemagick'])) ? $imageLibs['imagemagick'] : "Imagemagick" ); 
		return JHTML::_('select.genericlist', $thumbcreator, $items[0]['varname'], 'class="text_area" size="3"', 'value', 'text', self::getValue($items[0]) );
	}
	
	public static function yesno($items, $config=null)
	{
		$arr = array(
			JHtml::_('select.option', '0', JText::_('JNO')),
			JHtml::_('select.option', '1', JText::_('JYES'))
		);

		$html = '<fieldset class="radio" style="margin-bottom:0">';
		$html .= JHtml::_('select.radiolist', $arr, $items[0]['varname'], 'class="text_area"', 'value', 'text', (int) self::getValue($items[0]));
		$html .= '</fieldset>';
		return $html;
	}

	public static function cat_order($items, $config=null) {
		# Sort Direction
		$sort[] = JHTML::_('select.option', "asc", JText::_( 'COM_MTREE_ASCENDING' ) );
		$sort[] = JHTML::_('select.option', "desc", JText::_( 'COM_MTREE_DESCENDING' ) );

		# Category Order
		$cat_order = array();
		$cat_order[] = JHTML::_('select.option', '', JText::_( '' ) );
		$cat_order[] = JHTML::_('select.option', "lft", JText::_( 'COM_MTREE_CONFIG_CUSTOM_ORDER' ) );
		$cat_order[] = JHTML::_('select.option', "cat_name", JText::_( 'COM_MTREE_NAME' ) );
		$cat_order[] = JHTML::_('select.option', "cat_featured", JText::_( 'COM_MTREE_FEATURED' ) );
		$cat_order[] = JHTML::_('select.option', "cat_created", JText::_( 'COM_MTREE_CREATED' ) );
		
		$html = JHTML::_('select.genericlist', $cat_order, $items[0]['varname'], 'class="inputbox" size="1"', 'value', 'text', self::getValue($items[0]) );
		$html .= JHTML::_('select.genericlist', $sort, $items[1]['varname'], 'class="inputbox" size="1"', 'value', 'text', self::getValue($items[1]) );

		return $html;
	}
	
	
	public static function predefined_reply_title($items, $config=null)
	{
		$html = '<input name="'.$items[0]['varname'].'" value="'.self::getValue($items[0]).'" size="60" class="text_area" />';
		$html .= '<br />';
		$html .= '<textarea style="margin-top:5px" name="'.$items[1]['varname'].'" class="text_area" cols="80" rows="8" />'.self::getValue($items[1]).'</textarea>';
		
		return $html;
	}
	
	public static function note($items)
	{
		return JText::_( 'COM_MTREE_CONFIGNOTE_'.strtoupper($items[0]['varname']) );
	}
	
	public static function listing_order($items, $config=null)
	{
		# Sort Direction
		$sort[] = JHTML::_('select.option', "asc", JText::_( 'COM_MTREE_ASCENDING' ) );
		$sort[] = JHTML::_('select.option', "desc", JText::_( 'COM_MTREE_DESCENDING' ) );
		
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
		$listing_order[] = JHTML::_('select.option', "ordering", JText::_( 'COM_MTREE_ORDERING' ) );

		$html = JHTML::_('select.genericlist', $listing_order, $items[0]['varname'], 'class="inputbox" size="1"', 'value', 'text', self::getValue($items[0]) );
		$html .= JHTML::_('select.genericlist', $sort, $items[1]['varname'], 'class="inputbox" size="1"', 'value', 'text', self::getValue($items[1]) );

		return $html;
	}
	
	public static function review_order($items, $config=null)
	{
		# Sort Direction
		$sort[] = JHTML::_('select.option', "asc", JText::_( 'COM_MTREE_ASCENDING' ) );
		$sort[] = JHTML::_('select.option', "desc", JText::_( 'COM_MTREE_DESCENDING' ) );
		
		# Review Order
		$review_order[] = JHTML::_('select.option', '', JText::_( '' ) );
		$review_order[] = JHTML::_('select.option', "rev_date", JText::_( 'COM_MTREE_REVIEW_DATE' ) );
		$review_order[] = JHTML::_('select.option', "vote_helpful", JText::_( 'COM_MTREE_TOTAL_HELPFUL_VOTES' ) );
		$review_order[] = JHTML::_('select.option', "vote_total", JText::_( 'COM_MTREE_TOTAL_VOTES' ) );

		$html = JHTML::_('select.genericlist', $review_order, $items[0]['varname'], 'class="inputbox" size="1"', 'value', 'text', self::getValue($items[0]) );
		$html .= JHTML::_('select.genericlist', $sort, $items[1]['varname'], 'class="inputbox" size="1"', 'value', 'text', self::getValue($items[1]) );

		return $html;
	}
	
	public static function sort($items, $config=null)
	{
		$sort_by_options = array('-link_created', '-link_modified', '-link_hits', '-link_visited', '-link_rating', '-link_votes', 'link_name', '-price','price');

		foreach( $sort_by_options AS $sort_by_option ) {
			$sort_by[] = JHTML::_('select.option', $sort_by_option, JText::_( 'COM_MTREE_ALL_LISTINGS_SORT_OPTION_'.strtoupper($sort_by_option) ) );
		}
		$html = JHTML::_('select.genericlist', $sort_by, $items[0]['varname'], 'class="inputbox" size="1"', 'value', 'text', self::getValue($items[0]) );
		return $html;
	}
	
	public static function sort_options($items, $config=null)
	{
		$sort_by_options = array('-link_created', '-link_modified', '-link_hits', '-link_visited', '-link_rating', '-link_votes', 'link_name', '-price','price');

		$sort_by_option_values = self::getValue($items[0]);
		if( !is_array($sort_by_option_values) ) {
			$sort_by_option_values = explode('|',$sort_by_option_values);
		}

		$html = '';
		$html .= '<fieldset>';
		foreach( $sort_by_options AS $sort_by_option ) {
			$html .= '<input type="checkbox" name="'.$items[0]['varname'].'[]" value="'.$sort_by_option.'"';
			$html .= ' style="clear:left"';
			if( isset($sort_by_option_values) && in_array($sort_by_option,$sort_by_option_values) ) {
				$html .= ' checked';
			}
			$html .= ' />';
			$html .= '<label style="clear:none;float:none;margin:3px 0 0 5px;float:left">';
			$html .= JText::_( 'COM_MTREE_ALL_LISTINGS_SORT_OPTION_'.strtoupper($sort_by_option) );
			$html .= '</label>';
		}
		$html .= '</fieldset>';
		return $html;		
	}
	
	public static function getValue($item) {
		if( $item['override'] != '' ) {return $item['override'];}
		else {return $item['value'];}
	}

}
?>