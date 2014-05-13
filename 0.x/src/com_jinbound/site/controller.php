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
		// send reports emails
		require_once JPATH_ADMINISTRATOR . '/components/com_jinbound/models/reports.php';
		$model = $this->getModel('Reports', 'JInboundModel');
		$model->send();
		// handle sending campaign emails
		require_once JPATH_ADMINISTRATOR . '/components/com_jinbound/models/emails.php';
		$model = $this->getModel('Emails', 'JInboundModel');
		$model->send();
		// exit
		jexit();
	}
	
	/**
	 * Disables all emails sent from jinbound to this user
	 * 
	 */
	function unsubscribe() {
		$app  = JFactory::getApplication();
		$db   = JFactory::getDbo();
		$menu = $app->getMenu('site')->getDefault()->id;
		// default message sent to user
		$redirect = JRoute::_('index.php?Itemid=' . $menu, false);
		// find the contact based on email
		$email = trim('' . $app->input->get('email', '', 'string'));
		// if no email, bail
		if (empty($email)) {
			$app->redirect($redirect, JText::_('COM_JINBOUND_UNSUBSCRIBE_FAILED_NO_EMAIL'), 'error');
			jexit();
		}
		// lookup contact based on email
		$db->setQuery($db->getQuery(true)
			->select('id')
			->from('#__jinbound_contacts')
			->where('email = ' . $db->quote($email))
		);
		try {
			$contact_id = (int) $db->loadResult();
			if (empty($contact_id)) {
				throw new Exception('COM_JINBOUND_UNSUBSCRIBE_FAILED_NO_CONTACT');
			}
		}
		catch (Exception $e) {
			$app->redirect($redirect, JText::_($e->getMessage()), 'error');
			jexit();
		}
		// blind delete this contact & rebuild entry
		$db->setQuery($db->getQuery(true)
			->delete('#__jinbound_subscriptions')
			->where("contact_id = $contact_id")
		);
		try {
			$db->query();
		}
		catch (Exception $e) {
			$app->redirect($redirect, JText::_($e->getMessage()), 'error');
			jexit();
		}
		$db->setQuery($db->getQuery(true)
			->insert('#__jinbound_subscriptions')
			->columns(array('contact_id', 'enabled'))
			->values("$contact_id, 0")
		);
		try {
			$db->query();
		}
		catch (Exception $e) {
			$app->redirect($redirect, JText::_($e->getMessage()), 'error');
			jexit();
		}
		// handle exit
		$app->redirect($redirect, JText::_('COM_JINBOUND_UNSUBSCRIBED'), 'message');
		jexit();
	}
	
	function landingpageurl() {
		$id   = JFactory::getApplication()->input->get('id', 0, 'int');
		$data = array();
		if ($id) {
			JInbound::registerHelper('url');
			$nonsef         = JInboundHelperUrl::view('page', false, array('id' => $id));
			// Before continuing make sure we had an Itemid
			if (!preg_match('/Itemid\=[1-9][0-9]*?/', $nonsef)) {
				$data['error'] = JText::_('COM_JINBOUND_NEEDS_MENU');
			}
			else {
				$sef            = JInboundHelperUrl::view('page', true, array('id' => $id));
				$data['nonsef'] = JInboundHelperUrl::toFull($nonsef);
				$data['sef']    = JInboundHelperUrl::toFull($sef);
				$data['root']   = JURI::root();
				$data['rel']    = array('nonsef' => $nonsef, 'sef' => $sef);
			}
		}
		else {
			$data['error'] = JText::_('COM_JINBOUND_NOT_FOUND');
		}
		
		echo json_encode($data);
		die;
	}
}
