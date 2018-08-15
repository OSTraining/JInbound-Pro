<?php
/**
 * @package             jInbound
 * @subpackage          plg_system_jinboundmailchimp
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

use Joomla\Registry\Registry;

defined('_JEXEC') or die;

class JinboundMailchimp
{
    /**
     * @var MCAPI
     */
    protected $mcApi;

    /**
     * @var Registry
     */
    protected $params = null;

    /**
     * @var object[]
     */
    protected static $lists = null;

    /**
     * @var object[]
     */
    protected static $categories = null;

    /**
     * @var object[]
     */
    protected static $groups = null;

    /**
     * @var object[]
     */
    protected static $fields = null;

    protected $fieldSubTags = array(
        'address' => array('addr1', 'addr2', 'city', 'state', 'zip', 'country')
    );

    /**
     * @var object[]
     */
    protected static $memberships = array();

    public function __construct($config = array())
    {
        $this->params = new Registry(empty($config['params']) ? null : $config['params']);

        $apiKey = $this->params->get('mailchimp_key');
        if ($apiKey) {
            // Load the MailChimp library
            require_once __DIR__ . '/MCAPI.class.php';

            $this->mcApi = new MCAPI($apiKey);
        }
    }

    /**
     * @param int $campaignId
     * @param int $contactId
     *
     * @return void
     * @throws Exception
     */
    public function onJinboundSetStatus($campaignId, $contactId)
    {
        if (!$this->mcApi) {
            return;
        }

        $app = JFactory::getApplication();

        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jinbound/tables');

        /** @var JInboundTableCampaign $campaign */
        $campaign = JTable::getInstance('Campaign', 'JinboundTable');
        $campaign->load($campaignId);
        $campaign->params = new Registry($campaign->params);

        /** @var JInboundTableContact $contact */
        $contact = JTable::getInstance('Contact', 'JinboundTable');
        $contact->load($contactId);

        if (!$campaign->id || !$contact->id) {
            return;
        }

        $listsAdd    = array_filter((array)$campaign->params->get('addlists', array()));
        $listsRemove = array_filter((array)$campaign->params->get('removelists', array()));

        // @TODO: params should be configured to save the list id as part of the group id
        $groupsAdd    = $this->verifyGroups((array)$campaign->params->get('addgroups', array()), true);
        $groupsRemove = $this->verifyGroups((array)$campaign->params->get('removegroups', array()), false);

        $currentMemberships = $this->mcApi->getMemberships($contact->email);
        if ($lists = array_intersect(array_keys($currentMemberships), $listsRemove)) {
            // Remove from MailChimp lists
            $deleteMember = (bool)$this->params->get('delete_member', false);
            foreach ($lists as $listId) {
                $this->mcApi->unsubscribe($contact->email, $listId, $deleteMember);
            }
        }

        // Add to MailChimp list
        if ($listsAdd) {
            $mergeFields = $this->getMergeFields($contact, $campaign);

            $newStatus = $this->params->get('doubleOptin', false) ? 'pending' : 'subscribed';
            $emailType = $this->params->get('emailType', 'html');

            foreach ($listsAdd as $listId) {
                if (JDEBUG) {
                    $app->enqueueMessage(
                        sprintf(
                            '<h3>[%s] Merge Fields</h3><pre>%s</pre>',
                            __METHOD__,
                            htmlspecialchars(print_r($mergeFields, 1), ENT_QUOTES, 'UTF-8')
                        )
                    );
                }

                $params = array(
                    'status_if_new' => $newStatus,
                    'email_type'    => $emailType
                );
                if ($mergeFields) {
                    $params['merge_fields'] = $mergeFields;
                }
                if ($groupsAdd[$listId]) {
                    $params['interests'] = $groupsAdd[$listId];
                }

                $this->mcApi->subscribe($contact->email, $listId, $params);
            }
        }

        $currentMemberships = $this->mcApi->getMemberships($contact->email);

        $listUpdates = array_intersect_key($currentMemberships, $groupsRemove);
        foreach ($listUpdates as $listId => $list) {
            try {
                $this->mcApi->update($contact->email, $listId, array('interests' => $groupsRemove[$listId]));

            } catch (Exception $e) {
                $app->enqueueMessage($e->getMessage(), 'error');
            }
        }
    }

    /**
     * Get either a hash for all MC Lists or a single list
     *
     * @param string|string[] $listId
     *
     * @return object[]
     * @throws Exception
     */
    public function getLists($listIds = null)
    {
        if (static::$lists === null && $this->mcApi) {
            try {
                static::$lists = $this->mcApi->getLists();

            } catch (Exception $e) {
                static::$lists = array();
                JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            }
        }

        if ($listIds) {
            if (!is_array($listIds)) {
                $listIds = array($listIds);
            }
            $listIds = array_filter($listIds);

            return array_intersect_key(static::$lists, array_flip($listIds));
        }

        return static::$lists;
    }

    /**
     * @param string|string[] $categoryIds
     *
     * @return object[]
     * @throws Exception
     */
    public function getCategories($categoryIds = null)
    {
        if (!$this->mcApi) {
            return array();
        }

        if (static::$categories === null) {
            static::$categories = array();

            $lists = $this->getLists();
            foreach ($lists as $listId => $list) {
                try {
                    $categories = $this->mcApi->getCategories($listId);
                    foreach ($categories as $categoryId => $category) {
                        $category->list = $lists[$listId];

                        static::$categories[$categoryId] = $category;
                    }

                } catch (Exception $e) {
                    JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                }
            }
        }

        if ($categoryIds) {
            if (!is_array($categoryIds)) {
                $categoryIds = array($categoryIds);
            }
            $categoryIds = array_filter($categoryIds);

            return array_intersect_key(static::$categories, array_flip($categoryIds));
        }

        return static::$categories;
    }

    /**
     * @param string|string{} $listIds
     *
     * @return object[]
     * @throws Exception
     */
    public function getCategoriesByList($listIds)
    {
        if (!$this->mcApi) {
            return array();
        }

        if (!is_array($listIds)) {
            $listIds = array($listIds);
        }
        $listIds = array_filter($listIds);

        $selectedCategories = array();
        if ($listIds) {
            $categories = $this->getCategories();
            foreach ($categories as $categoryId => $category) {
                if (in_array($category->list_id, $listIds)) {
                    $selectedCategories[$categoryId] = $category;
                }
            }
        }

        return $selectedCategories;
    }

    /**
     * @param string|string[] $groupIds
     *
     * @return object[]
     * @throws Exception
     */
    public function getGroups($groupIds = null)
    {
        if (!$this->mcApi) {
            return array();
        }

        if (static::$groups === null) {
            static::$groups = array();

            $categories = $this->getCategories();
            foreach ($categories as $categoryId => $category) {
                $groups = $this->mcApi->getGroups($category->list_id, $category->id);
                foreach ($groups as $groupId => $group) {
                    $group->category = $category;

                    static::$groups[$groupId] = $group;
                }
            }
        }

        if ($groupIds) {
            if (!is_array($groupIds)) {
                $groupIds = array($groupIds);
            }
            $groupIds = array_filter($groupIds);

            return array_intersect_key(static::$groups, array_flip($groupIds));
        }

        return static::$groups;
    }

    /**
     * @param string|string[] $listIds
     *
     * @return object[]
     * @throws Exception
     */
    public function getGroupsByList($listIds)
    {
        if (!$this->mcApi) {
            return array();
        }

        if (!is_array($listIds)) {
            $listIds = array($listIds);
        }
        $listIds = array_filter($listIds);

        $selectedGroups = array();
        if ($listIds) {
            $groups = $this->getGroups();

            foreach ($groups as $groupId => $group) {
                if (in_array($group->category->list->id, $listIds)) {
                    $selectedGroups[$groupId] = $group;
                }
            }
        }

        return $selectedGroups;
    }

    /**
     * @param string|string[] $listIds
     *
     * @return object[]
     * @throws Exception
     */
    public function getFields($listIds = null)
    {
        if (!$this->mcApi) {
            return array();
        }

        if (static::$fields === null) {
            static::$fields = array();

            if ($this->mcApi) {
                $lists = $this->getLists();

                foreach ($lists as $list) {
                    try {
                        $fields = $this->mcApi->getFields($list->id);

                        // Fabricate the required email field that isn't returned otherwise
                        $fields[] = (object)array(
                            'merge_id'      => 0,
                            'tag'           => 'EMAIL',
                            'name'          => JText::_('PLG_SYSTEM_JINBOUNDMAILCHIMP_EMAIL'),
                            'type'          => 'text',
                            'required'      => true,
                            'default_value' => '',
                            'public'        => false,
                            'display_order' => 1,
                            'options'       => (object)array(),
                            'help_text'     => '',
                            'list_id'       => $list->id,
                            '_links'        => array()
                        );

                        uasort($fields, function ($a, $b) {
                            return $a->display_order - $b->display_order;
                        });

                        static::$fields[$list->id] = (object)array(
                            'list'   => $list,
                            'fields' => $fields
                        );

                    } catch (Exception $e) {
                        JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                    }
                }
            }
        }

        if ($listIds) {
            if (!is_array($listIds)) {
                $listIds = array($listIds);
            }
            $listIds = array_filter($listIds);

            return array_intersect_key(static::$fields, array_flip($listIds));
        }

        return static::$fields;
    }

    /**
     * @param $email
     *
     * @return object[]
     * @throws Exception
     */
    public function getMemberships($email)
    {
        if (!$this->mcApi) {
            return null;
        }

        $key = md5($email);
        if (!isset(static::$memberships[$key])) {
            static::$memberships[$key] = false;

            try {
                if ($memberships = $this->mcApi->getMemberships($email)) {
                    $lists = $this->getLists(array_keys($memberships));
                    foreach ($memberships as $listId => $membership) {
                        $membership->list = $lists[$listId];

                        $interests = empty($membership->interests)
                            ? array()
                            : array_filter(json_decode(json_encode($membership->interests), true));

                        $membership->interests = $interests ? $this->getGroups(array_keys($interests)) : array();
                    }
                }
                static::$memberships[$key] = $memberships;

            } catch (Exception $e) {
                JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            }
        }

        return static::$memberships[$key];
    }

    /*
     * Return the MailChimp groups as an array of options
     */

    /**
     * @param JInboundTableContact  $contact
     * @param JInboundTableCampaign $campaign
     *
     * @return array
     * @throws Exception
     */
    protected function getMergeFields(JInboundTableContact $contact, JInboundTableCampaign $campaign)
    {
        /*
         * getting the form data to be sent to mailchimp presents a bit of a problem
         * this method only knows the contact id, the campaign id, and the status id
         * and everything else must be extrapolated from that
         * what we can do is load the fields related to the pages with the same
         * form and campaign, then check the user's conversions for that page
         * this should give us the conversion data, including the fields
         */

        $app = JFactory::getApplication();
        $db  = JFactory::getDbo();

        // get form field params from db first
        $campaignFields = $db->setQuery(
            $db->getQuery(true)
                ->select(
                    array(
                        'Field.name',
                        'Field.params',
                        'Page.id AS page_id',
                        'Page.campaign'
                    )
                )
                ->from('#__jinbound_fields AS Field')
                ->leftJoin('#__jinbound_form_fields AS FormField ON FormField.field_id = Field.id')
                ->leftJoin('#__jinbound_pages AS Page ON Page.formid = FormField.form_id')
                ->leftJoin('#__jinbound_contacts_campaigns AS ContactCampaign ON ContactCampaign.campaign_id = Page.campaign')
                ->where(
                    array(
                        'Page.id IS NOT NULL',
                        'Page.campaign = ' . (int)$campaign->id
                    )
                )
                ->group('Field.id')
        )
            ->loadObjectList();

        if (JDEBUG) {
            $app->enqueueMessage(
                sprintf(
                    '<h3>[%s] Field Data</h3><pre>%s</pre>',
                    __METHOD__,
                    htmlspecialchars(print_r($campaignFields, 1), ENT_QUOTES, 'UTF-8')
                )
            );
        }

        $mergeValues = array(
            'FNAME' => $contact->first_name,
            'LNAME' => $contact->last_name
        );

        /*
         * NOTE: during contact save, this ends up triggering BEFORE the conversion
         * is saved, so having no conversions at all means that it's likely
         * that this is a new contact - in order to make this work correctly,
         * we have to also check the post data with this request
         * so what we'll do is loop through the fields first, set initial data
         * from the conversions, then override using post data
         */
        $contactConversions = JInboundHelperContact::getContactConversions($contact->id);

        if ($token = $app->input->getCmd('token')) {
            $rawPostParam = preg_replace('/^(.*?)\.(.*?)\.(.*?)$/', '${1}_${3}', $token);

        } else {
            $rawPostParam = 'jform';
        }
        $rawPostData = $app->input->post->get($rawPostParam, array(), 'array');

        $filter = JFilterInput::getInstance();
        foreach ($campaignFields as $campaignField) {
            $params = new Registry($campaignField->params);

            $mappedFields = (array)$params->get('mailchimp.mapped_field', array());
            if ($mappedFields) {
                $campaignFieldName = $campaignField->name;

                foreach ($contactConversions as $contactConversion) {
                    if ($campaignField->page_id == $contactConversion->page_id) {
                        $value = empty($contactConversion->formdata['lead']->$campaignFieldName)
                            ? null
                            : $contactConversion->formdata['lead']->$campaignFieldName;

                        if ($value) {
                            $this->updateMergeValue($mergeValues, $mappedFields, $value);
                        }
                    }
                }

                // now override using post data
                if (!empty($rawPostData['lead'][$campaignField->name])) {
                    $postValue = $filter->clean($rawPostData['lead'][$campaignFieldName]);

                    if ($postValue) {
                        $this->updateMergeValue($mergeValues, $mappedFields, $postValue);
                    }
                }
            }
        }

        return $mergeValues;
    }

    /**
     * @param string|string[] $groups
     *
     * @return string[][]
     * @throws Exception
     */
    protected function verifyGroups(array $groups, $value)
    {
        $result = array();
        if ($groups) {
            if ($groups = $this->getGroups($groups)) {
                foreach ($groups as $groupId => $group) {
                    $listId = $group->list_id;
                    if (!isset($result[$listId])) {
                        $result[$listId] = array();
                    }
                    $result[$group->list_id][$groupId] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Returns Mailchimp subfields for compound fields like addresses
     *
     * @param string $type
     *
     * @return string[]
     */
    public function getSubTags($type)
    {
        $type = strtolower($type);

        return empty($this->fieldSubTags[$type]) ? null : $this->fieldSubTags[$type];
    }

    /**
     * Add/Update mapped field values array from the list of fieldnames
     *
     * @param string[] $values
     * @param string[] $fieldNames
     * @param mixed    $value
     *
     * @return void
     */
    protected function updateMergeValue(array &$values, array $fieldNames, $value)
    {
        foreach ($fieldNames as $fieldName) {
            if (strpos($fieldName, ':')) {
                list($baseName, $subField) = explode(':', $fieldName, 2);
                if (!isset($values[$baseName])) {
                    $values[$baseName] = array();
                }
                $values[$baseName][$subField] = $value;

            } else {
                $values[$fieldName] = $value;
            }
        }
    }
}
