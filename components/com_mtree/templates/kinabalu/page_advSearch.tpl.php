<h2 class="contentheading"><?php echo JText::_( 'COM_MTREE_ADVANCED_SEARCH' ) ?></h2>

<form action="<?php echo JRoute::_("index.php") ?>" method="get" name="mtForm" id="mtForm">
<table class="advanced-search" width="100%" cellpadding="4" cellspacing="10" border="0" align="center">
	<tr>
		<td colspan="2"><?php printf(JText::_( 'COM_MTREE_RETURN_RESULTS_IF_X_OF_THE_FOLLOWING_CONDITIONS_ARE_MET' ),$this->lists['searchcondition']); ?></td>
	</tr>
	<tr>
		<td colspan="2">
		<input type="submit" value="<?php echo JText::_( 'COM_MTREE_SEARCH' ) ?>" class="button" />	<input type="reset" value="<?php echo JText::_( 'COM_MTREE_RESET' ) ?>" class="button" /></td>
	</tr>
	<?php if( isset($this->catlist) ) { ?>
	<tr>
		<td width="20%"><?php echo JText::_( 'COM_MTREE_CATEGORY' ) ?>:</td>
		<td width="80%"><?php echo $this->catlist; ?></td>
	</tr>
	<?php
	}

	while( $this->fields->hasNext() ) {
		$field = $this->fields->getField();
		if($field->hasSearchField()) {
			echo '<tr>';
			echo '<td width="20%" valign="top" align="left">' . $field->caption . ':' . '</td>';
			echo '<td width="80%" align="left">';
			echo $field->getSearchHTML();
			echo '</td>';
			echo '</tr>';
		}
		$this->fields->next();
	}
	?>
	<tr>
		<td colspan="2">
		<input type="submit" value="<?php echo JText::_( 'COM_MTREE_SEARCH' ) ?>" class="button" />	<input type="reset" value="<?php echo JText::_( 'COM_MTREE_RESET' ) ?>" class="button" /></td>
	</tr>
</table>
<?
if( !$this->hasCategoryCF && !isset($this->catlist) ) {
?><input type="hidden" name="cat_id" value="<?php echo $this->cat_id ?>" /><?php	
}
?>
<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ?>" />
<input type="hidden" name="option" value="com_mtree" />
<input type="hidden" name="task" value="listall" />
</form>