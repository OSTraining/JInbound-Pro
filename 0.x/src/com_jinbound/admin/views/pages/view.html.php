<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundInflector', 'inflector');
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');

class JInboundViewPages extends JInboundListView
{
	function display($tpl = null, $echo = true) {
		foreach (array('categories', 'campaigns') as $var) {
			$single = JInboundInflector::singularize($var);
			$$var = $this->get(ucwords($var) . 'Options');
			// if we don't have any categories yet, warn the user
			// there's always going to be one option in this list
			if (1 >= count($$var)) {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_JINBOUND_NO_' . strtoupper($var) . '_YET'), 'warning');
			}
			// add category filter
			$this->addFilter(JText::_('COM_JINBOUND_SELECT_' . strtoupper($single)), 'filter_' . $single, $$var, $this->get('State')->get('filter.' . $single));
		}
		
		return parent::display($tpl, $echo);
	}
}