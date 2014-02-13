<?php
/* no direct access*/

defined( '_JEXEC' ) or die( 'Restricted access' );
if(!class_exists('ContentHelperRoute')) require_once (JPATH_SITE . '/components/com_content/helpers/route.php'); 

jimport('joomla.plugin.plugin');
jimport('joomla.html.parameter');

/**
 * plgContentSocialShare
 */  
class plgContentSocialShare  extends JPlugin {
   /**
    * Constructor
    * Loads the plugin settings and assigns them to class variables
    */
    public function __construct(&$subject)
    {
        parent::__construct($subject);
		
        // Loading plugin parameters
		$lr_settings = $this->sociallogin_getsettings();

		//Properties holding plugin settings
		$this->enableshare = (!empty($lr_settings['enableshare']) ? $lr_settings['enableshare'] : "");
        $this->horizontal_rearrange = (!empty($lr_settings['horizontal_rearrange']) ? unserialize($lr_settings['horizontal_rearrange']) : "");
		$this->vertical_rearrange = (!empty($lr_settings['vertical_rearrange']) ? unserialize($lr_settings['vertical_rearrange']) : "");
		$this->horizontal_articles = (!empty($lr_settings['h_articles']) ? unserialize($lr_settings['h_articles']) : "");
		$this->vertical_articles = (!empty($lr_settings['v_articles']) ? unserialize($lr_settings['v_articles']) : "");
        $this->horizontalcounter = (!empty($lr_settings['horizontalcounter']) ? unserialize($lr_settings['horizontalcounter']) : "");
		$this->verticalcounter = (!empty($lr_settings['verticalcounter']) ? unserialize($lr_settings['verticalcounter']) : "");
        $this->sharehorizontal = (!empty($lr_settings['sharehorizontal']) ? $lr_settings['sharehorizontal'] : "");
		$this->sharevertical = (!empty($lr_settings['sharevertical']) ? $lr_settings['sharevertical'] : "");
		$this->shareOnTopPos = (!empty($lr_settings['shareOnTopPos']) ? $lr_settings['shareOnTopPos'] : "");
		$this->shareOnBottomPos = (!empty($lr_settings['shareOnBottomPos']) ? $lr_settings['shareOnBottomPos'] : "");
		$this->sharetitle = (!empty($lr_settings['beforesharetitle']) ? $lr_settings['beforesharetitle'] : "");
		if($this->enableshare == '1'){
			$document = JFactory::getDocument();
			$document->addScript(JURI::root(true).'/plugins/content/socialshare/socialshare.js');
			$document->addScriptDeclaration($this->horizontalShare());
			$document->addScriptDeclaration($this->verticalShare());
		}
    }
	/**
	 * Before display content method
	 */
	public function onContentBeforeDisplay($context, &$article, &$params, $limitstart=0) {
		$beforediv ='';		
		if($this->enableshare == '1'){
			if($this->shareOnTopPos == 'on'){
			$app = JFactory::getApplication();
			if ($this->sharehorizontal == '1') {
				if(is_array($this->horizontal_articles)){
				foreach ($this->horizontal_articles as $key=>$value) {
				  if ($article->id == $value) {
					$sharetitle = '<div style="margin:0;"><b>'.$this->sharetitle.'</b></div>';
					if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']!='Off' && !empty($_SERVER['HTTPS'])):
						$http='https://';
					else:
						$http='http://';
					endif;
						if(!isset($article->language) && empty($article->language)):
							$article->language = 0;
						endif;
						if(!isset($article->catid) && empty($article->catid)):
							$article->catid = 0;
						endif;
					$articlelink = $http.$_SERVER['HTTP_HOST'].JRoute::_(ContentHelperRoute::getArticleRoute($article->id, $article->catid, $article->language));
					$beforediv .= "<div align='left' style='padding-bottom:10px;padding-top:10px;'>".$sharetitle."<div class='lrsharecontainer' data-share-url='" . $articlelink . "'></div></div>";
				  }
				}
			}
		  }
		}
		if ($this->sharevertical == '1') {
			$app = JFactory::getApplication();
			if (is_array($this->vertical_articles)) {
            foreach ($this->vertical_articles as $key=>$value) {
			  if ($article->id == $value) {
				$beforediv .= "<div align='left' style='padding-bottom:10px;padding-top:10px;'><div class='lrverticalsharecontainer'></div></div>";
			}
		  }
		}
	  }
	  return $beforediv;
	}
}
	/**
	 * After display content method
	 */
	public function onContentAfterDisplay($context, &$article, &$params, $limitstart=0) {
			$afterdiv = '';
			if($this->enableshare == '1'){
		if($this->shareOnBottomPos == 'on' && $this->sharehorizontal == '1'){
			$app = JFactory::getApplication();
			if (is_array($this->horizontal_articles)) {
				$afterdiv .= $this->horizontalShare();
			foreach ($this->horizontal_articles as $key=>$value) {
			  if ($article->id == $value) {
				$sharetitle = '<div style="margin:0;"><b>'.$this->sharetitle.'</b></div>';
				if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']!='Off' && !empty($_SERVER['HTTPS'])):
						$http='https://';
					else:
						$http='http://';
					endif;
						if(!isset($article->language) && empty($article->language)):
							$article->language = 0;
						endif;
						if(!isset($article->catid) && empty($article->catid)):
							$article->catid = 0;
						endif;
				$articlelink = $http.$_SERVER['HTTP_HOST'].JRoute::_(ContentHelperRoute::getArticleRoute($article->id, $article->catid, $article->language));
				$afterdiv .= "<div align='left' style='padding-bottom:10px;padding-top:10px;'>".$sharetitle."<div class='lrsharecontainer' data-share-url='" . $articlelink . "'></div></div>";
			  	}
		    	}
			  }
		  return $afterdiv;
			}
		}
	}
	//horizontal interface for social sharing function
	private function horizontalShare() {
      $lr_settings = $this->sociallogin_getsettings();
		  if(isset($lr_settings['sharehorizontal']) && $lr_settings['sharehorizontal'] == '1' && isset($lr_settings['choosehorizontalshare'])){	  
			  if($lr_settings['choosehorizontalshare'] == '0'){
			  		$size = '32';
					$interface = 'horizontal';
			  }elseif($lr_settings['choosehorizontalshare'] == '1'){
					$size = '16';
					$interface = 'horizontal';
			  }elseif($lr_settings['choosehorizontalshare'] == '2'){
					$size = '32';
					$interface = 'simpleimage';
			  }elseif($lr_settings['choosehorizontalshare'] == '3'){
					$size = '16';
					$interface = 'simpleimage';
			  }elseif($lr_settings['choosehorizontalshare'] == '4'){
					$ishorizontal = 'true';
					$interface = 'horizontal';
			  }elseif($lr_settings['choosehorizontalshare'] == '5'){
					$ishorizontal = 'true';
					$interface = 'vertical';
			  }
			 if(isset($size) && !empty($size)){
			$sharescript = 'LoginRadius.util.ready(function () {$i = $SS.Interface.'.$interface.'; $SS.Providers.Top = [';
			$horizontal_rearrange_List=$this->horizontal_rearrange;
				if(empty($horizontal_rearrange_List)){
					$horizontal_rearrange_List[] = 'facebook';
					$horizontal_rearrange_List[] = 'googleplus';
					$horizontal_rearrange_List[] = 'twitter';
					$horizontal_rearrange_List[] = 'linkedin';
					$horizontal_rearrange_List[] = 'pinterest';
				}

			foreach ($horizontal_rearrange_List as $key=>$value) { 
		  		$sharescript .= '"' .$value .'",';
        	}
			$sharescript .= ']; $u = LoginRadius.user_settings; $u.sharecounttype = \'url\'; ';
			if(isset($lr_settings['apikey']) && $lr_settings['apikey'] != ""){
				$sharescript .= '$u.apikey = "' . $lr_settings['apikey'] . '"; ';
			}
			$sharescript .= '$i.size = '.$size.';$i.show("lrsharecontainer"); });';
			}else if(isset($ishorizontal) && !empty($ishorizontal)){
				$sharescript = 'LoginRadius.util.ready(function () { $SC.Providers.Selected = [';
			if(empty($this->horizontalcounter)){
					$this->horizontalcounter[] = 'Facebook Like';
					$this->horizontalcounter[] = 'Twitter Tweet';
					$this->horizontalcounter[] = 'Google+ Share';
					$this->horizontalcounter[] = 'LinkedIn Share';
				}
			if(is_array($this->horizontalcounter)){																																																																				
			foreach ($this->horizontalcounter as $key=>$value) { 
		  		$sharescript .= '"' .$value .'",';
        	}
			}
			$sharescript .= ']; $S = $SC.Interface.simple; $S.isHorizontal = '.$ishorizontal.'; $S.countertype = "'.$interface.'"; $S.show("lrsharecontainer"); });';
			}
			return $sharescript;
		}
}
	
	//vertical interface for social sharing function
	private function verticalShare() {
		$lr_settings = $this->sociallogin_getsettings();
		  	if(isset($lr_settings['sharevertical']) && $lr_settings['sharevertical'] == '1' && isset($lr_settings['chooseverticalshare'])){
			  if($lr_settings['chooseverticalshare'] == '0'){
			  		$size = '32';
					$vinterface = 'Simplefloat';
			  }elseif($lr_settings['chooseverticalshare'] == '1'){
					$size = '16';
					$vinterface = 'Simplefloat';
			  }elseif($lr_settings['chooseverticalshare'] == '2'){
					$isvertical = 'false';
					$vinterface = 'horizontal';	
			  }elseif($lr_settings['chooseverticalshare'] == '3'){
					$isvertical = 'false';
					$vinterface = 'vertical';
			  }

				if ($lr_settings['verticalsharepos'] == '0') {
					$vershretop = (is_numeric($lr_settings['verticalsharetopoffset'])? $lr_settings['verticalsharetopoffset'] : '0').'px';
					$vershreright = '';
					$vershrebottom = '';
					$vershreleft = '0px';
        }
       			else if ($lr_settings['verticalsharepos'] == '1') {
					$vershretop = (is_numeric($lr_settings['verticalsharetopoffset'])? $lr_settings['verticalsharetopoffset'] : '0').'px';
					$vershreright = '0px';
					$vershrebottom = '';
					$vershreleft = '';
        }
         		else if ($lr_settings['verticalsharepos'] == '2') {
					$vershretop = (is_numeric($lr_settings['verticalsharetopoffset']) ? $lr_settings['verticalsharetopoffset'].'px' : '0px');
					$vershreright = '';
					$vershrebottom = (is_numeric($lr_settings['verticalsharetopoffset']) ? '' : '0px');
					$vershreleft = '0px';
        }
        		else if ($lr_settings['verticalsharepos'] == '3') {
					$vershretop = (is_numeric($lr_settings['verticalsharetopoffset']) ? $lr_settings['verticalsharetopoffset'].'px' : '0px');
					$vershreright = '0px';
					$vershrebottom = (is_numeric($lr_settings['verticalsharetopoffset']) ? '' : '0px');
					$vershreleft = '';
		}
				else {
					$vershretop = (is_numeric($lr_settings['verticalsharetopoffset']) && is_numeric($lr_settings['verticalsharetopoffset']) ? $lr_settings['verticalsharetopoffset'] : '0px').'px';
					$vershreright = '';
					$vershrebottom = '';
					$vershreleft = '';
		}
		
			 if(isset($size) && !empty($size)){
			 $vsharescript = 'LoginRadius.util.ready(function () {$i = $SS.Interface.'.$vinterface.'; $SS.Providers.Top = [';
			$vertical_rearrange_List = $this->vertical_rearrange;
				if(empty($vertical_rearrange_List)){
					$vertical_rearrange_List[] = 'facebook';
					$vertical_rearrange_List[] = 'googleplus';
					$vertical_rearrange_List[] = 'twitter';
					$vertical_rearrange_List[] = 'linkedin';
					$vertical_rearrange_List[] = 'pinterest';
				}

			foreach ($vertical_rearrange_List as $key=>$value) { 
		  		$vsharescript .= '"' .$value .'",';
        	}
			$vsharescript .=']; $u = LoginRadius.user_settings; ';
			if(isset($lr_settings['apikey']) && $lr_settings['apikey'] != ""){
				$vsharescript .= '$u.apikey = "' . $lr_settings['apikey'] . '"; ';
			}
			$vsharescript .= '$i.size = '.$size.';$i.left = "'.$vershreleft.'"; $i.top = "'.$vershretop.'";$i.right = "'.$vershreright.'";$i.bottom = "'.$vershrebottom.'"; $i.show("lrverticalsharecontainer"); });';
			}else if(isset($isvertical) && !empty($isvertical)){
				$vsharescript = 'LoginRadius.util.ready(function () { $SC.Providers.Selected = [';
			if(empty($this->verticalcounter)){
					$this->verticalcounter[] = 'Facebook Like';
					$this->verticalcounter[] = 'Twitter Tweet';
					$this->verticalcounter[] = 'Google+ Share';
					$this->verticalcounter[] = 'LinkedIn Share';
				}
			if(is_array($this->verticalcounter)){
			foreach ($this->verticalcounter as $key=>$value) { 
		  		$vsharescript .= '"' .$value .'",';
        	}
			}
			$vsharescript .= ']; $S = $SC.Interface.simple; $S.isHorizontal = '.$isvertical.'; $S.countertype = "'.$vinterface.'"; $S.left = "'.$vershreleft.'"; $S.top = "'.$vershretop.'";$S.right = "'.$vershreright.'";$S.bottom = "'.$vershrebottom.'"; $S.show("lrverticalsharecontainer"); });';
			}
		return $vsharescript;
		}
	}

/**
 * Get the databse settings.
 */

	private function sociallogin_getsettings () {
      $lr_settings = array ();
      $db = JFactory::getDBO ();
	  $sql = "SELECT * FROM #__LoginRadius_settings";
      $db->setQuery ($sql);
      $rows = $db->LoadAssocList ();
      if (is_array ($rows)) {
        foreach ($rows AS $key => $data) {
          $lr_settings [$data ['setting']] = $data ['value'];
        }
      }
      return $lr_settings;
    }
}  