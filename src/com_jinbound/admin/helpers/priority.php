<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 **********************************************
 * JInbound
 * Copyright (c) 2013 Anything-Digital.com
 * Copyright (c) 2018 Open Source Training, LLC
 **********************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.n *
 * This header must not be removed. Additional contributions/changes
 * may be added to this header as long as no information is deleted.
 */

defined('JPATH_PLATFORM') or die;

abstract class JInboundHelperPriority
{
    static public function getDefaultPriority()
    {
        static $default;

        if (is_null($default)) {
            $db = JFactory::getDbo();

            $default = $db->setQuery($db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from('#__jinbound_priorities')
                ->order($db->quoteName('ordering'))
            )->loadResult();

            if (is_null($default)) {
                $default = false;
            }
        }

        return $default;
    }

    static public function setContactPriorityForCampaign($priority_id, $contact_id, $campaign_id, $user_id = null)
    {
        $db   = JFactory::getDbo();
        $date = JFactory::getDate()->toSql();
        // save the status
        return $db->setQuery($db->getQuery(true)
            ->insert('#__jinbound_contacts_priorities')
            ->columns(array(
                'priority_id'
            ,
                'campaign_id'
            ,
                'contact_id'
            ,
                'created'
            ,
                'created_by'
            ))
            ->values($db->quote($priority_id)
                . ', ' . $db->quote($campaign_id)
                . ', ' . $db->quote($contact_id)
                . ', ' . $db->quote($date)
                . ', ' . $db->quote(JFactory::getUser($user_id)->get('id'))
            )
        )->query();
    }

    static public function getSelectOptions()
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
            ->select('id AS value, name AS text')
            ->from('#__jinbound_priorities')
            ->where('published = 1')
            ->order('ordering ASC');

        $db->setQuery($query);

        try {
            $options = $db->loadObjectList();
        } catch (Exception $e) {
            $options = array();
        }

        return $options;
    }
}
