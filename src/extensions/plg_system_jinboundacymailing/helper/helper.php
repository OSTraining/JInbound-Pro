<?php
/**
 * @package             jInbound
 * @subpackage          plg_system_jinboundacymailing
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

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');

class JinboundAcymailing
{
    protected $db;

    public function __construct($config = array())
    {
        $this->db = JFactory::getDbo();
    }

    public function onJinboundSetStatus($status_id, $campaign_id, $contact_id)
    {
        // init
        $this->getAcyListHelper();
        $campaign    = $this->getComponentItem('campaign', $campaign_id);
        $status      = $this->getComponentItem('status', $status_id);
        $contact     = $this->getComponentItem('contact', $contact_id);
        $addLists    = $status->final ? array() : array_filter($campaign->params->get('acymailing_addlists', array()));
        $removeLists = $status->final ? array_filter($campaign->params->get('acymailing_addlists', array())) : array();
        $subClass    = acymailing_get('class.subscriber');
        $subtable    = '#__acymailing_subscriber';
        $subid       = $this->db->setQuery($this->db->getQuery(true)
            ->select('subid')
            ->from($subtable)
            ->where('email = ' . $this->db->quote($contact->email))
        )->loadResult();
        if (!$subid) {
            // https://www.acyba.com/acymailing/64-acymailing-developer-documentation.html#highlight-7
            $user  = (object)array(
                'email'  => $contact->email
            ,
                'userid' => $contact->user_id
            ,
                'name'   => trim($contact->first_name . ' ' . $contact->last_name)
            ,
                'source' => 'jInbound'
            );
            $subid = $subClass->save($user);
        }
        $subArray = array();
        if (!empty($addLists)) {
            foreach ($addLists as $list) {
                $subArray[$list] = array('status' => 1);
            }
        }
        if (!empty($removeLists)) {
            foreach ($removeLists as $list) {
                $subArray[$list] = array('status' => 0);
            }
        }
        if (!empty($subArray)) {
            $subClass->saveSubscription($subid, $subArray);
        }
    }

    private function getAcyListHelper()
    {
        if (!class_exists('acylistHelper')) {
            if (!$this->loadAcyHelperFile('list')) {
                return false;
            }
        }
        if (!class_exists('acylistHelper')) {
            return false;
        }
        if (!function_exists('acymailing_level')) {
            if (!$this->loadAcyHelperFile('helper')) {
                return false;
            }
        }
        if (!function_exists('acymailing_level')) {
            return false;
        }
        return new acylistHelper();
    }

    private function loadAcyHelperFile($file)
    {
        if (file_exists(($file = JPATH_ADMINISTRATOR . '/components/com_acymailing/helpers/' . $file . '.php'))) {
            require_once $file;
            return true;
        } else {
            return false;
        }
    }

    protected function getComponentItem($type, $id)
    {
        require_once JPATH_ADMINISTRATOR . '/components/com_jinbound/tables/' . $type . '.php';

        $class = 'JInboundTable' . ucwords($type);

        /** @var JInboundTable $obj */
        $obj = new $class($this->db);
        $obj->load($id);

        if (property_exists($obj, 'params') && !$obj->params instanceof Registry) {
            $obj->params = new Registry($obj->params);
        }

        return $obj;
    }

    public function getListSelectOptions($level)
    {
        return $this->db->setQuery($this->db->getQuery(true)
            ->select('listid AS value, name AS text')
            ->from('#__acymailing_list')
            ->where($this->db->quoteName('type') . '=' . $this->db->quote('list'))
            ->where($this->db->quoteName('published') . '=' . $this->db->quote('1'))
        )->loadObjectList();
    }

    public function getListTable($email, $id = null)
    {
        JFactory::getLanguage()->load('com_acymailing', JPATH_ROOT);
        if (empty($email)) {
            return JText::_('PLG_SYSTEM_JINBOUNDACYMAILING_CONTACT_NO_EMAIL');
        }
        $lists = $this->getEmailListDetails($email);
        if (empty($lists)) {
            return JText::_('PLG_SYSTEM_JINBOUNDACYMAILING_CONTACT_NO_LISTS');
        }
        $filter = JFilterInput::getInstance();
        if (!empty($id)) {
            $id = ' id="' . $filter->clean($id) . '"';
        }
        $html   = array();
        $html[] = '<table class="table table-striped"' . $id . '>';
        $html[] = '<thead><tr>';
        $html[] = '<th>' . JText::_('COM_JINBOUND_NAME') . '</th>';
        $html[] = '<th>' . JText::_('JSTATUS') . '</th>';
        $html[] = '</tr></thead>';
        $html[] = '<tbody>';
        foreach ($lists as $list) {
            $status = ($list->status == 1) ? JText::_('SUBSCRIBED') : (($list->status == -1) ? JText::_('UNSUBSCRIBED') : JText::_('PENDING_SUBSCRIPTION'));
            $html[] = '<tr>';
            $html[] = '<td><h3>' . $filter->clean($list->name) . '</h3></td>';
            $html[] = '<td>' . $filter->clean($status) . '</td>';
            $html[] = '</tr>';
        }
        $html[] = '</tbody>';
        $html[] = '</table>';
        return implode("\n", $html);
    }

    private function updateListSub($list, $sub, $subdate = false, $unsubdate = false, $status = 1)
    {
        $query = $this->db->getQuery(true)
            ->update('#__acymailing_listsub')
            ->set('status = ' . intval($status))
            ->where('listid = ' . intval($list))
            ->where('subid = ' . intval($sub));
        if ($subdate) {
            $query->set('subdate = ' . $this->db->quote($subdate));
        }
        if ($unsubdate) {
            $query->set('unsubdate = ' . $this->db->quote($unsubdate));
        }
        $this->db->setQuery($query)->query();
    }

    private function addListSub($list, $sub, $subdate = false, $unsubdate = false, $status = 1)
    {
        $columns = array('listid', 'subid', 'status');
        $values  = array(intval($list), intval($sub), intval($status));
        if ($subdate) {
            $method    = 'subscribe';
            $columns[] = 'subdate';
            $values[]  = $this->db->quote($subdate);
        }
        if ($unsubdate) {
            $method    = 'unsubscribe';
            $columns[] = 'unsubdate';
            $values[]  = $this->db->quote($unsubdate);
        }
        $this->db->setQuery($this->db->getQuery(true)
            ->insert('#__acymailing_listsub')
            ->columns($columns)
            ->values(implode(',', $values))
        )->query();
        $helper = $this->getAcyListHelper();
        if (false !== $helper) {
            $helper->$method($sub, array($list));
        }
    }

    private function getListSub($list, $sub)
    {
        return $this->db->setQuery($this->db->getQuery(true)
            ->select('*')
            ->from('#__acymailing_listsub')
            ->where('subid = ' . $sub)
            ->where('listid = ' . (int)$list)
        )->loadObject();
    }
}
