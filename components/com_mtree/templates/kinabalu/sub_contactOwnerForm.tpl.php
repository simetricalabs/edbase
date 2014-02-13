	<form id="contact-form" action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate">
		<div class="title"><?php echo MText::_( 'CONTACT_OWNER', $this->tlcat_id ); ?></div>
		<fieldset>
			<legend><?php echo JText::_('COM_MTREE_LISTING_CONTACT_FORM_LABEL'); ?></legend>
			<dl>
				<dt><?php echo $this->form->getLabel('contact_name'); ?></dt>
				<dd><?php echo $this->form->getInput('contact_name'); ?></dd>
	
				<dt><?php echo $this->form->getLabel('contact_email'); ?></dt>
				<dd><?php echo $this->form->getInput('contact_email'); ?></dd>
	
				<?php //Dynamically load any additional fields from plugins. ?>
				<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
				        <?php if ($fieldset->name != 'contact'):?>
				               <?php $fields = $this->form->getFieldset($fieldset->name);?>
				               <?php foreach($fields as $field): ?>
				                    <?php if ($field->hidden): ?>
				                         <?php echo $field->input;?>
				                    <?php else:?>
				                         <dt>
				                            <?php echo $field->label; ?>
				                         </dt>
				                         <dd><?php echo $field->input;?></dd>
				                    <?php endif;?>
				               <?php endforeach;?>
			        <?php endif ?>
			     	<?php endforeach;?>

				<dt><?php echo $this->form->getLabel('contact_message'); ?></dt>
				<dd><?php echo $this->form->getInput('contact_message'); ?></dd>

				<?php if( $this->config->get('use_captcha_contact') ) { ?>
				<dt>
					<label class="required" for="recaptcha_response_field" id="recaptcha_response_field-lbl">
					<?php echo JText::_( 'COM_MTREE_CAPTCHA_LABEL' ) ?><span class="star">&nbsp;*</span>
					</label>
				</dt>
				<dd><?php echo $this->captcha_html; ?></dd>
				<?php } ?>
				
				<dt></dt>
				<dd>
					<input type="hidden" name="option" value="<?php echo $this->option ?>" />
					<input type="hidden" name="task" value="send_contact" />
					<input type="hidden" name="link_id" value="<?php echo $this->link->link_id ?>" />
					<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ?>" />
					<?php echo JHTML::_( 'form.token' ); ?>
					<button class="button validate" type="submit"><?php echo JText::_('COM_MTREE_SEND'); ?></button>
				</dd>
	
		</dl>
		</fieldset>
	</form>