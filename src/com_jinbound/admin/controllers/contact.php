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

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('contact');
JInbound::registerHelper('status');
JInbound::registerHelper('priority');
JInbound::registerLibrary('JInboundFormController', 'controllers/basecontrollerform');

class JInboundControllerContact extends JInboundFormController
{
    public function edit($key = 'id', $urlVar = 'id')
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_jinbound.contact')) {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }
        return parent::edit($key, $urlVar);
    }

    public function status()
    {
        $this->_changeContact('status');
    }

    private function _changeContact($how)
    {
        $app = JFactory::getApplication();
        $id = $app->input->get('id');
        $campaign = $app->input->get('campaign_id');
        $value = $app->input->get('value');
        $model = $this->getModel();
        $model->$how($id, $campaign, $value);
    }

    public function priority()
    {
        $this->_changeContact('priority');
    }

    /**
     * Saves campaign, status etc.
     *
     * (non-PHPdoc)
     * @see JControllerForm::postSaveHook()
     */
    protected function postSaveHook($model, $validData = array())
    {
        // only operate on valid records
        $contact = (int)$model->getState('contact.id');
        if ($contact) {
            // clear this contact's campaigns
            $db = JFactory::getDbo();
            $db->setQuery($db->getQuery(true)
                ->delete('#__jinbound_contacts_campaigns')
                ->where('contact_id = ' . $db->quote($contact))
            )->query();

            // Get a list of all active campaign ID's
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__jinbound_campaigns')
                ->where('published = 1');
            $db->setQuery($query);
            $all_campaigns = $db->loadColumn();

            // ensure campaigns is an array
            $campaigns = is_array($validData['_campaigns']) ? $validData['_campaigns'] : (
            empty($validData['_campaigns']) ? array() : array($validData['_campaigns'])
            );
            JArrayHelper::toInteger($campaigns);

            // Get a list of active campaign ID's to which the lead does not belong
            //  either because the lead never belonged to that campaign or was removed
            //  from the campaign.
            $removed_campaigns = array_diff($all_campaigns, $campaigns);

            // re-add to the desired campaigns
            if (!empty($campaigns)) {
                $query = $db->getQuery(true)
                    ->insert('#__jinbound_contacts_campaigns')
                    ->columns(array('contact_id', 'campaign_id'));
                foreach ($campaigns as $campaign) {
                    $query->values($contact . ',' . $campaign);
                }

                $db->setQuery($query)->query();

                // find campaigns this contact has no status for yet
                $new_campaigns = $db->setQuery($db->getQuery(true)
                    ->select('campaign_id')
                    ->from('#__jinbound_contacts_campaigns')
                    ->where('campaign_id NOT IN(('
                        . $db->getQuery(true)
                            ->select('DISTINCT campaign_id')
                            ->from('#__jinbound_contacts_statuses')
                            ->where('contact_id = ' . $contact)
                        . '))')
                )->loadColumn();

                // this user does not have a status for these campaigns - add a default status for each
                if (!empty($new_campaigns)) {
                    // the default status
                    $status_id = JInboundHelperStatus::getDefaultStatus();
                    sort($new_campaigns);

                    foreach (array_unique($new_campaigns) as $new_campaign) {
                        JInboundHelperStatus::setContactStatusForCampaign($status_id, $contact, $new_campaign);
                    }
                }

                // find campaigns this contact has no priority for yet
                $new_campaigns = $db->setQuery($db->getQuery(true)
                    ->select('campaign_id')
                    ->from('#__jinbound_contacts_campaigns')
                    ->where('campaign_id NOT IN(('
                        . $db->getQuery(true)
                            ->select('DISTINCT campaign_id')
                            ->from('#__jinbound_contacts_priorities')
                            ->where('contact_id = ' . $contact)
                        . '))')
                )->loadColumn();

                // this user does not have a status for these campaigns - add a default status for each
                if (!empty($new_campaigns)) {
                    // the default status
                    $priority_id = JInboundHelperPriority::getDefaultPriority();
                    sort($new_campaigns);

                    foreach (array_unique($new_campaigns) as $new_campaign) {
                        JInboundHelperPriority::setContactPriorityForCampaign($priority_id, $contact, $new_campaign);
                    }
                }

                // Get contact information and create a formdata array
                $contact_info = $model->getItem($contact);
                $formdata     = array(
                    'lead' => array(
                        'first_name' => $contact_info->first_name,
                        'last_name'  => $contact_info->last_name,
                        'email'      => $contact_info->email
                    )
                );

                // Get a list of active pages associated with each added campaign
                //  along with a list of conversions associated with those pages
                foreach ($campaigns as $campaign) {
                    $pages = $this->_getPages($campaign);

                    $page_ids = array();
                    foreach ($pages as $page) {
                        $page_ids[] = $page->id;
                    }

                    $conversions = $this->_getConversions($contact, $page_ids);

                    // If there are no conversions, create 1
                    if (empty($conversions)) {
                        if (!empty($pages)) {
                            // We can only create a valid conversion record if there is
                            //  a page associated with this campaign

                            $page_id = $pages[0]->id;
                            $form_id = $pages[0]->formid;

                            // Get the current date/time to store with conversion record for created
                            $now = "'" . JFactory::getDate() . "'";

                            // Get a base date/time to store for modified and checked_out_time
                            $never = "'0000-00-00 00:00:00'";

                            // Insert a new conversion record
                            $query = $db->getQuery(true)
                                ->insert('#__jinbound_conversions')
                                ->columns(array(
                                    'page_id',
                                    'contact_id',
                                    'published',
                                    'created',
                                    'created_by',
                                    'modified',
                                    'modified_by',
                                    'checked_out',
                                    'checked_out_time',
                                    'formdata'
                                ))
                                ->values($page_id . ', ' . $contact . ', 1, ' . $now . ', ' . JFactory::getUser()->id . ', ' . $never . ', 0, 0, ' . $never . ', \'' . json_encode($formdata) . '\'');
                            $db->setQuery($query)->query();
                        }
                    }
                }
            }
            // If there are removed campaigns, we need to also remove their conversions if they exist
            if (!empty($removed_campaigns)) {
                // Get a list of active pages associated with each added campaign
                //  along with a list of conversions associated with those pages
                foreach ($removed_campaigns as $campaign) {
                    $pages = $this->_getPages($campaign);

                    $page_ids = array();
                    foreach ($pages as $page) {
                        $page_ids[] = $page->id;
                    }

                    $conversions = $this->_getConversions($contact, $page_ids);

                    if (!empty($conversions)) {
                        // Remove the conversions associated with the removed campaigns
                        $db->setQuery($db->getQuery(true)
                            ->delete('#__jinbound_conversions')
                            ->where('id IN (' . implode(',', $conversions) . ')')
                        )->query();
                    }
                }
            }
        }
    }

    private function _getPages($campaign)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
            ->select('id, formid')
            ->from('#__jinbound_pages')
            ->where('campaign = ' . $campaign)
            ->where('published = 1');
        $db->setQuery($query);
        $pages = $db->loadObjectList();

        return $pages;
    }

    private function _getConversions($contact, $page_ids)
    {
        $db = JFactory::getDbo();

        if (!empty($page_ids)) {
            $query = $db->getQuery(true)
                ->select('id')
                ->from('#__jinbound_conversions')
                ->where('contact_id = ' . $contact)
                ->where('page_id IN (' . implode(',', $page_ids) . ')');
            $db->setQuery($query);
            $conversions = $db->loadColumn();
        } else {
            $conversions = array();
        }

        return $conversions;
    }
}
