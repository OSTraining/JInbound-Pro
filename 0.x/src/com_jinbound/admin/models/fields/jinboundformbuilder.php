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
		// ensure defaults are set
		$value = $this->getFormValue();
		// get the view
		$view = $this->getView();
		// set data
		$view->input = $this;
		$view->value = $value;
		// return template html
		return $view->loadTemplate();
	}
	
	/**
	 * This method is used in the form display to show extra data
	 * 
	 */
	public function getSidebar() {
		$view = $this->getView();
		// set data
		$view->input = $this;
		// return template html
		return $view->loadTemplate('sidebar');
	}
	
	/**
	 * gets a new instance of the base field view
	 * 
	 * @return JInboundFieldView
	 */
	protected function getView() {
		$viewConfig = array('template_path' => dirname(__FILE__) . '/formbuilder');
		$view = new JInboundFieldView($viewConfig);
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
		return $this->value;
	}
	
	/**
	 * get the available form fields
	 * 
	 * TODO: make this better later
	 */
	public function getFormFields() {
		return array(
			json_decode(json_encode(array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_FIRST_NAME'),
				'id'   => 'first_name',
				'type' => 'text'
			))),
			json_decode(json_encode(array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_LAST_NAME'),
				'id'   => 'last_name',
				'type' => 'text'
			))),
			json_decode(json_encode(array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_EMAIL'),
				'id'   => 'email',
				'type' => 'text'
			))),
			json_decode(json_encode(array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_WEBSITE'),
				'id'   => 'website',
				'type' => 'text'
			))),
			json_decode(json_encode(array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_COMPANY_NAME'),
				'id'   => 'company_name',
				'type' => 'text'
			))),
			json_decode(json_encode(array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_PHONE_NUMBER'),
				'id'   => 'phone_number',
				'type' => 'text'
			))),
			json_decode(json_encode(array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_ADDRESS'),
				'id'   => 'address',
				'type' => 'textarea'
			))),
			json_decode(json_encode(array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_TEXT'),
				'id'   => 'text',
				'type' => 'text'
			))),
			json_decode(json_encode(array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_TEXTAREA'),
				'id'   => 'textarea',
				'type' => 'textarea'
			))),
			json_decode(json_encode(array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_CHECKBOXES'),
				'id'   => 'checkboxes',
				'type' => 'checkboxes'
			))),
			json_decode(json_encode(array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_RADIO'),
				'id'   => 'radio',
				'type' => 'radio'
			))),
			json_decode(json_encode(array(
				'name' => JText::_('COM_JINBOUND_PAGE_FIELD_SELECT'),
				'id'   => 'select',
				'type' => 'select'
			)))
		);
	}
}
