<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInboundBaseController', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/controllers/basecontroller.php');
JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');

class JInboundController extends JInboundBaseController
{
	function display($cachable = false, $urlparams = false) {
		$app  = JFactory::getApplication();
		$view = $app->input->get('view', 'page', 'cmd');
		$app->input->set('view', $view);
		if ('page' !== $view)
		{
			return parent::display($cachable);
		}
		$pop = $app->input->get('pop', array(), 'raw');
		if (is_array($pop) && !empty($pop))
		{
			$state = $app->getUserState('com_jinbound.page.data');
			if (is_object($state))
			{
				$state->lead = $pop;
			}
			else if (is_array($state))
			{
				$state['lead'] = $pop;
			}
			$app->setUserState('com_jinbound.page.data', $state);
		}
		return parent::display($cachable);
	}
	
	/**
	 * controller action to run cron tasks
	 * 
	 * TODO
	 */
	function cron() {
		$out = JInbound::config("debug", 0);
		// send reports emails
		if ($out)
		{
			echo "<h2>Sending reports</h2>\n";
		}
		require_once JPATH_ADMINISTRATOR . '/components/com_jinbound/models/reports.php';
		$model = $this->getModel('Reports', 'JInboundModel');
		$model->send();
		// handle sending campaign emails
		if ($out)
		{
			echo "<h2>Sending campaigns</h2>\n";
		}
		require_once JPATH_ADMINISTRATOR . '/components/com_jinbound/models/emails.php';
		$model = $this->getModel('Emails', 'JInboundModel');
		$model->send();
		// handle old tracks
		$debug    = (int) JInbound::config('debug', 0);
		$history  = (int) JInbound::config('history', 365);
		$interval = $debug ? 'SECOND' : 'DAY';
		if (0 < $history)
		{
			$db = JFactory::getDbo();
			$db->setQuery($db->getQuery(true)
				->delete('#__jinbound_tracks')
				->where("created < DATE_SUB(NOW(), INTERVAL $history $interval)")
			)->query();
			if ($debug)
			{
				$count = $db->getAffectedRows();
				echo "\n<h4>Clearing old Tracks...</h4>";
				echo "\n<p>Removed $count tracks!</p>\n";
			}
		}
		// exit
		jexit();
	}
	
	/**
	 * Disables all emails sent from jinbound to this user
	 * 
	 */
	function unsubscribe() {
		$app  = JFactory::getApplication();
		$db   = JFactory::getDbo();
		$menu = $app->getMenu('site')->getDefault()->id;
		// default message sent to user
		$redirect = JRoute::_('index.php?Itemid=' . $menu, false);
		// find the contact based on email
		$email = trim('' . $app->input->get('email', '', 'string'));
		// if no email, bail
		if (empty($email)) {
			$app->redirect($redirect, JText::_('COM_JINBOUND_UNSUBSCRIBE_FAILED_NO_EMAIL'), 'error');
			jexit();
		}
		// lookup contact based on email
		$db->setQuery($db->getQuery(true)
			->select('id')
			->from('#__jinbound_contacts')
			->where('email = ' . $db->quote($email))
		);
		try {
			$contact_id = (int) $db->loadResult();
			if (empty($contact_id)) {
				throw new Exception('COM_JINBOUND_UNSUBSCRIBE_FAILED_NO_CONTACT');
			}
		}
		catch (Exception $e) {
			$app->redirect($redirect, JText::_($e->getMessage()), 'error');
			jexit();
		}
		// blind delete this contact & rebuild entry
		$db->setQuery($db->getQuery(true)
			->delete('#__jinbound_subscriptions')
			->where("contact_id = $contact_id")
		);
		try {
			$db->query();
		}
		catch (Exception $e) {
			$app->redirect($redirect, JText::_($e->getMessage()), 'error');
			jexit();
		}
		$db->setQuery($db->getQuery(true)
			->insert('#__jinbound_subscriptions')
			->columns(array('contact_id', 'enabled'))
			->values("$contact_id, 0")
		);
		try {
			$db->query();
		}
		catch (Exception $e) {
			$app->redirect($redirect, JText::_($e->getMessage()), 'error');
			jexit();
		}
		// handle exit
		$app->redirect($redirect, JText::_('COM_JINBOUND_UNSUBSCRIBED'), 'message');
		jexit();
	}
	
	function landingpageurl() {
		$id   = JFactory::getApplication()->input->get('id', array(), 'array');
		$data = array('links' => array());
		if (!empty($id)) {
			JInbound::registerHelper('url');
			if (!is_array($id)) {
				$id = array($id);
			}
			foreach ($id as $i) {
				$link   = array();
				$nonsef = JInboundHelperUrl::view('page', false, array('id' => $i));
				// Before continuing make sure we had an Itemid
				if (!preg_match('/Itemid\=[1-9][0-9]*?/', $nonsef)) {
					$link['error'] = JText::_('COM_JINBOUND_NEEDS_MENU');
				}
				else {
					$sef            = JInboundHelperUrl::view('page', true, array('id' => $i));
					$link['nonsef'] = JInboundHelperUrl::toFull($nonsef);
					$link['sef']    = JInboundHelperUrl::toFull($sef);
					$link['root']   = JURI::root();
					$link['rel']    = array('nonsef' => $nonsef, 'sef' => $sef);
				}
				$link['id'] = $i;
				$data['links'][] = $link;
			}
			if (1 == count($id)) {
				$data = array_shift($data['links']);
			}
		}
		else {
			$data['error'] = JText::_('COM_JINBOUND_NOT_FOUND');
		}
		$data['request'] = array('id' => $id);
		
		echo json_encode($data);
		die;
	}
	
	/**
	 * Used to "fake" com_ajax on J2.5
	 * 
	 */
	function ajax()
	{
		$app = JFactory::getApplication();
		// non-legacy versions of joomla can just use core
		if (file_exists($com_ajax = JPATH_ROOT . '/components/com_ajax/ajax.php'))
		{
			require $com_ajax;
			$app->close();
		}
		// legacy versions of joomla - copied from components/com_ajax/ajax.php
		// JInput object
		$input = $app->input;

		// Requested format passed via URL
		$format = strtolower($input->getWord('format'));

		// Initialize default response and module name
		$results = null;
		$parts   = null;

		// Check for valid format
		if (!$format)
		{
			$results = new InvalidArgumentException(JText::_('COM_JINBOUND_AJAX_SPECIFY_FORMAT'), 404);
		}
		/*
		 * Module support.
		 *
		 * modFooHelper::getAjax() is called where 'foo' is the value
		 * of the 'module' variable passed via the URL
		 * (i.e. index.php?option=com_ajax&module=foo).
		 *
		 */
		elseif ($input->get('module'))
		{
			jimport('joomla.application.module.helper');
			$module       = $input->get('module');
			$moduleObject = JModuleHelper::getModule('mod_' . $module, null);

			/*
			 * As JModuleHelper::isEnabled always returns true, we check
			 * for an id other than 0 to see if it is published.
			 */
			if ($moduleObject->id != 0)
			{
				$helperFile = JPATH_BASE . '/modules/mod_' . $module . '/helper.php';

				if (strpos($module, '_'))
				{
					$parts = explode('_', $module);
				}
				elseif (strpos($module, '-'))
				{
					$parts = explode('-', $module);
				}

				if ($parts)
				{
					$class = 'mod';

					foreach ($parts as $part)
					{
						$class .= ucfirst($part);
					}

					$class .= 'Helper';
				}
				else
				{
					$class = 'mod' . ucfirst($module) . 'Helper';
				}

				$method = $input->get('method') ? $input->get('method') : 'get';

				if (is_file($helperFile))
				{
					require_once $helperFile;

					if (method_exists($class, $method . 'Ajax'))
					{
						// Load language file for module
						$basePath = JPATH_BASE;
						$lang     = JFactory::getLanguage();
						$lang->load('mod_' . $module, $basePath, null, false, true)
						||  $lang->load('mod_' . $module, $basePath . '/modules/mod_' . $module, null, false, true);

						try
						{
							$results = call_user_func($class . '::' . $method . 'Ajax');
						}
						catch (Exception $e)
						{
							$results = $e;
						}
					}
					// Method does not exist
					else
					{
						$results = new LogicException(JText::sprintf('COM_JINBOUND_AJAX_METHOD_NOT_EXISTS', $method . 'Ajax'), 404);
					}
				}
				// The helper file does not exist
				else
				{
					$results = new RuntimeException(JText::sprintf('COM_JINBOUND_AJAX_FILE_NOT_EXISTS', 'mod_' . $module . '/helper.php'), 404);
				}
			}
			// Module is not published, you do not have access to it, or it is not assigned to the current menu item
			else
			{
				$results = new LogicException(JText::sprintf('COM_JINBOUND_AJAX_MODULE_NOT_ACCESSIBLE', 'mod_' . $module), 404);
			}
		}
		/*
		 * Plugin support by default is based on the "Ajax" plugin group.
		 * An optional 'group' variable can be passed via the URL.
		 *
		 * The plugin event triggered is onAjaxFoo, where 'foo' is
		 * the value of the 'plugin' variable passed via the URL
		 * (i.e. index.php?option=com_ajax&plugin=foo)
		 *
		 */
		elseif ($input->get('plugin'))
		{
			$group      = $input->get('group', 'ajax');
			JPluginHelper::importPlugin($group);
			$plugin     = ucfirst($input->get('plugin'));
			$dispatcher = JDispatcher::getInstance();

			try
			{
				$results = $dispatcher->trigger('onAjax' . $plugin);
			}
			catch (Exception $e)
			{
				$results = $e;
			}
		}

		// Return the results in the desired format
		switch ($format)
		{
			// JSONinzed
			case 'json' :
				JLoader::register('JResponseJson', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/compat/response/json.php');
				echo new JResponseJson($results, null, false, $input->get('ignoreMessages', true, 'bool'));

				break;

			// Human-readable format
			case 'debug' :
				echo '<pre>' . print_r($results, true) . '</pre>';
				$app->close();

				break;

			// Handle as raw format
			default :
				// Output exception
				if ($results instanceof Exception)
				{
					// Log an error
					JLog::add($results->getMessage(), JLog::ERROR);

					// Set status header code
					$app->setHeader('status', $results->getCode(), true);

					// Echo exception type and message
					$out = get_class($results) . ': ' . $results->getMessage();
				}
				// Output string/ null
				elseif (is_scalar($results))
				{
					$out = (string) $results;
				}
				// Output array/ object
				else
				{
					$out = implode((array) $results);
				}

				echo $out;

				break;
		}
	}
}
