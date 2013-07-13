<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInboundBaseController', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/controllers/basecontroller.php');
JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');

class JInboundController extends JInboundBaseController
{
	function display($cachable = false, $urlparams = false) {
		$app  = JFactory::getApplication();
		$view = $app->input->get('view', 'page', 'cmd');
		$app->input->set('view', $view);
		parent::display($cachable);
	}
	
	/**
	 * controller action to run cron tasks
	 * 
	 * TODO
	 */
	function cron() {
		require_once JPATH_ADMINISTRATOR . '/components/com_jinbound/models/emails.php';
		$model = $this->getModel('Emails', 'JInboundModel');
		$model->send();
		jexit();
	}
}
