<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
 **********************************************
 * jInbound
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

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('contact');
JInbound::registerHelper('status');
JInbound::registerLibrary('JInboundAdminModel', 'models/basemodeladmin');

JPluginHelper::importPlugin('content');
JPluginHelper::importPlugin('jinbound');

/**
 * This models supports retrieving a contact.
 *
 * @package        jInbound
 * @subpackage     com_jinbound
 */
class JInboundModelContact extends JInboundAdminModel
{
    /**
     * Model context string.
     *
     * @var        string
     */
    protected $context = 'com_jinbound.contact';

    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.' . $this->name, $this->name,
            array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        $campaigns = JInboundHelperContact::getContactCampaigns($form->getValue('id'));
        $value     = array();

        if (is_array($campaigns) && !empty($campaigns)) {
            foreach ($campaigns as $campaign) {
                $value[] = $campaign->id;
            }
        }

        $form->setValue('_campaigns', null, $value);
        // check published permissions
        if (!JFactory::getUser()->authorise('core.edit.state', 'com_jinbound.contact')) {
            $form->setFieldAttribute('published', 'readonly', 'true');
        }
        // return the form
        return $form;
    }

    /**
     * set the lead status details for an item
     *
     * @param unknown_type $contact_id
     * @param unknown_type $campaign_id
     * @param unknown_type $status_id
     *
     * @return mixed
     */
    public function status($contact_id, $campaign_id, $status_id, $creator = null)
    {
        return JInboundHelperStatus::setContactStatusForCampaign($status_id, $contact_id, $campaign_id, $creator);
    }

    /**
     * set the lead priority details
     *
     * @param unknown_type $id
     * @param unknown_type $value
     *
     * @return mixed
     */
    public function priority($contact_id, $campaign_id, $priority_id, $creator = null)
    {
        $dispatcher = JDispatcher::getInstance();

        $db = JFactory::getDbo();

        // some info for the status and priority
        $created = JFactory::getDate()->toSql();
        $creator = JFactory::getUser($creator)->get('id');
        // save the status
        $return = $db->setQuery($db->getQuery(true)
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
                . ', ' . $db->quote($created)
                . ', ' . $db->quote($creator)
            )
        )->query();

        $result = $dispatcher->trigger('onJInboundChangeState', array(
                'com_jinbound.contact.priority',
                $campaign_id,
                array($contact_id),
                $priority_id
            )
        );

        if (is_array($result) && !empty($result) && in_array(false, $result, true)) {
            return false;
        }

        return $return;
    }

    /**
     * get lead notes
     *
     * @param unknown_type $id
     */
    public function getNotes($id = null)
    {
        if (property_exists($this, 'item') && $this->item && $this->item->id == $id) {
            $item = $this->item;
        } else {
            $item = $this->getItem($id);
        }
        $db = JFactory::getDbo();

        try {
            $notes = $db->setQuery($db->getQuery(true)
                ->select('Note.id, Note.created, Note.text, User.name AS author')
                ->from('#__jinbound_notes AS Note')
                ->leftJoin('#__users AS User ON User.id = Note.created_by')
                ->where('Note.published = 1')
                ->where('Note.lead_id = ' . (int)$item->id)
                ->group('Note.id')
            )->loadObjectList();
            if (!is_array($notes) || empty($notes)) {
                throw new Exception('Empty');
            }
        } catch (Exception $e) {
            $notes = array();
        }

        return $notes;
    }

    public function getItem($id = null)
    {
        $item = parent::getItem($id);
        $db   = JFactory::getDbo();

        $item->campaigns          = array();
        $item->conversions        = array();
        $item->emails             = array();
        $item->previous_campaigns = array();
        $item->priorities         = array();
        $item->statuses           = array();

        if ($item->id) {
            $item->campaigns          = JInboundHelperContact::getContactCampaigns($item->id);
            $item->conversions        = JInboundHelperContact::getContactConversions($item->id);
            $item->emails             = JInboundHelperContact::getContactEmails($item->id);
            $item->previous_campaigns = JInboundHelperContact::getContactCampaigns($item->id, true);
            $item->priorities         = JInboundHelperContact::getContactPriorities($item->id);
            $item->statuses           = JInboundHelperContact::getContactStatuses($item->id);
        }

        // add tracks
        try {
            $item->tracks = $db->setQuery($db->getQuery(true)
                ->select('Track.*')
                ->from('#__jinbound_tracks AS Track')
                ->where('Track.cookie = ' . $db->quote($item->cookie))
                ->order('Track.created DESC')
            )->loadObjectList();
        } catch (Exception $e) {
            $item->tracks = array();
        }

        return $item;
    }
}
