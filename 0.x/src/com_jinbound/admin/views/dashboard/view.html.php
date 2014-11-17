<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundView', 'views/baseview');

class JInboundViewDashboard extends JInboundView
{
	function display($tpl = null, $safeparams = false) {
		$app = JFactory::getApplication();
		// check for updates
		$updateInfo = LiveUpdate::getUpdateInformation();
		if (!$updateInfo->supported) {
			$app->enqueueMessage(JText::_('COM_JINBOUND_UPDATE_UNSUPPORTED'), 'error');
		}
		else if ($updateInfo->stuck) {
			$app->enqueueMessage(JText::_('COM_JINBOUND_UPDATE_STUCK'), 'warning');
		}
		else if ($updateInfo->hasUpdates) {
			$app->enqueueMessage(JText::sprintf('COM_JINBOUND_UPDATE_HASUPDATES', $updateInfo->version), 'message');
		}
		// get our libraries
		JInbound::registerHelper('path');
		JLoader::register('JInboundViewReports', JInboundHelperPath::admin('views/reports/view.html.php'));
		// get original data for layout and template
		$tmpl   = $app->input->get('tmpl');
		$layout = $app->input->get('layout');
		
		// get a reports view & load it's output
		$app->input->set('tmpl', 'component');
		$app->input->set('layout', 'default');
		$app->setUserState('list.limit', 10);
		$app->setUserState('list.start', 0);
		$reportView = new JInboundViewReports();
		
		$this->reports = new stdClass;
		$this->reports->glance       = $reportView->loadTemplate(null, 'glance');
		$this->reports->script       = $reportView->loadTemplate('script', 'default');
		$this->reports->top_pages    = $reportView->loadTemplate('pages', 'top');
		$this->reports->recent_leads = $reportView->loadTemplate('leads', 'recent');
		
		// get RSS view and display its contents
		JInbound::registerLibrary('JInboundRSSView', 'views/rssview');
		$app->input->set('layout', 'rss');
		$url = 'http://feeds.feedburner.com/jinbound';
		$rss = new JInboundRSSView();
		$rss->url = $url;
		$rss->getFeed($url);
		$this->feed = $rss->loadTemplate(null, 'rss');
		
		// reset template and layout data
		$app->input->set('tmpl', $tmpl);
		$app->input->set('layout', $layout);
		
		return parent::display($tpl, $safeparams);
	}
	
	/**
	 * used to add administrator toolbar
	 */
	public function addToolBar() {
		parent::addToolBar();
		if (JFactory::getUser()->authorise('core.admin', JInbound::COM)) {
			JToolbarHelper::custom('reset', 'refresh.png', 'refresh_f2.png', 'COM_JINBOUND_RESET', false);
		}
	}
}
