<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerHelper('filter');
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
		// set the document title
		$doc  = JFactory::getDocument();
		if (method_exists($doc, 'setTitle')) {
			$doc->setTitle($this->item->metatitle);
		}
		if (method_exists($doc, 'setDescription')) {
			$doc->setDescription($this->item->metadescription);
		}
		
		return $display;
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
		
		
		/*
		$tags = array(
			"heading"     => $this->item->heading
		,	"subheading"  => $this->item->heading
		,	"maintext"    => $this->item->heading
		,	"sidebartext" => $this->item->heading
		,	"image"       => $this->item->heading
		,	"form"        => $this->item->heading
		,	"form:open" => $this->item->heading
		,	"form:close" => $this->item->heading
		,	"form:firstname" => $this->item->heading
		,	"form:lastname" => $this->item->heading
		,	"form:email" => $this->item->heading
		,	"form:website" => $this->item->heading
		,	"form:companyname" => $this->item->heading
		,	"form:phonenumber" => $this->item->heading
		,	"form:fulladdress" => $this->item->heading
		,	"form:address" => $this->item->heading
		,	"form:suburb" => $this->item->heading
		,	"form:state" => $this->item->heading
		,	"form:country" => $this->item->heading
		,	"form:postcode" => $this->item->postcode
		);
		*/
		
		return $text;
	}
}