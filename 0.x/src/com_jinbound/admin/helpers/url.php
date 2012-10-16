<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInboundHelperPath', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/path.php');
JLoader::register('JInbound', JInboundHelperPath::helper('jinbound'));

abstract class JInboundHelperUrl
{
	/**
	 * array of known Itemids, for creating our own internal urls
	 * 
	 * @var array
	 */
	protected static $lookup;
	
	/**
	 * creates an internal (Joomla) url string
	 * 
	 * @param unknown_type $params
	 * @param unknown_type $sef
	 */
	public static function _($params = array(), $sef = true) {
		// start building
		$urlparams = array();
		// always have com first
		$urlparams['option'] = JInbound::COM;
		if (isset($params['option'])) {
			$urlparams['option'] = $params['option'];
			unset($params['option']);
		}
		$urlparams = array_merge($urlparams, $params);
		// check our new array to see if we're handling our own urls (from com_jinbound)
		// if so, we need to fetch the appropriate Itemid and add this to the url
		if (JInbound::COM == $urlparams['option']) {
			// here comes some fun - we can't just call this method without constructing a needles array :(
			$needles = null;
			// apparently we can only append the Itemid if we don't have a task 
			if (array_key_exists('task', $urlparams)) {
				if (array_key_exists('Itemid', $urlparams)) unset($urlparams['Itemid']);
			}
			else {
				$Itemid = self::findItemid($needles);
				if ($Itemid && !array_key_exists('Itemid', $urlparams)) $urlparams['Itemid'] = $Itemid;
			}
		}
		// round 2
		$url = array();
		foreach ($urlparams as $name => $attr) {
			if (is_numeric($name)) continue;
			$url[] = $name . '=' . urlencode(str_replace(' ', '%20', $attr));
		}
		$url = 'index.php?' . implode('&', $url);
		if ($sef) {
			$url = JRoute::_($url, false);
		}
		return $url;
	}
	
	/**
	 * static method to return the current url, with optional parameters
	 * 
	 * @param unknown_type $extra
	 * @param unknown_type $remove
	 */
	public static function page($extra = array(), $remove = array()) {
		// get the current URI
		$uri = clone JURI::getInstance();
		// add any extras if they are set
		if (!empty($extra)) {
			foreach ($extra as $key => $value) {
				// don't set numeric keys
				if (is_numeric($key)) continue;
				// set extra to the URI
				$uri->setVar($key, $value);
			}
		}
		// remove any keys if they are set
		if (!empty($remove)) {
			foreach ($remove as $key) {
				// don't use numeric keys
				if (is_numeric($key)) continue;
				// set extra to the URI
				$uri->delVar($key);
			}
		}
		// send back the URI as a string
		return $uri->toString();
	}
	
	/**
	 * static method to generate a JInbound url based on "view"
	 * 
	 * @param unknown_type $view
	 * @param unknown_type $sef
	 * @param unknown_type $extra
	 */
	public static function view($view, $sef = true, $extra = array()) {
		$url = array('view'=>$view);
		if (!empty($extra)) $url = array_merge($url, $extra);
		return self::_($url, $sef);
	}
	
	/**
	 * static method to generate a JInbound url based on "task"
	 * 
	 * @param $task
	 * @param $sef
	 * @param $extra
	 */
	public static function task($task, $sef = true, $extra = array()) {
		$url = array('task'=>$task);
		if (!empty($extra)) $url = array_merge($url, $extra);
		return self::_($url, $sef);
	}
	
	/**
	 * static method to generate a JInbound url with a slug from "alias"
	 * 
	 * @param unknown_type $id
	 * @param unknown_type $view
	 * @param unknown_type $sef
	 * @param unknown_type $extra
	 */
	private static function _slug($id, $view, $sef = true, $extra = array()) {
		$view = strtolower($view);
		// we're going to store the event slugs here so we only load once
		static $slugs;
		if (!is_array($slugs)) {
			JTable::addIncludePath(JInboundHelperPath::admin('tables'));
			$slugs = array();
		}
		if (!array_key_exists($view, $slugs)) {
			$slugs[$view] = array();
		}
		// force our id to an int
		$id = intval($id);
		// set the main url properties
		$url = array('view' => $view, 'id' => $id);
		// add extra params if necessary
		if (!empty($extra)) {
			// check if we've been passed a slug and if not load the event & obtain it
			if (array_key_exists('slug', $extra)) {
				$slugs[$view][$id] = $extra['slug'];
				unset($extra['slug']);
			}
			// merge the data
			$url = array_merge($url, $extra);
		}
		// if we still don't have a slug, load it from the database
		if (!array_key_exists($id, $slugs[$view])) {
			$table = JTable::getInstance(ucwords($view), 'JInboundTable');
			if ($table->load($id)) {
				$slugs[$view][$id] = (!empty($table->alias) ? $table->alias : JApplication::stringURLSafe($table->title));
			}
		}
		// if we have a slug, reset id
		if (array_key_exists($id, $slugs[$view])) $url['id'] = $id . ':' . $slugs[$view][$id];
		// return our url
		return self::_($url, $sef);
	}
	
	/**
	 * static method to fetch the media url
	 * 
	 * @param bool true for full, false for relative
	 */
	public static function media($relative = true) {
		$prefix = (JFactory::getApplication()->isAdmin() ? '../' : '');
		$root   = rtrim(JUri::base(), '/');
		return str_replace('/administrator/..', '', (($relative ? '' : "{$root}/") . "{$prefix}media/jinbound"));
	}
	
	/**
	 * static method to force a relative url to an absolute one
	 * 
	 * @param string $url
	 * @return string the absolute url
	 */
	public static function toFull($url) {
		if (preg_match('/^https?\:\/{2}/', $url)) return $url;
		return str_replace('/administrator', '', rtrim(str_replace('/administrator', '', JUri::root()), '/') . '/' . ltrim(str_replace(JUri::root(true), '', $url), '/'));
	}
}
