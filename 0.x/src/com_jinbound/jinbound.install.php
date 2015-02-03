<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.form.form');

class com_JInboundInstallerScript
{
	public function uninstall($parent) {
		$app = JFactory::getApplication();
		// TODO: remove contacts added with jinbound, including category (must remove contacts first)
		$db = JFactory::getDbo();
		// first get the com_jinbound categories
		$db->setQuery($db->getQuery(true)
			->select('id')
			->from('#__categories')
			->where($db->quoteName('extension') . ' = ' . $db->quote('com_contact'))
			->where($db->quoteName('note') . ' = ' . $db->quote('com_jinbound'))
		);
		
		try {
			$catids = $db->loadColumn();
		}
		catch (Exception $e) {
			$catids = array();
		}
		
		if (is_array($catids) && !empty($catids)) {
			$deletedCats  = array();
			$deletedLeads = array();
			
			JArrayHelper::toInteger($catids);
			// we cannot just do a delete in the database
			// we have to go through code so we can clean up assets, etc
			$db->setQuery($db->getQuery(true)
				->select('id')
				->from('#__contact_details')
				->where($db->quoteName('catid') . ' IN (' . implode(',', $catids) . ')')
			);
			
			try {
				$ids = $db->loadColumn();
			}
			catch (Exception $e) {
				$ids = array();
			}
			
			if (is_array($ids) && !empty($ids)) {
				jimport('joomla.database.table');
				JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_contact/tables');
				foreach ($ids as $id) {
					$table = JTable::getInstance('Contact', 'ContactTable');
					$table->load($id);
					if ($table->id == $id) {
						if ($table->delete($id)) {
							$deletedLeads[] = $id;
						}
					}
				}
			}
			
			// now delete the category
			foreach ($catids as $catid) {
				$table = JTable::getInstance('Category');
				$table->load($catid);
				if ($table->id == $catid) {
					if ($table->delete($catid)) {
						$deletedCats[] = $catid;
					}
				}
			}
			
			if (!empty($deletedCats)) {
				$app->enqueueMessage(JText::sprintf('COM_JINBOUND_UNINSTALL_DELETED_N_CATEGORIES', count($deletedCats)));
			}
			if (!empty($deletedLeads)) {
				$app->enqueueMessage(JText::sprintf('COM_JINBOUND_UNINSTALL_DELETED_N_LEADS', count($deletedLeads)));
			}
		}
	}
	
	
	public function postflight($type, $parent) {
		$lang = JFactory::getLanguage();
		if (method_exists($parent, 'source')) {
			$root = $parent->getPath('source');
		}
		else {
			$root = $parent->getParent()->getPath('source');
		}
		$lang->load('com_jinbound', $root) || $lang->load('com_jinbound', JPATH_ADMINISTRATOR);
		$lang->load('com_jinbound.sys', $root) || $lang->load('com_jinbound.sys', JPATH_ADMINISTRATOR);
		switch ($type) {
			case 'install':
			case 'discover_install':
				// fix component config
				$this->_saveDefaults($parent);
			case 'update':
				$this->_saveDefaultAssets($parent);
				$this->_forceReportEmailOption($parent);
				// move data from the older 1.0 schema
				$this->_migrateOldData($root);
				// contacts no longer need categories?
				$this->_checkContactCategory();
				$this->_checkInboundCategory();
				$this->_checkCampaigns($root);
				$this->_checkContactSubscriptions();
				$this->_checkDefaultPriorities();
				$this->_checkDefaultStatuses();
				$this->_checkEmailVersions();
				$this->_fixMissingLanguageDefaults();
				break;
		}
	}
	
	private function _forceReportEmailOption($parent)
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		// get the params
		$db->setQuery($db->getQuery(true)
			->select('params')
			->from('#__extensions')
			->where('element = ' . $db->quote('com_jinbound'))
		);
		try {
			$json = $db->loadResult();
			$params = json_decode($json);
			if (!is_object($params))
			{
				throw new UnexpectedValueException("Data is not an object");
			}
		}
		catch (Exception $e) {
			$app->enqueueMessage($e->getMessage(), 'error');
			return;
		}
		// if this variable is set, don't update the value
		// defaults method will handle new installs
		if (property_exists($params, 'report_force_admin'))
		{
			return;
		}
		// check that there's a value - don't mess with non-empty values
		if (property_exists($params, 'report_recipients') && !empty($params->report_recipients))
		{
			return;
		}
		// set from config
		$config = new JConfig();
		$params->report_recipients = $config->mailfrom;
		// save
		$db->setQuery($db->getQuery(true)
			->update('#__extensions')
			->set('params = ' . $db->quote(json_encode($params)))
			->where('element = ' . $db->quote('com_jinbound'))
		);
		try {
			$db->query();
		}
		catch (Exception $e) {
			$app->enqueueMessage($e->getMessage(), 'error');
		}
	}
	
	private function _checkCampaigns($sourcepath)
	{
		JTable::addIncludePath($sourcepath . '/admin/tables');
		$db = JFactory::getDbo();
		try
		{
			$campaigns = $db->setQuery($db->getQuery(true)
				->select('*')
				->from('#__jinbound_campaigns')
			)->loadObjectList();
			
			if (!empty($campaigns))
			{
				return;
			}
			
			$data = array(
				'name'       => JText::_('COM_JINBOUND_DEFAULT_CAMPAIGN_NAME')
			,	'published'  => 1
			,	'created'    => JFactory::getDate()->toSql()
			,	'created_by' => JFactory::getUser()->get('id')
			);
			
			$table = JTable::getInstance('Campaign', 'JInboundTable');
			if (!($table->bind($data) && $table->check() && $table->store()))
			{
				throw new RuntimeException($table->getError());
			}
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}
	
	/**
	 * adds initial versions to all emails, updates records to reflect
	 * 
	 * NOTE: can't do anything about data we didn't track before, sorry folks
	 * 
	 */
	private function _checkEmailVersions() {
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();
		
		// get all the emails that don't appear in the email versions table
		$db->setQuery('SELECT Email.id FROM #__jinbound_emails AS Email LEFT JOIN #__jinbound_emails_versions AS Version ON Email.id = Version.email_id WHERE Version.id IS NULL GROUP BY Email.id');
		try {
			$emails = $db->loadObjectList();
		}
		catch (Exception $e) {
			$app->enqueueMessage($e->getMessage(), 'error');
			return;
		}
		
		if (empty($emails)) {
			return;
		}
		
		foreach ($emails as $email) {
			$db->setQuery('INSERT INTO #__jinbound_emails_versions (email_id, subject, htmlbody, plainbody) SELECT id, subject, htmlbody, plainbody FROM #__jinbound_emails WHERE id = ' . $email->id);
			try {
				$db->query();
			}
			catch (Exception $e) {
				$app->enqueueMessage($e->getMessage(), 'error');
				continue;
			}
			// update version_id in records table to match the newly created version
			$db->setQuery('UPDATE #__jinbound_emails_records SET version_id = ((SELECT MAX(id) FROM #__jinbound_emails_versions WHERE email_id = ' . $email->id . ')) WHERE email_id = ' . $email->id);
			try {
				$db->query();
			}
			catch (Exception $e) {
				$app->enqueueMessage($e->getMessage(), 'error');
				continue;
			}
		}
	}
	
	private function _saveDefaultAssets(&$parent)
	{
		if (!class_exists('JInboundHelperAccess'))
		{
			if (method_exists($parent, 'extension_root')) {
				$base = $parent->getPath('extension_root');
			}
			else {
				$base = $parent->getParent()->getPath('extension_root');
			}
			foreach (array('jinbound', 'access') as $helper)
			{
				$file = "$base/helpers/$helper.php";
				if (!JFile::exists($file)) {
					continue;
				}
				require_once $file;
			}
		}
		if (!class_exists('JInboundHelperAccess'))
		{
			return;
		}
		foreach (array('campaign', 'contact', 'conversion', 'email', 'page', 'priority', 'status', 'report') as $type)
		{
			if (($parent = JInboundHelperAccess::getParent($type)))
			{
				return;
			}
			JInboundHelperAccess::saveRules($type, new stdClass, false);
		}
	}
	
	private function _saveDefaults(&$parent) {
		$version = new JVersion;
		$global_config = new JConfig();
		
		if (method_exists($parent, 'extension_root')) {
			$configfile = $parent->getPath('extension_root') . '/config.xml';
		}
		else {
			$configfile = $parent->getParent()->getPath('extension_root') . '/config.xml';
		}
		
		if (!JFile::exists($configfile)) {
			return;
		}
		
		$xml       = JFile::read($configfile);
		$form      = JForm::getInstance('installer', $xml, array(), false, '/config');
		$params    = array();
		$fieldsets = $form->getFieldsets();
		
		if (!empty($fieldsets)) {
			foreach ($fieldsets as $fieldset) {
				$fields = $form->getFieldset($fieldset->name);
				if (!empty($fields)) {
					foreach ($fields as $name => $field) {
						$fieldname  = $field->__get('name');
						$fieldvalue = $field->__get('value');
						switch ($fieldname) {
							// force report email value
							case 'report_recipients':
								$fieldvalue = $global_config->mailfrom;
								break;
							// handle some compat params
							case 'load_jquery_back':
							case 'load_jquery_ui_back':
							case 'load_bootstrap_back':
								$fieldvalue = (int) (!$version->isCompatible('3.0.0'));
								break;
							default: break;
						}
						$params[$fieldname] = $fieldvalue;
					}
				}
			}
		}
		
		$db = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->update('#__extensions')
			->set('params = ' . $db->quote(json_encode($params)))
			->where('element = ' . $db->quote($parent->get('element')))
		);
		try {
			$db->query();
		}
		catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		
	}
	
	private function _checkContactCategory() {
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->select('id')
			->from('#__categories')
			->where($db->quoteName('extension') . ' = ' . $db->quote('com_contact'))
			->where($db->quoteName('published') . ' = 1')
			->where($db->quoteName('note') . ' = ' . $db->quote('com_jinbound'))
		);
		try {
			$categories = $db->loadColumn();
		}
		catch (Exception $e) {
			$app->enqueueMessage($e->getMessage());
			return;
		}
		if (is_array($categories) && !empty($categories)) {
			$app->enqueueMessage(JText::_('COM_JINBOUND_CONTACT_CATEGORIES_FOUND'));
			return;
		}
		jimport('joomla.database.table');
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_contact/tables');
		$table = JTable::getInstance('Category');
		$bind = array(
			'parent_id'   => $table->getRootId(),
			'extension'   => 'com_contact',
			'title'       => JText::_('COM_JINBOUND_DEFAULT_CONTACT_CATEGORY_TITLE'),
			'note'        => 'com_jinbound',
			'description' => JText::_('COM_JINBOUND_DEFAULT_CONTACT_CATEGORY_DESCRIPTION'),
			'published'   => 1,
			'language'    => '*'
		);
		if (!$table->bind($bind)) {
			$app->enqueueMessage(JText::_('COM_JINBOUND_CONTACT_CATEGORIES_BIND_ERROR'));
			return;
		}
		if (!$table->check()) {
			$app->enqueueMessage(JText::_('COM_JINBOUND_CONTACT_CATEGORIES_CHECK_ERROR'));
			return;
		}
		if (!$table->store()) {
			$app->enqueueMessage(JText::_('COM_JINBOUND_CONTACT_CATEGORIES_STORE_ERROR'));
			return;
		}
		$table->moveByReference(0, 'last-child', $table->id);
		$app->enqueueMessage(JText::_('COM_JINBOUND_CONTACT_CATEGORIES_INSTALLED'));
	}
	
	private function _checkInboundCategory() {
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->select('id')
			->from('#__categories')
			->where($db->quoteName('extension') . ' = ' . $db->quote('com_jinbound'))
			->where($db->quoteName('published') . ' = 1')
		);
		try {
			$categories = $db->loadColumn();
		}
		catch (Exception $e) {
			$app->enqueueMessage($e->getMessage());
			return;
		}
		if (is_array($categories) && !empty($categories)) {
			$app->enqueueMessage(JText::_('COM_JINBOUND_JINBOUND_CATEGORIES_FOUND'));
			return;
		}
		jimport('joomla.database.table');
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_contact/tables');
		$table = JTable::getInstance('Category');
		$bind = array(
			'parent_id'   => $table->getRootId(),
			'extension'   => 'com_jinbound',
			'title'       => JText::_('COM_JINBOUND_DEFAULT_JINBOUND_CATEGORY_TITLE'),
			'description' => JText::_('COM_JINBOUND_DEFAULT_JINBOUND_CATEGORY_DESCRIPTION'),
			'published'   => 1,
			'language'    => '*'
		);
		if (!$table->bind($bind)) {
			$app->enqueueMessage(JText::_('COM_JINBOUND_JINBOUND_CATEGORIES_BIND_ERROR'));
			return;
		}
		if (!$table->check()) {
			$app->enqueueMessage(JText::_('COM_JINBOUND_JINBOUND_CATEGORIES_CHECK_ERROR'));
			return;
		}
		if (!$table->store()) {
			$app->enqueueMessage(JText::_('COM_JINBOUND_JINBOUND_CATEGORIES_STORE_ERROR'));
			return;
		}
		$table->moveByReference(0, 'last-child', $table->id);
		$app->enqueueMessage(JText::_('COM_JINBOUND_JINBOUND_CATEGORIES_INSTALLED'));
	}
	
	/**
	 * checks for the presence of priorities and if none are found creates them
	 * 
	 */
	private function _checkDefaultPriorities() {
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->select('id')
			->from('#__jinbound_priorities')
			->order('ordering ASC')
		);
		try {
			$priorities = $db->loadColumn();
		}
		catch (Exception $e) {
			$app->enqueueMessage($e->getMessage());
			return;
		}
		if (is_array($priorities) && !empty($priorities)) {
			$app->enqueueMessage(JText::_('COM_JINBOUND_PRIORITIES_FOUND'));
			return;
		}
		jimport('joomla.database.table');
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jinbound/tables');
		
		foreach (array('COLD', 'WARM', 'HOT', 'ON_FIRE') as $i => $p) {
			$table = JTable::getInstance('Priority', 'JInboundTable');
			$bind = array(
				'name'        => JText::_('COM_JINBOUND_PRIORITY_' . $p),
				'description' => JText::_('COM_JINBOUND_PRIORITY_' . $p . '_DESC'),
				'published'   => 1,
				'ordering'    => $i + 1
			);
			if (!$table->bind($bind)) {
				continue;
			}
			if (!$table->check()) {
				continue;
			}
			if (!$table->store()) {
				continue;
			}
		}
	}
	
	private function _checkDefaultStatuses() {
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->select('id')
			->from('#__jinbound_lead_statuses')
		);
		try {
			$statuses = $db->loadColumn();
		}
		catch (Exception $e) {
			$app->enqueueMessage($e->getMessage());
			return;
		}
		if (is_array($statuses) && !empty($statuses)) {
			$app->enqueueMessage(JText::_('COM_JINBOUND_STATUSES_FOUND'));
			return;
		}
		jimport('joomla.database.table');
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jinbound/tables');
		
		$leads   = array('NEW_LEAD', 'NOT_INTERESTED', 'EMAIL', 'VOICEMAIL', 'GOAL_COMPLETED');
		$default = 0;
		$final   = count($leads) - 1;
		
		foreach ($leads as $i => $p) {
			$table = JTable::getInstance('Status', 'JInboundTable');
			$bind = array(
				'name'        => JText::_('COM_JINBOUND_STATUS_' . $p),
				'description' => JText::_('COM_JINBOUND_STATUS_' . $p . '_DESC'),
				'published'   => 1,
				'ordering'    => $i + 1,
				'default'     => (int) ($i == $default),
				'active'      => (int) !('NOT_INTERESTED' == $p),
				'final'       => (int) ($i == $final)
			);
			if (!$table->bind($bind)) {
				continue;
			}
			if (!$table->check()) {
				continue;
			}
			if (!$table->store()) {
				continue;
			}
		}
	}
	
	function _checkContactSubscriptions() {
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->select('Contact.id')
			->from('#__contact_details AS Contact')
			->leftJoin('#__jinbound_subscriptions AS Subs ON Subs.contact_id = Contact.id')
			->where('Subs.enabled IS NULL')
			->group('Contact.id')
		);
		try {
			$contacts = $db->loadColumn();
			if (!is_array($contacts) || empty($contacts)) {
				return;
			}
		}
		catch (Exception $e) {
			$app->enqueueMessage($e->getMessage());
			return;
		}
		JArrayHelper::toInteger($contacts);
		$query = $db->getQuery(true)
			->insert('#__jinbound_subscriptions')
			->columns(array('contact_id', 'enabled'))
		;
		foreach ($contacts as $contact) {
			$query->values("$contact, 1");
		}
		$db->setQuery($query);
		try {
			$db->query();
		}
		catch (Exception $e) {
			$app->enqueueMessage($e->getMessage());
			return;
		}
	}
	
	/**
	 * some language strings were not present and saved to the database
	 * 
	 */
	private function _fixMissingLanguageDefaults() {
		$tags = array(
			'lead_statuses' => array(
				'COM_JINBOUND_STATUS_CONVERTED_DESC'
			,	'COM_JINBOUND_STATUS_EMAIL_DESC'
			,	'COM_JINBOUND_STATUS_NEW_LEAD_DESC'
			,	'COM_JINBOUND_STATUS_NOT_INTERESTED_DESC'
			,	'COM_JINBOUND_STATUS_VOICEMAIL_DESC'
			)
		,	'priorities' => array(
				'COM_JINBOUND_PRIORITY_COLD_DESC'
			,	'COM_JINBOUND_PRIORITY_WARM_DESC'
			,	'COM_JINBOUND_PRIORITY_HOT_DESC'
			,	'COM_JINBOUND_PRIORITY_ON_FIRE_DESC'
			)
		);
		
		// connect to the database and fix each one
		$db    = JFactory::getDbo();
		foreach ($tags as $table => $labels) {
			foreach ($labels as $label) {
				$db->setQuery($db->getQuery(true)
					->update('#__jinbound_' . $table)
					->set($db->quoteName('description') . ' = ' . $db->quote(JText::_($label)))
					->where($db->quoteName('description') . ' = ' . $db->quote($label))
				);
				
				try {
					$db->query();
				}
				catch (Exception $e) {
					// skip
				}
			}
		}
	}
	
	/**
	 * Takes data from old 1.0.x installs and migrates to new schema
	 * 
	 * take #__jinbound_leads and #__contact_details data and move to 
	 * #__jinbound_contacts and #__jinbound_conversions as well as entries in
	 * #__jinbound_contacts_campaigns, #__jinbound_contacts_priorities, and
	 * #__jinbound_contacts_statuses
	 * 
	 * Conversion table:
	 * 
	 * #__jinbound_leads.id               = NO CONVERSION
	 * NO CONVERSION                      = #__jinbound_contacts.id
	 * NO CONVERSION                      = #__jinbound_contacts.asset_id
	 * NO CONVERSION                      = #__jinbound_contacts.cookie
	 * NO CONVERSION                      = #__jinbound_contacts.alias
	 * NO CONVERSION                      = #__jinbound_conversions.id
	 * NO CONVERSION                      = #__jinbound_conversions.contact_id
	 * #__jinbound_leads.asset_id         = NO CONVERSION, DELETE ASSET
	 * #__jinbound_leads.page_id          = #__jinbound_conversions.page_id
	 * #__jinbound_leads.contact_id       = #__jinbound_contacts.core_contact_id
	 * #__jinbound_leads.priority_id      = #__jinbound_contacts_priorities.priority_id
	 * #__jinbound_leads.status_id        = #__jinbound_contacts_statuses.status_id
	 * #__jinbound_leads.campaign_id      = #__jinbound_contacts_campaigns.campaign_id
	 * #__jinbound_leads.first_name       = #__jinbound_contacts.first_name
	 * #__jinbound_leads.last_name        = #__jinbound_contacts.last_name
	 * #__jinbound_leads.ip               = NO CONVERSION
	 * #__contact_details.webpage         = #__jinbound_contacts.website
	 * #__contact_details.email_to        = #__jinbound_contacts.email
	 * #__contact_details.address         = #__jinbound_contacts.address
	 * #__contact_details.suburb          = #__jinbound_contacts.suburb
	 * #__contact_details.state           = #__jinbound_contacts.state
	 * #__contact_details.country         = #__jinbound_contacts.country
	 * #__contact_details.postcode        = #__jinbound_contacts.postcode
	 * #__contact_details.telephone       = #__jinbound_contacts.telephone
	 * #__contact_details.user_id         = #__jinbound_contacts.user_id
	 * #__jinbound_leads.published        = #__jinbound_contacts.published
	 * #__jinbound_leads.created          = #__jinbound_contacts.created
	 * #__jinbound_leads.created_by       = #__jinbound_contacts.created_by
	 * #__jinbound_leads.modified         = #__jinbound_contacts.modified
	 * #__jinbound_leads.modified_by      = #__jinbound_contacts.modified_by
	 * #__jinbound_leads.checked_out      = #__jinbound_contacts.checked_out
	 * #__jinbound_leads.checked_out_time = #__jinbound_contacts.checked_out_time
	 * #__jinbound_leads.formdata         = #__jinbound_conversions.formdata
	 * 
	 * #__jinbound_pages.id               = #__jinbound_landing_pages_hits.page_id
	 * #__jinbound_pages.hits             = #__jinbound_landing_pages_hits.hits
	 */
	private function _migrateOldData($source_path)
	{
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();
		$cfg = new JConfig;
		// get the data from the old table if it exists
		try
		{
			$hasolddata = $db->setQuery('SHOW TABLES LIKE "' . $cfg->dbprefix . 'jinbound_leads"')->loadResult();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
			return;
		}
		if (empty($hasolddata))
		{
			return;
		}
		try
		{
			$oldcontacts = $db->setQuery($db->getQuery(true)
				->select('a.*')
				->select('b.webpage')
				->select('b.email_to')
				->select('b.address')
				->select('b.suburb')
				->select('b.state')
				->select('b.country')
				->select('b.postcode')
				->select('b.telephone')
				->select('b.user_id')
				->from('#__jinbound_leads AS a')
				->leftJoin('#__contact_details AS b ON b.id = a.contact_id')
			)->loadObjectList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
			return;
		}
		if (empty($oldcontacts))
		{
			return;
		}
		// get the model
		require_once $source_path . '/admin/models/contact.php';
		require_once $source_path . '/admin/tables/contact.php';
		require_once $source_path . '/admin/tables/conversion.php';
		$contact_model = new JInboundModelContact();
		// save all the contacts and conversions
		foreach ($oldcontacts as $oldcontact)
		{
			// init
			$contact_data    = array();
			$conversion_data = array();
			// populate contact
			$contact_data['user_id']          = (int) $oldcontact->user_id;
			$contact_data['core_contact_id']  = (int) $oldcontact->contact_id;
			$contact_data['first_name']       = $oldcontact->first_name;
			$contact_data['last_name']        = $oldcontact->last_name;
			$contact_data['website']          = $oldcontact->webpage;
			$contact_data['email']            = $oldcontact->email_to;
			$contact_data['address']          = $oldcontact->address;
			$contact_data['suburb']           = $oldcontact->suburb;
			$contact_data['state']            = $oldcontact->state;
			$contact_data['country']          = $oldcontact->country;
			$contact_data['postcode']         = $oldcontact->postcode;
			$contact_data['telephone']        = $oldcontact->telephone;
			$contact_data['published']        = $oldcontact->published;
			$contact_data['created']          = $oldcontact->created;
			$contact_data['created_by']       = (int) $oldcontact->created_by;
			$contact_data['modified']         = $oldcontact->modified;
			$contact_data['modified_by']      = (int) $oldcontact->modified_by;
			$contact_data['checked_out']      = (int) $oldcontact->checked_out;
			$contact_data['checked_out_time'] = $oldcontact->checked_out_time;
			// bind
			$contact = JTable::getInstance('Contact', 'JInboundTable');
			try
			{
				if (!$contact->bind($contact_data))
				{
					throw new Exception('Cannot migrate contact!');
				}
				if (!$contact->check())
				{
					throw new Exception('Cannot migrate contact!');
				}
				if (!$contact->store())
				{
					throw new Exception('Cannot migrate contact!');
				}
			}
			catch (Exception $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
				continue;
			}
			// populate conversion
			$conversion_data['page_id']          = (int) $oldcontact->page_id;
			$conversion_data['contact_id']       = (int) $contact->id;
			$conversion_data['published']        = (int) $oldcontact->published;
			$conversion_data['created']          = $oldcontact->created;
			$conversion_data['created_by']       = (int) $oldcontact->created_by;
			$conversion_data['modified']         = $oldcontact->modified;
			$conversion_data['modified_by']      = (int) $oldcontact->modified_by;
			$conversion_data['checked_out']      = $oldcontact->checked_out;
			$conversion_data['checked_out_time'] = $oldcontact->checked_out_time;
			$conversion_data['formdata']         = (array) json_decode($oldcontact->formdata);
			// bind
			$conversion = JTable::getInstance('Conversion', 'JInboundTable');
			try
			{
				if (!$conversion->bind($contact_data))
				{
					throw new Exception('Cannot migrate conversion');
				}
				if (!$conversion->check())
				{
					throw new Exception('Cannot migrate conversion');
				}
				if (!$conversion->store())
				{
					throw new Exception('Cannot migrate conversion');
				}
				if ($conversion->id && $contact->id)
				{
					$db->setQuery($db->getQuery(true)
						->update('#__jinbound_conversions')
						->set('page_id = ' . (int) $conversion_data['page_id'])
						->set('contact_id = ' . (int) $contact->id)
						->where('id = ' . (int) $conversion->id)
					)->query();
				}
				else
				{
					throw new Exception('Could not find contact or conversion ids');
				}
			}
			catch (Exception $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
				continue;
			}
			$contact_model->status($contact->id, $oldcontact->campaign_id, $oldcontact->status_id);
			$contact_model->priority($contact->id, $oldcontact->campaign_id, $oldcontact->priority_id);
			$db->setQuery($db->getQuery(true)
				->insert('#__jinbound_contacts_campaigns')
				->columns(array('contact_id', 'campaign_id', 'enabled'))
				->values($contact->id . ', ' . $oldcontact->campaign_id . ', 1')
			)->query();
		}
		try
		{
			$db->setQuery('DROP TABLE IF EXISTS #__jinbound_leads')->query();
			$db->setQuery('INSERT INTO #__jinbound_landing_pages_hits SELECT NOW() AS day, id AS page_id, hits FROM #__jinbound_pages')->query();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}
		
	}
}
