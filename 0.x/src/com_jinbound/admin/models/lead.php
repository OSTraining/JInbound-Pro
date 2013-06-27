<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInboundAdminModel', JPATH_ADMINISTRATOR.'/components/com_jinbound/libraries/models/basemodeladmin.php');

/**
 * This models supports retrieving a lead.
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelLead extends JInboundAdminModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $context  = 'com_jinbound.lead';

	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm($this->option.'.'.$this->name, $this->name, array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}
	
	public function getItem($id = null) {
		// get our item
		$item = parent::getItem($id);
		
		if (!is_object($item)) {
			return $item;
		}
		
		// add the contact
		jimport('joomla.database.table');
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_contact/tables');
		$contact = JTable::getInstance('Contact', 'ContactTable');
		if ($item->contact_id) {
			$contact->load($item->contact_id);
		}
		$item->_contact = $contact;
		
		return $item;
	}
	
	public function status($id, $value) {
		$db = JFactory::getDbo();
		
		$db->setQuery($db->getQuery(true)
			->update('#__jinbound_leads')
			->set($db->quoteName('status_id') . ' = ' . (int) $value)
			->where($db->quoteName('id') . ' = ' . (int) $id)
		);
		
		$db->query();
	}
	
	public function priority($id, $value) {
		$db = JFactory::getDbo();
		
		$db->setQuery($db->getQuery(true)
			->update('#__jinbound_leads')
			->set($db->quoteName('priority_id') . ' = ' . (int) $value)
			->where($db->quoteName('id') . ' = ' . (int) $id)
		);
		
		$db->query();
	}
	
	public function getNotes($id = null) {
		$item = $this->getItem($id);
		$db = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->select('id, created, text')
			->from('#__jinbound_notes')
			->where('published = 1')
			->where('lead_id = ' . (int) $item->id)
		);
		
		try {
			$notes = $db->loadObjectList();
			if (!is_array($notes) || empty($notes)) {
				throw new Exception('Empty');
			}
		}
		catch (Exception $e) {
			$notes = array();
		}
		
		return $notes;
	}
	
	public function getPage($id = null) {
		$item = $this->getItem($id);
		$db = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->select('*')
			->from('#__jinbound_pages')
			->where('id = ' . (int) $item->page_id)
		);
		
		try {
			$page = $db->loadObject();
			if (!is_object($page) || empty($page)) {
				throw new Exception('Empty');
			}
		}
		catch (Exception $e) {
			$page = array();
		}
		
		return $page;
	}
	
	public function getRecords($id = null) {
		$item = $this->getItem($id);
		$db = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->select('Record.*')
			->from('#__jinbound_emails_records AS Record')
			->select('Email.subject AS email_subject')
			->select('Email.name AS email_name')
			->leftJoin('#__jinbound_emails AS Email ON Email.id = Record.email_id')
			->where('Record.lead_id = ' . (int) $item->id)
			->group('Record.id')
		);
		
		try {
			$records = $db->loadObjectList();
			if (!is_array($records) || empty($records)) {
				throw new Exception('Empty');
			}
		}
		catch (Exception $e) {
			$records = array();
		}
		
		return $records;
	}
}
