<?php
/**
 * @package		jInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('url');
JInbound::registerLibrary('JInboundBaseModel', 'models/basemodel');
JInbound::registerLibrary('JInboundFieldView', 'views/fieldview');

class JFormFieldJInboundFields extends JFormField
{
	public $type = 'Jinboundfields';
	
	private function prepareValue()
	{
		if (!is_array($this->value)) {
			if (false !== strpos((string) $this->value, ',')) {
				$this->value = explode(',', (string) $this->value);
			}
			else if (false !== strpos((string) $this->value, '|')) {
				$this->value = explode('|', (string) $this->value);
			}
			else if (!empty($this->value)) {
				$this->value = (array) $this->value;
			}
			else {
				$this->value = array();
			}
		}
	}

	protected function getInput()
	{
		// prepare the value
		$this->prepareValue();
		// load the published fields using db, not model, so we get all
		$db = JFactory::getDbo();
		$fields = $db->setQuery($db->getQuery(true)
			->select('*')
			->from('#__jinbound_fields')
			->where('published = 1')
		)->loadObjectList();
		// make sure we actually HAVE fields to add :)
		if (empty($fields)) {
			return '<div>' . JText::_('COM_JINBOUND_FORMFIELDS_NO_FIELDS') . '</div>';
		}
		// fix our ordering
		$ordered = array();
		foreach ($this->value as $fieldid) {
			foreach ($fields as $field) {
				if ($field->id == $fieldid) {
					$ordered[] = $field;
					break;
				}
			}
		}
		foreach ($fields as $field) {
			if (!in_array($field->id, $this->value)) {
				$ordered[] = $field;
			}
		}
		// load scripts
		JText::script('COM_JINBOUND_JCALFORMFIELD_ERROR');
		JText::script('COM_JINBOUND_JCALFORMFIELD_NOSORTABLE');
		$doc = JFactory::getDocument();
		$doc->addScript(JUri::root() . '/media/jinbound/js/field.js');
		// load the stylesheet that controls the display of this field
		$doc->addStyleSheet(JUri::root() . '/media/jinbound/css/field.css');
		// load the view
		$view = $this->getView();
		$view->input_id   = $this->id;
		$view->input_name = $this->name;
		$view->fields     = $ordered;
		$view->value      = $this->value;
		
		return $view->loadTemplate();
	}
	
	/**
	 * gets a new instance of the base field view
	 *
	 * @return JInboundFieldView
	 */
	protected function getView() {
		$viewConfig = array('template_path' => dirname(__FILE__) . '/fields');
		$view = new JInboundFieldView($viewConfig);
		return $view;
	}
}
