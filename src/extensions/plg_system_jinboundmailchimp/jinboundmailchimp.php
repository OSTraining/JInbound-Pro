<?php
/**
 * @package             JInbound
 * @subpackage          plg_system_jinboundmailchimp
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

jimport('joomla.filesystem.file');
jimport('joomla.plugin.plugin');

$db      = JFactory::getDbo();
$plugins = $db->setQuery($db->getQuery(true)
    ->select('extension_id')->from('#__extensions')
    ->where($db->qn('element') . ' = ' . $db->q('com_jinbound'))
    ->where($db->qn('enabled') . ' = 1')
)->loadColumn();
defined('PLG_SYSTEM_JINBOUNDMAILCHIMP') or define('PLG_SYSTEM_JINBOUNDMAILCHIMP', 1 === count($plugins));

class plgSystemJInboundmailchimp extends JPlugin
{
    protected $app;

    /**
     * Constructor
     *
     * @param unknown_type $subject
     * @param unknown_type $config
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->app = JFactory::getApplication();
        $this->loadLanguage('plg_system_jinboundmailchimp', JPATH_ADMINISTRATOR);
        $this->loadLanguage('plg_system_jinboundmailchimp.sys', JPATH_ADMINISTRATOR);
    }

    public function onContentPrepareForm($form)
    {
        if (!PLG_SYSTEM_JINBOUNDMAILCHIMP) {
            return true;
        }
        if (JDEBUG) {
            $this->app->enqueueMessage(__METHOD__);
        }
        if (!($form instanceof JForm)) {
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
                if ($this->app->isSite()) {
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
        if ('com_jinbound.contact.status' !== $context || !PLG_SYSTEM_JINBOUNDMAILCHIMP) {
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
