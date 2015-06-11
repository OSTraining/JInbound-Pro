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
	
	protected $app;
	
	/**
	 * Constructor
	 * 
	 * @param unknown_type $subject
	 * @param unknown_type $config
	 */
	public function __construct(&$subject, $config) {
		$this->app = JFactory::getApplication();
		// if something happens & the helper class can't be found, we don't want a fatal error here
		if (class_exists('JInbound')) {
			JInbound::language(JInbound::COM, JPATH_ADMINISTRATOR);
			self::$_run = true;
		}
		else {
			$this->loadLanguage();
			$this->app->enqueueMessage(JText::_('PLG_SYSTEM_JINBOUND_COMPONENT_NOT_INSTALLED'));
			self::$_run = false;
		}
		parent::__construct($subject, $config);
	}
	
	public function loadLanguage($extension = 'plg_system_jinbound.sys', $basePath = JPATH_ADMINISTRATOR) {
		parent::loadLanguage($extension, $basePath);
	}
	
	public function onAfterInitialise()
	{
		if (!$this->app->isAdmin())
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
		$opt = $this->app->input->get('option', '', 'cmd');
		if ($this->app->isAdmin()) {
			$this->onAfterDispatchAdmin($opt);
		}
		else {
			$this->onAfterDispatchSite($opt);
		}
	}
	
	public function onAfterDispatchSite($option) {
		// stub
	}
	
	public function onAfterDispatchAdmin($option) {
		switch ($option) {
			case 'com_categories':
				$this->onAfterDispatchAdminCategories();
				break;
			case 'com_menus':
				JInbound::registerHelper('url');
				if ('edit' == $this->app->input->get('layout') && 'item' == $this->app->input->get('view')) {
					JText::script('COM_JINBOUND_MENU_NOT_SET_TO_USE_JINBOUND_TEMPLATE');
					JFactory::getDocument()->addScript(JInboundHelperUrl::media() . '/js/admin.menu.js');
				}
				break;
			default: break;
		}
	}
	
	private function onAfterDispatchAdminCategories()
	{
		// we want to add some extras to com_categories
		if (class_exists('JInbound') && JInbound::COM == $this->app->input->get('extension', '', 'cmd'))
		{
			// add css
			$doc = JFactory::getDocument();
			if (method_exists($doc, 'addStyleSheet'))
			{
				JInbound::registerHelper('url');
				$doc->addStyleSheet(JInboundHelperUrl::media() . '/css/admin.categories.css');
			}
			// joomla 3 handles this via helper
			if (JInbound::version()->isCompatible('3.0.0'))
			{
				return;
			}
			// add submenu to categories
			JInbound::registerLibrary('JInboundView', 'views/baseview');
			$comView = new JInboundView();
			$comView->addMenuBar();
		}
	}
	
	public function onAfterRoute()
	{
		// read from the database any campaign params given the cookie value
		$db     = JFactory::getDbo();
		$cookie = $this->getCookieValue();
		try
		{
			$campaign_data = $db->setQuery($db->getQuery(true)
				->select('ContactCampaign.contact_id AS contact_id')
				->select('ContactCampaign.campaign_id AS campaign_id')
				->select('Campaign.conversion_url')
				->from('#__jinbound_contacts_campaigns AS ContactCampaign')
				->leftJoin('#__jinbound_contacts AS Contact ON Contact.id = ContactCampaign.contact_id')
				->leftJoin('#__jinbound_campaigns AS Campaign ON Campaign.id = ContactCampaign.campaign_id')
				->where('Contact.cookie = ' . $db->quote($cookie))
				->where('Campaign.conversion_url <> ' . $db->quote(''))
			)->loadObjectList();
		}
		catch (Exception $e)
		{
			if (defined('JDEBUG') && JDEBUG)
			{
				$this->app->enqueueMessage($e->getMessage(), 'error');
			}
			return;
		}
		// this visitor is not associated
		if (empty($campaign_data))
		{
			return;
		}
		// get the final status
		JInbound::registerHelper('status');
		$status_id = JInboundHelperStatus::getFinalStatus();
		// don't process the same data more than once
		$processed = array();
		// this visitor is detected as being in one or more campaigns
		// check if this request is for one of the conversion urls
		foreach ($campaign_data as $data)
		{
			// only process once
			// TODO just fix query instead
			$processed_key = $data->contact_id . ':' . $data->campaign_id;
			if (in_array($processed_key, $processed))
			{
				continue;
			}
			$processed[] = $processed_key;
			// params
			$params = array();
			$param_string = trim($data->conversion_url);
			// remove "index.php?"
			if (substr($param_string, 0, 10) === 'index.php?')
			{
				$param_string = substr($param_string, 10);
			}
			// remove leading ? if needed
			$param_string = ltrim($param_string, '?');
			// this url *might* be in sef :(
			$matches = false;
			// if so, just compare to query string
			$trimmed = trim($param_string, '/');
			$trim_root = trim(JUri::root(true), '/');
			if (!empty($trimmed) && array_key_exists('REQUEST_URI', $_SERVER)
				&& (trim($_SERVER['REQUEST_URI'], '/') === $trimmed
				 || trim($_SERVER['REQUEST_URI'], '/') === trim($trim_root . '/' . $trimmed, '/')
				))
			{
				$matches = true;
			}
			else if (false !== strpos($param_string, '='))
			{
				// handle non-sef
				parse_str($param_string, $params);
				// get just the given params from request and compare the arrays
				$request = array();
				foreach (array_keys($params) as $param)
				{
					$request[$param] = $this->app->input->get($param);
				}
				// fix ids, catids, etc, for slugs
				foreach (array('id', 'a_id', 'cat', 'catid') as $fix)
				{
					if (array_key_exists($fix, $request))
					{
						$request[$fix] = preg_replace('/^([1-9][0-9]*?).*$/', '$1', $request[$fix]);
					}
				}
				if (!empty($request))
				{
					// get the diff
					$diff = array_diff_assoc($params, $request);
					$matches = empty($diff);
				}
			}
			// if the arrays are the same, there's a match - assign if there's a final status
			if ($matches && $status_id)
			{
				JInboundHelperStatus::setContactStatusForCampaign($status_id, $data->contact_id, $data->campaign_id);
				continue;
			}
		}
	}
	
	/**
	 * Alter the response body before sending to the client
	 * 
	 */
	public function onAfterRender() {
		if (!self::$_run || $this->app->isAdmin()) {
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
		$ip           = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
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
		,	'user_agent'       => $db->quote(filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'))
		,	'created'          => $db->quote(JFactory::getDate()->toSql())
		,	'ip'               => $db->quote($ip)
		,	'session_id'       => $db->quote($session)
		,	'type'             => $db->quote(strtoupper(filter_input(INPUT_SERVER, 'REQUEST_METHOD')))
		,	'url'              => $db->quote(filter_input(INPUT_SERVER, 'REQUEST_URI'))
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
		$c = filter_input(INPUT_COOKIE, '__jib__');
		if (!empty($c))
		{
			return $c;
		}
		static $cookie;
		if (is_null($cookie))
		{
			$ua     = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');
			$ip     = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
			$salt   = strrev(md5(JFactory::getConfig()->get('secret')));
			$cookie = sha1("$ua.$salt.$ip", false);
		}
		return $cookie;
	}
}
