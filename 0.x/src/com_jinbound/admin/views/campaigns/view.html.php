<?php
/**
 * @version		$Id$
 * @package		jInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');

class JInboundViewCampaigns extends JInboundListView
{
	function display($tpl = null, $echo = true) {
		$this->addFilter(JText::_('COM_JINBOUND_SELECT_STATUS'), 'filter_status', $this->get('StatusOptions'), $this->get('State')->get('filter.status'));
		return parent::display($tpl, $echo);
	}
}
