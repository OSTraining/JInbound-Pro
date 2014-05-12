<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

abstract class JInboundHelperPriority
{
	static public function getDefaultPriority()
	{
		static $default;
		
		if (is_null($default))
		{
			$db = JFactory::getDbo();
			
			$default = $db->setQuery($db->getQuery(true)
				->select($db->quoteName('id'))
				->from('#__jinbound_priorities')
				->order($db->quoteName('ordering'))
			)->loadResult();
			
			if (is_null($default))
			{
				$default = false;
			}
		}
		
		return $default;
	}
	
	static public function setContactPriorityForCampaign($priority_id, $contact_id, $campaign_id, $user_id = null)
	{
		$db = JFactory::getDbo();
		// some info for the status and priority
		$date    = new DateTime();
		$created = $date->format('Y-m-d H:i:s');
		// save the status
		return $db->setQuery($db->getQuery(true)
			->insert('#__jinbound_contacts_priorities')
			->columns(array(
				'priority_id'
			,	'campaign_id'
			,	'contact_id'
			,	'created'
			,	'created_by'
			))
			->values($db->quote($priority_id)
			. ', ' . $db->quote($campaign_id)
			. ', ' . $db->quote($contact_id)
			. ', ' . $db->quote($created)
			. ', ' . $db->quote(JFactory::getUser($user_id)->get(id))
			)
		)->query();
	}
}
