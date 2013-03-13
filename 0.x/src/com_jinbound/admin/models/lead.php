<?php
/**
 * @version		$Id$
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
}
