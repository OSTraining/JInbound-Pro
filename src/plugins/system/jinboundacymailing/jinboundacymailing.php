<?php
/**
 * @package             JInbound
 * @subpackage          plg_system_jinboundacymailing
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
    ->where('('
        . $db->qn('element') . ' = ' . $db->q('com_acymailing')
        . ' OR '
        . $db->qn('element') . ' = ' . $db->q('com_jinbound')
        . ')')
    ->where($db->qn('enabled') . ' = 1')
)->loadColumn();
defined('PLG_SYSTEM_JINBOUNDACYMAILING') or define('PLG_SYSTEM_JINBOUNDACYMAILING', 2 === count($plugins));

class plgSystemJInboundacymailing extends JPlugin
{
    /**
     * Constructor
     *
     * @param unknown_type $subject
     * @param unknown_type $config
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage('plg_system_jinboundacymailing.sys', JPATH_ADMINISTRATOR);
    }

    public function onAfterInitialise()
    {
        if (JFactory::getApplication()->isSite() || !PLG_SYSTEM_JINBOUNDACYMAILING) {
            return;
        }
        $option = array_key_exists('option', $_REQUEST) ? $_REQUEST['option'] : '';
        $view   = array_key_exists('view', $_REQUEST) ? $_REQUEST['view'] : '';
        if ('plg_system_jinboundacymailing' === $option && 'liveupdate' === $view) {
            require_once JPATH_ROOT . '/plugins/system/jinboundacymailing/liveupdate/liveupdate.php';
            $updateInfo = LiveUpdate::getUpdateInformation();
            if ($updateInfo->hasUpdates) {
                echo JText::sprintf('PLG_SYSTEM_JINBOUNDACYMAILING_UPDATE_HASUPDATES', $updateInfo->version);
            }
            jexit();
        }
    }

    public function onJinboundDashboardUpdate()
    {
        return "index.php?option=plg_system_jinboundacymailing&view=liveupdate";
    }

    public function onContentPrepareForm($form)
    {
        if (!PLG_SYSTEM_JINBOUNDACYMAILING) {
            return true;
        }
        if (!($form instanceof JForm)) {
            $this->_subject->setError('JERROR_NOT_A_FORM');
            return false;
        }
        switch ($form->getName()) {
            case 'com_jinbound.campaign':
                $file = 'jinboundacymailing';
                break;
            case 'com_jinbound.contact':
                if (JFactory::getApplication()->isSite()) {
                    return true;
                }
                $file = 'jinboundacymailingcontact';
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
        if ('com_jinbound.contact.status' !== $context || !PLG_SYSTEM_JINBOUNDACYMAILING) {
            return;
        }
        require_once realpath(dirname(__FILE__) . '/helper/helper.php');
        $helper = new JinboundAcymailing(array('params' => $this->params));
        foreach ($contacts as $contact_id) {
            $helper->onJinboundSetStatus($status_id, $campaign_id, $contact_id);
        }
    }

    public function onJInboundAfterJsonChangeState($how, $contact_id, $campaign_id, $value, $result)
    {
        if (!$result || 'status' !== $how || !PLG_SYSTEM_JINBOUNDACYMAILING) {
            return;
        }
        $db    = JFactory::getDbo();
        $email = $db->setQuery($db->getQuery(true)
            ->select('email')
            ->from('#__jinbound_contacts')
            ->where('id = ' . intval($contact_id))
        )->loadResult();
        require_once realpath(dirname(__FILE__) . '/helper/helper.php');
        $helper = new JinboundAcymailing(array('params' => $this->params));
        return array('acymailing' => $helper->getListTable($email, 'jform_acymailing_table'));
    }
}
