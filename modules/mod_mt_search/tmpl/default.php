<?php defined('_JEXEC') or die('Restricted access'); ?>
<form action="index.php" method="post">
	<div class="search<?php echo $moduleclass_sfx; ?>">
	<input type="text" id="mod_mt_search_searchword<?php echo $parent_cat_id; ?>" name="searchword" maxlength="<?php echo $mtconf->get('limit_max_chars'); ?>" class="inputbox" size="<?php echo $width; ?>" value="<?php echo $searchword; ?>"  placeholder="<?php echo $placeholder_text; ?>" />
	<?php if( $lists['categories'] ) { ?>
	<br /><?php echo $lists['categories'];
	} ?>
	
	<?php if ( $search_button ) { ?>
		<br /><input type="submit" value="<?php echo JText::_( 'MOD_MT_SEARCH_SEARCH' ) ?>" class="button" />
	<?php } ?>

	<?php if ( $advsearch ) { ?>
		<br /><a href="<?php echo $advsearch_link; ?>"><?php echo JText::_( 'MOD_MT_SEARCH_ADVANCED_SEARCH' ) ?></a>
	<?php } ?>
	<input type="hidden" name="option" value="com_mtree" />
	<input type="hidden" name="task" value="search" />
	<?php if ( $searchCategory == 1 ) { ?>
	<input type="hidden" name="search_cat" value="1" />
	<?php } ?>
	</div>
</form>