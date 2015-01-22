<?php
/**
 * @package		JInbound
 * @subpackage	plg_system_jinboundmailchimp
@ant_copyright_header@
 */

defined('_JEXEC') or die;

class JFormFieldJinboundmailchimpcontactinfo extends JFormField
{
	protected function getInput()
	{
		$email = $this->form->getValue('email');
		if (empty($email))
		{
			return JText::_('PLG_SYSTEM_JINBOUNDMAILCHIMP_CONTACT_NO_EMAIL');
		}
		$plugin = JPluginHelper::getPlugin('system', 'jinboundmailchimp');
		require_once realpath(dirname(__FILE__).'/../library/helper.php');
		$helper = new JinboundMailchimp(array('params' => $plugin->params));
		$lists = $helper->getEmailListDetails($email);
		if (empty($lists))
		{
			return JText::_('PLG_SYSTEM_JINBOUNDMAILCHIMP_CONTACT_NO_LISTS');
		}
		$filter = JFilterInput::getInstance();
		$html = array();
		$html[] = '<table class="table table-striped">';
		$html[] = '<thead><tr>';
		$html[] = '<th>' . JText::_('COM_JINBOUND_NAME') . '</th>';
		$html[] = '<th>' . JText::_('PLG_SYSTEM_JINBOUNDMAILCHIMP_GROUPS') . '</th>';
		$html[] = '<th>' . JText::_('JSTATUS') . '</th>';
		$html[] = '</tr></thead>';
		$html[] = '<tbody>';
		foreach ($lists as $id => $list)
		{
			foreach ($list['data'] as $data)
			{
				$html[] = '<tr>';
				$html[] = '<td><h3>' . $filter->clean($data['list_name']) . '</h3></td>';
				$html[] = '<td>';
				if (array_key_exists('merges', $data)
					&& is_array($data['merges'])
					&& array_key_exists('GROUPINGS', $data['merges'])
					&& is_array($data['merges']['GROUPINGS'])
					&& !empty($data['merges']['GROUPINGS']))
				{
					$html[] = '<ul>';
					foreach ($data['merges']['GROUPINGS'] as $group)
					{
						$html[] = '<li>' . $filter->clean($group['groups']) . '</li>';
					}
					$html[] = '</ul>';
				}
				$html[] = '</td>';
				$html[] = '<td>' . $filter->clean($data['status']) . '</td>';
				$html[] = '</tr>';
			}
		}
		$html[] = '</tbody>';
		$html[] = '</table>';
		return implode("\n", $html);
	}
	
	protected function getLabel()
	{
		return '';
	}
}
