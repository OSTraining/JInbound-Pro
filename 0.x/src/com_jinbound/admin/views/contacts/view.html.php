<?php
/**
 * @package		jInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');

class JInboundViewContacts extends JInboundListView
{
	/**
	 * 
	 * 
	 * (non-PHPdoc)
	 * @see JInboundListView::display()
	 */
	function display($tpl = null, $safeparams = false) {
		$campaigns = $this->get('CampaignsOptions');
		$pages = $this->get('PagesOptions');
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		if (1 >= count($campaigns)) {
			$this->app->enqueueMessage(JText::_('COM_JINBOUND_NO_CAMPAIGNS_YET'), 'warning');
		}
		
		$campaign = JFactory::getApplication()->getUserStateFromRequest('com_jinbound.contacts.filter_campaign', 'filter_campaign', '');
		$this->addFilter(JText::_('COM_JINBOUND_SELECT_CAMPAIGN'), 'filter_campaign', $campaigns, $campaign);
		
		$page = JFactory::getApplication()->getUserStateFromRequest('com_jinbound.contacts.filter_page', 'filter_page', '');
		$this->addFilter(JText::_('COM_JINBOUND_SELECT_PAGE'), 'filter_page', $pages, $page);
		
		return parent::display($tpl, $safeparams);
	}
	
	public function addToolBar() {
		$icon = 'export';
		if (JInbound::version()->isCompatible('3.0.0'))
		{
			$icon = 'download';
		}
		// export icons
		if (JFactory::getUser()->authorise('core.create', JInbound::COM . '.report'))
		{
			JToolBarHelper::custom('reports.exportleads', "{$icon}.png", "{$icon}_f2.png", 'COM_JINBOUND_EXPORT_LEADS', false);
		}
		parent::addToolBar();
	}
}
