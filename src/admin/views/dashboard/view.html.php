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

JInbound::registerLibrary('JInboundView', 'views/baseview');

class JInboundViewDashboard extends JInboundView
{
    protected $feeds = array(
        'feed' => array('url' => 'https://jinbound.com/blog/feed/rss.html', 'showDescription' => false)
    ,
        'news' => array('url' => 'https://jinbound.com/news/?format=feed', 'showDescription' => false)
    );

    function display($tpl = null, $safeparams = false)
    {
        $app = JFactory::getApplication();

        JInbound::registerHelper('path');
        JLoader::register('JInboundViewReports', JInboundHelperPath::admin('views/reports/view.html.php'));
        // get original data for layout and template
        $tmpl   = $app->input->get('tmpl');
        $layout = $app->input->get('layout');

        // get a reports view & load it's output
        $app->input->set('tmpl', 'component');
        $app->input->set('layout', 'default');
        $app->setUserState('list.limit', 10);
        $app->setUserState('list.start', 0);
        $reportView = new JInboundViewReports();

        $this->reports               = new stdClass;
        $this->reports->glance       = $reportView->loadTemplate(null, 'glance');
        $this->reports->script       = $reportView->loadTemplate('script', 'default');
        $this->reports->top_pages    = $reportView->loadTemplate('pages', 'top');
        $this->reports->recent_leads = $reportView->loadTemplate('leads', 'recent');

        // instead of loading the RSS initially, allow the urls to be loaded via ajax
        $this->feed = (object)$this->feeds['feed'];
        $this->news = (object)$this->feeds['news'];
        /*
        JInbound::registerLibrary('JInboundRSSView', 'views/rssview');
        $app->input->set('layout', 'rss');
        foreach ($this->feeds as $var => $feed)
        {
            // get RSS view and display its contents
            try
            {
                $rss = new JInboundRSSView();
                $rss->showDetails = array_key_exists('showDetails', $feed) ? $feed['showDetails'] : false;
                $rss->showDescription = array_key_exists('showDescription', $feed) ? $feed['showDescription'] : true;
                $rss->url = $feed['url'];
                $rss->getFeed($feed['url']);
                $this->$var = $rss->loadTemplate(null, 'rss');
            }
            catch (Exception $e)
            {
                $this->$var = $e->getMessage();
            }
        }
        */

        // reset template and layout data
        $app->input->set('tmpl', $tmpl);
        $app->input->set('layout', $layout);

        // apply plugin update info
        $this->updates = JDispatcher::getInstance()->trigger('onJinboundDashboardUpdate');

        return parent::display($tpl, $safeparams);
    }

    /**
     * used to add administrator toolbar
     */
    public function addToolBar()
    {
        parent::addToolBar();
        if (JFactory::getUser()->authorise('core.admin', JInbound::COM)) {
            JToolbarHelper::custom('reset', 'refresh.png', 'refresh_f2.png', 'COM_JINBOUND_RESET', false);
        }
    }
}
