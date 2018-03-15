<?php
/**
 * @package             jInbound
 * @subpackage          plg_system_jinboundleadmap
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

if (!defined('JINP_LOADED')) {
    $path = JPATH_ADMINISTRATOR . '/components/com_jinbound/include.php';
    if (is_file($path)) {
        require_once $path;
    }
}
require_once dirname(__FILE__) . '/lib/geoip2.phar';

use GeoIp2\Database\Reader;

class PlgSystemJinboundleadmap extends JPlugin
{
    /**
     * Loads the language, if in admin
     */
    public function onAfterInitialise()
    {
        if (JFactory::getApplication()->isAdmin()) {
            // load language files
            $this->loadLanguage('com_jinbound', JPATH_ADMINISTRATOR);
            $this->loadLanguage('plg_system_jinboundleadmap.sys', JPATH_ROOT);
        }
    }

    /**
     * Rebuilds this plugin's menu item
     *
     * @throws Exception
     */
    public function onJinboundRebuildMenu()
    {
        $app   = JFactory::getApplication();
        $db    = JFactory::getDbo();
        $table = JTable::getInstance('menu');
        // load the parent menu item
        $parent_id = $db->setQuery($db->getQuery(true)
            ->select('id')->from('#__menu')->where('client_id = 1')
            ->where('title = ' . $db->quote('COM_JINBOUND'))
        )->loadResult();
        // if the parent is missing, stop unless uninstalling
        if (empty($parent_id)) {
            if (JDEBUG) {
                $app->enqueueMessage('[' . __METHOD__ . '] Parent menu is empty');
            }
            return;
        }
        // load the lead menu item
        $lead_id = $db->setQuery($db->getQuery(true)
            ->select('id')->from('#__menu')->where('client_id = 1')
            ->where('parent_id = ' . (int)$parent_id)
            ->where('title = ' . $db->quote('COM_JINBOUND_LEADS'))
        )->loadResult();
        // if the parent is missing, stop unless uninstalling
        if (empty($lead_id)) {
            if (JDEBUG) {
                $app->enqueueMessage('[' . __METHOD__ . '] Lead menu is empty');
            }
            return;
        }
        // load the existing plg_system_jinboundleadmap menu item
        $existing = $db->setQuery($db->getQuery(true)
            ->select('id')
            ->from('#__menu')
            ->where('parent_id = ' . (int)$parent_id)
            ->where("client_id = 1")
            ->where("link LIKE " . $db->quote('%jinboundleadmap%'))
        )->loadResult();
        // if there is an existing menu, remove it
        if ($existing) {
            if (JDEBUG) {
                $app->enqueueMessage('[' . __METHOD__ . '] Removing existing menu item ' . $existing);
            }
            if (!$table->delete((int)$existing)) {
                $app->enqueueMessage($table->getError(), 'error');
            }
            $table->rebuild();
        }
        if (JDEBUG) {
            $app->enqueueMessage('[' . __METHOD__ . '] Adding menu item');
        }
        $component            = $db->setQuery($db->getQuery(true)
            ->select('e.extension_id')
            ->from('#__extensions AS e')
            ->where('e.element = ' . $db->quote('com_jinbound'))
        )->loadResult();
        $data                 = array();
        $data['menutype']     = 'main';
        $data['client_id']    = 1;
        $data['title']        = 'plg_system_jinboundleadmap_view_title';
        $data['alias']        = 'plg_system_jinboundleadmap_view_title';
        $data['type']         = 'component';
        $data['published']    = 0;
        $data['parent_id']    = $parent_id;
        $data['component_id'] = $component;
        $data['img']          = 'class:component';
        $data['home']         = 0;
        $data['link']         = $this->getUrl('jinboundleadmapview', array(), false, false);
        try {
            $location = $table->setLocation($lead_id, 'after');
            if (false === $location) {
                throw new Exception(JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_ERROR_LOCATION_NOT_SET'));
            }
        } catch (Exception $ex) {
            $app->enqueueMessage(JText::sprintf('PLG_SYSTEM_JINBOUNDLEADMAP_ERROR_INSTALLING_MENU_ITEM',
                $ex->getMessage()), 'error');
            return;
        }
        if (!$table->bind($data) || !$table->check() || !$table->store()) {
            $app->enqueueMessage(JText::sprintf('PLG_SYSTEM_JINBOUNDLEADMAP_ERROR_INSTALLING_MENU_ITEM',
                $table->getError()), 'error');
        }
        $table->rebuild();
    }

    protected function getUrl($plugin = 'jinboundleadmapview', $params = array(), $sef = true, $escape = true)
    {
        $parts = array(
            'option' => 'com_ajax',
            'group'  => 'system',
            'format' => 'html',
            'plugin' => $plugin
        );
        $url   = 'index.php?' . http_build_query(array_merge($parts, $params));
        if ($sef) {
            $url = JRoute::_($url, $escape);
        }
        return $url;
    }

    /**
     * Adds menu item to submenu
     *
     * @param JViewLegacy $view
     */
    public function onJinboundBeforeMenuBar($view)
    {
        // init
        $input  = JFactory::getApplication()->input;
        $url    = $this->getUrl();
        $active = 'jinboundleadmapview' == $input->getCmd('plugin')
            && 'html' == $input->getCmd('format')
            && 'com_ajax' == $input->getCmd('option');

        $label = JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_VIEW_TITLE');

        $newSidebar = array();
        foreach ($view->sidebarItems as $sidebarItem) {
            $newSidebar[] = $sidebarItem;
            if (JText::_('COM_JINBOUND_LEADS') === $sidebarItem[0]) {
                $newSidebar[] = array($label, $url, $active);
            }
        }
        $view->sidebarItems = $newSidebar;
    }

    /**
     * Display lead map view
     *
     * @return mixed
     * @throws Exception
     */
    public function onAjaxJinboundleadmapview()
    {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        // only allow in admin
        if (!$app->isAdmin()) {
            throw new RuntimeException(JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_NOT_FOUND'), 404);
        }
        // get the map data
        $data = $this->getData();
        if ('json' == $app->input->get('format')) {
            return $data;
        }
        // add styles
        $doc->addStyleSheet(JUri::root() . 'media/jinboundleadmap/css/leadmap.css');
        // add paths for filter form
        JForm::addFormPath(dirname(__FILE__) . '/forms');
        JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_jinbound/models/fields');
        // render the view
        $view               = $this->getView();
        $view->data         = $data;
        $view->state        = new JRegistry();
        $view->download_url = $this->getUrl('jinboundleadmapdownload');
        try {
            $filters = $app->input->get('filter', array(), 'array');
            $view->state->set('filters', $filters);
            $view->filterForm = JInboundHelperForm::getForm('filter_leadmap',
                dirname(__FILE__) . '/forms/filter_leadmap.xml');
            if (!empty($filters)) {
                foreach ($filters as $filter => $value) {
                    $view->filterForm->setValue($filter, 'filter', $value);
                }
            }
        } catch (Exception $e) {
            $app->enqueueMessage($e->getMessage(), 'error');
        }
        $view->addMenuBar();
        $view->addToolBar();
        if (class_exists('JToolBarHelper')) {
            JToolBarHelper::title(JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_VIEW_TITLE'), 'jinbound-leadmap');
        }
        return $view->loadTemplate();
    }

    protected function getData()
    {
        $app             = JFactory::getApplication();
        $mmdb            = $this->getMaxmindDbFileName();
        $data            = new stdClass();
        $data->locations = array();
        $data->url       = $this->getUrl();
        if (!file_exists($mmdb)) {
            $mmdbpath = dirname($mmdb);
            $mmdbfile = strtolower(basename($mmdb));
            if (!file_exists($mmdb = $mmdbpath . '/' . $mmdbfile)) {
                return new Exception(JText::sprintf('PLG_SYSTEM_JINBOUNDLEADMAP_NO_MAXMIND_DB',
                    $this->getUrl('jinboundleadmapdownload')));
            }
        }
        $reader   = new Reader($mmdb);
        $patterns = array('/^127\.0\.0\.1$/', '/^10\./', '/^192\.168\./');
        $db       = JFactory::getDbo();
        $filter   = $app->input->get('filter', array(), 'array');
        $query    = $db->getQuery(true)
            ->select('t.ip, l.id AS lead_id')
            ->from('#__jinbound_tracks AS t')
            ->leftJoin('#__jinbound_contacts AS l ON l.cookie = t.cookie')
            ->where('t.ip <> ' . $db->q(''))
            ->group('l.id')
            ->group('t.ip');
        if (array_key_exists('isnew', $filter) && '' !== $filter['isnew']) {
            $query->where('l.id IS ' . ((int)$filter['isnew'] ? '' : 'NOT ') . 'NULL');
        }
        if (array_key_exists('campaign', $filter) && $filter['campaign']) {
            $query
                ->leftJoin('#__jinbound_contacts_campaigns AS cc ON cc.contact_id = l.id AND cc.enabled = 1 AND cc.campaign_id = ' . (int)$filter['campaign'])
                ->where('cc.campaign_id IS NOT NULL');
        }
        if (array_key_exists('page', $filter) && $filter['page']) {
            $query
                ->leftJoin('#__jinbound_conversions AS s ON l.id = s.contact_id AND s.page_id = ' . (int)$filter['page'])
                ->where('s.page_id IS NOT NULL');
        }
        if (array_key_exists('status', $filter) && $filter['status']) {
            // join in only the latest status
            $query->leftJoin('('
                . $db->getQuery(true)
                    ->select('s1.*')
                    ->from('#__jinbound_contacts_statuses AS s1')
                    ->leftJoin('#__jinbound_contacts_statuses AS s2 ON s1.contact_id = s2.contact_id AND s1.campaign_id = s2.campaign_id AND s1.created < s2.created')
                    ->where('s2.contact_id IS NULL')
                . ') AS cs ON cs.contact_id = l.id'
            )->where('cs.status_id = ' . (int)$filter['status']);
        }
        if (array_key_exists('priority', $filter) && $filter['priority']) {
            // join in only the latest priority
            $query->leftJoin('('
                . $db->getQuery(true)
                    ->select('p1.*')
                    ->from('#__jinbound_contacts_priorities AS p1')
                    ->leftJoin('#__jinbound_contacts_priorities AS p2 ON p1.contact_id = p2.contact_id AND p1.campaign_id = p2.campaign_id AND p1.created < p2.created')
                    ->where('p2.contact_id IS NULL')
                . ') AS cp ON cp.contact_id = l.id'
            )->where('cp.priority_id = ' . (int)$filter['priority']);
        }
        if (array_key_exists('search', $filter) && $filter['search']) {
            $search = $db->q('%' . $filter['search'] . '%');
            $wheres = array();
            foreach (array(
                         'first_name',
                         'last_name',
                         'company',
                         'website',
                         'email',
                         'address',
                         'suburb',
                         'state',
                         'country',
                         'postcode',
                         'telephone'
                     ) as $column) {
                $wheres[] = $db->qn($column) . ' LIKE ' . $search;
            }
            $query->where('(' . implode(' OR ', $wheres) . ')');
        }
        $records = $db->setQuery($query)->loadObjectList();
        if (JDEBUG) {
            $app->enqueueMessage('[' . __METHOD__ . '] IPs: ' . htmlspecialchars(print_r($records, 1), ENT_QUOTES,
                    'UTF-8'));
        }
        if (!empty($records)) {
            $checked = array();
            foreach ($records as $record) {
                if (empty($record->ip) || in_array($record->ip, $checked)) {
                    continue;
                }
                $checked[] = $record->ip;
                $check     = true;
                foreach ($patterns as $pattern) {
                    $check = $check && !preg_match($pattern, $record->ip);
                }
                if (!$check) {
                    if (JDEBUG) {
                        $app->enqueueMessage('[' . __METHOD__ . '] IP does not qualify: "' . $record->ip . '"');
                    }
                    continue;
                }
                $lead_ids = explode(',', $record->lead_id);
                $islead   = false;
                if (!empty($lead_ids)) {
                    foreach ($lead_ids as $lead_id) {
                        if (($islead = (int)$lead_id)) {
                            break;
                        }
                    }
                }
                try {
                    $record            = $reader->city($record->ip);
                    $data->locations[] = (object)array(
                        'latitude'  => $record->location->latitude
                    ,
                        'longitude' => $record->location->longitude
                    ,
                        'city'      => $record->city->name
                    ,
                        'lead'      => $islead
                    );
                } catch (Exception $ex) {
                    continue;
                }
            }
        }
        return $data;
    }

    protected function getMaxmindDbFileName()
    {
        return dirname(__FILE__) . '/lib/GeoLite2-City.mmdb';
    }

    /**
     * Get view class instance
     *
     * @return \JInboundPluginView
     */
    protected function getView()
    {
        JLoader::register('JInboundPluginView',
            JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/views/pluginview.php');
        $viewConfig = array('template_path' => dirname(__FILE__) . '/tmpl');
        $view       = new JInboundPluginView($viewConfig);
        return $view;
    }

    public function onAjaxJinboundleadmapdownload()
    {
        // try to kill the time limit
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);
        $app     = JFactory::getApplication();
        $url     = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz';
        $parts   = parse_url($url);
        $options = array(
            'http' => array(
                'method'          => 'GET'
            ,
                'request_fulluri' => true
            ,
                'header'          => array(
                    "Host: {$parts['host']}"
                ,
                    "Connection: keep-alive"
                ,
                    "Cache-Control: max-age=0"
                ,
                    "User-Agent: Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)"
                ,
                    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"
                ,
                    "Referer: {$parts['scheme']}://{$parts['host']}"
                ,
                    "Accept-Language: en"
                ,
                    "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7"
                )
            )
        );
        $context = stream_context_create($options);
        @set_time_limit(0);
        $response = file_get_contents($url, false, $context);
        $config   = new JConfig;
        $destfile = $this->getMaxmindDbFileName();
        $tmpfile  = rtrim($config->tmp_path, '/') . '/' . basename($destfile) . '.gz';
        file_put_contents($tmpfile, $response);
        @set_time_limit(0);
        jimport('joomla.filesystem.archive');
        JArchive::extract($tmpfile, dirname($destfile));
        unlink($tmpfile);
        $app->redirect($this->getUrl('jinboundleadmapview', array(), false));
    }
}
