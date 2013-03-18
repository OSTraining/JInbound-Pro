<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;


JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundAdminModel', 'models/basemodeladmin');

class JInboundModelPage extends JInboundAdminModel
{
	public $_context = 'com_jinbound.page';
	
	private $_registryColumns = array('formbuilder');
	
	/**
	 * force frontend lead form
	 * 
	 * (non-PHPdoc)
	 * @see JInboundAdminModel::getForm()
	 */
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm(JInbound::COM.'.lead_front', 'lead_front', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		// create a new form xml element
		$xml = new JXMLElement('<form></form>');
		// store this lead in its own namespace
		$xmlFields = $xml->addChild('fields');
		$xmlFields->addAttribute('name', 'lead');
		// create a fieldset (and we'll name it based on the form)
		$xmlFieldset = $xmlFields->addChild('fieldset');
		$xmlFieldset->addAttribute('name', 'lead');
		$xmlFieldset->addAttribute('label', JText::_('COM_JINBOUND_FIELDSET_LEAD'));
		// add each field from the page item's formbuilder property
		$formbuilder = $this->getItem()->formbuilder;
		// get the form data
		if (!method_exists($formbuilder, 'toArray')) {
			$reg = new JRegistry();
			$formbuilder = $reg;
		}
		$allowedFields = 0;
		foreach ($formbuilder->toArray() as $name => $field) {
			if (0 == $field['enabled']) continue;
			$allowedFields++;
			$xmlField = $xmlFieldset->addChild('field');
			$xmlField->addAttribute('name', $name);
			$xmlField->addAttribute('type', array_key_exists('type', $field) ? $field['type'] : 'text');
			$xmlField->addAttribute('label', $field['title']);
			$xmlField->addAttribute('description', $field['title']);
		}
		// if we have allowed fields, add them
		if (0 < $allowedFields) {
			// ok, we should have enough now to add to the form
			$form->load($xml, false);
		}
		
		return $form;
	}
}
