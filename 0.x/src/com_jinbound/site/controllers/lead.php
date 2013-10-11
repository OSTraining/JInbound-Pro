<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundBaseController', 'controllers/basecontroller');

class JInboundControllerLead extends JInboundBaseController
{
	public function save() {
		$app    = JFactory::getApplication();
		$debug  = defined('JDEBUG') && JDEBUG;
		$Itemid = $app->input->get('Itemid', 0, 'int');
		// fetch the page id
		$id     = $app->input->post->get('page_id', 0, 'int');
		// fetch only the lead data
		$data   = $app->input->post->get('jform', array(), 'array');
		// start building the bind data
		$bind   = array('page_id' => $id);
		// get a page model so we can pull the formbuilder variable from it
		$model  = $this->getModel('Page', 'JInboundModel');
		$page   = $model->getItem($id);
		if (!$page || empty($page->id)) {
			$this->setError(JText::_('COM_JINBOUND_NO_PAGE_FOUND'));
			return;
		}
		if ($debug) {
			$app->enqueueMessage(JText::sprintf('COM_JINBOUND_DEBUG_PAGE', htmlspecialchars(print_r($page, 1))));
		}
		// get the form data
		if (!method_exists($page->formbuilder, 'toArray')) {
			$reg = new JRegistry();
			if (is_string($page->formbuilder)) {
				$reg->loadString($reg->formbuilder);
			}
			else if (is_array($page->formbuilder)) {
				$reg->loadArray($reg->formbuilder);
			}
			else if (is_object($page->formbuilder)) {
				$reg->loadObject($reg->formbuilder);
			}
			$page->formbuilder = $reg;
		}
		
		$formbuilder = $page->formbuilder->toArray();
		// build data from formbuilder
		foreach ($page->formbuilder->toArray() as $name => $element) {
			if (1 !== (int) $element['enabled']) {
				continue;
			}
			$bind[$name] = $data['lead'][$name];
		}
		// force some variables into this
		$bind['id']          = 0;
		$bind['published']   = 1;
		$bind['campaign_id'] = $page->campaign;
		$bind['formdata']    = json_encode($data);
		// now get a lead table
		$message     = JText::_('COM_JINBOUND_LEAD_SAVED');
		$messageType = 'message';
		$lead        = JTable::getInstance('Lead', 'JInboundTable');
		// see if there is an existing lead for this user
		if ($lead->load(array('first_name' => $bind['first_name'], 'last_name' => $bind['last_name']))) {
			if ($debug) {
				$app->enqueueMessage(JText::sprintf('COM_JINBOUND_DEBUG_LEAD_BEFORE_SAVE', htmlspecialchars(print_r($lead, 1))));
			}
			$bind['id'] = $lead->id;
		}
		// show data before bind
		if ($debug) {
			$app->enqueueMessage(JText::sprintf('COM_JINBOUND_DEBUG_LEAD_DATA_BEFORE_BIND', htmlspecialchars(print_r($bind, 1))));
		}
		if (!$lead->bind($bind)) {
			$message     = JText::sprintf('COM_JINBOUND_LEAD_FAILED_BIND', $lead->getError());
			$messageType = 'error';
		}
		if (!$lead->check()) {
			$message     = JText::sprintf('COM_JINBOUND_LEAD_FAILED_CHECK', $lead->getError());
			$messageType = 'error';
		}
		if (!$lead->store()) {
			$message     = JText::sprintf('COM_JINBOUND_LEAD_FAILED_STORE', $lead->getError());
			$messageType = 'error';
		}
		if ($debug) {
			$app->enqueueMessage(JText::sprintf('COM_JINBOUND_DEBUG_LEAD_AFTER_SAVE', htmlspecialchars(print_r($lead, 1))));
		}
		
		// alert if necessary
		$emails = $page->notification_email;
		if (!empty($emails)) {
			$html   = array();
			$html[] = '<table>';
			foreach ($data['lead'] as $key => $val) {
				$html[] = '	<tr>';
				$html[] = '		<td>';
				$html[] = '			' . htmlspecialchars($key);
				$html[] = '		</td>';
				$html[] = '		<td>';
				$html[] = '			' . htmlspecialchars($val);
				$html[] = '		</td>';
				$html[] = '	</tr>';
			}
			$html[]  = '</table>';
			$emails  = explode(',', $emails);
			$subject = JText::_('COM_JINBOUND_NOTIFICATION_EMAIL_SUBJECT');
			$body    = JText::sprintf('COM_JINBOUND_NOTIFICATION_EMAIL_BODY', $page->formname, implode("\n", $html));
			$mailer  = JFactory::getMailer();
			$mailer->IsHTML(true);
			$mailer->setSubject($subject);
			$mailer->setBody($body);
			$mailer->addBCC($emails);
			$mailer->Send();
		}
		
		// build the redirect
		if ('message' != $messageType) {
			$redirect = JRoute::_('index.php?option=com_jinbound&id=' . $page->id);
		}
		else {
			switch ($page->after_submit_sendto) {
				case 'menuitem':
					if (!empty($page->menu_item)) {
						$redirect = JRoute::_('index.php?Itemid=' . $page->menu_item);
					}
					break;
				case 'url':
					if (!empty($page->send_to_url)) {
						$redirect = JRoute::_($page->send_to_url);
					}
					break;
				case 'message':
					if (!empty($page->sendto_message)) {
						$message = $page->sendto_message;
					}
				default:
					$redirect = JURI::root();
					break;
			}
		}
		
		
		$app->redirect($redirect, $message, $messageType);
		$app->close();
	}
}
