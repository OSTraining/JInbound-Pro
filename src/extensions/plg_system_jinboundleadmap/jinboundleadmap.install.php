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

class plgSystemJinboundleadmapInstallerScript
{
    public function postflight($type, $parent)
    {
        $app          = JFactory::getApplication();
        $db           = JFactory::getDbo();
        $table        = JTable::getInstance('menu');
        $uninstalling = ('uninstall' == $type);
        if (JDEBUG) {
            $app->enqueueMessage('[' . __METHOD__ . '] Type "' . $type . '"');
        }
        if (!$uninstalling) {
            $this->enable();
        }
        // load the parent menu item
        $parent_id = $db->setQuery($db->getQuery(true)
            ->select('m.id')
            ->from('#__menu AS m')
            ->leftJoin('#__extensions AS e ON m.component_id = e.extension_id')
            ->where('m.parent_id = 1')
            ->where("m.client_id = 1")
            ->where('e.element = ' . $db->quote('com_jinbound'))
        )->loadResult();
        // if the parent is missing, stop unless uninstalling
        if (empty($parent_id)) {
            if (JDEBUG) {
                $app->enqueueMessage('[' . __METHOD__ . '] Parent menu is empty');
            }
            if (!$uninstalling) {
                return;
            }
        }
        // load the existing plg_system_jinboundleadmap menu item
        $existing = $db->setQuery($db->getQuery(true)
            ->select('m.id')
            ->from('#__menu AS m')
            ->where('m.parent_id = ' . (int)$parent_id)
            ->where("m.client_id = 1")
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
        if ($uninstalling) {
            return;
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
        $data['link']         = 'index.php?option=com_'
            . (version_compare(JVERSION, '3.0.0', '>=') ? '' : 'jinbound&task=')
            . 'ajax&group=system&plugin=jinboundleadmapview&format=html';
        try {
            // find the "leads" link in the existing menu
            $lead_id  = $db->setQuery($db->getQuery(true)
                ->select('id')->from('#__menu')->where('client_id = 1')
                ->where('parent_id = ' . (int)$parent_id)
                ->where('title = ' . $db->quote('COM_JINBOUND_LEADS'))
            )->loadResult();
            $location = false;
            if ($lead_id) {
                $location = $table->setLocation($lead_id, 'after');
            }
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

    protected function enable()
    {
        $app = JFactory::getApplication();
        $db  = JFactory::getDbo();

        // find this plugin in the database, if possible...
        $db->setQuery($db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where($db->quoteName('element') . ' = ' . $db->quote('jinboundleadmap'))
            ->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
        );

        try {
            $eid = $db->loadResult();
            if (!$eid) {
                throw new Exception('Could not enable plugin! ' . __METHOD__);
            }
        } catch (Exception $e) {
            if (defined('JDEBUG') && JDEBUG) {
                $app->enqueueMessage(htmlspecialchars($e->getMessage()));
            }
            return;
        }

        // force-enable this plugin
        $db->setQuery($db->getQuery(true)
            ->update('#__extensions')
            ->set($db->quoteName('enabled') . ' = 1')
            ->where($db->quoteName('extension_id') . ' = ' . (int)$eid)
        );

        try {
            $db->query();
        } catch (Exception $e) {
            if (defined('JDEBUG') && JDEBUG) {
                $app->enqueueMessage(htmlspecialchars($e->getMessage()));
            }
            return;
        }

    }
}
