<?php
/**
 * @version		$Id$
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
		$item = $this->get('Item');
		// if we don't have an item, it's a 404
		if (0 == $item->id) {
			JError::raiseError('404', JText::_('COM_JINBOUND_NOT_FOUND'));
		}
		// increase the hit count
		if (!method_exists($item, 'hit')) {
			$table = JTable::getInstance('Page', 'JInboundTable');
			$table->load($item->id);
			$table->hit();
		}
		else {
			$item->hit();
		}
		// display the item
		$display = parent::display($tpl, $echo);
		// set the document title
		$doc  = JFactory::getDocument();
		if (method_exists($doc, 'setTitle')) {
			$doc->setTitle($item->metatitle);
		}
		if (method_exists($doc, 'setDescription')) {
			$doc->setDescription($item->metadescription);
		}
		
		return $display;
	}
}