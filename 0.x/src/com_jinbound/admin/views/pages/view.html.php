<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');

class JInboundViewPages extends JInboundListView
{
	function display($tpl = null, $echo = true) {
		// add category filter
		$this->addFilter(JText::_('COM_JINBOUND_SELECT_CATEGORY'), 'filter_category', $this->get('CategoryOptions'), $this->get('State')->get('filter.category'));
		return parent::display($tpl, $echo);
	}
}