<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.mtForm;
		if (pressbutton == 'cancel') {
			submitform( 'viewlink' );
			return;
		}
		if (form.rev_text.value == ""){
			alert( "<?php echo JText::_( 'COM_MTREE_PLEASE_FILL_IN_REVIEW' ) ?>" );
		} else if (form.rev_title.value == ""){
			alert( "<?php echo JText::_( 'COM_MTREE_PLEASE_FILL_IN_TITLE' ) ?>" );
		<?php
		if( 
			$this->config->get('require_rating_with_review')
			&& 
			$this->config->get('allow_rating_during_review') 
			&&
			(
				$this->config->get('user_rating') == '0'
				||
				($this->config->get('user_rating') == '1' && $this->my->id > 0)
				||
				($this->config->get('user_rating') == '2' && $this->my->id > 0 && $this->my->id != $this->link->user_id)
			)
		) {			
			echo '} else if (form.rating.value == ""){ alert("' . JText::_( 'COM_MTREE_PLEASE_FILL_IN_RATING' ) . '"); ';
		}		
		?>} else {
			form.submit();
		}
	}
</script>
 
<h2 class="contentheading"><?php echo JText::_( 'COM_MTREE_WRITE_REVIEW' ) . ' - ' . $this->link->link_name; ?></h2>

<div id="listing">

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="mtForm" id="mtForm">
<table border="0" cellpadding="3" cellspacing="0" width="100%">
	<?php if ( !($this->my->id > 0) ) { ?>
	<tr>
		<td align="left">
			<?php echo JText::_( 'COM_MTREE_YOUR_NAME' ) ?>:
		</td>
	</tr>
	<tr>
		<td align="left">
			<input type="text" name="guest_name" class="inputbox" size="20" value="<?php echo $this->user_fields_data['guest_name']; ?>" />
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td align="left">
			<?php echo JText::_( 'COM_MTREE_REVIEW_TITLE' ) ?>:
		</td>
	</tr>
	<tr>
		<td align="left">
			<input type="text" name="rev_title" class="inputbox" size="69" value="<?php echo $this->user_fields_data['rev_title']; ?>" />
		</td>
	</tr>
	<tr>
		<td align="left">
			<?php
			if( 
				$this->config->get('allow_rating_during_review') 
				&&
				(
					$this->config->get('user_rating') == '0'
					||
					($this->config->get('user_rating') == '1' && $this->my->id > 0)
					||
					($this->config->get('user_rating') == '2' && $this->my->id > 0 && $this->my->id != $this->link->user_id)
				)
			) {
			?>
			<select name="rating" class="inputbox">
			<?php echo $this->plugin( "options", $this->rating_options, $this->user_fields_data['rating'] ); ?>
			</select>
			<?php } ?>
		</td>
	</tr>
	<tr>
		<td align="left">
			<?php echo JText::_( 'COM_MTREE_REVIEW' ) ?>:
		</td>
	</tr>
	<tr>
		<td align="left">
			<?php $this->plugin('textarea', 'rev_text', $this->user_fields_data['rev_text'], 8, 69, 'class="inputbox"'); ?>
		</td>
	</tr>
	<?php if( $this->config->get('use_captcha_review') ) { ?>
	<tr>
		<td align="left">
			<?php echo JText::_( 'COM_MTREE_CAPTCHA_LABEL' ) ?>:
		</td>
	</tr>
	<tr>
		<td align="left">
			<?php echo $this->captcha_html; ?>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td align="left">
			<br /><br />
			<input type="hidden" name="option" value="<?php echo $this->option ?>" />
			<input type="hidden" name="task" value="addreview" />
			<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ?>" />
			<input type="hidden" name="link_id" value="<?php echo $this->link->link_id ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
			<input type="button" value="<?php echo JText::_( 'COM_MTREE_ADD_REVIEW' ) ?>" onclick="javascript:submitbutton('addreview')" class="button" /> <input type="button" value="<?php echo JText::_( 'COM_MTREE_CANCEL' ) ?>" onclick="history.back();" class="button" />
		</td>
	</tr>
</table>
</form>

</div>