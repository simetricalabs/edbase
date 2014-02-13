<?php
/**
 * @version	$Id: customfields.mtree.html.php 1626 2012-11-08 08:46:55Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class HTML_mtcustomfields {
	
	function managefieldtypes( $option, $rows ) {
		global $mtconf;
	?>
	<div style="position:relative;top:5px;clear:both;text-align:left;margin-bottom:20px;"><img style="position:relative;top:4px;" src="..<?php echo $mtconf->get('relative_path_to_images'); ?>arrow_left.png" width="16" height="16" /> <a href="index.php?option=com_mtree&amp;task=customfields"><b><?php echo JText::_( 'COM_MTREE_BACK_TO_CUSTOM_FIELDS' ) ?></b></a></div>
		
	<table class="adminlist">
	<thead>
	<tr>
		<th width="25%" class="title"><?php echo JText::_( 'COM_MTREE_FIELD_TYPE' ) ?></th>
		<th width="35%" class="title"><?php echo JText::_( 'COM_MTREE_DESCRIPTION' ) ?></th>
		<th width="5%" align="center"><?php echo JText::_( 'COM_MTREE_VERSION' ) ?></th>
		<th width="20%" align="left"><?php echo JText::_( 'COM_MTREE_WEBSITE' ) ?></th>
	</tr>
	</thead>
	<?php
	if(count($rows) > 0) {
		$rc = 0;
		$i=0;
		foreach($rows AS $row) {
			?>
		<tr class="<?php echo "row$rc"; ?>">
			<td valign="top">
			<?php echo $row->ft_caption; ?></td>
			<td><?php 
			if($row->iscore) {
				echo '<b>' . JText::_( 'COM_MTREE_CORE_FIELDTYPE' ) . '</b>';	
			} else {
				echo $row->ft_desc;
			}
			?></td>
			<td><?php echo $row->ft_version; ?></td>
			<td><a href="<?php echo $row->ft_website; ?>" target="_blank"><?php echo $row->ft_website; ?></a></td>
		</tr>
			<?php 
			$rc = $rc == 0 ? 1 : 0;
			$i++;
		} 
	} else {
		echo '<tr><td colspan="5">No custom field type installed.</td></tr>';
	}
	?>
	<tfoot>
	<tr><th colspan="5"></th></tr>
	<tfoot>
	</table>
	<?php
	}
	
	function customfields( $custom_fields, $pageNav, $option ) {
		global $mtconf;
	?>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminheading">
		<tr><td>
			<a href="index.php?option=com_mtree&amp;task=managefieldtypes"><?php echo JText::_( 'COM_MTREE_VIEW_INSTALLED_FIELD_TYPES' ) ?></a>
		</td></tr>
	</table>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		<thead>
		<th width="20"><?php echo JText::_( 'COM_MTREE_ID' ) ?></th>
		<th width="20"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $custom_fields ); ?>);" /></th>
		<th width="40%" align="left" nowrap><?php echo JText::_( 'COM_MTREE_CAPTION' ) ?></th>
		<th width="20%" align="left"><?php echo JText::_( 'COM_MTREE_FIELD_TYPE' ) ?></th>
		<th width="50" align="center" nowrap><?php echo JText::_( 'COM_MTREE_ADVANCED_SEARCHABLE' ) ?></th>
		<th width="50" align="center" nowrap><?php echo JText::_( 'COM_MTREE_SIMPLE_SEARCHABLE' ) ?></th>
		<th width="50" align="center" nowrap><?php echo JText::_( 'COM_MTREE_REQUIRED' ) ?></th>

		<th width="50" align="center" nowrap><?php echo JText::_( 'COM_MTREE_SUMMARY_VIEW' ) ?></th>
		<th width="50" align="center" nowrap><?php echo JText::_( 'COM_MTREE_DETAILS_VIEW' ) ?></th>

		<th width="10%" align="center" nowrap><?php echo JText::_( 'COM_MTREE_PUBLISHED' ) ?></th>
		<th width="4%" align="center" nowrap colspan="2"><?php echo JText::_( 'COM_MTREE_ORDERING' ) ?></th>
		</thead>
	
		<?php
		$k = 0;
		for ($i=0, $n=count( $custom_fields ); $i < $n; $i++) {
			$row = &$custom_fields[$i];
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td align="center"><?php echo $row->cf_id;?></td>
			<td>
				<input type="checkbox" id="cb<?php echo $i;?>" name="cfid[]" value="<?php echo $row->cf_id; ?>" onClick="isChecked(this.checked);" />
			</td>
			<td align="left">
				<a href="index.php?option=com_mtree&amp;task=editcf&amp;cfid=<?php echo $row->cf_id; ?>"><?php 
					if ( strlen($row->caption) > 55 ) {
						echo strip_tags(substr($row->caption, 0, 55))."...";
					} else {
						echo strip_tags($row->caption);
					}
				?></a>
			</td>
			<td><?php 
				if($row->iscore) {
					echo '<b>' . strtoupper(JText::_( 'COM_MTREE_CORE' )) . '</b>';
				} else { 
					if( is_null($row->ft_caption) ) {
						echo JText::_( 'COM_MTREE_FIELD_TYPE_' . strtoupper($row->field_type) );
					} else {
						echo $row->ft_caption;
					}
				} ?></td>
			<?php if ($row->hidden) { 
				?>
				<td align="center" colspan="5"><strong><?php echo JText::_( 'COM_MTREE_HIDDEN_FIELD' ) ?></strong></td>
				<?php
			} else { ?>
			<td align="center">
				<?php echo JHtml::_('jgrid.published', $row->advanced_search, $i, '', false); ?>
			</td>
			<td align="center">
				<?php echo JHtml::_('jgrid.published', $row->simple_search, $i, '', false); ?>
			</td>
			<td align="center">
				<?php echo JHtml::_('jgrid.published', $row->required_field, $i, '', false); ?>
			</td>
			<td align="center">
				<?php echo JHtml::_('jgrid.published', $row->summary_view, $i, '', false); ?>
			</td>
			<td align="center">
				<?php echo JHtml::_('jgrid.published', $row->details_view, $i, '', false); ?>
			</td>
			<?php
			
			}
			
				$task = $row->published ? 'cf_unpublish' : 'cf_publish';
				$img = $row->published ? 'publish_g.png' : 'publish_x.png';
			?>
			<td align="center">
				<?php if ($row->field_type <> 'corename') { ?>
				<?php echo JHtml::_('jgrid.published', $row->published, $i, 'cf_', true); ?>
				<?php } else {echo JHtml::_('jgrid.published', $row->published, $i, '', false);} ?>
			</td>
			<td class="order">
				<span><?php echo $pageNav->orderUpIcon( $i, true, 'cf_orderup' ); ?></span>
			</td>
			<td class="order">
				<span><?php echo $pageNav->orderDownIcon( $i, $n, true, 'cf_orderdown'  ); ?></span>
			</td>
		</tr>
		<?php
			$k = 1 - $k;
		}
		?>
		<tfoot>
			<tr>
				<td colspan="12">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="task" value="customfields" />
	<input type="hidden" name="boxchecked" value="0">
	<?php echo JHTML::_( 'form.token' ); ?>
	</form>
	<?php
	}

	function editcf( $row, $custom_cf_types, $lists, $cats, $fields_map_cats, $form, $option ) {
		JHtml::_('behavior.tooltip');
	?>
	<script language="javascript">
	<!--
	Joomla.submitbutton = function(task) {
		var form = document.getElementById('adminForm');
		if(task=='cancelcf') {
			Joomla.submitform(task, form);
			return;
		}
		if (form.caption.value == "") {
			alert( "<?php echo JText::_( 'COM_MTREE_PLEASE_FILL_IN_THE_FIELDS_CAPTION' ) ?>" );
		} else if (form.iscore.value == "0" && ( form.field_type.value == "checkbox" || form.field_type.value == "selectlist" || form.field_type.value == "selectmultiple" || form.field_type.value == "radiobutton" ) && form.field_elements.value == "" ) {
			alert( "Please fill in the Field Elements." );
		} else {
			Joomla.submitform(task, form);
		}
	}
	function updateInputs(ftype) {
		var f = document.adminForm;
		if (ftype=='selectlist'||ftype=='selectmultiple'||ftype=='checkbox'||ftype=='radiobutton'||ftype=='corecountry'||ftype=='corestate'||ftype=='corecity'<?php
		foreach( $custom_cf_types AS $custom_cf_type ) {
			if($custom_cf_type->use_elements) {	echo '||ftype==\'' . $custom_cf_type->field_type . '\''; }
		}
		?>) {
			f.field_elements.disabled=false;
			f.field_elements.style.backgroundColor='#FFFFFF'; 
		} else {
			f.field_elements.style.backgroundColor='#f5f5f5'; 
			f.field_elements.disabled=true;
		}

		if (ftype=='selectlist'||ftype=='selectmultiple'||ftype=='checkbox'||ftype=='radiobutton'||ftype=='corecountry'||ftype=='corestate'||ftype=='corecity'<?php
		foreach( $custom_cf_types AS $custom_cf_type ) {
			if($custom_cf_type->taggable) {	echo '||ftype==\'' . $custom_cf_type->field_type . '\''; }
		}
		?>) {
			f.tag_search[0].disabled=false;
			f.tag_search[1].disabled=false;
		} else {
			f.tag_search[0].disabled=true;
			f.tag_search[1].disabled=true;
		}
		
		if(ftype=='checkbox'||ftype =='radiobutton'<?php
		foreach( $custom_cf_types AS $custom_cf_type ) {
			if(!$custom_cf_type->use_size) {	echo '||ftype==\'' . $custom_cf_type->field_type . '\''; }
		}
		?>) {
			f.size.disabled=true;
		} else {
			f.size.disabled=false;
		}
		
		if (ftype=='coreprice'||ftype=='coreaddress'||ftype=='coreaddress'||ftype=='corepostcode'||ftype=='coretelephone'||ftype=='corefax'||ftype=='coreemail'||ftype=='corewebsite'||ftype=='corename'||ftype=='coredesc'||ftype=='coremetakey'||ftype==''||ftype=='coremetadesc'||ftype=='corecountry'||ftype=='corestate'||ftype=='corecity'<?php
		foreach( $custom_cf_types AS $custom_cf_type ) {
			if($custom_cf_type->use_placeholder) {	echo '||ftype==\'' . $custom_cf_type->field_type . '\''; }
		}
		?>) {
			f.placeholder_text.disabled=false;
			f.placeholder_text.style.backgroundColor='#FFFFFF'; 
		} else {
			f.placeholder_text.style.backgroundColor='#f5f5f5'; 
			f.placeholder_text.disabled=true;
		}
		
		<?php
		$tmp_fields = array();
		foreach( $custom_cf_types AS $custom_cf_type )
		{
			if($custom_cf_type->is_file)
			{	
				$tmp_fields[] = 'ftype==\'' . $custom_cf_type->field_type . '\'';
			}
		}
		if( !empty($tmp_fields) )
		{
			echo 'if(ftype==\'corecreated\'||ftype==\'coremodified\'||ftype==\'corefeatured\'||ftype==\'corerating\'||ftype==\'corevotes\'||ftype==\'corehits\'||'.implode('||',$tmp_fields).')';
		}
		?>{
			f.default_value.style.backgroundColor='#f5f5f5'; 
			f.default_value.disabled=true;
		} else {
			f.default_value.disabled=false;
			f.default_value.style.backgroundColor='#FFFFFF'; 
		}
	}
	-->
	</script>
	<style type="text/css">
	table.paramlist td {
		background-color:#F6F6F6;
		border-bottom:1px solid #E9E9E9;
		padding:5px 3px;
	}
	</style>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php
	$fieldsets = array();
	if( $form )
	{
		$fieldsets = $form->getFieldsets();
	}
	?>
	<div class="width-<?php echo ($fieldsets?'60':'100')?> fltlft">
	<fieldset class="panelform">
	<legend><?php echo JText::_( 'COM_MTREE_CUSTOM_FIELD' ) ?></legend>
	<ul class="adminformlist">
		<li>
			<label><?php echo JText::_( 'COM_MTREE_FIELD_TYPE' ) ?>:</label>
			<?php
			if( $row->iscore ) { 
				echo '<b>' . JText::_( 'COM_MTREE_CORE_FIELD' ) . '</b>';
				echo '<input type="hidden" name="field_type" value="' . $row->field_type. '" />';
			} else { 
				echo $lists['field_types']; 
			}
			echo '<input type="hidden" name="iscore" value="' . $row->iscore . '" />';
			if( !$row->iscore && $row->cf_id == 0 ) {
				echo '<span style="background-color:white;margin-left:10px;">' . JText::_( 'COM_MTREE_SOME_FIELDTYPE_HAS_PARAMS_DESC' ) . '</span>';
			}
			?>
		</li>
		<li>
			<label><?php echo JText::_( 'COM_MTREE_CAPTION' ) ?>:</label>
			<input type="text" size="40" name="caption" class="text_area" value="<?php echo htmlspecialchars($row->caption) ?>" />
		</li>
		<li>
			<fieldset class="checkboxes">
			<input type="checkbox" name="hide_caption" id="hide_caption" class="text_area" value="1"<?php echo ($row->hide_caption) ? ' checked' : '' ?> /> <label for="hide_caption"><?php echo JText::_( 'COM_MTREE_HIDE_CAPTION' ) ?></label>
			</fieldset>
		</li>
		<li>
			<label><?php echo JText::_( 'COM_MTREE_FIELD_ELEMENTS' ) ?>:</label>
			<!-- <fieldset> -->
			<textarea name="field_elements" rows="8" cols="50" class="text_area" style="width:auto;"><?php echo $row->field_elements ?></textarea>
			<!-- </fieldset> -->
			<br /><input type="text" class="readonly" readonly="readonly" style="width:80%;margin-left:145px;clear:left" value="<?php echo JText::_( 'COM_MTREE_FIELD_ELEMENTS_NOTE' ) ?>" >
			<!-- <br /><div><?php echo JText::_( 'COM_MTREE_FIELD_ELEMENTS_NOTE' ) ?></div> -->
		</li>
		<li>
			<label style="max-width:100%"><?php echo JText::_( 'COM_MTREE_PREFIX_AND_SUFFIX_TEXT_TO_DISPLAY_DURING_FIELD_MODIFICATION' ) ?>:</label>
		</li>
		<li>
			<label>&nbsp;</label>
			<input type="text" class="readonly" readonly="readonly" value="<?php echo JText::_( 'COM_MTREE_PREFIX' ) ?>" >
			<input type="text" size="40" name="prefix_text_mod" class="text_area" value="<?php echo htmlspecialchars($row->prefix_text_mod) ?>" />
		</li>
		<li>
			<label>&nbsp;</label>
			<input type="text" class="readonly" readonly="readonly" value="<?php echo JText::_( 'COM_MTREE_SUFFIX' ) ?>" >
			<input type="text" size="40" name="suffix_text_mod" class="text_area" value="<?php echo htmlspecialchars($row->suffix_text_mod) ?>" />
		</li>
		<li>
			<label style="max-width:100%"><?php echo JText::_( 'COM_MTREE_PREFIX_AND_SUFFIX_TEXT_TO_DISPLAY_DURING_DISPLAY' ) ?>:</label>
		</li>
		<li>
			<label>&nbsp;</label>
			<input type="text" class="readonly" readonly="readonly" value="<?php echo JText::_( 'COM_MTREE_PREFIX' ) ?>" >
			<input type="text" size="40" name="prefix_text_display" class="text_area" value="<?php echo htmlspecialchars($row->prefix_text_display) ?>" />
		</li>
		<li>
			<label>&nbsp;</label>
			<input type="text" class="readonly" readonly="readonly" value="<?php echo JText::_( 'COM_MTREE_SUFFIX' ) ?>" >
			<input type="text" size="40" name="suffix_text_display" class="text_area" value="<?php echo htmlspecialchars($row->suffix_text_display) ?>" /></li>
		<li>
			<label><?php echo JText::_( 'COM_MTREE_SIZE' ) ?>:</label>
			<input type="text" size="40" name="size" class="text_area" value="<?php echo $row->size ?>" />
		</li>
		<li>
			<label><?php echo JText::_( 'COM_MTREE_ALIAS' ) ?>:</label>
			<input type="text" size="40" name="alias" class="text_area" value="<?php echo $row->alias ?>" />
		</li>
		<li>
			<label><?php echo JText::_( 'COM_MTREE_PLACEHOLDER_TEXT' ) ?>:</label>
			<input type="text" size="40" name="placeholder_text" class="text_area" value="<?php echo htmlspecialchars($row->placeholder_text) ?>" />
		</li>
		<li>
			<label><?php echo JText::_( 'COM_MTREE_DEFAULT_CUSTOM_FIELD_VALUE' ) ?>:</label>
			<input type="text" size="40" name="default_value" class="text_area" value="<?php echo htmlspecialchars($row->default_value) ?>" />
		</li>
		<?php if ($row->field_type <> 'corename') { ?>
		<li>
			<label><?php echo JText::_( 'COM_MTREE_PUBLISHED' ) ?>:</label>
			<fieldset class="radio">
			<?php echo $lists['published'] ?>
			</fieldset>
		</li>
		<?php } else { ?><input type="hidden" name="published" value="1"><?php
		} 
		?>
		<li>
			<label><?php echo JText::_( 'COM_MTREE_SHOWN_IN_DETAILS_VIEW' ) ?>:</label>
			<fieldset class="radio">
			<?php echo $lists['details_view'] ?>
			</fieldset>
		</li>
		<li>
			<label><?php echo JText::_( 'COM_MTREE_SHOWN_IN_SUMMARY_VIEW' ) ?>:</label>
			<fieldset class="radio">
			<?php echo $lists['summary_view'] ?>
			</fieldset>
		</li>
		<li>
			<label title="<?php echo JText::_( 'COM_MTREE_TAGGABLE' ) ?>::<?php echo JText::_( 'COM_MTREE_TAGGABLE_TOOLTIP' ) ?>" class="hasTip"><?php echo JText::_( 'COM_MTREE_TAGGABLE' ) ?>:</label>
			<fieldset class="radio">
			<?php echo $lists['tag_search'] ?>
			</fieldset>
		</li>
		<li>
			<label title="<?php echo JText::_( 'COM_MTREE_SIMPLE_SEARCHABLE' ) ?>::<?php echo JText::_( 'COM_MTREE_SIMPLE_SEARCHABLE_TOOLTIP' ) ?>" class="hasTip"><?php echo JText::_( 'COM_MTREE_SIMPLE_SEARCHABLE' ) ?>:</label>
			<fieldset class="radio">
			<?php echo $lists['simple_search'] ?>
			</fieldset>
		</li>
		<li>
			<label><?php echo JText::_( 'COM_MTREE_ADVANCED_SEARCHABLE' ) ?>:</label>
			<fieldset class="radio">
			<?php echo $lists['advanced_search'] ?>
			</fieldset>
		</li>
		<li>
			<label title="<?php echo JText::_( 'COM_MTREE_FILTER_SEARCHABLE' ) ?>::<?php echo JText::_( 'COM_MTREE_FILTER_SEARCHABLE_TOOLTIP' ) ?>" class="hasTip"><?php echo JText::_( 'COM_MTREE_FILTER_SEARCHABLE' ) ?>:</label>
			<fieldset class="radio">
			<?php echo $lists['filter_search'] ?>
			</fieldset>
		</li>
		<li>
			<label><?php echo JText::_( 'COM_MTREE_REQUIRED_FIELD' ) ?>:</label>
			<fieldset class="radio">
			<?php echo $lists['required_field'] ?>
			</fieldset>
		</li>
		<?php if ($row->field_type <> 'corename') { ?>
		<li>
			<label title="<?php echo JText::_( 'COM_MTREE_HIDDEN_FIELD' ) ?>::<?php echo JText::_( 'COM_MTREE_HIDDEN_FIELD_TOOLTIP' ) ?>" class="hasTip"><?php echo JText::_( 'COM_MTREE_HIDDEN_FIELD' ) ?>:</label>
			<fieldset class="radio">
			<?php echo $lists['hidden'] ?>
			</fieldset>
		</li>
		<?php } else { ?><input type="hidden" name="hidden" value="0"><?php
		} 

		if($row->cf_id) { ?>
		<li>
			<label><?php echo JText::_( 'COM_MTREE_ORDERING' ) ?>:</label>
			<?php echo $lists['order'] ?>
		</li>
		<?php } ?>
	</ul>
	</fieldset>
	<fieldset class="panelform" style="background-color:#fff;">
		<legend><?php echo JText::_( 'COM_MTREE_EDIT_FIELD_FIELDS_ASSIGNMENT' ) ?></legend>
		<?php echo JText::_( 'COM_MTREE_EDIT_FIELD_FIELDS_ASSIGNMENT_INSTRUCTIONS' ) ?>
		<button type="button" id="jform_toggle" class="jform-rightbtn" onclick="$$('.chk-menulink').each(function(el) { el.checked = !el.checked; });">
			<?php echo JText::_('JGLOBAL_SELECTION_INVERT'); ?>
		</button>
		
		<ul class="menu-links">
			<li class="menu-link">
			<?php 
				echo '<input type="checkbox" id="category-0" name="fields_map_cats[]"' 
					.	' value="0" class="chk-menulink"'
					.	(in_array(0,$fields_map_cats) ? ' checked="checked"' : '').'/>';
				echo '<label for="category-0">'.JText::_( 'COM_MTREE_ROOT' ).'</label>';
			?>
			</li>
			<?php
			foreach( $cats AS $cat )
			{
				$checked = (in_array($cat->cat_id,$fields_map_cats) ? ' checked="checked"' : '');
				echo '<li class="menu-link" style="clear:both">';
				echo '<input type="checkbox" id="category-'.$cat->cat_id.'" name="fields_map_cats[]"' .
						' value="'.$cat->cat_id.'" class="chk-menulink"'
						.$checked.'/>';
				echo '<label for="category-'.$cat->cat_id.'"> — '.$cat->cat_name.'</label>';
				echo '</li>';
			}
		
			?>
		</ul>
	</fieldset>
	</div>
	
	<?php 
	if( !empty($fieldsets) ):
	?>
	<div class="width-40 fltrt">
	<fieldset class="panelform">
	<legend><?php echo JText::_( 'COM_MTREE_PARAMETERS' ) ?></legend>
	<ul class="adminformlist">
	<?php $hidden_fields = ''; ?>
	<?php 

	foreach ($fieldsets as $fieldset): 
		foreach($form->getFieldset($fieldset->name) AS $field):
		?>
                    <?php if ($field->hidden): ?>
			<?php $hidden_fields.= $field->input; ?>
                    <?php else:?>
		<li>
                           <?php echo $field->label; ?>
			<?php echo $field->input;?>
		</li>
                    <?php endif;?>
               <?php endforeach;?>
     	<?php endforeach; ?>
	</ul>		
	<?php echo $hidden_fields; ?>
	</fieldset>
	</div>
	<?php endif; ?>
	
	<input type="hidden" name="option" value="<?php echo $option; ?>">
	<input type="hidden" name="cf_id" value="<?php echo $row->cf_id; ?>">
	<input type="hidden" name="task" value="save_customfields" />
	<?php echo JHTML::_( 'form.token' ); ?>
	</form>
	<script language="javascript"><!--
	updateInputs(document.adminForm.field_type.value);
	--></script>
	<?php
	}
}
?>