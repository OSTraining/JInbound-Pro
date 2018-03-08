<?php
/**
 * @package             JInbound
 * @subpackage          mod_jinbound_cta
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

defined('_JEXEC') or die;

// NOTE repeated here as it is needed for com_ajax/com_jinbound

// check that jinbound is installed
$jinbound_base = JPATH_ADMINISTRATOR . '/components/com_jinbound';
if (!is_dir($jinbound_base)) {
    return false;
}

// load required classes
JLoader::register('JInbound', "$jinbound_base/libraries/jinbound.php");

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
     *
     * @var bool
     */
    static public $running = false;

    /**
     * Array of adapter instances
     *
     * @var array
     */
    static private $adapters = array();

    /**
     * Generates the "default" urls used by the module config with data
     * so the field doesn't HAVE to be loaded via ajax, because com_ajax
     * refuses to load the helper if the module isn't published at least once.
     *
     * Lame.
     *
     * TODO possibly remove ajax loading all together. Might want to add plugin support
     * later, so it can stay for now...
     *
     * And in case someone else stumbles into this code and wonders what the $#@!
     * happened, it all started innocently enough and worked, but apparently needed
     * to be overly complicated for some reason unbeknownst to me.
     *
     */
    static public function getDefaultEmptyFields($input_value = null)
    {
        $buttons = static::getButtonData();
        if (is_object($input_value)) {
            foreach (array(
                         'isnew',
                         'campaign',
                         'priority',
                         'status',
                         'priority_campaign',
                         'priority_status'
                     ) as $what) {
                if (!is_array($input_value->$what)) {
                    continue;
                }
                foreach ($input_value->$what as $i) {
                    $buttons["default_{$what}_{$i}"] = array_merge($buttons[$what], array('default' => $i));
                }
            }
        }
        //index.php?option=com_ajax&module=jinbound_cta&method=getField&format=json&type=jinboundcampaignlist&group=c1_conditions&label=MOD_JINBOUND_CTA_CAMPAIGN_LABEL&desc=MOD_JINBOUND_CTA_CAMPAIGN_DESC&name=campaign&options[0][text]=MOD_JINBOUND_CTA_CAMPAIGN_SELECT&options[0][value]=
        $option = 'com_ajax';
        if (!JInbound::version()->isCompatible('3.0.0')) {
            $option = 'com_jinbound&task=ajax';
        }
        $url = JURI::root(false) . 'index.php?option=' . $option . '&module=jinbound_cta&method=getField&format=json';
        // type
        // group
        // label
        // desc
        // name
        // options
        $data = array();
        // TODO read from jform or something, this is gross
        foreach (array('c1_', 'c2_', 'c3_') as $i) {
            $group = $i . 'conditions';
            foreach ($buttons as $button) {
                if ('isnew' === $button['name']) {
                    $args    = '&type=' . $button['field'] . '&group=' . $group . '&label='
                        . $button['label'] . '&desc=' . $button['desc'] . '&name=' . $button['name']
                        . (array_key_exists('default', $button) ? '&default=' . $button['default'] : '')
                        . '&options[0][text]=JYES&options[0][value]=1'
                        . '&options[1][text]=JNO&options[1][value]=0';
                    $options = array(
                        0 => array('text' => JText::_('JYES'), 'value' => '1')
                    ,
                        1 => array('text' => JText::_('JNO'), 'value' => '0')
                    );
                } else {
                    $args    = '&type=' . $button['field'] . '&group=' . $group . '&label='
                        . $button['label'] . '&desc=' . $button['desc'] . '&name=' . $button['name']
                        . (array_key_exists('default', $button) ? '&default=' . $button['default'] : '')
                        . '&options[0][text]=' . $button['empty'] . '&options[0][value]=';
                    $options = array(
                        0 => array('text' => $button['empty'], 'value' => '')
                    );
                }
                $request = array_merge($button, array(
                    'group'   => $group,
                    'format'  => 'json',
                    'type'    => $button['field'],
                    'options' => $options
                ));
                $data[]  = array(
                    'url'  => $url . $args
                ,
                    'data' => array(
                        'data'    => static::getFieldAjax($request)
                    ,
                        'success' => true
                    )
                );
            }
            // add in yes/no for campaigns
            // http://local.jeff/j34/index.php?option=com_ajax&module=jinbound_cta&method=getField&format=json&type=radio&label=x&desc=x&name=campaign_yesno&options[0][text]=JYES&options[0][value]=1&options[1][text]=JNO&options[1][value]=0&class=radio%20btn-group%20btn-group-yesno%20mod_jinbound_cta_campaign_toggle&default=1&group=c1_conditions
            $data[] = array(
                'url'  => $url . '&type=radio&label=x&desc=x&name=campaign_yesno&options[0][text]=JYES&options[0][value]=1&options[1][text]=JNO&options[1][value]=0&class=radio%20btn-group%20btn-group-yesno%20mod_jinbound_cta_campaign_toggle&default=1&group=' . $group
            ,
                'data' => array(
                    'data'    => static::getFieldAjax(array(
                        'type'    => 'radio',
                        'label'   => 'x',
                        'desc'    => 'x',
                        'name'    => 'campaign_yesno',
                        'class'   => 'radio btn-group btn-group-yesno mod_jinbound_cta_campaign_toggle',
                        'default' => '1',
                        'group'   => $group,
                        'options' => array(
                            0 => array('text' => 'JYES', 'value' => '1')
                        ,
                            1 => array('text' => 'JNO', 'value' => '0')
                        )
                    ))
                ,
                    'success' => true
                )
            );
            // add in campaign select for priority/status
            foreach (array('priority', 'status') as $what) {
                $data[] = array(
                    'url'  => $url . '&type=jinboundcampaignlist&label=x&desc=x&name=' . $what . '_campaign&options[0][text]=MOD_JINBOUND_CTA_ANY_CAMPAIGN&options[0][value]=&group=' . $group
                ,
                    'data' => array(
                        'data'    => static::getFieldAjax(array(
                            'type'    => 'jinboundcampaignlist',
                            'label'   => 'x',
                            'desc'    => 'x',
                            'name'    => $what . '_campaign',
                            'default' => '',
                            'group'   => $group,
                            'options' => array(
                                0 => array('text' => 'MOD_JINBOUND_CTA_ANY_CAMPAIGN', 'value' => '')
                            )
                        ))
                    ,
                        'success' => true
                    )
                );
            }
        }
        return $data;
    }

    public static function getButtonData()
    {
        $data = array(
            'isnew'    => array(
                'name'  => 'isnew'
            ,
                'field' => 'radio'
            ,
                'label' => 'MOD_JINBOUND_CTA_ISNEW_LABEL'
            ,
                'desc'  => 'MOD_JINBOUND_CTA_ISNEW_DESC'
            ,
                'class' => 'btn-group btn-group-yesno'
            )
        ,
            'status'   => array(
                'name'  => 'status'
            ,
                'field' => 'jinboundstatuses'
            ,
                'label' => 'MOD_JINBOUND_CTA_STATUS_LABEL'
            ,
                'desc'  => 'MOD_JINBOUND_CTA_STATUS_DESC'
            ,
                'empty' => 'MOD_JINBOUND_CTA_STATUS_SELECT'
            )
        ,
            'priority' => array(
                'name'  => 'priority'
            ,
                'field' => 'jinboundpriorities'
            ,
                'label' => 'MOD_JINBOUND_CTA_PRIORITY_LABEL'
            ,
                'desc'  => 'MOD_JINBOUND_CTA_PRIORITY_DESC'
            ,
                'empty' => 'MOD_JINBOUND_CTA_PRIORITY_SELECT'
            )
        ,
            'campaign' => array(
                'name'  => 'campaign'
            ,
                'field' => 'jinboundcampaignlist'
            ,
                'label' => 'MOD_JINBOUND_CTA_CAMPAIGN_LABEL'
            ,
                'desc'  => 'MOD_JINBOUND_CTA_CAMPAIGN_DESC'
            ,
                'empty' => 'MOD_JINBOUND_CTA_CAMPAIGN_SELECT'
            )
        );
        // TODO plugin
        return $data;
    }

    /**
     * option=com_ajax&module=jinbound_cta&method=getField&type={field type}&format={format}
     *
     * @return object
     */
    static public function getFieldAjax($variables = array())
    {
        // init
        jimport('joomla.form.form');
        jimport('joomla.form.field');
        JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_jinbound/models/fields');
        JFactory::getLanguage()->load('mod_jinbound_cta.sys', JPATH_ROOT);
        $app     = JFactory::getApplication();
        $format  = $app->input->getWord('format');
        $type    = $app->input->getWord('type');
        $group   = $app->input->getCmd('group', null);
        $control = $app->input->getWord('control', 'jform');
        $name    = $app->input->getCmd('name');
        $default = $app->input->getString('default', '');
        $label   = $app->input->getString('label', '');
        $desc    = $app->input->getString('desc', '');
        $class   = $app->input->getString('class', '');
        $options = $app->input->get('options', array(), 'raw');
        // allow overrides
        if (!empty($variables)) {
            foreach ($variables as $variable => $value) {
                if ('app' === $variable) {
                    continue;
                }
                $$variable = $value;
            }
        }
        // validate
        if (empty($type)) {
            throw new LogicException('Field type required', 500);
        }
        if (empty($name)) {
            throw new LogicException('Field name required', 500);
        }
        // start building form
        $form   = JForm::getInstance('jinbound_form_module', '<form><!-- --></form>', array('control' => $control));
        $xml    = new JXMLElement('<form></form>');
        $params = $xml->addChild('fields');
        $params->addAttribute('name', 'params');
        if (is_null($group) || empty($group)) {
            $group    = null;
            $fieldset = $params->addChild('fieldset');
        } else {
            $fields = $params->addChild('fields');
            $fields->addAttribute('name', $group);
            $fieldset = $fields->addChild('fieldset');
        }
        $field = $fieldset->addChild('field');
        // configure field
        $field->addAttribute('name', $name);
        $field->addAttribute('type', $type);
        $field->addAttribute('default', $default);
        $field->addAttribute('class', $class);
        $field->addAttribute('label', JText::_($label));
        $field->addAttribute('description', JText::_($desc));
        // add options, if needed
        if (is_array($options) && !empty($options)) {
            foreach ($options as $option) {
                if (!(is_array($option) && array_key_exists('value', $option) && array_key_exists('text', $option))) {
                    continue;
                }
                $opt = $field->addChild('option', JText::_($option['text']));
                $opt->addAttribute('value', $option['value']);
            }
        }
        // build the result
        $form->load($xml, false);
        $obj    = $form->getField($name, "params.$group");
        $result = (object)array(
            'field' => (object)array(
                'label' => $obj->label
            ,
                'field' => $obj->input
            )
        );
        if ('json' === $format || 'debug' === $format) {
            return $result;
        }
        return $result->field;
    }

    /**
     * Gets the adapter instance
     *
     * @param JRegistry $params
     * @param bool      $cached
     *
     * @return ModJInboundCTAAdapter
     * @throws RuntimeException
     */
    static public function getAdapter(JRegistry $params, $cached = true)
    {
        // init
        $app    = JFactory::getApplication();
        $filter = JFilterInput::getInstance();
        // in order to load the correct adapter, conditions need to be checked
        // get the correct adapter parameter name
        // should return one of: '', 'c1_', 'c2_', 'c3_'
        $pfx = $filter->clean(static::findAdapterType($params), 'cmd');
        // determine which adapter to use
        $name = $filter->clean($params->get($pfx . 'mode', 'module'), 'cmd');
        // if this adapter exists, send it along
        if (array_key_exists($name, self::$adapters) && $cached) {
            if (JDEBUG) {
                $app->enqueueMessage("Found pre-existing adapter '$name' ...");
            }
            return self::$adapters[$name];
        }
        // try to load the adapter class
        if (!class_exists($class = 'ModJInboundCTA' . ucfirst($name) . 'Adapter')) {
            if (!file_exists($require = dirname(__FILE__) . '/adapters/' . $name . '.php')) {
                throw new RuntimeException('Adapter not found', 404);
            }
            require_once $require;
            if (!class_exists($class)) {
                throw new RuntimeException('Adapter file not found', 404);
            }
        }
        if (JDEBUG) {
            $app->enqueueMessage("Creating new adapter instance '$class' with pfx '$pfx' ...");
        }
        $adapter = new $class($params);
        // set the adapter prefix
        $adapter->pfx = $pfx;
        // set the adapter and return it
        return self::$adapters[$name] = $adapter;
    }

    static protected function findAdapterType(JRegistry $params)
    {
        // init
        $app  = JFactory::getApplication();
        $pfxs = array('c1_', 'c2_', 'c3_');
        $data = static::loadContactData();
        $type = '';
        if (JDEBUG) {
            $app->enqueueMessage("Checking against this user data:<pre>" . htmlspecialchars(print_r($data,
                    1)) . "</pre>");
        }
        foreach ($pfxs as $pfx) {
            if (static::checkData($data, $params, $pfx)) {
                $type = $pfx;
                break;
            }
        }
        if (JDEBUG) {
            $app->enqueueMessage("Found adapter type '$type' ...");
        }
        return $type;
    }

    static protected function loadContactData()
    {
        // init
        $cookie            = filter_input(INPUT_COOKIE, '__jib__');
        $contact_id        = static::getContactId();
        $contact           = new stdClass();
        $contact->id       = $contact_id;
        $contact->isnew    = empty($cookie);
        $contact->campaign = array();
        $contact->priority = array();
        $contact->status   = array();
        // validate
        if (!empty($contact_id)) {
            // use the component helper class to load relevant info
            JInbound::registerHelper('contact');
            $contact->campaign = JInboundHelperContact::getContactCampaigns($contact_id);
            $contact->priority = JInboundHelperContact::getContactPriorities($contact_id);
            $contact->status   = JInboundHelperContact::getContactStatuses($contact_id);
        }
        return $contact;
    }

    static protected function getContactId()
    {
        $cookie = plgSystemJInbound::getCookieValue();
        $db     = JFactory::getDbo();
        return (int)$db->setQuery($db->getQuery(true)
            ->select($db->quoteName('id'))->from('#__jinbound_contacts')
            ->where($db->quoteName('cookie') . ' = ' . $db->quote($cookie))
        )->loadResult();
    }

    static protected function checkData($data, JRegistry $params, $pfx)
    {
        // init
        $app     = JFactory::getApplication();
        $enabled = (int)$params->get($pfx . 'enabled', ModJInboundCTAHelper::CONDITION_ANY);
        if (!$enabled) {
            if (JDEBUG) {
                $app->enqueueMessage("Conditions for prefix '$pfx' not enabled...");
            }
            return false;
        } else {
            if (JDEBUG) {
                $app->enqueueMessage("Conditions for prefix '$pfx' enabled...");
            }
        }
        $match      = (int)$params->get($pfx . 'match', ModJInboundCTAHelper::CONDITION_ANY);
        $conditions = $params->get($pfx . 'conditions');
        $matches    = array();
        // conditions MUST be an object to continue
        if (is_object($conditions)) {
            // fix conditions
            foreach (array(
                         'isnew',
                         'campaign',
                         'campaign_yesno',
                         'priority',
                         'priority_campaign',
                         'status',
                         'status_campaign'
                     ) as $property) {
                if (!property_exists($conditions, $property)) {
                    continue;
                }
                if (!is_array($conditions->$property)) {
                    $conditions->$property = array_values((array)$conditions->$property);
                }
            }
            if (JDEBUG) {
                $app->enqueueMessage(serialize($conditions));
            }
            // isnew
            if (property_exists($conditions, 'isnew')) {
                $isnew = (array)$conditions->isnew;
                foreach ($isnew as $key => $value) {
                    foreach ((array)$value as $v) {
                        if (JDEBUG) {
                            $app->enqueueMessage("User should " . ((int)$v ? '' : 'NOT ') . "be new ...");
                        }
                        $matches[] = ((int)$v) && $data->isnew;
                    }
                }
            }
            // there should be 3*2 variables:
            // campaign && campaign_yesno
            if (property_exists($conditions, 'campaign') && property_exists($conditions, 'campaign_yesno')) {
                // check if the user is in one of the campaigns
                foreach ($conditions->campaign as $key => $value) {
                    // check if the user is in this campaign
                    $in = false;
                    foreach ($data->campaign as $c) {
                        if ($c->id == $value) {
                            $in = true;
                            break;
                        }
                    }
                    if (JDEBUG) {
                        $app->enqueueMessage("User is " . ($in ? '' : 'NOT ') . "in campaign '$value' ($key) ...");
                    }
                    // check yes/no
                    $yn = $conditions->campaign_yesno[$key];
                    if (JDEBUG) {
                        $app->enqueueMessage("User should " . ($yn ? '' : 'NOT ') . "be in campaign '$value' ($key, $yn) ...");
                    }
                    $matches[] = (bool)($in ? $yn : !$yn);
                }
            }
            // status && status_campaign
            if (property_exists($conditions, 'status') && property_exists($conditions, 'status_campaign')) {
                // check each status
                foreach ($conditions->status as $key => $value) {
                    // get the campaign to be checked
                    $status_campaign = $conditions->status_campaign[$key];
                    // if there is a specific campaign, check only that
                    if (!empty($status_campaign)) {
                        // if the key does not exist at all, then the user has no status for this campaign
                        if (!array_key_exists($status_campaign, $data->status)) {
                            // cannot have this status in this campaign
                            $matches[] = false;
                            if (JDEBUG) {
                                $app->enqueueMessage("User has no status for campaign '$status_campaign' ($key) ...");
                            }
                        } // key must exist, so check the first entry (shouldn't be empty?)
                        else {
                            $in        = ($value == $data->status[$status_campaign][0]->status_id);
                            $matches[] = (bool)$in;
                            if (JDEBUG) {
                                $app->enqueueMessage("User has " . ($in ? '' : 'in') . "correct status for campaign '$status_campaign' ($key) ...");
                            }
                        }
                    } // otherwise loop through all campaigns
                    else {
                        $set = false;
                        foreach ($data->status as $statuses) {
                            // if one matches, we're satisfied
                            if ($statuses[0]->status_id == $value) {
                                $set = true;
                                break;
                            }
                        }
                        if (JDEBUG) {
                            $app->enqueueMessage("User " . ($set ? 'has' : 'does not have') . " status '$value' ($key) ...");
                        }
                        $matches[] = (bool)$set;
                    }
                }
            }
            // priority && priority_campaign
            if (property_exists($conditions, 'priority') && property_exists($conditions, 'priority_campaign')) {
                // check each priority
                foreach ($conditions->priority as $key => $value) {
                    // get the campaign to be checked
                    $priority_campaign = $conditions->priority_campaign[$key];
                    // if there is a specific campaign, check only that
                    if (!empty($priority_campaign)) {
                        // if the key does not exist at all, then the user has no priority for this campaign
                        if (!array_key_exists($priority_campaign, $data->priority)) {
                            // cannot have this priority in this campaign
                            $matches[] = false;
                            if (JDEBUG) {
                                $app->enqueueMessage("User has no priority for campaign '$priority_campaign' ($key) ...");
                            }
                        } // key must exist, so check the first entry (shouldn't be empty?)
                        else {
                            $in        = ($value == $data->priority[$priority_campaign][0]->priority_id);
                            $matches[] = (bool)$in;
                            if (JDEBUG) {
                                $app->enqueueMessage("User has " . ($in ? '' : 'in') . "correct priority for campaign '$priority_campaign' ($key) ...");
                            }
                        }
                    } // otherwise loop through all campaigns
                    else {
                        $set = false;
                        foreach ($data->priority as $priorities) {
                            // if one matches, we're satisfied
                            if ($priorities[0]->priority_id == $value) {
                                $set = true;
                                break;
                            }
                        }
                        if (JDEBUG) {
                            $app->enqueueMessage("User " . ($set ? 'has' : 'does not have') . " priority '$value' ($key) ...");
                        }
                        $matches[] = (bool)$set;
                    }
                }
            }
        }
        // no matches at all? bail
        if (empty($matches)) {
            if (JDEBUG) {
                $app->enqueueMessage("Found no matches for this user...");
            }
            return false;
        }

        $result = false;
        switch ($match) {
            case ModJInboundCTAHelper::CONDITION_ANY:
                if (JDEBUG) {
                    $app->enqueueMessage('User must match ANY ...');
                }
                $place  = array_search(true, $matches, true);
                $result = (false !== $place);
                break;
            case ModJInboundCTAHelper::CONDITION_NONE:
                if (JDEBUG) {
                    $app->enqueueMessage('User must match NONE ...');
                }
                $place  = array_search(true, $matches, true);
                $result = (false === $place);
                break;
            case ModJInboundCTAHelper::CONDITION_ALL:
                if (JDEBUG) {
                    $app->enqueueMessage('User must match ALL ...');
                }
                $place  = array_search(false, $matches, true);
                $result = (false === $place);
                break;
        }
        if (JDEBUG) {
            $app->enqueueMessage("User is " . ($result ? '' : 'NOT ') . "a match!");
        }
        return $result;
    }
}
