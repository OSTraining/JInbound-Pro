<?php
/**
 * @version		$Id$
 * @package		jInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundInflector', 'inflector');
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');
JLoader::register('JInboundModelPages', JPATH_ADMINISTRATOR . '/components/com_jinbound/models/pages.php');

class JInboundViewEmails extends JInboundListView
{
	function display($tpl = null, $safeparams = false) {
		$model     = new JInboundModelPages(array());
		$campaigns = $model->getCampaignsOptions();
		// if we don't have any categories yet, warn the user
		// there's always going to be one option in this list
		if (1 >= count($campaigns)) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JINBOUND_NO_CAMPAIGNS_YET'), 'warning');
		}
		$this->adviceText = JText::_('COM_JINBOUND_LEAD_MANAGER_RANDOM_ADVICE_' . rand(1,4));
		$this->addFilter(JText::_('COM_JINBOUND_SELECT_STATUS'), 'filter_status', $this->get('StatusOptions'), $this->get('State')->get('filter.status'));
		return parent::display($tpl, $safeparams);
	}
}
