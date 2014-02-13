<?php
/**
 * @version	$Id: mod_mt_alphaindex.php 1621 2012-11-08 07:26:35Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

require( JPATH_ROOT . DS.'components'.DS.'com_mtree'.DS.'init.module.php');
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mtree'.DS.'mfields.class.php' );
require_once( dirname(__FILE__).DS.'helper.php' );

if( !$moduleHelper->isModuleShown() ) { return; }

# Get params
$moduleclass		= $params->get( 'moduleclass',		'mainlevel'	);
$direction		= $params->get( 'direction',		'vertical'	);
$show_number		= $params->get( 'show_number',		1	);
$display_total_links	= $params->get( 'display_total_links',	0	);
$show_empty		= $params->get( 'show_empty',		0	);
$seperator		= $params->get( 'seperator',		'&nbsp;'	);
$moduleclass_sfx	= $params->get( 'moduleclass_sfx',	''	);

$list	= modMTAlphaindexHelper::getList($params);

if( $direction == 'horizontal' ) {
	require(JModuleHelper::getLayoutPath('mod_mt_alphaindex', $direction));
} else {
	require(JModuleHelper::getLayoutPath('mod_mt_alphaindex'));
}
?>