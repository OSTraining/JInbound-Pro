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
JInbound::registerLibrary('JInboundFormController', 'controllers/basecontrollerform');

class JInboundControllerEmail extends JInboundFormController
{
    public function test()
    {
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
        JInbound::registerHelper('path');
        require_once JInboundHelperPath::admin('models/emails.php');
        // init
        $dispatcher = JDispatcher::getInstance();
        $app        = JFactory::getApplication();
        $input      = $app->input;
        $to         = $input->get('to', '', 'string');
        $fromname   = $input->get('fromname', '', 'string');
        $fromemail  = $input->get('fromemail', '', 'string');
        $subject    = $input->get('subject', '', 'string');
        $htmlbody   = $input->get('htmlbody', '', 'raw');
        $plainbody  = $input->get('plainbody', '', 'string');
        $type       = $input->get('type', '', 'string');
        if (!JInbound::version()->isCompatible('3.0.0')) {
            $htmlbody = JRequest::getVar('htmlbody', '', '', 'string', JREQUEST_ALLOWRAW);
        }
        // check
        foreach (array('to', 'fromname', 'fromemail', 'subject', 'type') as $var) {
            if (empty($$var)) {
                throw new Exception("Variable $var cannot be empty");
            }
        }
        // setup data for email tags
        $result = new stdClass();
        switch ($type) {
            case 'report':
                $tags = array(
                    'reports.goals.count',
                    'reports.goals.percent',
                    'reports.leads.count',
                    'reports.leads.percent',
                    'reports.leads.list',
                    'reports.pages.hits',
                    'reports.pages.list',
                    'reports.pages.top.name',
                    'reports.pages.top.url',
                    'reports.pages.lowest.name',
                    'reports.pages.lowest.url',
                    'reports.date.start',
                    'reports.date.end'
                );
                $dispatcher->trigger('onJInboundReportEmailTags', array(&$tags));
                $result->date          = (object)array(
                    'start' => '2015-01-01 00:00:00',
                    'end'   => '2015-01-07 23:59:59'
                );
                $result->goals         = (object)array('count' => 201, 'percent' => 11.0);
                $result->leads         = (object)array(
                    'count'   => 302,
                    'percent' => 0.0,
                    'list'    => '<table>'
                        . '<thead><tr>'
                        . '<th>' . JText::_('COM_JINBOUND_NAME') . '</th>'
                        . '<th>' . JText::_('COM_JINBOUND_DATE') . '</th>'
                        . '<th>' . JText::_('COM_JINBOUND_FORM_CONVERTED_ON') . '</th>'
                        . '<th>' . JText::_('COM_JINBOUND_LANDING_PAGE_NAME') . '</th>'
                        . '</tr></thead>'
                        . '<tbody>'
                        . '<tr>'
                        . '<td>John Smith</td><td>2015-01-05 12:34:56</td>'
                        . '<td>My Form</td><td>Example Page</td>'
                        . '</tr>'
                        . '<tr>'
                        . '<td>Martha Jones</td><td>2015-01-05 01:23:45</td>'
                        . '<td>My Form</td><td>Example Page</td>'
                        . '</tr>'
                        . '<tr>'
                        . '<td>Rose Tyler</td><td>2015-01-04 16:27:02</td>'
                        . '<td>My Form</td><td>Example Page</td>'
                        . '</tr>'
                        . '<tr>'
                        . '<td>Amy Pond</td><td>2015-01-03 08:11:43</td>'
                        . '<td>My Form</td><td>Example Page</td>'
                        . '</tr>'
                        . '<tr>'
                        . '<td>Rory Williams</td><td>2015-01-02 11:51:16</td>'
                        . '<td>My Form</td><td>Example Page</td>'
                        . '</tr>'
                        . '</tbody>'
                        . '</table>'
                );
                $result->pages         = (object)array(
                    'hits' => 1902,
                    'list' => '<table>'
                        . '<thead><tr>'
                        . '<th>' . JText::_('COM_JINBOUND_LANDING_PAGE_NAME') . '</th>'
                        . '<th>' . JText::_('COM_JINBOUND_VISITS') . '</th>'
                        . '<th>' . JText::_('COM_JINBOUND_SUBMISSIONS') . '</th>'
                        . '<th>' . JText::_('COM_JINBOUND_LEADS') . '</th>'
                        . '<th>' . JText::_('COM_JINBOUND_GOAL_COMPLETIONS') . '</th>'
                        . '<th>' . JText::_('COM_JINBOUND_GOAL_COMPLETION_RATE') . '</th>'
                        . '</tr></thead>'
                        . '<tbody>'
                        . '<tr>'
                        . '<td>Example Page</td>'
                        . '<td>1902</td>'
                        . '<td>341</td>'
                        . '<td>302</td>'
                        . '<td>201</td>'
                        . '<td>11.0%</td>'
                        . '</tr>'
                        . '</tbody>'
                        . '</table>'
                );
                $result->pages->top    = (object)array('name' => 'Example Page', 'url' => 'http://example.com');
                $result->pages->bottom = (object)array('name' => 'Example Page', 'url' => 'http://example.com');
                break;
            case 'campaign':
            default:
                $tags                     = array('email.campaign_name', 'email.form_name');
                $result->lead             = new stdClass();
                $result->lead->first_name = 'Howard';
                $result->lead->last_name  = 'Moon';
                $result->lead->email      = $to;
                $result->campaign_name    = 'Test Campaign';
                $result->form_name        = 'Test Form';
                break;
        }

        // init tags data
        $params = new JRegistry();
        // trigger before event
        $dispatcher->trigger('onContentBeforeDisplay', array('com_jinbound.email', &$result, &$params, 0));
        // parse tags
        $htmlbody  = JInboundModelEmails::_replaceTags($htmlbody, $result, $tags);
        $plainbody = JInboundModelEmails::_replaceTags($plainbody, $result, $tags);
        // trigger after event
        $dispatcher->trigger('onContentAfterDisplay', array('com_jinbound.email', &$result, &$params, 0));
        // send
        $mail = JFactory::getMailer();
        $mail->ClearAllRecipients();
        $mail->SetFrom($fromemail, $fromname);
        $mail->addRecipient($to, 'Test Email');
        $mail->setSubject($subject);
        $mail->setBody($htmlbody);
        $mail->IsHTML(true);
        $mail->AltBody = $plainbody;
        $sent          = $mail->Send();
        if ($sent instanceof Exception) {
            throw $sent;
        }
        if (empty($sent)) {
            throw new Exception('Cannot send email');
        }
        echo 'Done';
        jexit();
    }

    public function edit($key = 'id', $urlVar = 'id')
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_jinbound.email')) {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }
        return parent::edit($key, $urlVar);
    }

    protected function getRedirectToItemAppend($recordId = null, $urlVar = 'set')
    {
        $set    = JFactory::getApplication()->input->get('set', 'a', 'cmd');
        $append = parent::getRedirectToItemAppend($recordId, $urlVar);
        $append .= '&set=' . $set;
        return $append;
    }
}
