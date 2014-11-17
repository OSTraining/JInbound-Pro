<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

abstract class JInboundHelperContact
{
	static public function getContactEmails($contact_id)
	{
		$db = JFactory::getDbo();
		try
		{
			$emails = $db->setQuery($db->getQuery(true)
				->select('Email.*')
				->from('#__jinbound_emails_records AS Email')
				->where('Email.lead_id = ' . $db->quote($contact_id))
			)->loadObjectList();
		}
		catch (Exception $e)
		{
			$emails = array();
		}
		
		return $emails;
	}
	
	/**
	 * Load conversions for a given contact id
	 * 
	 * @param unknown_type $contact_id
	 */
	static public function getContactConversions($contact_id)
	{
		$db = JFactory::getDbo();
		try
		{
			$conversions = $db->setQuery($db->getQuery(true)
				->select('Conversion.*')
				->select('Page.name AS page_name')
				->select(sprintf('IF(Conversion.created_by = 0, %s, User.name) AS created_by_name', $db->quote('guest')))
				->from('#__jinbound_conversions AS Conversion')
				->leftJoin('#__jinbound_pages AS Page ON Page.id = Conversion.page_id')
				->leftJoin('#__users AS User ON User.id = Conversion.created_by')
				->where('Conversion.contact_id = ' . $db->quote($contact_id))
				->group('Conversion.id')
			)->loadObjectList();
		}
		catch (Exception $e)
		{
			$conversions = array();
		}
		
		if (!empty($conversions))
		{
			foreach ($conversions as &$conversion)
			{
				$conversion->formdata = (array) json_decode($conversion->formdata);
			}
		}
		
		return $conversions;
	}
	
	/**
	 * Load campaigns for a given contact
	 * 
	 * @param int  $contact_id
	 * @param bool $previous
	 */
	static public function getContactCampaigns($contact_id, $previous = false)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('Campaign.*')
			->from('#__jinbound_campaigns AS Campaign')
		;
		// previous entries are those that have statuses but not campaigns
		if ($previous)
		{
			$query
				->where('Campaign.id NOT IN(('
					. $db->getQuery(true)
						->select('DISTINCT ContactCampaigns.campaign_id')
						->from('#__jinbound_contacts_campaigns AS ContactCampaigns')
						->where('ContactCampaigns.contact_id = ' . $db->quote($contact_id))
					. '))'
				)
				->where('Campaign.id IN(('
					. $db->getQuery(true)
						->select('DISTINCT ContactStatuses.campaign_id')
						->from('#__jinbound_contacts_statuses AS ContactStatuses')
						->where('ContactStatuses.contact_id = ' . $db->quote($contact_id))
					. '))'
				)
			;
		}
		// current campaigns
		else
		{
			$query->where('Campaign.id IN(('
				. $db->getQuery(true)
					->select('ContactCampaigns.campaign_id')
					->from('#__jinbound_contacts_campaigns AS ContactCampaigns')
					->where('ContactCampaigns.contact_id = ' . $db->quote($contact_id))
				. '))'
			);
		}
		try
		{
			$campaigns = $db->setQuery($query)->loadObjectList();
		}
		catch (Exception $e)
		{
			$campaigns = array();
		}
		return $campaigns;
	}
	
	static public function getContactStatuses($contact_id)
	{
		$db = JFactory::getDbo();
		try
		{
			$statuses = $db->setQuery($db->getQuery(true)
				->select('ContactStatus.*, Status.name, Status.description')
				->select(sprintf('IF(ContactStatus.created_by = 0, %s, User.name) AS created_by_name', $db->quote('guest')))
				->from('#__jinbound_contacts_statuses AS ContactStatus')
				->leftJoin('#__jinbound_lead_statuses AS Status ON Status.id = ContactStatus.status_id')
				->leftJoin('#__users AS User ON User.id = ContactStatus.created_by')
				->where('ContactStatus.contact_id = ' . $db->quote($contact_id))
				->order('ContactStatus.created DESC')
			)->loadObjectList();
			if (empty($statuses))
			{
				throw new Exception('empty');
			}
		}
		catch (Exception $e)
		{
			return array();
		}
		$list = array();
		foreach ($statuses as $status)
		{
			$key = $status->campaign_id;
			if (!array_key_exists($key, $list))
			{
				$list[$key] = array();
			}
			$list[$key][] = $status;
		}
		return $list;
	}
	
	static public function getContactPriorities($contact_id)
	{
		$db = JFactory::getDbo();
		try
		{
			$priorities = $db->setQuery($db->getQuery(true)
				->select('ContactPriority.*, Priority.name, Priority.description')
				->select(sprintf('IF(ContactPriority.created_by = 0, %s, User.name) AS created_by_name', $db->quote('guest')))
				->from('#__jinbound_contacts_priorities AS ContactPriority')
				->leftJoin('#__jinbound_priorities AS Priority ON Priority.id = ContactPriority.priority_id')
				->leftJoin('#__users AS User ON User.id = ContactPriority.created_by')
				->where('ContactPriority.contact_id = ' . $db->quote($contact_id))
				->order('ContactPriority.created DESC')
			)->loadObjectList();
			if (empty($priorities))
			{
				throw new Exception('empty');
			}
		}
		catch (Exception $e)
		{
			return array();
		}
		$list = array();
		foreach ($priorities as $priority)
		{
			$key = $priority->campaign_id;
			if (!array_key_exists($key, $list))
			{
				$list[$key] = array();
			}
			$list[$key][] = $priority;
		}
		return $list;
	}
}
