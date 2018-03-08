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
			$this->app->enqueueMessage(JText::_('PLG_SYSTEM_JINBOUND_COMPONENT_NOT_INSTALLED'));
			self::$_run = false;
		}
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	
	public function loadLanguage($extension = 'plg_system_jinbound.sys', $basePath = JPATH_ADMINISTRATOR) {
		parent::loadLanguage($extension, $basePath);
	}
	
	public function onAfterInitialise()
	{
		self::profile('BeforeInitialise');
		if (!$this->app->isAdmin())
		{
			$this->setUserCookie();
		}
		else
		{
			$modules = $this->getJinboundModules();
			$option = array_key_exists('option', $_REQUEST) ? $_REQUEST['option'] : '';
			$view = array_key_exists('view', $_REQUEST) ? $_REQUEST['view'] : '';
			if (in_array($option, $modules) && 'liveupdate' === $view)
			{
				$name = preg_replace('/^mod_/', '', $option);
				require_once JPATH_ROOT . '/modules/' . $option . '/liveupdate/liveupdate.php';
				$updateInfo = LiveUpdate::getUpdateInformation();
				if ($updateInfo->hasUpdates) {
					echo JText::sprintf('PLG_SYSTEM_JINBOUND_MODULE_UPDATE_HASUPDATES', $name, $option, $updateInfo->version);
				}
				jexit();
			}
		}
		self::profile('AfterInitialise');
	}
	
	/**
	 * onAfterDispatch
	 * 
	 * handles flair after dispatch
	 */
	public function onAfterDispatch() {
		self::profile('BeforeDispatch');
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
		self::profile('AfterDispatch');
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
		if (!self::$_run || $this->app->isAdmin())
		{
			return;
		}
		self::profile('BeforeRoute');
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
			self::profile('AfterRoute');
			return;
		}
		// this visitor is not associated
		if (empty($campaign_data))
		{
			self::profile('AfterRoute');
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
			$uri = static::getURI(false);
			if (!empty($trimmed)
				&& (trim($uri, '/') === $trimmed
				 || trim($uri, '/') === trim($trim_root . '/' . $trimmed, '/')
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
		self::profile('AfterRoute');
	}
	
	/**
	 * Alter the response body before sending to the client
	 * 
	 */
	public function onAfterRender() {
		if (!self::$_run || $this->app->isAdmin())
		{
			return;
		}
		self::profile('BeforeRender');
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
		self::profile('BeforeTrack');
		$this->recordUserTrack();
		self::profile('AfterRender');
	}
	
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		if (!self::$_run || false === strpos($url, 'jinbound.com'))
		{
			return;
		}
		JLoader::import('joomla.application.component.helper');
		$component = JComponentHelper::getComponent('com_jinbound');
		$dlid = $component->params->get('downloadid', '');
		if (empty($dlid))
		{
			if ($this->app->isAdmin())
			{
				$this->app->enqueueMessage(JText::_('PLG_SYSTEM_JINBOUND_EMPTY_DLID'));
			}
			return;
		}
		$url .= (false === strpos($url, '?') ? '?' : '&') . 'dlid=' . $dlid;
	}
	
	public function onJinboundDashboardUpdate()
	{
		$modules = $this->getJinboundModules();
		$urls    = array();
		foreach ($modules as $module)
		{
			$urls[] = "index.php?option=$module&view=liveupdate";
		}
		if (!empty($urls))
		{
			return $urls;
		}
	}
	
	private function getJinboundModules()
	{
		$modules = array();
		$modbase = JPATH_ROOT . '/modules';
		// locate any installed jinbound modules - for now just use names
		if (($files = scandir($modbase)))
		{
			foreach ($files as $file)
			{
				$filename = basename($file);
				$root = "$modbase/$filename";
				if (!(is_dir($root) && preg_match('/^mod_jinbound_/', $filename)))
				{
					continue;
				}
				if (!file_exists("$root/liveupdate/liveupdate.php"))
				{
					continue;
				}
				$modules[] = $filename;
			}
		}
		return $modules;
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
		$ip           = self::getIp();
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
		,	'user_agent'       => $db->quote(self::getUserAgent())
		,	'created'          => $db->quote(JFactory::getDate()->toSql())
		,	'ip'               => $db->quote($ip)
		,	'session_id'       => $db->quote($session)
		,	'type'             => $db->quote(strtoupper(filter_input(INPUT_SERVER, 'REQUEST_METHOD')))
		,	'url'              => $db->quote(self::getURI())
		);
		self::profile('BeforeInsertTrack');
		$db->setQuery($db->getQuery(true)
			->insert('#__jinbound_tracks')
			->columns(array_keys($track))
			->values(implode(',', $track))
		);
		try {
			$db->query();
		}
		catch (Exception $e) {}
		
		self::profile('BeforeInsertUserTrack');
		// only record user cookie for non-guests
		if (0 === $detecteduser && (int) $track['current_user_id'])
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
	 * Copied from sh404sef (which copied from Joomla)
	 * Modified a wee bit
	 * @return string
	 */
	static public function getURI($full = true)
	{
		if ($full)
		{
			// copied from Joomla, as JURI keeps the original URI
			// as a protected var, we can't access it
			// Determine if the request was over SSL (HTTPS).
			if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off'))
			{
				$https = 's://';
			}
			else
			{
				$https = '://';
			}
			$theURI = 'http' . $https . $_SERVER['HTTP_HOST'];
		}
		else
		{
			$theURI = '';
		}
		
		// Since we are assigning the URI from the server variables, we first need
		// to determine if we are running on apache or IIS.  If PHP_SELF and REQUEST_URI
		// are present, we will assume we are running on apache.
		
		if (!empty($_SERVER['PHP_SELF']) && !empty($_SERVER['REQUEST_URI']))
		{
			// To build the entire URI we need to prepend the protocol, and the http host
			// to the URI string.
			$theURI .= $_SERVER['REQUEST_URI'];
		}
		else
		{
			// Since we do not have REQUEST_URI to work with, we will assume we are
			// running on IIS and will therefore need to work some magic with the SCRIPT_NAME and
			// QUERY_STRING environment variables.
			
			// IIS uses the SCRIPT_NAME variable instead of a REQUEST_URI variable... thanks, MS
			$theURI .= $_SERVER['SCRIPT_NAME'];
			
			// If the query string exists append it to the URI string
			if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
			{
				$theURI .= '?' . $_SERVER['QUERY_STRING'];
			}
		}
		return $theURI;
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
			$ua     = self::getUserAgent();
			$ip     = self::getIp();
			$salt   = strrev(md5(JFactory::getConfig()->get('secret')));
			$cookie = sha1("$ua.$salt.$ip", false);
		}
		return $cookie;
	}
	
	static public function getIp()
	{
		return self::getServerVar('REMOTE_ADDR');
	}
	
	static public function getUserAgent()
	{
		return self::getServerVar('HTTP_USER_AGENT');
	}
	
	static public function getServerVar($variable)
	{
		if (filter_has_var(INPUT_SERVER, $variable))
		{
			$value = filter_input(INPUT_SERVER, $variable);
		}
		else
		{
			$value = (isset($_SERVER[$variable]) ? filter_var($_SERVER[$variable],
FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE) : null);
		}
		return $value;
	}
	
	static public function profile($action)
	{
		if (!is_string($action) || empty($action))
		{
			return;
		}
		$profiler = JProfiler::getInstance('Application');
		$profiler->mark("onPlgSysJinbound$action");
	}
}
