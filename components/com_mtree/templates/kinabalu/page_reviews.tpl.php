<div id="listing" class="reviews">

<h2><?php echo JText::_('COM_MTREE_USER_REVIEWS'); ?></h2>
<h2><?php 
$link_name = $this->fields->getFieldById(1);
$this->plugin( 'ahreflisting', $this->link, $link_name->getOutput(1), '', array("delete"=>true) ) ?></h2>

<?php
include $this->loadTemplate( 'sub_reviews.tpl.php' );	
?>

</div>