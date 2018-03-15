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

defined('JPATH_PLATFORM') or die;

class plgSystemJInboundmailchimp extends JPlugin
{
    /**
     * @var JApplicationCms
     */
    protected $app = null;

    /**
     * @var bool
     */
    protected $enabled = null;

    /**
     * Constructor
     *
     * @param JEventDispatcher $subject
     * @param array            $config
     *
     * @return void
     * @throws Exception
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);

        $this->app = JFactory::getApplication();
        $this->loadLanguage('plg_system_jinboundmailchimp');
        $this->loadLanguage('plg_system_jinboundmailchimp.sys');

        if (!defined('JINB_LOADED')) {
            $includePath = JPATH_ADMINISTRATOR . '/components/com_jinbound/include.php';
            if (is_file($includePath)) {
                require_once $includePath;
            }
        }
        $this->enabled = defined('JINB_LOADED');
    }

    public function onContentPrepareForm($form)
    {
        if (!$this->enabled) {
            return true;
        }

        if (JDEBUG) {
            $this->app->enqueueMessage(__METHOD__);
        }
        if (!$form instanceof JForm) {
            $this->_subject->setError('JERROR_NOT_A_FORM');
            return false;
        }

        switch ($form->getName()) {
            case 'com_jinbound.campaign':
                $file = 'jinboundmailchimp';
                break;

            case 'com_jinbound.field':
                $file = 'jinboundmailchimpfield';
                break;

            case 'com_jinbound.contact':
                if ($this->app->isClient('site')) {
                    return true;
                }
                $file = 'jinboundmailchimpcontact';
                break;

            default:
                return true;
        }

        JForm::addFormPath(dirname(__FILE__) . '/form');
        JForm::addFieldPath(dirname(__FILE__) . '/field');
        $result = $form->loadFile($file, false);

        return $result;
    }

    public function onJInboundChangeState($context, $campaign_id, $contacts, $status_id)
    {
        if (!$this->enabled || $context !== 'com_jinbound.contact.status') {
            return;
        }

        if (JDEBUG) {
            $this->app->enqueueMessage(__METHOD__);
        }

        require_once realpath(dirname(__FILE__) . '/library/helper.php');
        $helper = new JinboundMailchimp(array('params' => $this->params));

        foreach ($contacts as $contact_id) {
            $helper->onJinboundSetStatus($status_id, $campaign_id, $contact_id);
        }
    }
}
