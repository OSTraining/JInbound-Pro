<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundTable', 'table');

class JInboundTableLead extends JInboundTable
{
	function __construct(&$db) {
		parent::__construct('#__jinbound_leads', 'id', $db);
	}
	
	/**
	 * override to save a contact with this lead
	 * 
	 * (non-PHPdoc)
	 * @see JInboundTable::store()
	 */
	public function store($updateNulls = false) {
		$isNew = empty($this->id);
		$store = parent::store();
		if ($store) {
			// either update or add a contact
			jimport('joomla.database.table');
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_contact/tables');
			$contact = JTable::getInstance('Contact', 'ContactTable');
			
			if ($item->contact_id) {
				$contact->load($item->contact_id);
			}
			
			$bind = array(
				'name'      => $this->first_name . ' ' . $this->last_name
			,	'address'   => $this->address
			,	'telephone' => $this->phone
			,	'email_to'  => $this->email
			);
			
			if (!$contact->bind($bind)) {
				JFactory::getApplication()->enqueueMessage('bind: ' . $contact->getError());
				return $store;
			}
			
			if (!$contact->check()) {
				JFactory::getApplication()->enqueueMessage('check: ' . $contact->getError());
				return $store;
			}
			
			if (!$contact->store()) {
				JFactory::getApplication()->enqueueMessage('store: ' . $contact->getError());
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
}
