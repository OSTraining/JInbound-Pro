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
	protected $feeds = array(
		'feed' => array('url' => 'https://jinbound.com/blog/feed/rss.html', 'showDescription' => false)
	,	'news' => array('url' => 'https://jinbound.com/news/?format=feed', 'showDescription' => false)
	);
	
	function display($tpl = null, $safeparams = false) {
		$app = JFactory::getApplication();
		// check for updates
		if (!class_exists('LiveUpdate'))
		{
			require_once JPATH_COMPONENT_ADMINISTRATOR . '/liveupdate/liveupdate.php';
		}
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
		
		
		JInbound::registerLibrary('JInboundRSSView', 'views/rssview');
		$app->input->set('layout', 'rss');
		foreach ($this->feeds as $var => $feed)
		{
			// get RSS view and display its contents
			try
			{
				$rss = new JInboundRSSView();
				$rss->showDetails = array_key_exists('showDetails', $feed) ? $feed['showDetails'] : false;
				$rss->showDescription = array_key_exists('showDescription', $feed) ? $feed['showDescription'] : true;
				$rss->url = $feed['url'];
				$rss->getFeed($feed['url']);
				$this->$var = $rss->loadTemplate(null, 'rss');
			}
			catch (Exception $e)
			{
				$this->$var = $e->getMessage();
			}
		}
		
		// reset template and layout data
		$app->input->set('tmpl', $tmpl);
		$app->input->set('layout', $layout);
		
		// apply plugin update info
		$this->updates = JDispatcher::getInstance()->trigger('onJinboundDashboardUpdate');
		
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
