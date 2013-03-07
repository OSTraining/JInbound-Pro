<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundView', 'views/baseview');

class JInboundViewDashboard extends JInboundView
{
	function display($tpl = null, $echo = true) {
		$app = JFactory::getApplication();
		// get our libraries
		JInbound::registerHelper('path');
		JLoader::register('JInboundViewReports', JInboundHelperPath::admin('views/reports/view.html.php'));
		// get a reports view & load it's output
		$tmpl   = $app->input->get('tmpl');
		$layout = $app->input->get('layout');
		$app->input->set('tmpl', 'component');
		$app->input->set('layout', 'default');
		$reportView = new JInboundViewReports();
		
		$this->reports = new stdClass;
		$this->reports->glance       = $reportView->loadTemplate(null, 'glance');
		$this->reports->top_pages    = $reportView->loadTemplate('pages', 'top');
		$this->reports->recent_leads = $reportView->loadTemplate('leads', 'recent');
		
		$app->input->set('tmpl', $tmpl);
		$app->input->set('layout', $layout);
		
		return parent::display($tpl, $echo);
	}
}
