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

class JFormFieldJInboundFormFields extends JFormField
{
	public $type = 'Jinboundformfields';
	
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

	protected function getInput() {
		// text
		JText::script('COM_JINBOUND_JINBOUNDFORMFIELD_ERROR');
		JText::script('COM_JINBOUND_JINBOUNDFORMFIELD_NOSORTABLE');
		// prep the value - it SHOULD be an array, but who knows - maybe it won't be?
		// this is just some defensive coding, really - there's a slim to none chance this code will EVER be accessed!
		$this->prepareValue();
		// load the published fields - we'll sort them into two groups later
		JInboundBaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jinbound/models', 'JInboundModel');
		$model  = JInboundBaseModel::getInstance('Fields', 'JInboundModel');
		// TODO: why does setState not work?!
		$model->setState('filter.published', '1');
		$fields = $model->getItems();
		// make sure we actually HAVE fields to add :)
		if (empty($fields)) {
			return '<div>' . JText::_('COM_JINBOUND_FORMFIELDS_NO_FIELDS') . '</div>';
		}
		// load up the core filter, to save keystrokes later
		$filter = JFilterInput::getInstance();
		// start our arrays for sorting between the two
		$available = '';
		$assigned  = '';
		// loop the value first, so we can maintain ordering
		if (!empty($this->value)) {
			foreach ($this->value as $fid) {
				// now loop the fields and assign them as they're found
				foreach ($fields as $field) {
					if ($field->id == $fid) {
						$assigned[] = $field;
					}
				}
			}
		}
		// now get the available array
		foreach ($fields as $field) {
			// since we're looping here, go ahead and set an extra variable for the formtype class
			$field->_formtypeclass = 'jinboundformfieldformtype';
			// go ahead and just add this to available if our value is empty
			if (empty($this->value)) {
				$available[] = $field;
				continue;
			}
			// check this field against our array of values
			if (!in_array($field->id, $this->value)) {
				$available[] = $field;
			}
		}
		// ok, we have each sorted - start constructing the html
		$html = array();
		// start by opening our element div
		$html[] = '<div class="jinboundformfields jinboundformfieldsclear">';
		// we need the field lists inside other containers so we can add text labels (#329)
		$html[] = '<div class="jinboundformfieldslist">';
		// add the header to this list
		$html[] = '<h4>' . JText::_('COM_JINBOUND_FORMFIELDS_AVAILABLE') . '</h4>';
		// loop through the available fields and create new tags for each
		// take note we're adding the ul tag regardless so we maintain the 2 lists
		$html[] = '<ul class="jinboundformfieldsavailable jinboundformfieldssortable">';
		if (!empty($available)) {
			foreach ($available as $field) {
				// start this field element
				$html[] = '<li class="jinboundformfield ' . $field->_formtypeclass . '" style="' . $this->_getIconStyle($this->_getIcon($field->type)) . '">';
				// add the text
				$html[] = $filter->clean($field->title, 'string');
				// also add a hidden input element so we can keep track of this element's id
				$html[] = '<input type="hidden" value="' . $field->id . '" />';
				// end this field element
				$html[] = '</li>';
			}
		}
		// end the available fields element
		$html[] = '</ul>';
		// add some extra text
		$html[] = '<p class="jinboundformfieldsdesc">' . JText::_('COM_JINBOUND_FORMFIELDS_AVAILABLE_DESC') . '</p>';
		// end the container
		$html[] = '</div>';
		// open another div for the available fields
		$html[] = '<div class="jinboundformfieldslist">';
		// add the header to this list
		$html[] = '<h4>' . JText::_('COM_JINBOUND_FORMFIELDS_ASSIGNED') . '</h4>';
		// start the list element
		$html[] = '<ul class="jinboundformfieldsassigned jinboundformfieldssortable">';
		// loop through the assigned fields and create new tags for each
		if (!empty($assigned)) {
			foreach ($assigned as $field) {
				// start this field element
				$html[] = '<li class="jinboundformfield ' . $field->_formtypeclass . '" style="' . $this->_getIconStyle($this->_getIcon($field->type)) . '">';
				// for now just add the text
				$html[] = $filter->clean($field->title, 'string');
				// also add a hidden input element so we can keep track of this element's id
				$html[] = '<input type="hidden" value="' . intval($field->id) . '" />';
				// end this field element
				$html[] = '</li>';
			}
		}
		// end the assigned fields element
		$html[] = '</ul>';
		// add some extra text
		$html[] = '<p class="jinboundformfieldsdesc">' . JText::_('COM_JINBOUND_FORMFIELDS_ASSIGNED_DESC') . '</p>';
		// end the container
		$html[] = '</div>';
		// it's not good to have this here, but in the interest of keeping things from breaking add a clearin element
		$html[] = '<div class="jinboundformfieldsclear"><!-- --></div>';
		// go ahead and append a hidden input that will act as our main field
		$html[] = '<input type="' . (JDEBUG ? 'text' : 'hidden') . '" class="jinboundformfieldsinput" name="' . $filter->clean($this->name) . '" value="' . $filter->clean('' . implode('|', $this->value)) . '" />';
		// end the main element
		$html[] = '</div>';
		
		if (JInbound::version()->isCompatible('3.0.0')) {
			JHtml::_('jquery.ui', array('core', 'sortable'));
		}
		// load the javascript that controls the drag & drop
		JFactory::getDocument()->addScript(rtrim(JUri::root(), '/') . '/media/jinbound/js/formfield.js');
		// load the stylesheet that controls the display of this field
		JFactory::getDocument()->addStyleSheet(rtrim(JUri::root(), '/') . '/media/jinbound/css/formfield.css');
		// return the html to the form
		return implode("\n", $html);
	}
	
	private function _getIcon($field) {
		static $icons;
		$relpath  = '/images/fields';
		$base     = JInboundHelperUrl::media() . $relpath;
		$iconpath = JPATH_ROOT . '/media/jinbound' . $relpath;
		if (is_null($icons)) {
			$icons = array();
			if (JFolder::exists($iconpath)) {
				// grab the available icons
				$icons = JFolder::files($iconpath, '.png$');
			}
		}
		$icon = "icon-{$field}.png";
		if (in_array($icon, $icons)) {
			return $base . '/' . $icon;
		}
		return $base . '/icon-unknown.png';
	}
	
	private function _getIconStyle($icon) {
		return 'background-image:url(' . $icon . ');background-repeat:no-repeat;background-position:2px center;';
	}
}
