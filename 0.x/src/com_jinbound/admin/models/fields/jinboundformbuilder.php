<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
 @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');

JLoader::register('JInboundFieldView', JPATH_ADMINISTRATOR.'/components/com_jinbound/libraries/views/fieldview.php');

class JFormFieldJinboundFormBuilder extends JFormField
{
	protected $type = 'JinboundFormBuilder';
	
	/**
	 * Builds the input element for the form builder
	 * 
	 * (non-PHPdoc)
	 * @see JFormField::getInput()
	 */
	protected function getInput() {
		// return template html
		return $this->getView()->loadTemplate();
	}
	
	/**
	 * This method is used in the form display to show extra data
	 * 
	 */
	public function getSidebar() {
		// return template html
		return $this->getView()->loadTemplate('sidebar');
	}
	
	/**
	 * gets a new instance of the base field view
	 * 
	 * @return JInboundFieldView
	 */
	protected function getView() {
		$viewConfig = array('template_path' => dirname(__FILE__) . '/formbuilder');
		$view = new JInboundFieldView($viewConfig);
		$view->input    = $this;
		$view->value    = $this->getFormValue();
		$view->input_id = $view->escape($this->id);
		$dispatcher     = JDispatcher::getInstance();
		$dispatcher->trigger('onJinboundFormbuilderView', array(&$view));
		return $view;
	}
	
	/**
	 * public method to fetch the value
	 * 
	 * TODO finish this
	 */
	public function getFormValue() {
		if (!($this->value instanceof JRegistry)) {
			$reg = new JRegistry();
			if (is_array($this->value)) {
				$reg->loadArray($this->value);
			}
			else if (is_object($this->value)) {
				$reg->loadObject($this->value);
			}
			else if (is_string($this->value)) {
				$reg->loadString($this->value);
			}
			$this->value = $reg;
		}
		foreach (array('first_name', 'last_name', 'email') as $field) {
			$def = $this->value->get($field, false);
			if (!$def) {
				$this->value->set($field, json_decode(json_encode(array(
					'title'    => JText::_('COM_JINBOUND_PAGE_FIELD_'.$field)
				,	'name'     => $field
				,	'enabled'  => 1
				,	'required' => 1
				))));
			}
			else if (is_object($def) && $def instanceof JRegistry) {
				$def->set('enabled', 1);
				$def->set('required', 1);
			}
			else if (is_object($def)) {
				$def->enabled  = 1;
				$def->required = 1;
			}
			else if (is_array($def)) {
				$def['enabled']  = 1;
				$def['required'] = 1;
			}
		}
		
		$ordering = $this->value->get('__ordering');
		
		if (empty($ordering)) {
			return $this->value->toArray();
		}
		
		if (!is_array($ordering)) {
			$ordering = explode('|', $ordering);
		}
		
		$unordered = $this->value->toArray();
		$ordered   = new JRegistry();
		foreach ($ordering as $key) {
			if (array_key_exists($key, $unordered)) {
				$ordered->set($key, $unordered[$key]);
			}
		}
		foreach ($unordered as $key => $value) {
			if (!array_key_exists($key, $ordered)) {
				$ordered->set($key, $value);
			}
		}
		
		$this->value = $ordered;
		
		return $this->value->toArray();
	}
	
	/**
	 * get the available form fields
	 * 
	 * TODO: make this better later
	 */
	public function getFormFields() {
		$fields = array(
			(object) array(
				'name'  => JText::_('COM_JINBOUND_PAGE_FIELD_FIRST_NAME'),
				'id'    => 'first_name',
				'type'  => 'text',
				'multi' => 0
			),
			(object) array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_LAST_NAME'),
				'id'   => 'last_name',
				'type' => 'text',
				'multi' => 0
			),
			(object) array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_EMAIL'),
				'id'   => 'email',
				'type' => 'text',
				'multi' => 0
			),
			(object) array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_WEBSITE'),
				'id'   => 'website',
				'type' => 'text',
				'multi' => 0
			),
			(object) array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_COMPANY_NAME'),
				'id'   => 'company_name',
				'type' => 'text',
				'multi' => 0
			),
			(object) array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_PHONE_NUMBER'),
				'id'   => 'phone_number',
				'type' => 'text',
				'multi' => 0
			),
			(object) array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_ADDRESS'),
				'id'   => 'address',
				'type' => 'text',
				'multi' => 0
			),
			(object) array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_SUBURB'),
				'id'   => 'suburb',
				'type' => 'text',
				'multi' => 0
			),
			(object) array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_STATE'),
				'id'   => 'state',
				'type' => 'text',
				'multi' => 0
			),
			(object) array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_COUNTRY'),
				'id'   => 'country',
				'type' => 'text',
				'multi' => 0
			),
			(object) array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_POSTCODE'),
				'id'   => 'postcode',
				'type' => 'text',
				'multi' => 0
			),
			(object) array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_TEXT'),
				'id'   => 'text',
				'type' => 'text',
				'multi' => 1
			),
			(object) array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_TEXTAREA'),
				'id'   => 'textarea',
				'type' => 'textarea',
				'multi' => 1
			),
			(object) array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_CHECKBOXES'),
				'id'   => 'checkboxes',
				'type' => 'checkboxes',
				'multi' => 1
			),
			(object) array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_RADIO'),
				'id'   => 'radio',
				'type' => 'radio',
				'multi' => 1
			),
			(object) array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_SELECT'),
				'id'   => 'select',
				'type' => 'list',
				'multi' => 1
			)
		);
		$dispatcher     = JDispatcher::getInstance();
		$dispatcher->trigger('onJinboundFormbuilderFields', array(&$fields));
		return $fields;
	}
}
