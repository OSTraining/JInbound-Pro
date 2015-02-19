<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerHelper('filter');
JInbound::registerHelper('form');
JInbound::registerHelper('url');
JInbound::registerLibrary('JInboundItemView', 'views/baseviewitem');

class JInboundViewPage extends JInboundItemView
{
	function display($tpl = null, $echo = true) {
		// display the item
		$display = parent::display($tpl, $echo);
		// if we don't have an item, it's a 404
		if (0 == $this->item->id || 1 != (int) $this->item->published) {
			JError::raiseError('404', JText::_('COM_JINBOUND_NOT_FOUND'));
		}
		// increase the hit count
		if (!method_exists($this->item, 'hit')) {
			$table = JTable::getInstance('Page', 'JInboundTable');
			$table->load($this->item->id);
			$table->hit();
		}
		else {
			$this->item->hit();
		}
		// fix for legacy
		$form = JTable::getInstance('Form', 'JInboundTable');
		$form->load($this->item->formid);
		$this->item->formname = $form->title;
		// set the document title
		$doc  = JFactory::getDocument();
		if (method_exists($doc, 'setTitle')) {
			$doc->setTitle($this->item->metatitle);
		}
		if (method_exists($doc, 'setDescription')) {
			$doc->setDescription($this->item->metadescription);
		}
		// add custom css
		if (!empty($this->item->css))
		{
			$doc->addStyleDeclaration($this->item->css);
		}
		
		return $display;
	}
	
	public function prepareItem()
	{
		// trigger content events
		JPluginHelper::importPlugin('content');
		// "fix" for content plugins
		foreach (array('maintext', 'sidebartext', 'template') as $text) {
			$this->item->text = $this->item->$text;
			// get dispatcher and trigger the event
			$dispatcher = JDispatcher::getInstance();
			$params = new JRegistry;
			$dispatcher->trigger('onContentPrepare', array('com_jinbound.page', &$this->item, &$params, 0));
			$this->item->$text = $this->item->text;
			unset($this->item->text);
		}
	}

	public function setDocument() {
		parent::setDocument();
		// add behaviors
		JHtml::_('behavior.tooltip');
		// add script
		$document = JFactory::getDocument();
		$document->addScript(JInboundHelperUrl::media() . '/js/jinbound.js');
	}
	
	function renderCustomLayout() {
		// get the custom layout data & replace all the tags
		$text = $this->item->template;
		
		$tags = array();
		$basetags = array("heading", "subheading", "maintext", "sidebartext");
		foreach ($basetags as $basetag) {
			if (!property_exists($this->item, $basetag)) {
				continue;
			}
			$tags[$basetag] = $this->item->$basetag;
		}
		
		if (!empty($this->item->image)) {
			$tags['image'] = $this->loadTemplate('image', 'default');
		}
		
		// load these from templates
		$tags['form:open']  = $this->loadTemplate('form_open', 'default');
		$tags['form:close'] = $this->loadTemplate('form_close', 'default');
		$tags['submit']     = $this->loadTemplate('form_submit', 'default');
		$tags['form']       = $this->loadTemplate('form', 'default');
		
		$tagsToFields = array(
			'first_name'   => 'firstname'
		,	'last_name'    => 'lastname'
		,	'company_name' => 'companyname'
		,	'phone_number' => 'phonenumber'
		);
		
		// BUGFIX names are stored generically for custom fields, fix tags for unique names
		// TODO test this and maybe fix leads during migration?
		$tagsToFieldsAlt = array();
		$formbuilder = $this->item->formbuilder->toArray();
		foreach ($formbuilder as $formfieldname => $formfield)
		{
			if (!is_array($formfield))
			{
				continue;
			}
			if (!$formfield['enabled'] || !array_key_exists('name', $formfield))
			{
				continue;
			}
			$tagsToFieldsAlt[$formfieldname] = $formfield['name'];
		}
		
		$fullAddress = array(
			'address'  => ''
		,	'suburb'   => ''
		,	'state'    => ''
		,	'country'  => ''
		,	'postcode' => ''
		);
		
		foreach ($this->form->getFieldset('lead') as $key => $field) {
			$tagKey = str_replace('jform_lead_', '', $key);
			if (array_key_exists($tagKey, $tagsToFields)) {
				$tagKey = $tagsToFields[$tagKey];
			}
			else if (array_key_exists($tagKey, $tagsToFieldsAlt)) {
				$tagKey = $tagsToFieldsAlt[$tagKey];
			}
			$this->_currentField = $field;
			$tags['form:' . $tagKey] = $this->loadTemplate('form_field');
			
			if (array_key_exists($tagKey, $fullAddress)) {
				$fullAddress[$tagKey] = $tags['form:' . $tagKey];
			}
		}
		$tags['form:fulladdress'] = implode("", array_values($fullAddress));
		
		// replace tags
		foreach ($tags as $tag => $value) {
			if (false === JString::strpos($text, $tag)) {
				continue;
			}
			$text = implode($value, explode('{' . $tag . '}', $text));
		}
		
		return $text;
	}
}