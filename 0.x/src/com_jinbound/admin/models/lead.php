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
		
		$user = JFactory::getUser($item->user_id);
		
		foreach (array('name', 'username', 'email') as $var) {
			$item->{$var} = $user->{$var};
		}
		
		return $item;
	}
}
