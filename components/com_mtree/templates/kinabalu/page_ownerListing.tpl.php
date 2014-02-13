 
<h2 class="contentheading"><?php 
	if( $this->my->id == $this->owner->id ) {
		echo JText::_( 'COM_MTREE_MY_PAGE' ) ?> (<?php echo $this->owner->username ?>)<?php
	} else {
		echo $this->owner->username;
	}
?></h2>
<?php include $this->loadTemplate('sub_ownerProfile.tpl.php'); ?>
<div class="users-tab">
<div class="users-listings-active"><span><?php echo JText::_( 'COM_MTREE_LISTINGS' ) ?></span>(<?php echo $this->pageNav->total  ?>)</div>
<?php if($this->mtconf['show_review']) { ?><div class="users-reviews"><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=viewusersreview&user_id=".$this->owner->id."&Itemid=$this->Itemid") ?>"><?php echo JText::_( 'COM_MTREE_REVIEWS' ) ?></a>(<?php echo $this->total_reviews ?>)</div><?php } ?>
<?php if($this->mtconf['show_favourite']) { ?><div class="users-favourites"><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=viewusersfav&user_id=".$this->owner->id."&Itemid=$this->Itemid") ?>"><?php echo JText::_( 'COM_MTREE_FAVOURITES' ) ?></a>(<?php echo $this->total_favourites ?>)</div><?php } ?>
</div>
<div id="listings"><?php
if (is_array($this->links) && !empty($this->links)) {

	?>
	<div class="pages-links">
		<span class="xlistings"><?php echo $this->pageNav->getResultsCounter(); ?></span>
		<?php echo $this->pageNav->getPagesLinks(); ?>
	</div>
	<?php
	$i = 0;
	foreach ($this->links AS $link) {
		$i++;
		$link_fields = $this->links_fields[$link->link_id];
		include $this->loadTemplate('sub_listingSummary.tpl.php');
	}
	
	if( $this->pageNav->total > $this->pageNav->limit ) {
		?>
		<div class="pages-links">
			<span class="xlistings"><?php echo $this->pageNav->getResultsCounter(); ?></span>
			<?php echo $this->pageNav->getPagesLinks(); ?>
		</div>
		<?php
	}

} else {

	?><center><?php
	
	if( $this->my->id == $this->owner->id ) {
		echo JText::_( 'COM_MTREE_YOU_DO_NOT_HAVE_ANY_LISTINGS' );
	} else {
		echo JText::_( 'COM_MTREE_THIS_USER_DO_NOT_HAVE_ANY_LISTINGS' );
	}
	
	?></center><?php
	
} ?></div>