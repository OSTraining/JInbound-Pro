<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInboundAdminModel', JPATH_ADMINISTRATOR.'/components/com_jinbound/libraries/models/basemodeladmin.php');

/**
 * This models supports retrieving a lead status.
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelStatus extends JInboundAdminModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $context  = 'com_jinbound.status';
	
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm($this->option.'.'.$this->name, $this->name, array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}
	
	public function setDefault($id = 0) {
		// Initialise variables.
		$user   = JFactory::getUser();
		$db             = $this->getDbo();
		
		// Access checks.
		if (!$user->authorise('core.edit.state', 'com_jinbound')) {
			throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
		}
		
		$status = JTable::getInstance('Status', 'JInboundTable');
		if (!$status->load((int)$id)) {
			throw new Exception(JText::_('COM_JINBOUND_ERROR_STATUS_NOT_FOUND'));
		}
		
		// Reset the home fields for the client_id.
		$db->setQuery('UPDATE #__jinbound_lead_statuses SET `default` = 0 WHERE 1');
		
		if (!$db->query()) {
			throw new Exception($db->getErrorMsg());
		}
		
		// Set the new home style.
		$db->setQuery(
				'UPDATE #__jinbound_lead_statuses' .
				' SET `default` = 1' .
				' WHERE id = ' . (int) $id
		);
		
		if (!$db->query()) {
			throw new Exception($db->getErrorMsg());
		}
		
		// Clean the cache.
		$this->cleanCache();
		
		return true;
	}
	
	public function setFinal($id = 0) {
		// Initialise variables.
		$user   = JFactory::getUser();
		$db             = $this->getDbo();
		
		// Access checks.
		if (!$user->authorise('core.edit.state', 'com_jinbound')) {
			throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
		}
		
		$status = JTable::getInstance('Status', 'JInboundTable');
		if (!$status->load((int)$id)) {
			throw new Exception(JText::_('COM_JINBOUND_ERROR_STATUS_NOT_FOUND'));
		}
		
		// Reset the home fields for the client_id.
		$db->setQuery('UPDATE #__jinbound_lead_statuses SET final = 0 WHERE 1');
		
		if (!$db->query()) {
			throw new Exception($db->getErrorMsg());
		}
		
		// Set the new home style.
		$db->setQuery(
				'UPDATE #__jinbound_lead_statuses' .
				' SET final = 1' .
				' WHERE id = ' . (int) $id
		);
		
		if (!$db->query()) {
			throw new Exception($db->getErrorMsg());
		}
		
		// Clean the cache.
		$this->cleanCache();
		
		return true;
	}
}
