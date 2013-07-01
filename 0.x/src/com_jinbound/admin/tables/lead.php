<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundTable', 'table');

class JInboundTableLead extends JInboundTable
{
	private $_contactData;
	
	function __construct(&$db) {
		parent::__construct('#__jinbound_leads', 'id', $db);
	}
	
	public function load($keys = null, $reset = true) {
		$load = parent::load($keys, $reset);
		$contact = $this->getContact();
		$this->address = $contact->address;
		$this->phone   = $contact->telephone;
		$this->email   = $contact->email_to;
		$this->website = $contact->webpage;
		return $load;
	}
	
	public function bind($array, $ignore = '') {
		$columns = $this->getFields();
		$unset   = array();
		if (!empty($array)) {
			foreach ($array as $key => $value) {
				if (false !== array_search($key, $columns)) {
					continue;
				}
				$var = '_' . $key;
				$this->$var = $value;
				$unset[] = $var;
			}
			if (!empty($unset)) {
				foreach ($unset as $var) {
					unset($array[$var]);
				}
			}
		}
		return parent::bind($array, $ignore);
	}
	
	/**
	 * override to save a contact with this lead
	 * 
	 * (non-PHPdoc)
	 * @see JInboundTable::store()
	 */
	public function store($updateNulls = false) {
		$app   = JFactory::getApplication();
		$isNew = empty($this->id);
		foreach (array('address', 'email', 'phone', 'website') as $col) {
			if (property_exists($this, $col)) {
				unset($this->$col);
			}
		}
		$store = parent::store();
		if ($store) {
			// get the category id for jinbound contacts
			$this->_db->setQuery($this->_db->getQuery(true)
				->select('id')
				->from('#__categories')
				->where($this->_db->quoteName('extension') . ' = ' . $this->_db->quote('com_contact'))
				->where($this->_db->quoteName('published') . ' = 1')
				->where($this->_db->quoteName('note') . ' = ' . $this->_db->quote('com_jinbound'))
			);
			try {
				$catid = $this->_db->loadResult();
			}
			catch (Exception $e) {
				$app->enqueueMessage(JText::_('COM_JINBOUND_NO_CONTACT_CATEGORY'), 'error');
				return $store;
			}
			// either update or add a contact
			$contact = $this->getContact();
			
			$bind = array(
				'name'      => $this->first_name . ' ' . $this->last_name
			,	'address'   => $this->_address
			,	'telephone' => $this->_phone
			,	'email_to'  => $this->_email
			,	'webpage'   => $this->_website
			,	'catid'     => $catid
			,	'published' => $this->published
			,	'language'  => '*'
			);
			
			if (!$contact->bind($bind)) {
				$app->enqueueMessage('bind: ' . $contact->getError());
				return $store;
			}
			
			if (!$contact->check()) {
				$app->enqueueMessage('check: ' . $contact->getError());
				return $store;
			}
			
			if (!$contact->store()) {
				$app->enqueueMessage('store: ' . $contact->getError());
				return $store;
			}
			
			$this->contact_id = $contact->id;
			$k = $this->_tbl_key;
			
			if ($this->$k) {
				$stored = $this->_db->updateObject($this->_tbl, $this, $k, $updateNulls);
			}
			else {
				$stored = $this->_db->insertObject($this->_tbl, $this, $k);
			}
		}
		return $store;
	}
	
	public function getContact() {
		// either update or add a contact
		jimport('joomla.database.table');
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_contact/tables');
		$this->_contact = JTable::getInstance('Contact', 'ContactTable');
			
		if ($this->contact_id) {
			$this->_contact->load($this->contact_id);
		}
		
		return $this->_contact;
	}
}
