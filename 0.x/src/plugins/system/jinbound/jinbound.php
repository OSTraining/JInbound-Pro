<?php
/**
 * @package		JInbound
 * @subpackage	plg_system_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.plugin.plugin');
// we HAVE to force-load the helper here to prevent fatal errors!
$helper = JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php';
if (JFile::exists($helper)) require_once $helper;

class plgSystemJInbound extends JPlugin
{
	private static $_run;
	
	private static $_debug;
	
	private static $_setCookieInJs;
	
	/**
	 * Constructor
	 * 
	 * @param unknown_type $subject
	 * @param unknown_type $config
	 */
	public function __construct(&$subject, $config) {
		// if something happens & the helper class can't be found, we don't want a fatal error here
		if (class_exists('JInbound')) {
			JInbound::language(JInbound::COM, JPATH_ADMINISTRATOR);
			self::$_run = true;
		}
		else {
			$this->loadLanguage();
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_JINBOUND_COMPONENT_NOT_INSTALLED'));
			self::$_run = false;
		}
		parent::__construct($subject, $config);
	}
	
	public function loadLanguage($extension = 'plg_system_jinbound.sys', $basePath = JPATH_ADMINISTRATOR) {
		parent::loadLanguage($extension, $basePath);
	}
	
	public function onAfterInitialise()
	{
		if (!JFactory::getApplication()->isAdmin())
		{
			$this->setUserCookie();
		}
	}
	
	/**
	 * onAfterDispatch
	 * 
	 * handles flair after dispatch
	 */
	public function onAfterDispatch() {
		if (!self::$_run) {
			return;
		}
		$app = JFactory::getApplication();
		$opt = $app->input->get('option', '', 'cmd');
		if ($app->isAdmin()) {
			$this->onAfterDispatchAdmin($opt);
		}
		else {
			$this->onAfterDispatchSite($opt);
		}
	}
	
	// stub for now
	public function onAfterDispatchSite($option) {
		$app = JFactory::getApplication();
		switch ($option) {
			default: break;
		}
	}
	
	public function onAfterDispatchAdmin($option) {
		$app = JFactory::getApplication();
		switch ($option) {
			case 'com_categories':
				// we want to add some extras to com_categories
				if (class_exists('JInbound') && JInbound::COM == $app->input->get('extension', '', 'cmd')) {
					// joomla 3 handles this via helper
					if (JInbound::version()->isCompatible('3.0')) {
						return;
					}
					// add submenu to categories
					JInbound::registerLibrary('JInboundView', 'views/baseview');
					$comView = new JInboundView();
					$comView->addMenuBar();
				}
				break;
			case 'com_menus':
				JInbound::registerHelper('url');
				if ('edit' == $app->input->get('layout') && 'item' == $app->input->get('view')) {
					JText::script('COM_JINBOUND_MENU_NOT_SET_TO_USE_JINBOUND_TEMPLATE');
					JFactory::getDocument()->addScript(JInboundHelperUrl::media() . '/js/admin.menu.js');
				}
				break;
			default: break;
		}
	}
	
	/**
	 * Alter the response body before sending to the client
	 * 
	 */
	public function onAfterRender() {
		if (!self::$_run || JFactory::getApplication()->isAdmin()) {
			return;
		}
		JInbound::registerHelper('filter');
		JInbound::registerHelper('url');
		// get the response body so it can be altered
		$body = JResponse::getBody();
		// this is our addition
		$add  = '';
		// show cron iframe
		if (0 == intval(JInbound::config()->def('cron_type', '')))
		{
			$url  = JInboundHelperFilter::escape(JInboundHelperUrl::task('cron', false));
			$add .= '<iframe src="' . $url . '" style="width:1px;height:1px;position:absolute;left:-999px;border:0px"></iframe>';
		}
		// add cookie script
		if (self::$_setCookieInJs)
		{
			$cookie = JInboundHelperFilter::escape(self::getCookieValue());
			$add .= '<script data-jib="' . $cookie . '" id="jinbound_tracks" type="text/javascript" src="' . JInboundHelperUrl::media() . '/js/track.js"></script>';
		}
		// only alter if needed
		if (!empty($add))
		{
			$body = str_replace('</body>', $add . '</body>', $body);
			JResponse::setBody($body);
		}
		$this->recordUserTrack();
	}
	
	/**
	 * Sets the jInbound user cookie
	 * 
	 * TODO stupid EU cookie law crap
	 * 
	 */
	public static function setUserCookie()
	{
		if (headers_sent())
		{
			self::$_setCookieInJs = true;
		}
		else
		{
			self::$_setCookieInJs = !setcookie('__jib__', self::getCookieValue());
		}
	}
	
	/**
	 * records the user's request
	 * 
	 */
	private function recordUserTrack()
	{
		$db           = JFactory::getDbo();
		$ip           = $_SERVER['REMOTE_ADDR'];
		$session      = session_id();
		$id           = microtime() . $ip . md5($session);
		$detecteduser = self::getCookieUser();
		// check detected?
		if (is_array($detecteduser))
		{
			$detecteduser = $detecteduser[0];
		}
		// our track
		$track   = array(
			'id'               => $db->quote($id)
		,	'cookie'           => $db->quote(self::getCookieValue())
		,	'detected_user_id' => $db->quote($detecteduser) // TODO
		,	'current_user_id'  => $db->quote(JFactory::getUser()->get('id'))
		,	'user_agent'       => $db->quote($_SERVER['HTTP_USER_AGENT'])
		,	'created'          => $db->quote(JFactory::getDate()->toSql())
		,	'ip'               => $db->quote($ip)
		,	'session_id'       => $db->quote($session)
		,	'type'             => $db->quote(strtoupper($_SERVER['REQUEST_METHOD']))
		,	'url'              => $db->quote($_SERVER['REQUEST_URI'])
		);
		$db->setQuery($db->getQuery(true)
			->insert('#__jinbound_tracks')
			->columns(array_keys($track))
			->values(implode(',', $track))
		);
		try {
			$db->query();
		}
		catch (Exception $e) {}
		
		// only record user cookie for non-guests
		if (0 === $detecteduser && $track['current_user_id'])
		{
			$db->setQuery($db->getQuery(true)
				->insert('#__jinbound_users_tracks')
				->columns(array('user_id', 'cookie'))
				->values($track['current_user_id'] . ', ' . $track['cookie'])
			);
			try {
				$db->query();
			}
			catch (Exception $e) {}
		}
	}
	
	/**
	 * Checks the database for a user previously associated with this cookie
	 * 
	 * @return array if more than one user is detected
	 * @return int   detected user id, or 0 if no user
	 */
	static public function getCookieUser()
	{
		$db = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->select('user_id')
			->from('#__jinbound_users_tracks')
			->where('cookie = ' . $db->quote(self::getCookieValue()))
		);
		
		try {
			$ids = $db->loadColumn();
		} catch (Exception $e) {
			return 0;
		}
		
		if (empty($ids))
		{
			return 0;
		}
		
		if (1 < count($ids))
		{
			JArrayHelper::toInteger($ids);
			return $ids;
		}
		
		return (int) $ids[0];
	}
	
	/**
	 * Derives a unique cookie name for this user
	 * 
	 * @return string
	 */
	static public function getCookieValue()
	{
		if (isset($_COOKIE['__jib__']))
		{
			return $_COOKIE['__jib__'];
		}
		static $cookie;
		if (is_null($cookie))
		{
			$ua     = $_SERVER['HTTP_USER_AGENT'];
			$ip     = $_SERVER['REMOTE_ADDR'];
			$salt   = strrev(md5(JFactory::getConfig()->get('secret')));
			$cookie = sha1("$ua.$salt.$ip", false);
		}
		return $cookie;
	}
}
