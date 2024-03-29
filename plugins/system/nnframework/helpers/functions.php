<?php
/**
 * NoNumber Framework Helper File: Functions
 *
 * @package         NoNumber Framework
 * @version         14.1.1
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * Framework Functions
 */

class NNFrameworkFunctions
{
	var $_version = '14.1.1';

	public function getByUrl($url, $options = array())
	{
		// only allow url calls from administrator
		if (!JFactory::getApplication()->isAdmin())
		{
			die;
		}

		// only allow when logged in
		$user = JFactory::getUser();
		if (!$user->id)
		{
			die;
		}

		if (substr($url, 0, 4) != 'http')
		{
			$url = 'http://' . $url;
		}

		// only allow url calls to nonumber.nl domain
		if (!(preg_match('#^https?://([^/]+\.)?nonumber\.nl/#', $url)))
		{
			die;
		}

		// only allow url calls to certain files
		if (
			strpos($url, 'download.nonumber.nl/extensions.php') === false
		)
		{
			die;
		}

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-type: text/xml");

		return self::getContents($url);
	}

	public function getContents($url, $fopen = 0)
	{
		$html = '';
		if ((!$fopen || !ini_get('allow_url_fopen')) && function_exists('curl_init') && function_exists('curl_exec'))
		{
			$html = $this->curl($url);
		}
		else if (ini_get('allow_url_fopen'))
		{
			$file = @fopen($url, 'r');
			if ($file)
			{
				$html = array();
				while (!feof($file))
				{
					$html[] = fgets($file, 1024);
				}
				$html = implode('', $html);
			}
		}

		return $html;
	}

	protected function curl($url)
	{
		$timeout = JFactory::getApplication()->input->getInt('timeout', 3);
		$timeout = min(array(30, max(array(3, $timeout))));

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'NoNumber/' . $this->_version);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

		jimport('joomla.filesystem.file');
		if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_nonumbermanager/nonumbermanager.php'))
		{
			$config = JComponentHelper::getParams('com_nonumbermanager');
			if ($config && $config->get('use_proxy', 0) && $config->get('proxy_host'))
			{
				curl_setopt($ch, CURLOPT_PROXY, $config->get('proxy_host') . ':' . $config->get('proxy_port'));
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $config->get('proxy_login') . ':' . $config->get('proxy_password'));
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			}
		}

		//follow on location problems
		if (ini_get('open_basedir') == '' && ini_get('safe_mode') != '1' && ini_get('safe_mode') != 'On')
		{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$html = curl_exec($ch);
		}
		else
		{
			$html = $this->curl_redir_exec($ch);
		}
		curl_close($ch);
		return $html;
	}

	protected function curl_redir_exec($ch)
	{
		static $curl_loops = 0;
		static $curl_max_loops = 20;

		if ($curl_loops++ >= $curl_max_loops)
		{
			$curl_loops = 0;
			return false;
		}

		curl_setopt($ch, CURLOPT_HEADER, true);
		$data = curl_exec($ch);

		list($header, $data) = explode("\n\n", str_replace("\r", '', $data), 2);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($http_code == 301 || $http_code == 302)
		{
			$matches = array();
			preg_match('/Location:(.*?)\n/', $header, $matches);
			$url = @parse_url(trim(array_pop($matches)));
			if (!$url)
			{
				//couldn't process the url to redirect to
				$curl_loops = 0;
				return $data;
			}
			$last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
			if (!$url['scheme'])
			{
				$url['scheme'] = $last_url['scheme'];
			}
			if (!$url['host'])
			{
				$url['host'] = $last_url['host'];
			}
			if (!$url['path'])
			{
				$url['path'] = $last_url['path'];
			}
			$new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ($url['query'] ? '?' . $url['query'] : '');
			curl_setopt($ch, CURLOPT_URL, $new_url);
			return $this->curl_redir_exec($ch);
		}
		else
		{
			$curl_loops = 0;
			return $data;
		}
	}

	static function extensionInstalled($extension, $type = 'component', $folder = 'system')
	{
		switch ($type)
		{
			case 'component':
				if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_' . $extension . '/' . $extension . '.php')
					|| JFile::exists(JPATH_ADMINISTRATOR . '/components/com_' . $extension . '/admin.' . $extension . '.php')
					|| JFile::exists(JPATH_SITE . '/components/com_' . $extension . '/' . $extension . '.php')
				)
				{
					return 1;
				}
				break;
			case 'plugin':
				if (JFile::exists(JPATH_PLUGINS . '/' . $folder . '/' . $extension . '/' . $extension . '.php'))
				{
					return 1;
				}
				break;
			case 'module':
				if (JFile::exists(JPATH_ADMINISTRATOR . '/modules/mod_' . $extension . '/' . $extension . '.php')
					|| JFile::exists(JPATH_ADMINISTRATOR . '/modules/mod_' . $extension . '/mod_' . $extension . '.php')
					|| JFile::exists(JPATH_SITE . '/modules/mod_' . $extension . '/' . $extension . '.php')
					|| JFile::exists(JPATH_SITE . '/modules/mod_' . $extension . '/mod_' . $extension . '.php')
				)
				{
					return 1;
				}
				break;
		}
		return 0;
	}

	static function xmlToObject($url, $root)
	{
		if (!JFile::exists($url))
		{
			return new stdClass;
		}

		$xml = @new SimpleXMLElement($url, LIBXML_NONET, 1);

		if (!@count($xml))
		{
			return new stdClass;
		}

		if ($root)
		{
			if (!isset($xml->$root))
			{
				return new stdClass;
			}
			$xml = $xml->$root;
		}

		return self::convertXmlElement($xml);
	}

	static function convertXmlElement($el)
	{
		switch (gettype($el))
		{
			case 'object':
				if (empty($el))
				{
					return '';
				}
				$el = (object) (array) $el;
				break;
			case 'array':
				break;
			default:
				return $el;
		}

		$obj = array();
		foreach ($el as $key => $val)
		{
			if ('comment' == (string) $key)
			{
				continue;
			}
			$obj[$key] = self::convertXmlElement($val);
		}

		if ('object' == gettype($el))
		{
			$obj = (object) $obj;
		}

		return $obj;
	}

	/* Backwards compatibility */
	static function setSurroundingTags($pre, $post, $tags = 0)
	{
		require_once __DIR__ . '/tags.php';
		return NNTags::setSurroundingTags($pre, $post, $tags);
	}

	static function dateToDateFormat($dateFormat)
	{
		require_once __DIR__ . '/text.php';
		return NNText::dateToDateFormat($dateFormat);
	}

	static function dateToStrftimeFormat($dateFormat)
	{
		require_once __DIR__ . '/text.php';
		return NNText::dateToStrftimeFormat($dateFormat);
	}

	static function html_entity_decoder($given_html, $quote_style = ENT_QUOTES, $charset = 'UTF-8')
	{
		require_once __DIR__ . '/text.php';
		return NNText::html_entity_decoder($given_html, $quote_style, $charset);
	}

	static function cleanTitle($str, $striptags = 0)
	{
		require_once __DIR__ . '/text.php';
		return NNText::cleanTitle($str, $striptags);
	}

	static function isEditPage()
	{
		require_once __DIR__ . '/protect.php';
		return NNProtect::isEditPage();
	}

	static function getFormRegex($regex_format = 0)
	{
		require_once __DIR__ . '/protect.php';
		return NNProtect::getFormRegex($regex_format);
	}

	static function protectForm(&$string, $tags = array(), $protected = array())
	{
		require_once __DIR__ . '/protect.php';
		NNProtect::protectForm($string, $tags, $protected);
	}

	static function unprotectForm(&$string, $tags = array(), $protected = array())
	{
		require_once __DIR__ . '/protect.php';
		NNProtect::unprotectForm($string, $tags, $protected);
	}
}
