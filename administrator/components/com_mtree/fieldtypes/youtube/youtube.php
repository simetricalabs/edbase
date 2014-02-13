<?php
/**
 * @version	$Id: youtube.php 1390 2012-03-28 05:09:42Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_youtube extends mFieldType {

	function getOutput() {
		$html ='';
		$id = $this->getVideoId();

		$params['youtubeWidth'] = $this->getParam('youtubeWidth',560);
		$params['youtubeHeight'] = $this->getParam('youtubeHeight',315);
		
		$html .= '<object width="' . $params['youtubeWidth'] . '" height="' . $params['youtubeHeight'] . '">';
		$html .= '<param name="movie" value="http://www.youtube.com/v/' . $id . '"></param>';
		$html .= '<param name="wmode" value="transparent"></param>';
		$html .= '<embed src="http://www.youtube.com/v/' . $id . '" type="application/x-shockwave-flash" wmode="transparent" width="' . $params['youtubeWidth'] . '" height="' . $params['youtubeHeight'] . '"></embed>';
		$html .= '</object>';

		return $html;
	}
	
	function getVideoId() {
		$value = $this->getValue();
		$id = null;
		
		if(empty($value))
		{
			return null;
		}
		$url = parse_url($value);
		
		if( $url['host'] == 'youtu.be' )
		{
			$id = substr($url['path'],1);
		}
		else
		{
			parse_str($url['query'], $query);
			if (isset($query['v'])) {
		        	$id = $query['v'];
			}
		}

		return $id;
	}
	
	function getInputHTML() {
		$youtubeInputDescription = $this->getParam('youtubeInputDescription','Enter the full URL of the Youtube video page.<br />ie: <b>http://youtube.com/watch?v=OHpANlSG7OI</b>');

		$html = '';
		$html .= '<input class="text_area" type="text" name="' . $this->getInputFieldName(1) . '" id="' . $this->getInputFieldName(1) . '" size="' . $this->getSize() . '" value="' . htmlspecialchars($this->getInputValue()) . '" />';
		
		if(!empty($youtubeInputDescription))
		{
			$html .= '<p>' . $youtubeInputDescription . '</p>';
		}

		return $html;
	}
	
	function getSearchHTML( $showSearchValue=false, $showPlaceholder=false )
	{
		$checkboxLabel = $this->getParam('checkboxLabel','Contains video');
		$checkbox_value = $this->getSearchValue();
		
		$html = '';
		$html .= '<input class="text_area" type="checkbox" name="' . $this->getSearchFieldName(1) . '"';
		$html .=' value="1"';
		$html .=' id="' . $this->getSearchFieldName(1) . '"';
		if( $showSearchValue && $checkbox_value == 1 ) {
			$html .= ' checked';
		}
		$html .= ' />';
		$html .= '&nbsp;';
		$html .= '<label for="' . $this->getName() . '">';
		$html .= $checkboxLabel;
		$html .= '</label>';
		return $html;
	}
	
	function getWhereCondition() {
		if( func_num_args() == 0 ) {
			return null;
		} else {
			return '(cfv#.value <> \'\')';
		}
	}
}
?>