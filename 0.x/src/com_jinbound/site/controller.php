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
	
	/**
	 * Disables all emails sent from jinbound to this user
	 * 
	 */
	function unsubscribe() {
		$app  = JFactory::getApplication();
		$db   = JFactory::getDbo();
		$menu = $app->getMenu('site')->getDefault()->id;
		// default message sent to user
		$message  = 'COM_JINBOUND_UNSUBSCRIBED';
		$type     = 'message';
		$redirect = JRoute::_('index.php?Itemid=' . $menu, false);
		// find the contact based on email
		$email = trim('' . $app->input->get('email', '', 'string'));
		// if no email, bail
		if (empty($email)) {
			$message = 'COM_JINBOUND_UNSUBSCRIBE_FAILED_NO_EMAIL';
			$type    = 'error';
			goto done;
		}
		// lookup contact based on email
		$db->setQuery($db->getQuery(true)
			->select('id')
			->from('#__contact_details')
			->where('email_to = ' . $db->quote($email))
		);
		try {
			$contact_id = (int) $db->loadResult();
			if (empty($contact_id)) {
				throw new Exception('COM_JINBOUND_UNSUBSCRIBE_FAILED_NO_CONTACT');
			}
		}
		catch (Exception $e) {
			$message = $e->getMessage();
			$type    = 'error';
			goto done;
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
			$message = $e->getMessage();
			$type    = 'error';
			goto done;
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
			$message = $e->getMessage();
			$type    = 'error';
			goto done;
		}
		// handle exit
		done: {
			$app->redirect($redirect, JText::_($message), $type);
			jexit();
		}
	}
	
	function landingpageurl() {
		$id   = JFactory::getApplication()->input->get('id', 0, 'int');
		$data = array();
		if ($id) {
			JInbound::registerHelper('url');
			$data['nonsef'] = JInboundHelperUrl::toFull(JInboundHelperUrl::view('page', false, array('id' => $id)));
			$data['sef']    = JInboundHelperUrl::toFull(JInboundHelperUrl::view('page', true, array('id' => $id)));
		}
		else {
			$data['error'] = JText::_('COM_JINBOUND_NOT_FOUND');
		}
		
		done: {
			echo json_encode($data);
			die;
		}
	}
}
