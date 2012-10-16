<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

abstract class JInbound
{
	const COM = 'com_jinbound';
	
	/**
	 * static method to get either the component parameters,
	 * or when a key is supplied the value of that key
	 * if val is supplied (with a key) def() is used instead of get()
	 *
	 * @param  string $key
	 * @param  mixed  $val
	 * @return mixed
	 */
	public static function config($key = null, $val = null) {
		static $params;
		if (!isset($params)) {
			$app = JFactory::getApplication();
			// get the params, either from the helper or the application
			if ($app->isAdmin() || self::COM != $app->input->get('option', '', 'cmd')) {
				$params = JComponentHelper::getParams(self::COM);
			} else {
				$params = $app->getParams();
			}
		}
		// if we don't have a key, return the entire params object
		if (is_null($key) || empty($key)) {
			return $params;
		}
		// return the param value, with optional def
		if (is_null($val)) return $params->get($key);
		return $params->def($key, $val);
	}
	
	/**
	 * loads language files, english first then configured language
	 *
	 * @param string $name
	 * @param mixed  $client
	 */
	public static function language($name, $client = null) {
		// force client
		if (is_null($client) || !is_string($client)) $client = JPATH_ROOT;
		// we really only want to load once each asset
		static $langs;
		// initialize our static list
		if (!is_array($langs)) $langs = array();
		// create our key
		$key = md5($name . $client);
		// set the list item if it's not been set
		if (!array_key_exists($key, $langs)) {
			// what language should we try?
			$user  = JFactory::getUser();
			$ulang = $user->getParam('language', $user->getParam('admin_language'));
			$lang  = JFactory::getLanguage();
			$langs[$key] = $lang->load($name, $client, $ulang, true) || $lang->load($name, $client, 'en-GB');
		}
		// return the value :)
		return $langs[$key];
	}
	
	/**
	 * static method to keep track of debug info
	 *
	 * @param  string  $name
	 * @param  mixed   $data
	 * @return array
	 */
	public static function debugger($name = null, $data = null) {
		static $debug;
		if (!is_array($debug)) $debug = array();
		if (!is_null($name)) $debug[$name] = $data;
		return $debug;
	}
	
	/**
	 * static method to aide in debugging
	 *
	 * @param  mixed  $data
	 * @param  string $fin
	 * @return mixed
	 */
	public static function debug($data, $fin = 'echo') {
		if (defined('JDEBUG') && !JDEBUG) return '';
		$e       = new Exception;
		$output  = "<pre>\n" . htmlspecialchars(print_r($data, 1)) . "\n\n" . $e->getTraceAsString() . "\n</pre>\n";
		switch ($fin) {
			case 'return': return $output;
			case 'die'   : echo $output; die();
			case 'echo'  :
			default      :
				echo $output; return;
		}
	}
	
	/**
	 * gets an instance of JVersion
	 *
	 */
	public static function version() {
		static $version;
		if (is_null($version)) {
			$version = new JVersion;
		}
		return $version;
	}
}
