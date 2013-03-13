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
		$doc  = JFactory::getDocument();
		if (method_exists($doc, 'setTitle')) {
			$doc->setTitle($item->metatitle);
		}
		if (method_exists($doc, 'setDescription')) {
			$doc->setDescription($item->metadescription);
		}
		return parent::display($tpl, $echo);
	}
}