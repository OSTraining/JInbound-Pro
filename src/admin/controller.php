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

JLoader::register('JInboundBaseController',
    JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/controllers/basecontroller.php');
JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');

class JInboundController extends JInboundBaseController
{
    function display($cachable = false, $urlparams = false)
    {
        $app        = JFactory::getApplication();
        $view       = $app->input->get('view', 'Dashboard', 'cmd');
        $helpurl    = JInbound::config('help_url');
        $configured = preg_match('/^https?\:\/{2}/', $helpurl);
        if (!$configured) {
            $app->enqueuemessage(JText::_('COM_JINBOUND_SAVE_CONFIG_WARNING'), 'warning');
        }
        // the help view acts as a redirect to the REAL help page
        // we only really use this in the main component submenu,
        // as any link we can handle via code will just use the config option
        if ('help' == strtolower($view)) {
            $app->redirect($configured ? JInbound::config('help_url') : 'index.php?option=com_jinbound');
            // tear down the application
            jexit();
        }
        $app->input->set('view', $view);
        parent::display($cachable);
    }

    public function reset()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        if (!JFactory::getUser()->authorise('core.admin', 'com_jinbound')) {
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
        }
        JInbound::registerHelper('url');
        $db      = JFactory::getDbo();
        $app     = JFactory::getApplication();
        $errors  = array();
        $queries = array(
            'TRUNCATE TABLE #__jinbound_contacts'
        ,
            'TRUNCATE TABLE #__jinbound_contacts_campaigns'
        ,
            'TRUNCATE TABLE #__jinbound_contacts_priorities'
        ,
            'TRUNCATE TABLE #__jinbound_contacts_statuses'
        ,
            'TRUNCATE TABLE #__jinbound_conversions'
        ,
            'TRUNCATE TABLE #__jinbound_emails_records'
        ,
            'TRUNCATE TABLE #__jinbound_emails_versions'
        ,
            'TRUNCATE TABLE #__jinbound_landing_pages_hits'
        ,
            'TRUNCATE TABLE #__jinbound_leads'
        ,
            'TRUNCATE TABLE #__jinbound_notes'
        ,
            'TRUNCATE TABLE #__jinbound_subscriptions'
        ,
            'TRUNCATE TABLE #__jinbound_tracks'
        ,
            'TRUNCATE TABLE #__jinbound_users_tracks'
        ,
            'UPDATE #__jinbound_pages SET hits = 0 WHERE 1'
        );
        foreach ($queries as $query) {
            try {
                $db->setQuery($query)->query();
            } catch (Exception $e) {
                // this query should not generate an error
                if ('TRUNCATE TABLE #__jinbound_leads' !== $query) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        if (!empty($errors)) {
            $app->enqueueMessage(JText::sprintf('COM_JINBOUND_RESET_FAILED', implode('<br>', $errors)), 'error');
        } else {
            $app->enqueueMessage(JText::_('COM_JINBOUND_RESET_SUCCESS'));
        }
        $app->redirect(JInboundHelperUrl::_());
    }

    public function rss()
    {
        // feed whitelist
        $feeds = array(
            'feed' => array('url' => 'https://jinbound.com/blog/feed/rss.html', 'showDescription' => false)
        ,
            'news' => array('url' => 'https://jinbound.com/news/?format=feed', 'showDescription' => false)
        );
        $app   = JFactory::getApplication();
        // check type
        $var = $app->input->get('type');
        if (!in_array($var, array_keys($feeds))) {
            echo 'No data found';
            $app->close();
        }
        $feed = $feeds[$var];
        // load rss
        JInbound::registerLibrary('JInboundRSSView', 'views/rssview');
        $app->input->set('layout', 'rss');
        // get RSS view and display its contents
        try {
            $rss                  = new JInboundRSSView();
            $rss->showDetails     = array_key_exists('showDetails', $feed) ? $feed['showDetails'] : false;
            $rss->showDescription = array_key_exists('showDescription', $feed) ? $feed['showDescription'] : true;
            $rss->url             = $feed['url'];
            $rss->getFeed($feed['url']);
            echo $rss->loadTemplate(null, 'rss');
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $app->close();
    }
}
