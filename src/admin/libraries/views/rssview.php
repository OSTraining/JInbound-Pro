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

JLoader::register('JInboundBaseView', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/views/baseview.php');

class JInboundRSSView extends JInboundBaseView
{
    public $url;
    public $feed;

    public $showTitle       = true;
    public $showDescription = true;
    public $showDetails     = true;
    public $feedLimit       = 5;
    public $wordLimit       = 140;

    function display($tpl = null, $safeparams = false)
    {
        $this->feed = $this->getFeed($this->url);
        return parent::display($tpl, $safeparams);
    }

    /**
     * Method to load the feed html
     *
     */
    public function getFeed($url, $cacheTime = 900)
    {
        if (empty($url)) {
            return false;
        }
        //  get RSS parsed object
        $options  = array('rssUrl' => $url);
        $cacheDir = JPATH_BASE . '/cache';
        if (is_writable($cacheDir)) {
            $options['cache_time'] = $cacheTime;
        }

        jimport('joomla.feed.factory');
        if (class_exists('JFeedFactory')) {
            $feed   = new JFeedFactory;
            $rssDoc = $feed->getFeed($options['rssUrl']);
        } else {
            if (method_exists('JFactory', 'getXMLParser')) {
                $rssDoc = JFactory::getXMLParser('RSS', $options);
            } else {
                $ct     = array_key_exists('cache_time', $options) ? $options['cache_time'] : $cacheTime;
                $rssDoc = JFactory::getFeedParser($options['rssUrl'], $ct);
            }
        }

        $this->feed     = $rssDoc;
        $this->feed_url = $options['rssUrl'];

        return $this->feed;
    }
}
