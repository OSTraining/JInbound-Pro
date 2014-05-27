<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

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
				$this->_checkContactCategory();
				$this->_checkInboundCategory();
				$this->_checkContactSubscriptions();
				$this->_checkDefaultPriorities();
				$this->_checkDefaultStatuses();
				$this->_checkEmailVersions();
				$this->_fixMissingLanguageDefaults();
				break;
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
	
	private function _saveDefaults(&$parent) {
		jimport('joomla.filesystem.file');
		jimport('joomla.form.form');
		
		$version = new JVersion;
		
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
						// handle some compat params
						switch ($fieldname) {
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
		$fix = $db->getQuery(true)->update('#__jinbound_leads')->where('priority_id = 0');
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
			$fix->set('priority_id = ' . $priorities[0]);
			$db->setQuery($fix);
			try {
				$db->query();
			}
			catch (Exception $e) {
				
			}
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
		
		$leads   = array('NEW_LEAD', 'NOT_INTERESTED', 'EMAIL', 'VOICEMAIL', 'CONVERTED');
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
			$table->bind($bind);
			$table->check();
			$table->store();
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
}
