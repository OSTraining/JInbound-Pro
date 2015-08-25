<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('_JEXEC') or die;

abstract class ModJInboundCTAHelper
{
	const CONDITION_ANY  = -1;
	const CONDITION_ALL  = 1;
	const CONDITION_NONE = 0;
	
	const USER_ANY      = -1;
	const USER_NEW      = 1;
	const USER_EXISTING = 0;
	
	/**
	 * Flag so module cannot be placed into an infinite loop
	 * @var bool
	 */
	static public $running = false;
	
	/**
	 * Array of adapter instances
	 * @var array
	 */
	static private $adapters = array();
	
	/**
	 * Gets the adapter instance
	 * 
	 * @param JRegistry $params
	 * @param bool $cached
	 * @return ModJInboundCTAAdapter
	 * @throws RuntimeException
	 */
	static public function getAdapter(JRegistry $params, $cached = true)
	{
		// determine which adapter to use
		$name = JFilterInput::getInstance()->clean($params->get('cta_mode', 'module'), 'cmd');
		// if this adapter exists, send it along
		if (array_key_exists($name, self::$adapters) && $cached)
		{
			return self::$adapters[$name];
		}
		// try to load the adapter class
		if (!class_exists($class = 'ModJInboundCTA' . ucfirst($name) . 'Adapter'))
		{
			if (!file_exists($require = dirname(__FILE__) . '/adapters/' . $name . '.php'))
			{
				throw new RuntimeException('Adapter not found', 404);
			}
			require_once $require;
			if (!class_exists($class))
			{
				throw new RuntimeException('Adapter file not found', 404);
			}
		}
		$adapter = new $class($params);
		// set the adapter mode
		$adapter->is_alt = !self::isMatch($params);
		// set the adapter and return it
		return self::$adapters[$name] = $adapter;
	}
	
	static protected function isMatch(JRegistry $params)
	{
		$app = JFactory::getApplication();
		// first check if this is a new user
		$is_new = self::isNewUser();
		$in     = $params->get('cta_in_campaigns', array());
		$not_in = $params->get('cta_not_in_campaigns', array());
		$check_in     = !empty($in);
		$check_not_in = !empty($not_in);
		// new users won't be in any campaigns
		if ($is_new)
		{
			$in_campaigns     = false;
			$not_in_campaigns = true;
		}
		// existing users might be in campaigns
		else
		{
			if (!is_array($in))
			{
				$in = explode(',', $in);
			}
			if (!is_array($not_in))
			{
				$not_in = explode(',', $not_in);
			}
			$in_campaigns     = self::isInCampaigns($in);
			$not_in_campaigns = !self::isInCampaigns($not_in);
		}
		
		$skip_new  = ModJInboundCTAHelper::USER_ANY === (int) $params->get('cta_new_user', ModJInboundCTAHelper::USER_ANY);
		$condition = (int) $params->get('cta_condition', ModJInboundCTAHelper::CONDITION_ANY);
		
		if (JDEBUG)
		{
			if (!$skip_new)
			{
				$app->enqueueMessage('New User: ' . ($is_new ? 'true' : 'false'));
			}
			$app->enqueueMessage('In Campaigns: ' . ($in_campaigns ? 'true' : 'false'));
			$app->enqueueMessage('Not In Campaigns: ' . ($not_in_campaigns ? 'true' : 'false'));
		}
		
		// base decisions on condition
		$final = $skip_new ? false : $is_new;
		switch ($condition)
		{
			// if the condition is "any", we check all the conditions
			case ModJInboundCTAHelper::CONDITION_ANY:
				if (JDEBUG)
				{
					$app->enqueueMessage('Condition: ANY');
				}
				if (($check_in && $in_campaigns) || ($check_not_in && $not_in_campaigns))
				{
					$final = true;
				}
				return $final;
				//return $skip_new ? ($in_campaigns || $not_in_campaigns) : ($is_new || $in_campaigns || $not_in_campaigns);
			// "all" requires all of these to be true
			case ModJInboundCTAHelper::CONDITION_ALL:
				if (JDEBUG)
				{
					$app->enqueueMessage('Condition: ALL');
				}
				if ($check_in)
				{
					$final = $final && $in_campaigns;
				}
				if ($check_not_in)
				{
					$final = $final && $not_in_campaigns;
				}
				return $final;
				//return $skip_new ? ($in_campaigns && $not_in_campaigns) : ($is_new && $in_campaigns && $not_in_campaigns);
			// "none" requires none of these to be true
			case ModJInboundCTAHelper::CONDITION_NONE:
				if (JDEBUG)
				{
					$app->enqueueMessage('Condition: NONE');
				}
				if ($check_in)
				{
					$final = $final && $in_campaigns;
				}
				if ($check_not_in)
				{
					$final = $final && $not_in_campaigns;
				}
				return !$final;
				//return !($skip_new ? ($in_campaigns && $not_in_campaigns) : ($is_new && $in_campaigns && $not_in_campaigns));
			default:
				throw new RuntimeException('Unknown condition', 404);
		}
	}
	
	static protected function isNewUser()
	{
		$cookie     = plgSystemJInbound::getCookieValue();
		$db         = JFactory::getDbo();
		$contact_id = $db->setQuery($db->getQuery(true)
			->select($db->quoteName('id'))->from('#__jinbound_contacts')
			->where($db->quoteName('cookie') . ' = ' . $db->quote($cookie))
		)->loadResult();
		$result     = empty($contact_id);
		if (JDEBUG)
		{
			JFactory::getApplication()->enqueueMessage(($result ? 'Not a contact' : 'Found contact id ' . $contact_id) . ' (' . htmlspecialchars($cookie, ENT_QUOTES, 'UTF-8') . ')');
		}
		return $result;
	}
	
	static protected function isInCampaigns(array $campaigns)
	{
		$cookie  = plgSystemJInbound::getCookieValue();
		$db      = JFactory::getDbo();
		$app     = JFactory::getApplication();
		$records = $db->setQuery($db->getQuery(true)
			->select('c.campaign_id')
			->from('#__jinbound_contacts_campaigns AS c')
			->leftJoin('#__jinbound_contacts AS l ON l.id = c.contact_id')
			->where('c.enabled = 1')
			->where('l.cookie = ' . $db->quote($cookie))
		)->loadColumn();
		$records = is_array($records) ? array_unique($records) : array();
		if (JDEBUG)
		{
			$app->enqueueMessage('User is in the following campaigns: ' . implode(', ', $records));
		}
		foreach ($campaigns as $campaign)
		{
			if (in_array($campaign, $records))
			{
				if (JDEBUG)
				{
					$app->enqueueMessage('User is in campaign ' . (int) $campaign);
				}
				return true;
			}
		}
		if (JDEBUG)
		{
			$app->enqueueMessage('User is NOT in the selected campaign');
		}
		return false;
	}
}
