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
		// return the form
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
		
		// now that we have the contact, load formdata for any other leads that may be linked to the contact
		$item->_formdatas = array();
		if ($item->contact_id) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				// form data
				->select('Lead.formdata')
				// modified dates
				->select('Lead.created')
				->select('Lead.created_by')
				->select('Creator.username AS created_by_name')
				->select('Lead.modified')
				->select('Lead.modified_by')
				->select('Editor.username AS modified_by_name')
				// add ip
				->select('Lead.ip')
				// page name
				->select('Page.name AS pagename')
				->from('#__jinbound_leads AS Lead')
				->leftJoin('#__jinbound_pages AS Page ON Page.id = Lead.page_id')
				->leftJoin('#__users AS Creator ON Creator.id = Lead.created_by')
				->leftJoin('#__users AS Editor ON Editor.id = Lead.modified_by')
				->where('Lead.contact_id = ' . (int) $item->contact_id)
				//->where('Lead.id <> ' . (int) $item->id)
				->group('Lead.id')
			;
			$db->setQuery($query);
			
			try {
				$datas = $db->loadObjectList();
			}
			catch (Exception $e) {
				$datas = false;
			}
			
			if (is_array($datas) && !empty($datas)) {
				foreach ($datas as $data) {
					$reg = new JRegistry;
					$reg->loadString($data->formdata);
					$reg->set('pagename', $data->pagename);
					$reg->set('created', $data->created);
					$reg->set('created_by', $data->created_by);
					$reg->set('created_by_name', $data->created_by_name);
					$reg->set('modified', $data->modified);
					$reg->set('modified_by', $data->modified_by);
					$reg->set('modified_by_name', $data->modified_by_name);
					$reg->set('ip', $data->ip);
					$item->_formdatas[] = $reg->toArray();
				}
			}
			
		}
		
		// convert the formdata to an object
		$formdata = new JRegistry;
		if (!empty($item->formdata)) {
			$formdata->loadString($item->formdata);
			$item->formdata = $formdata;
		}
		
		return $item;
	}
	
	/**
	 * set the lead status details for an item
	 * 
	 * @param unknown_type $id
	 * @param unknown_type $value
	 * @return mixed
	 */
	public function status($id, $value) {
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		
		$db = JFactory::getDbo();
		
		$db->setQuery($db->getQuery(true)
			->update('#__jinbound_leads')
			->set($db->quoteName('status_id') . ' = ' . (int) $value)
			->where($db->quoteName('id') . ' = ' . (int) $id)
		);
		
		$return = $db->query();
		
		// Trigger the onContentChangeState event.
		$result = $dispatcher->trigger('onContentChangeState', array('com_jinbound.lead.status', array($id), $value));
		
		if (in_array(false, $result, true)) {
			return false;
		}
		
		return $return;
	}
	
	/**
	 * set the lead priority details
	 * 
	 * @param unknown_type $id
	 * @param unknown_type $value
	 * @return mixed
	 */
	public function priority($id, $value) {
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		
		$db = JFactory::getDbo();
		
		$db->setQuery($db->getQuery(true)
			->update('#__jinbound_leads')
			->set($db->quoteName('priority_id') . ' = ' . (int) $value)
			->where($db->quoteName('id') . ' = ' . (int) $id)
		);
		
		$return = $db->query();
		
		// Trigger the onContentChangeState event.
		$result = $dispatcher->trigger('onContentChangeState', array('com_jinbound.lead.priority', array($id), $value));
		
		if (in_array(false, $result, true)) {
			return false;
		}
		
		return $return;
	}
	
	/**
	 * get lead notes
	 * 
	 * @param unknown_type $id
	 */
	public function getNotes($id = null) {
		if (property_exists($this, 'item') && $this->item && $this->item->id == $id) {
			$item = $this->item;
		}
		else {
			$item = $this->getItem($id);
		}
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
	
	/**
	 * get lead page
	 * 
	 * @param unknown_type $id
	 */
	public function getPage($id = null) {
		if (property_exists($this, 'item') && $this->item && $this->item->id == $id) {
			$item = $this->item;
		}
		else {
			$item = $this->getItem($id);
		}
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
	
	/**
	 * get email records for lead
	 * 
	 * @param unknown_type $id
	 */
	public function getRecords($id = null) {
		if (property_exists($this, 'item') && $this->item && $this->item->id == $id) {
			$item = $this->item;
		}
		else {
			$item = $this->getItem($id);
		}
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
	
	public function getCampaign($id = null) {
		if (property_exists($this, 'item') && $this->item && $this->item->id == $id) {
			$item = $this->item;
		}
		else {
			$item = $this->getItem($id);
		}
		// our return object
		$data = new stdClass;
		$data->campaign = false;
		$data->emails   = false;
		// if the item has no campaign attached there is nothing to do
		if (empty($item->campaign_id)) {
			return $data;
		}
		// get the campaign from the database
		$db = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->select('Campaign.*')
			->from('#__jinbound_campaigns AS Campaign')
			->where('Campaign.id = ' . (int) $item->campaign_id)
		);
		
		try {
			$campaign = $db->loadObject();
		}
		catch (Exception $e) {
			// if there is an error just return the empty object
			return $data;
		}
		// if this is not an object bail
		if (!is_object($campaign)) {
			return $data;
		}
		// assign
		$data->campaign = $campaign;
		
		// fetch the emails associated with this campaign
		$db->setQuery($db->getQuery(true)
			->select('Email.*')
			->from('#__jinbound_emails AS Email')
			->select('Record.sent')
			->leftJoin('#__jinbound_emails_records AS Record ON Record.email_id = Email.id AND Record.lead_id = ' . (int) $item->id)
			->where('Email.campaign_id = ' . (int) $item->campaign_id)
			->group('Email.id')
		);
		
		try {
			$emails = $db->loadObjectList();
		}
		catch (Exception $e) {
			$emails = false;
		}
		// if the emails aren't empty, fix dates and assign
		if (!empty($emails)) {
			foreach ($emails as &$email) {
				// force empty (null, false, whatever) to be false
				if (empty($email->sent)) {
					$email->sent = false;
					continue;
				}
				// try to convert - any errors should set sent to false
				try {
					$date = new DateTime($email->sent);
				}
				catch (Exception $e) {
					$date = false;
				}
				if (!is_a($date, 'DateTime')) {
					$date = false;
				}
				$email->sent = $date;
			}
			$data->emails = $emails;
		}
		
		return $data;
	}
}
