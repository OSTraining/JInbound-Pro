<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundFormController', 'controllers/basecontrollerform');

class JInboundControllerEmail extends JInboundFormController
{
	public function test()
	{
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		JInbound::registerHelper('path');
		require_once JInboundHelperPath::admin('models/emails.php');
		// init
		$dispatcher = JDispatcher::getInstance();
		$app        = JFactory::getApplication();
		$input      = $app->input;
		$to         = $input->get('to', '', 'string');
		$fromname   = $input->get('fromname', '', 'string');
		$fromemail  = $input->get('fromemail', '', 'string');
		$subject    = $input->get('subject', '', 'string');
		$htmlbody   = $input->get('htmlbody', '', 'raw');
		$plainbody  = $input->get('plainbody', '', 'string');
		// check
		if (empty($to) || empty($fromname) || empty($fromemail) || empty($subject))
		{
			throw new Exception('Cannot send email');
		}
		// init tags data
		$params = new JRegistry();
		$result = new stdClass();
		$result->lead = new stdClass();
		$result->lead->first_name = 'Howard';
		$result->lead->last_name  = 'Moon';
		$result->lead->email      = $to;
		$result->campaign_name    = 'Test Campaign';
		$result->form_name        = 'Test Form';
		// trigger before event
		$status = $dispatcher->trigger('onContentBeforeDisplay', array('com_jinbound.email', &$result, &$params, 0));
		// parse tags
		$tags      = array('email.campaign_name', 'email.form_name');
		$htmlbody  = JInboundModelEmails::_replaceTags($htmlbody, $result, $tags);
		$plainbody = JInboundModelEmails::_replaceTags($plainbody, $result, $tags);
		// trigger after event
		$dispatcher->trigger('onContentAfterDisplay', array('com_jinbound.email', &$result, &$params, 0));
		// send
		$mail = JFactory::getMailer();
		$mail->ClearAllRecipients();
		$mail->SetFrom($fromemail, $fromname);
		$mail->addRecipient($to, 'Test Email');
		$mail->setSubject($subject);
		$mail->setBody($htmlbody);
		$mail->IsHTML(true);
		$mail->AltBody = $plainbody;
		if (!$mail->Send())
		{
			throw new Exception('Cannot send email');
		}
		echo 'Done';
		jexit();
	}
	
	public function edit($key = 'id', $urlVar = 'id') {
		if (!JFactory::getUser()->authorise('core.manage', 'com_jinbound.email')) {
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		return parent::edit($key, $urlVar);
	}
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'set') {
		$set     = JFactory::getApplication()->input->get('set', 'a', 'cmd');
		$append  = parent::getRedirectToItemAppend($recordId, $urlVar);
		$append .= '&set=' . $set;
		return $append;
	}
}
