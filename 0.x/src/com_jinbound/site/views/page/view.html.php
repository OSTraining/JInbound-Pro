<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundItemView', 'views/baseviewitem');

class JInboundViewPage extends JInboundItemView
{
	function display($tpl = null, $echo = true) {
		// display the item
		$display = parent::display($tpl, $echo);
		// if we don't have an item, it's a 404
		if (0 == $this->item->id) {
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
	
	function renderCustomLayout($item) {
		// get the custom layout data & replace all the tags
		$text = $item->template;
		
		$tags = array();
		$basetags = array("heading", "subheading", "maintext", "sidebartext");
		foreach ($basetags as $basetag) {
			$tags[$basetag] = $item->$basetag;
		}
		
		if (!empty($item->image)) {
			$tags['image'] = '<img src="' . JInboundHelperFilter::escape($item->image) . '" />';
		}
		
		foreach ($tags as $tag => $value) {
			if (false === JString::strpos($text, $tag)) {
				continue;
			}
			$text = implode($value, explode('{' . $tag . '}', $text));
		}
		
		
		/*
		$tags = array(
			"heading"     => $item->heading
		,	"subheading"  => $item->heading
		,	"maintext"    => $item->heading
		,	"sidebartext" => $item->heading
		,	"image"       => $item->heading
		,	"form"        => $item->heading
		,	"form:open" => $item->heading
		,	"form:close" => $item->heading
		,	"form:firstname" => $item->heading
		,	"form:lastname" => $item->heading
		,	"form:email" => $item->heading
		,	"form:website" => $item->heading
		,	"form:companyname" => $item->heading
		,	"form:phonenumber" => $item->heading
		,	"form:fulladdress" => $item->heading
		,	"form:address" => $item->heading
		,	"form:suburb" => $item->heading
		,	"form:state" => $item->heading
		,	"form:country" => $item->heading
		,	"form:postcode" => $item->postcode
		);
		*/
		
		return $text;
	}
}