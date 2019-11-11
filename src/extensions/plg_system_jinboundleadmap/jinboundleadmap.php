<?php
/**
 * @package   jInbound-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2015 Anything-Digital.com
 * @copyright 2016-2019 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of jInbound-Pro.
 *
 * jInbound-Pro is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * jInbound-Pro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with jInbound-Pro.  If not, see <http://www.gnu.org/licenses/>.
 */

use GeoIp2\Database\Reader;

defined('JPATH_PLATFORM') or die;

class PlgSystemJinboundleadmap extends JPlugin
{
    /**
     * @var string
     */
    protected $maxmindDBUrl = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz';

    /**
     * @var string
     */
    protected $maxmindDB = '/assets/GeoLite2-City.mmdb';

    /**
     * @var string
     */
    protected $maxmindDownloadUrl = null;

    /**
     * @var bool
     */
    protected static $enabled = null;

    /**
     * PlgSystemJinboundleadmap constructor.
     *
     * @param JEventDispatcher $subject
     * @param array            $config
     *
     * @throws Exception
     */
    public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);

        if (JFactory::getApplication()->isClient('administrator')) {
            // load language files
            $this->loadLanguage('com_jinbound', JPATH_ADMINISTRATOR);
            $this->loadLanguage('plg_system_jinboundleadmap.sys', JPATH_ROOT);
        }

        $this->maxmindDB          = __DIR__ . $this->maxmindDB;
        $this->maxmindDownloadUrl = $this->getUrl('jinboundleadmapdownload');
    }

    /**
     * @param string $client
     *
     * @return bool
     * @throws Exception
     */
    protected function isEnabled($client = '')
    {
        if ($client && !JFactory::getApplication()->isClient($client)) {
            return false;
        }

        if (static::$enabled === null) {
            if (!defined('JINB_LOADED')) {
                $path = JPATH_ADMINISTRATOR . '/components/com_jinbound/include.php';
                if (is_file($path)) {
                    require_once $path;
                }
            }

            $path = __DIR__ . '/vendor/autoload.php';
            if (is_file($path)) {
                require_once $path;

                static::$enabled = defined('JINB_LOADED');
            }
        }

        return static::$enabled;
    }

    /**
     * Rebuilds this plugin's menu item
     *
     * @return void
     * @throws Exception
     */
    public function onJinboundRebuildMenu()
    {
        if (!$this->isEnabled()) {
            return;
        }

        $app = JFactory::getApplication();
        $db  = JFactory::getDbo();

        $parentId = $db->setQuery(
            $db->getQuery(true)
                ->select('id')
                ->from('#__menu')
                ->where(
                    array(
                        'client_id = 1',
                        'title = ' . $db->quote('COM_JINBOUND')
                    )
                )
        )
            ->loadResult();

        if (empty($parentId)) {
            if (JDEBUG) {
                $app->enqueueMessage('[' . __METHOD__ . '] Parent menu is empty');
            }

            return;
        }

        // load the lead menu item
        $leadId = $db->setQuery(
            $db->getQuery(true)
                ->select('id')
                ->from('#__menu')
                ->where(
                    array(
                        'client_id = 1',
                        'parent_id = ' . (int)$parentId,
                        'title = ' . $db->quote('COM_JINBOUND_LEADS_MANAGER')
                    )
                )
        )
            ->loadResult();

        // if missing, stop unless uninstalling
        if (empty($leadId)) {
            if (JDEBUG) {
                $app->enqueueMessage('[' . __METHOD__ . '] Lead menu is empty');
            }

            return;
        }

        // load the existing plg_system_jinboundleadmap menu item
        $existing = $db->setQuery(
            $db->getQuery(true)
                ->select('id')
                ->from('#__menu')
                ->where(
                    array(
                        'parent_id = ' . (int)$parentId,
                        'client_id = 1',
                        'link LIKE ' . $db->quote('%jinboundleadmap%')
                    )
                )
        )
            ->loadResult();

        /** @var JTableMenu $table */
        $table = JTable::getInstance('menu');

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
        try {
            $table->setLocation($leadId, 'after');

            $component = $db->setQuery(
                $db->getQuery(true)
                    ->select('e.extension_id')
                    ->from('#__extensions AS e')
                    ->where('e.element = ' . $db->quote('com_jinbound'))
            )
                ->loadResult();

            $data = array(
                'menutype'     => 'main',
                'client_id'    => 1,
                'title'        => 'plg_system_jinboundleadmap_view_title',
                'alias'        => 'plg_system_jinboundleadmap_view_title',
                'type'         => 'component',
                'published'    => 0,
                'parent_id'    => $parentId,
                'component_id' => $component,
                'img'          => 'class:component',
                'home'         => 0,
                'link'         => $this->getUrl('jinboundleadmapview', array(), false, false)
            );

            if (!$table->bind($data)
                || !$table->check()
                || !$table->store()
                || !$table->rebuild()
            ) {
                throw new Exception($table->getError());
            }

        } catch (Exception $e) {
            $app->enqueueMessage(
                JText::sprintf('PLG_SYSTEM_JINBOUNDLEADMAP_ERROR_INSTALLING_MENU_ITEM', $e->getMessage()),
                'error'
            );

            return;
        }
    }

    /**
     * @param string $plugin
     * @param array  $params
     * @param bool   $sef
     * @param bool   $escape
     *
     * @return string
     */
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
     *
     * @return void
     * @throws Exception
     */
    public function onJinboundBeforeMenuBar($view)
    {
        if (!$this->isEnabled()) {
            return;
        }

        $app = JFactory::getApplication();

        if (property_exists($view, 'sidebarItems')) {
            $url = $this->getUrl();

            $active = 'jinboundleadmapview' == $app->input->getCmd('plugin')
                && 'html' == $app->input->getCmd('format')
                && 'com_ajax' == $app->input->getCmd('option');

            $label = JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_VIEW_TITLE');

            $newSidebar = array();
            foreach ($view->sidebarItems as $sidebarItem) {
                $newSidebar[] = $sidebarItem;
                if (JText::_('COM_JINBOUND_LEADS_MANAGER') === $sidebarItem[0]) {
                    $newSidebar[] = array($label, $url, $active);
                }
            }

            $view->sidebarItems = $newSidebar;

        } else {
            $app->enqueueMessage('No prop: ' . get_class($view));
        }
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
        if (!$this->isEnabled('administrator')) {
            throw new Exception(JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_NOT_FOUND'), '404');
        }

        try {
            $data = $this->getData();
            if ($app->input->getCmd('format') == 'json') {
                return $data;
            }

            JHtml::_('stylesheet', 'media/jinboundleadmap/css/leadmap.css');

            JForm::addFormPath(dirname(__FILE__) . '/forms');

            $view = $this->getView($data);
            try {
                $filters = $app->input->get('filter', array(), 'array');

                $view->setState('filters', $filters);
                $view->filterForm = JInboundHelperForm::getForm(
                    'filter_leadmap',
                    __DIR__ . '/forms/filter_leadmap.xml'
                );

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

            JToolBarHelper::title(JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_VIEW_TITLE'), 'jinbound-leadmap');

            return $view->loadTemplate();

        } catch (Exception $e) {
            $app->enqueueMessage($e->getFile() . ':' . $e->getLine() . '<br/>' . $e->getMessage(), 'error');

        } catch (Throwable $e) {
            $app->enqueueMessage($e->getFile() . ':' . $e->getLine() . '<br/>' . $e->getMessage(), 'error');
        }
    }

    /**
     * @return object
     * @throws Exception
     */
    protected function getData()
    {
        $app = JFactory::getApplication();

        if (!$this->isEnabled()) {
            $app->enqueueMessage('Problem loading lead map', 'error');
            $app->redirect('index.php');
        }

        $data = (object)array(
            'locations' => array(),
            'url'       => $this->getUrl()
        );

        if (!file_exists($this->maxmindDB)) {
            return new Exception(
                JText::sprintf(
                    'PLG_SYSTEM_JINBOUNDLEADMAP_MAXMIND_NO_DB',
                    $this->maxmindDownloadUrl,
                    $this->maxmindDBUrl,
                    str_replace(JPATH_ROOT, '', $this->maxmindDB)
                )
            );
        }

        $reader   = new Reader($this->maxmindDB);
        $patterns = array('/^127\.0\.0\.1$/', '/^10\./', '/^192\.168\./');
        $filter   = $app->input->get('filter', array(), 'array');

        $db    = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('t.ip, l.id AS lead_id')
            ->from('#__jinbound_tracks AS t')
            ->leftJoin('#__jinbound_contacts AS l ON l.cookie = t.cookie')
            ->where('t.ip <> ' . $db->q(''))
            ->group(
                array(
                    'l.id',
                    't.ip'
                )
            );

        if (array_key_exists('isnew', $filter) && $filter['isnew'] !== '') {
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

            $subQuery = $db->getQuery(true)
                ->select('s1.*')
                ->from('#__jinbound_contacts_statuses AS s1')
                ->leftJoin('#__jinbound_contacts_statuses AS s2 ON s1.contact_id = s2.contact_id AND s1.campaign_id = s2.campaign_id AND s1.created < s2.created')
                ->where('s2.contact_id IS NULL');
            $query
                ->leftJoin("({$subQuery}) AS cs ON cs.contact_id = l.id")
                ->where('cs.status_id = ' . (int)$filter['status']);
        }

        if (array_key_exists('priority', $filter) && $filter['priority']) {
            // join in only the latest priority
            $subQuery = $db->getQuery(true)
                ->select('p1.*')
                ->from('#__jinbound_contacts_priorities AS p1')
                ->leftJoin('#__jinbound_contacts_priorities AS p2 ON p1.contact_id = p2.contact_id AND p1.campaign_id = p2.campaign_id AND p1.created < p2.created')
                ->where('p2.contact_id IS NULL');
            $query
                ->leftJoin("({$subQuery}) AS cp ON cp.contact_id = l.id")
                ->where('cp.priority_id = ' . (int)$filter['priority']);
        }

        if (array_key_exists('search', $filter) && $filter['search']) {
            $search  = $db->q('%' . $filter['search'] . '%');
            $columns = array(
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
            );
            $wheres  = array();

            foreach ($columns as $column) {
                $wheres[] = $db->qn($column) . ' LIKE ' . $search;
            }
            $query->where(sprintf('(%s)', implode(' OR ', $wheres)));
        }

        $records = $db->setQuery($query)->loadObjectList();

        if (JDEBUG) {
            $app->enqueueMessage(
                sprintf(
                    '[%s] IPs: %s',
                    __METHOD__,
                    htmlspecialchars(print_r($records, 1), ENT_QUOTES, 'UTF-8')
                )
            );
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

                $leadIds = explode(',', $record->lead_id);
                $islead  = false;
                if (!empty($leadIds)) {
                    foreach ($leadIds as $leadId) {
                        if (($islead = (int)$leadId)) {
                            break;
                        }
                    }
                }

                try {
                    $location          = $reader->city($record->ip);
                    $data->locations[] = (object)array(
                        'latitude'  => $location->location->latitude,
                        'longitude' => $location->location->longitude,
                        'city'      => $location->city->name,
                        'lead'      => $islead
                    );

                } catch (Exception $ex) {
                    continue;
                }
            }
        }

        return $data;
    }

    /**
     * Get view class instance
     *
     * @param object $data
     *
     * @return \JInboundPluginView
     * @throws Exception
     */
    protected function getView($data)
    {
        $viewConfig = array(
            'template_path'      => __DIR__ . '/tmpl',
            'maxmindDBUrl'       => $this->maxmindDBUrl,
            'maxmindDB'          => $this->maxmindDB,
            'maxmindDownloadUrl' => $this->maxmindDownloadUrl,
            'data'               => $data
        );

        $view = new JInboundPluginView($viewConfig);

        return $view;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function onAjaxJinboundleadmapdownload()
    {
        $app = JFactory::getApplication();

        $tmpFolder = $app->get('tmp_path');
        $tmpfile   = sprintf('%s/%s.gz', rtrim($tmpFolder, '/'), basename($this->maxmindDB));

        copy($this->maxmindDBUrl, $tmpfile);

        $tmpfh  = gzopen($tmpfile, 'r');
        $destfh = fopen($this->maxmindDB, 'w');
        while ($buffer = gzread($tmpfh, 8192)) {
            fwrite($destfh, $buffer);
        }
        gzclose($tmpfh);
        fclose($destfh);

        unlink($tmpfile);

        $app->redirect($this->getUrl('jinboundleadmapview', array(), false));
    }
}
