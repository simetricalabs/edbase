<?php
/*
$cust_1 = $this->fields->getFieldByCaption('Custom Text'); // getFieldByCaption() allow you to get the field by the Caption. This is not the best way to get a field since changing the caption in the back-end will break the reference.
echo '<br />Field ID: ' . $cust_1->getId();

$cust_2 = $this->fields->getFieldById(29);  // getFieldById() is the ideal way of getting a field. The ID can be found at 'Custom Fields' section in Mosets Tree's back-end.
echo '<br />Name: ' . $cust_2->getName();
echo '<br />Has Caption? ' . (($cust_2->hasCaption()) ? 'Yes' : 'No');
echo '<br />Caption: ' . $cust_1->getCaption();
echo '<br />Value: ' . $cust_2->getValue();
echo '<br />Output: ' . $cust_2->getOutput(1);
echo '<hr />';
$this->fields->resetPointer();
while( $this->fields->hasNext() ) {
	$field = $this->fields->getField();
	echo '<br /><strong>' . $field->getCaption() . '</strong>';
	echo ': ';
	echo $field->getOutput(1); // getOutput() returns the formatted value of the field. ie: For a youtube video, the youtube player will be loaded
	// echo $field->getValue(); // getValue() returns the raw value without additional formatting. ie: When getting value from a Online Video field type, it will return the URL.
	$this->fields->next();
}
*/
	
?> 

<div id="listing" class="link-id-<?php echo $this->link_id; ?> cat-id-<?php echo $this->link->cat_id; ?> tlcat-id-<?php echo $this->link->tlcat_id; ?>" itemscope itemtype="http://schema.org/Thing">

<h2><?php 
$link_name = $this->fields->getFieldById(1);
$this->plugin( 'ahreflisting', $this->link, $link_name->getOutput(1), '', array("delete"=>true,"link"=>false) ) 
?></h2>

<?php
if ( !empty($this->mambotAfterDisplayTitle) ) { 
	echo trim( implode( "\n", $this->mambotAfterDisplayTitle ) );
}

if ( !empty($this->mambotBeforeDisplayContent) && $this->mambotBeforeDisplayContent[0] <> '' ) { 
	echo trim( implode( "\n", $this->mambotBeforeDisplayContent ) ); 
}
echo '<div class="column first">';

echo '<div class="listing-desc">';
if ($this->config->getTemParam('skipFirstImage','0') == 1) {
	array_shift($this->images);
}

if(!is_null($this->fields->getFieldById(2))) { 
	$link_desc = $this->fields->getFieldById(2);
	echo '<span itemprop="description">';
	if( $link_desc->hasValue() )
	{
		echo $link_desc->getDisplayPrefixText(); 
		echo $link_desc->getOutput(1);
		echo $link_desc->getDisplaySuffixText(); 
	}
	echo '</span>';
}
echo '</div>';

if ( !empty($this->mambotAfterDisplayContent) ) { echo trim( implode( "\n", $this->mambotAfterDisplayContent ) ); }

if( $this->config->get('show_favourite') == 1 || $this->config->get('show_rating') == 1 )
{
	echo '<div class="rating-fav">';
	if($this->config->get('show_rating')) {
		echo '<div class="rating">';
		$this->plugin( 'ratableRating', $this->link, $this->link->link_rating, $this->link->link_votes); 
		echo '<div id="total-votes">';
		if( $this->link->link_votes <= 1 ) {
			echo $this->link->link_votes . " " . strtolower(JText::_( 'COM_MTREE_VOTE' ));
		} elseif ($this->link->link_votes > 1 ) {
			echo $this->link->link_votes . " " . strtolower(JText::_( 'COM_MTREE_VOTES' ));
		}
		echo '</div>';
		echo '</div>';
	}

	if($this->config->get('show_favourite')) {
	?>
	<div class="favourite">
	<span class="fav-caption"><?php echo JText::_( 'COM_MTREE_FAVOURED' ) ?>:</span>
	<div id="fav-count"><?php echo number_format($this->total_favourites,0,'.',',') ?></div><?php 
		if($this->my->id > 0){ 
			if($this->is_user_favourite) {
				?><div id="fav-msg"><a href="javascript:fav(<?php echo $this->link->link_id ?>,-1);"><?php echo JText::_( 'COM_MTREE_REMOVE_FAVOURITE' ) ?></a></div><?php 
			} else {
				?><div id="fav-msg"><a href="javascript:fav(<?php echo $this->link->link_id ?>,1);"><?php echo JText::_( 'COM_MTREE_ADD_AS_FAVOURITE' ) ?></a></div><?php 
				}
		} ?>
	</div><?php
	}
	echo '</div>';
}

echo '</div>';

echo '<div class="column second">';

if (!empty($this->images)) include $this->loadTemplate( 'sub_images.tpl.php' );

echo '<h3>';
echo JText::_('COM_MTREE_LISTING_DETAILS');
echo '</h3>';
// Address
$address = '';
if( $this->config->getTemParam('displayAddressInOneRow','1') ) {
	$address_parts = array();
	$address_displayed = false;
	foreach( array( 4,5,6,7,8 ) AS $address_field_id )
	{
		$field = $this->fields->getFieldById($address_field_id);
		if( isset($field) && $output = $field->getOutput(1) )
		{
			$address_parts[] = $output;
		}
	}
	if( !empty($address_parts) ) { $address = implode(', ',$address_parts); }
}

// Other custom fields
echo '<ul class="fields">';
$number_of_columns = $this->config->getTemParam('numOfColumnsInDetailsView','1');
$field_count = 0;
$need_div_closure = false;

$this->fields->resetPointer();
while( $this->fields->hasNext() ) {
	$field = $this->fields->getField();
	$value = $field->getValue();
	$hasValue = $field->hasValue();
	if( 
		( 
			(
				(!$field->hasInputField() && !$field->isCore() && empty($value)) 
				||
				(!empty($value) || $value == '0')
			)
			&& 
			// This condition ensure that fields listed in array() are skipped
			!in_array($field->getName(),array('link_name','link_desc'))
			&&
			(
				(
					$this->config->getTemParam('displayAddressInOneRow','1') == 1
					&& 
					!in_array($field->getId(),array(5,6,7,8)) 
				)
				||
				$this->config->getTemParam('displayAddressInOneRow','1') == 0
			)
			&&
			$hasValue
		) 
		||
		// Fields in array() are always displayed regardless of its value.
		in_array($field->getName(),array('link_featured'))
	) {
		echo '<li id="field_'.$field->getId().'" class="fieldRow'.(($field_count % $number_of_columns == ($number_of_columns -1))?' lastFieldRow':'').'" style="width:'.floor(98/intval($number_of_columns)).'%">';
		
		if($this->config->getTemParam('displayAddressInOneRow','1') && in_array($field->getId(),array(4,5,6,7,8)) && $address_field = $this->fields->getFieldById(4)) {
			if( $address_displayed == false ) {
				echo '<div class="caption">';
				if($address_field->hasCaption()) {
					echo $address_field->getCaption();
				}
				echo '</div>';
				echo '<div class="output">';
				echo $address_field->getDisplayPrefixText(); 
				echo $address;
				echo $address_field->getDisplaySuffixText(); 
				echo '</div>';
				$address_displayed = true;
			}
		} else {
			echo '<div class="caption">';
			if($field->hasCaption()) {
				echo $field->getCaption();
			}
			echo '</div>';
			echo '<div class="output">';
			switch($field->getFieldType()) {
				case 'mfile':
					echo $field->getDisplayPrefixText(); 
					echo '<p class="mbutton">';
					echo $field->getOutput(1);
					echo '<p>';
					echo $field->getDisplaySuffixText(); 
					break;

				case ( $field->getFieldType() == 'coreprice' && $field->getValue() == 0 ):
					echo $field->getOutput(1);
					break;

				default:
					echo $field->getDisplayPrefixText(); 
					echo $field->getOutput(1);
					echo $field->getDisplaySuffixText(); 
			}
			echo '</div>';
		}
		echo '</li>';

		$field_count++;
	}
	$this->fields->next();
}

echo '</ul>';

echo '</div>';

if( $this->show_actions_rating_fav ) {
	?>
	<div class="actions-rating-fav">
	<?php if( $this->show_actions ) { ?>
	<div class="actions">
	<?php 
		$this->plugin( 'ahrefreview', $this->link, array("rel"=>"nofollow") ); 
		$this->plugin( 'ahrefrecommend', $this->link, array("rel"=>"nofollow") );	
		$this->plugin( 'ahrefprint', $this->link, array("rel"=>"nofollow") );
		$this->plugin( 'ahrefcontact', $this->link, array("rel"=>"nofollow") );
		$this->plugin( 'ahrefvisit', $this->link, '', 1, array("rel"=>"nofollow") );
		$this->plugin( 'ahrefreport', $this->link, array("rel"=>"nofollow") );
		$this->plugin( 'ahrefclaim', $this->link, array("rel"=>"nofollow") );
		$this->plugin( 'ahrefownerlisting', $this->link );
		$this->plugin( 'ahrefmap', $this->link, array("rel"=>"nofollow") );
	?></div><?php
	}
?></div><?php 
}

// Load User Profile
if( $this->config->get('show_user_profile_in_listing_details') )
{
	include $this->loadTemplate( 'sub_userProfile.tpl.php' );
}

// Load Contact Owner Form
if( $this->config->get('contact_form_location') == 2 )
{
	include $this->loadTemplate( 'sub_contactOwnerForm.tpl.php' );
}

?>
</div>