<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.mtForm;
		if (pressbutton == 'cancel') {
			form.task.value='viewlink';
			form.submit();
			return;
		}

	<?php if( $this->user_id <= 0 ) { ?>
		// do field validation
		if (form.your_name.value == ""){
			alert( "<?php echo JText::_( 'COM_MTREE_PLEASE_FILL_IN_THE_FORM' ) ?>" );
		} else {
	<?php } ?>
			form.task.value=pressbutton;
			try {
				form.onsubmit();
				}
			catch(e){}
			form.submit();
	<?php if( $this->user_id <= 0 ) { ?>
		}
	<?php } ?>
	}
</script>

 
<h2 class="contentheading"><?php echo JText::_( 'COM_MTREE_REPORT_LISTING' ) . ' - ' . $this->link->link_name; ?></h2>

<div id="listing">
<form action="<?php echo JRoute::_("index.php") ?>" method="post" name="mtForm" id="mtForm">
<table border="0" cellpadding="3" cellspacing="0" width="100%">
	<?php if( $this->user_id <= 0 ) { ?>
	<tr>
		<td width="20%"><?php echo JText::_( 'COM_MTREE_YOUR_NAME' ) ?>:</td>
		<td width="80%"><input type="text" name="your_name" class="inputbox" size="40" value="<?php echo $this->user_fields_data['your_name']; ?>" /></td>
	</tr>
	<?php } ?>
	<tr>
		<td><?php echo JText::_( 'COM_MTREE_REPORT_PROBLEM' ) ?>:</td>
		<td>
			<select name="report_type">
			<?php echo $this->plugin( "options", $this->report_types, $this->user_fields_data['report_type'] ); ?>
			</select>
		</td>
	</tr>
	<tr><td colspan="2"><b><?php echo JText::_( 'COM_MTREE_MESSAGE' ) ?>:</b></td></tr>
	<tr><td colspan="2"><textarea name="message" rows="8" cols="69" class="inputbox"><?php echo $this->user_fields_data['message']; ?></textarea></td></tr>
	<?php if( $this->config->get('use_captcha_report') ) { ?>
	<tr>
		<td colspan="2">
			<?php echo JText::_( 'COM_MTREE_CAPTCHA_LABEL' ) ?>:
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?php echo $this->captcha_html; ?>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="2">
			<input type="hidden" name="option" value="<?php echo $this->option ?>" />
			<input type="hidden" name="task" value="send_report" />
			<input type="hidden" name="link_id" value="<?php echo $this->link->link_id ?>" />
			<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ?>" />
			<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
			<input type="button" value="<?php echo JText::_( 'COM_MTREE_SEND' ) ?>" onclick="javascript:submitbutton('send_report')" class="button" /> <input type="button" value="<?php echo JText::_( 'COM_MTREE_CANCEL' ) ?>" onclick="javascript:submitbutton('cancel')" class="button" />
		</td>
	</tr>
</table>
</form>
</div>