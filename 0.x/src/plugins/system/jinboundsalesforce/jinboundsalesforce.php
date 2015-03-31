<?php
/**
 * @package		JInbound
 * @subpackage	plg_system_jinboundsalesforce
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.plugin.plugin');

$db = JFactory::getDbo();
$plugins = $db->setQuery($db->getQuery(true)
	->select('extension_id')->from('#__extensions')
	->where($db->qn('element') . ' = ' . $db->q('com_jinbound'))
	->where($db->qn('enabled') . ' = 1')
)->loadColumn();
$file = JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/views/fieldview.php';
defined('PLG_SYSTEM_JINBOUNDSALESFORCE') or define('PLG_SYSTEM_JINBOUNDSALESFORCE', 1 === count($plugins) && JFile::exists($file));
require_once $file;

class plgSystemJInboundsalesforce extends JPlugin
{
	protected $app;
	
	private $client;
	
	private $errors;
	
	/**
	 * Constructor
	 * 
	 * @param unknown_type $subject
	 * @param unknown_type $config
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->errors = array();
		$this->app = JFactory::getApplication();
		$this->loadLanguage('plg_system_jinboundsalesforce.sys', JPATH_ADMINISTRATOR);
	}
	
	public function getClient()
	{
		if (empty($this->client))
		{
			require_once dirname(__FILE__) . '/library/force/SforcePartnerClient.php';
			$this->startClient();
		}
		return $this->client;
	}
	
	private function startClient()
	{
		// check session for client info
		$session       = JFactory::getSession();
		$sess_wsdl     = $session->get('jinboundsalesforce.wsdl', '');
		$sess_location = $session->get('jinboundsalesforce.location', '');
		$sess_id       = $session->get('jinboundsalesforce.id', '');
		// confirm session info first
		if (!empty($sess_wsdl) && !empty($sess_location) && !empty($sess_id))
		{
			// if this works, it's all done
			try
			{
				$this->client = new SforcePartnerClient();
				$this->client->createConnection($sess_wsdl);
				$this->client->setEndpoint($sess_location);
				$this->client->setSessionHeader($sess_id);
				return;
			}
			// failure - kill the session data and continue connecting fresh
			catch (Exception $e)
			{
				$session->set('jinboundsalesforce.wsdl', null);
				$session->set('jinboundsalesforce.location', null);
				$session->set('jinboundsalesforce.id', null);
			}
		}
		// get config from plugin params
		$wsdl     = $this->params->get('wsdl', '');
		$username = $this->params->get('username', '');
		$password = $this->params->get('password', '');
		$token    = $this->params->get('security_token', '');
		$usetoken = (int) $this->params->get('use_token', 1);
		$wsdlfile = dirname(__FILE__) . '/wsdl/' . $wsdl;
		// confirm that required settings are available
		if (empty($wsdl) || !file_exists($wsdlfile) || !preg_match('/\.xml$/i', $wsdlfile)
			|| empty($username) || empty($password) || (empty($token) && $usetoken))
		{
			return;
		}
		// attempt to connect to salesforce
		try
		{
			$this->client = new SforcePartnerClient();
			$this->client->createConnection($wsdlfile);
			$this->client->login($username, $password . ($usetoken ? $token : ''));
		}
		// could not connect, bail
		catch (Exception $e)
		{
			$this->client = null;
			return;
		}
		// set data into session for reuse later
		$session->set('jinboundsalesforce.wsdl', $wsdlfile);
		$session->set('jinboundsalesforce.location', $this->client->getLocation());
		$session->set('jinboundsalesforce.id', $this->client->getSessionId());
	}
	
	public function onAfterInitialise()
	{
		if (JFactory::getApplication()->isSite() || !PLG_SYSTEM_JINBOUNDSALESFORCE)
		{
			return;
		}
		$option = array_key_exists('option', $_REQUEST) ? $_REQUEST['option'] : '';
		$view = array_key_exists('view', $_REQUEST) ? $_REQUEST['view'] : '';
		if ('plg_system_jinboundsalesforce' !== $option)
		{
			return;
		}
		$method = 'execTask' . ucwords($view);
		if (method_exists($this, $method))
		{
			$this->$method();
		}
		jexit();
	}
	
	private function execTaskLiveupdate()
	{
		require_once JPATH_ROOT . '/plugins/system/jinboundsalesforce/liveupdate/liveupdate.php';
		$updateInfo = LiveUpdate::getUpdateInformation();
		if ($updateInfo->hasUpdates) {
			echo JText::sprintf('PLG_SYSTEM_JINBOUNDSALESFORCE_UPDATE_HASUPDATES', $updateInfo->version);
		}
	}
	
	private function execTaskForm($token = true)
	{
		if ($token)
		{
			JFactory::getSession()->checkToken('get') or die(JText::_('JINVALID_TOKEN'));
		}
		$view = $this->getFieldView();
		$view->errors = $this->errors;
		$view->field  = $this->app->input->get('field', '', 'string');
		echo $view->display();
	}
	
	private function execTaskClose($file = null)
	{
		if (is_null($file))
		{
			$this->execTaskUpload();
			return;
		}
		$view = $this->getFieldView();
		$view->setLayout('close');
		$view->field = $this->app->input->get('field', '', 'string');
		$view->file  = $file;
		echo $view->display();
	}
	
	private function execTaskUpload()
	{
		JFactory::getSession()->checkToken() or die(JText::_('JINVALID_TOKEN'));
		$file = JFactory::getApplication()->input->files->get('wsdl');
		if (empty($file) || !is_array($file))
		{
			$this->errors[] = JText::_('PLG_SYSTEM_JINBOUNDSALESFORCE_NO_UPLOAD_FILE');
			$this->execTaskForm(false);
			return;
		}
		$filename = JFile::makeSafe($file['name']);
		$ext      = JFile::getExt($filename);
		$src      = $file['tmp_name'];
		$dest     = dirname(__FILE__) . "/wsdl/" . $filename;
		if (strtolower($ext) !== 'xml')
		{
			$this->errors[] = JText::_('PLG_SYSTEM_JINBOUNDSALESFORCE_FILE_MUST_BE_XML');
			$this->execTaskForm(false);
			return;
		}
		if (!JFile::upload($src, $dest))
		{
			$this->errors[] = JText::_('PLG_SYSTEM_JINBOUNDSALESFORCE_COULD_NOT_MOVE_FILE');
			$this->execTaskForm(false);
			return;
		}
		$this->execTaskClose($filename);
	}
	
	public function onJinboundDashboardUpdate()
	{
		return "index.php?option=plg_system_jinboundsalesforce&view=liveupdate";
	}
	
	public function onContentPrepareForm($form)
	{
		if (!PLG_SYSTEM_JINBOUNDSALESFORCE)
		{
			return true;
		}
		if (!($form instanceof JForm)) {
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}
		switch ($form->getName())
		{
			case 'com_jinbound.field':
				$file = 'jinboundsalesforce';
				break;
			default: return true;
		}
		JForm::addFormPath(dirname(__FILE__) . '/form');
		JForm::addFieldPath(dirname(__FILE__) . '/field');
		$result = $form->loadFile($file, false);
		return $result;
	}
	
	public function onContentAfterSave($context, $conversion, $isNew)
	{
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();
		// only operate on jinbound conversion contexts
		if ('com_jinbound.conversion' !== $context || !PLG_SYSTEM_JINBOUNDSALESFORCE)
		{
			return;
		}
		// get fields
		$fields = $this->getFieldsByPage($conversion->page_id);
		if (empty($fields))
		{
			return;
		}
		// store the object's fields in an array
		$objectfields = array();
		// decode data
		$formdata = json_decode($conversion->formdata);
		// loop fields
		foreach ($fields as $name => $field)
		{
			// decode params
			$params = json_decode($field->params);
			// check that params is an object
			if (!(is_object($params) && property_exists($params, 'salesforce')
				&& is_object($params->salesforce) && property_exists($params->salesforce, 'mapped_field')))
			{
				continue;
			}
			// add mapped field
			if (!empty($params->salesforce->mapped_field))
			{
				$objectfields[$params->salesforce->mapped_field] = $formdata->lead->$name;
			}
		}
		if (empty($objectfields))
		{
			return;
		}
		$client = $this->getClient();
		if ($client)
		{
			// create a new SObject
			$object = new SObject();
			$object->type = 'Contact';
			$object->fields = $objectfields;
			// check response
			try
			{
				$response = $client->create(array($object));
			}
			catch (Exception $e)
			{
				// TODO
			}
		}
		// TODO save response ids?
	}
	
	protected function getFieldsByPage($page_id)
	{
		$db = JFactory::getDbo();
		$rows = $db->setQuery($db->getQuery(true)
			->select('Field.*')
			->from('#__jinbound_fields AS Field')
			->leftJoin('#__jinbound_form_fields AS FormFields ON FormFields.field_id = Field.id')
			->leftJoin('#__jinbound_pages AS Page ON FormFields.form_id = Page.formid AND Page.id = ' . (int) $page_id)
			->where('Field.published = 1')
			->group('Field.id')
		)->loadObjectList();
		$result = array();
		foreach ($rows as $row)
		{
			$result[$row->name] = $row;
		}
		return $result;
	}
	
	public function onJInboundSalesforceFields(&$options)
	{
		// check that this plugin can be run
		if (!PLG_SYSTEM_JINBOUNDSALESFORCE)
		{
			return;
		}
		// get the client
		$client  = $this->getClient();
		// ensure there is a client
		if (!(is_object($client) && method_exists($client, 'describeSObject')))
		{
			return;
		}
		// get the contact
		$contact = $client->describeSObject('Contact');
		// check the contact object
		if (!(is_object($contact) && property_exists($contact, 'fields')
			&& is_array($contact->fields) && !empty($contact->fields)))
		{
			return;
		}
		foreach ($contact->fields as $field)
		{
			// only show fields that can be created
			if (!$field->createable || $field->deprecatedAndHidden)
			{
				continue;
			}
			$options[] = JHtml::_('select.option', $field->name, $field->label);
		}
	}
	
	/**
	 * gets a new instance of the base field view
	 * 
	 * @return JInboundFieldView
	 */
	protected function getFieldView() {
		$viewConfig = array('template_path' => dirname(__FILE__) . '/field/wsdl');
		$view = new JInboundFieldView($viewConfig);
		return $view;
	}
}
