<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

abstract class JInboundHelperStatus
{
	static public function getDefaultStatus()
	{
		static $default;
		
		if (is_null($default))
		{
			$db = JFactory::getDbo();
			
			$default = $db->setQuery($db->getQuery(true)
					->select($db->quoteName('id'))
					->from('#__jinbound_lead_statuses')
					->where($db->quoteName('default') . ' = 1')
					->where($db->quoteName('published') . ' = 1')
			)->loadResult();
			
			if (is_null($default))
			{
				$default = false;
			}
		}
		
		return $default;
	}
	
	static public function setContactStatusForCampaign($status_id, $contact_id, $campaign_id, $user_id = null)
	{
		$db = JFactory::getDbo();
		// some info for the status and priority
		$date    = new DateTime();
		$created = $date->format('Y-m-d H:i:s');
		// save the status
		return $db->setQuery($db->getQuery(true)
			->insert('#__jinbound_contacts_statuses')
			->columns(array(
				'status_id'
			,	'campaign_id'
			,	'contact_id'
			,	'created'
			,	'created_by'
			))
			->values($db->quote($status_id)
			. ', ' . $db->quote($campaign_id)
			. ', ' . $db->quote($contact_id)
			. ', ' . $db->quote($created)
			. ', ' . $db->quote(JFactory::getUser($user_id)->get(id))
			)
		)->query();
	}
}
