<?php
/**
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
	protected $context = 'com_jinbound.page';
	
	private $_registryColumns = array('formbuilder');
	
	/**
	 * force frontend lead form
	 * 
	 * (non-PHPdoc)
	 * @see JInboundAdminModel::getForm()
	 */
	public function getForm($data = array(), $loadData = true) {
		$fieldtypes = array(
			'select' => 'list'
		);
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
		if (!is_a($formbuilder, 'JRegistry')) {
			$reg = new JRegistry();
			if (is_string($formbuilder)) {
				$reg->loadString($formbuilder);
			}
			else if (is_array($formbuilder)) {
				$reg->loadArray($formbuilder);
			}
			else if (is_object($formbuilder)) {
				$reg->loadObject($formbuilder);
			}
			$formbuilder = $reg;
		}
		// order fields
		$unordered = $formbuilder->toArray();
		$ordered   = array();
		if (!array_key_exists('__ordering', $unordered)) {
			$ordered = $unordered;
		}
		else {
			if (empty($unordered['__ordering'])) {
				$ordered = $unordered;
			}
			else {
				$ordering = explode('|', $unordered['__ordering']);
				unset($unordered['__ordering']);
				foreach ($ordering as $orderkey) {
					if (array_key_exists($orderkey, $unordered)) {
						$ordered[$orderkey] = $unordered[$orderkey];
					}
				}
				foreach ($unordered as $orderkey => $orderval) {
					if (!array_key_exists($orderkey, $ordered)) {
						$ordered[$orderkey] = $orderval;
					}
				}
			}
		}
		
		
		// count how many fields we've added
		$allowedFields = 0;
		// add the fields
		foreach ($ordered as $name => $field) {
			// if this field isn't enabled, don't add
			if (0 == $field['enabled']) {
				continue;
			}
			// enabled - this field will be added
			$allowedFields++;
			// get the field type
			$type = array_key_exists('type', $field) ? $field['type'] : 'text';
			if (array_key_exists($type, $fieldtypes)) {
				$type = $fieldtypes[$type];
			}
			// get the default class
			$class = "";
			switch ($type) {
				case 'text':
				case 'list':
				case 'textarea':
					$class = "input-block-level";
					break;
				case 'checkboxes':
					$class = "checkbox";
					break;
			}
			// validate all email fields
			if ('email' == $name || 'email' == $type) {
				$type = 'email';
				if (!array_key_exists('attributes', $field)) {
					$field['attributes'] = array();
				}
				if (!array_key_exists('validate', $field['attributes'])) {
					$field['attributes']['validate'] = 'email';
				}
				$class = trim(trim($class) . ' validate-email');
			}
			// add the field
			$xmlField = $xmlFieldset->addChild('field');
			$xmlField->addAttribute('name', $name);
			$xmlField->addAttribute('type', $type);
			$xmlField->addAttribute('label', $field['title']);
			// add the options
			if (array_key_exists('options', $field) && is_array($field['options']) && array_key_exists('name', $field['options'])) {
				foreach ($field['options']['name'] as $k => $v) {
					if (empty($v)) {
						continue;
					}
					$xmlOpt = $xmlField->addChild('option', $v);
					$xmlOpt->addAttribute('value', $field['options']['value'][$k]);
				}
			}
			// add the options
			if (array_key_exists('attributes', $field) && is_array($field['attributes']) && array_key_exists('name', $field['attributes'])) {
				foreach ($field['attributes']['name'] as $k => $v) {
					if (empty($v)) {
						continue;
					}
					$xmlField->addAttribute($v, $field['attributes']['value'][$k]);
				}
			}
			// add the class
			if (!empty($class)) {
				$xmlField->addAttribute('class', $class);
			}
			// required
			if (array_key_exists('required', $field) && $field['required']) {
				$xmlField->addAttribute('required', true);
			}
		}
		$dispatcher     = JDispatcher::getInstance();
		$dispatcher->trigger('onJinboundFormbuilderDisplay', array(&$xml));
		// if we have allowed fields, add them
		if (0 < $allowedFields) {
			// ok, we should have enough now to add to the form
			$form->load($xml, false);
		}
		
		// rebind data
		if ($loadData) {
			// Get the data for the form.
			$data = $this->loadFormData();
		}

		// Load the data into the form after the plugins have operated.
		$form->bind($data);
		
		return $form;
	}
	
	protected function loadFormData()
	{
		if (!property_exists($this, 'data'))
		{
			$this->data = null;
		}
		if ($this->data === null)
		{
			$this->data = new stdClass;
			$app = JFactory::getApplication();
			// Override the base user data with any data in the session.
			$temp = (array) $app->getUserState('com_jinbound.page.data', array());
			foreach ($temp as $k => $v)
			{
				$this->data->$k = $v;
			}
		}
		return $this->data;
	}
}
