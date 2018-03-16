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
    /**
     * @param string            $type
     * @param JInstallerAdapter $parent
     *
     * @return void
     * @throws Exception
     */
    public function postflight($type, $parent)
    {
        $app = JFactory::getApplication();
        $db  = JFactory::getDbo();

        if (JDEBUG) {
            $app->enqueueMessage(sprintf('[%s] Type "%s"', __METHOD__, $type));
        }

        // load the parent menu item
        $parentId = $db->setQuery(
            $db->getQuery(true)
                ->select('m.id')
                ->from('#__menu AS m')
                ->leftJoin('#__extensions AS e ON m.component_id = e.extension_id')
                ->where(
                    array(
                        'm.parent_id = 1',
                        'm.client_id = 1',
                        'e.element = ' . $db->quote('com_jinbound')
                    )
                )
        )
            ->loadResult();

        if (!$parentId) {
            return;
        }

        $existing = $db->setQuery(
            $db->getQuery(true)
                ->select('m.id')
                ->from('#__menu AS m')
                ->where(
                    array(
                        'm.parent_id = ' . (int)$parentId,
                        'm.client_id = 1',
                        'link LIKE ' . $db->quote('%jinboundleadmap%')
                    )
                )
        )
            ->loadResult();

        if ($existing) {
            if (JDEBUG) {
                $app->enqueueMessage('[' . __METHOD__ . '] Removing existing menu item ' . $existing);
            }

            /** @var JTableMenu $table */
            $table = JTable::getInstance('menu');
            if (!$table->delete((int)$existing)) {
                $app->enqueueMessage($table->getError(), 'error');
            }

            $table->rebuild();
        }

        if (JDEBUG) {
            $app->enqueueMessage('[' . __METHOD__ . '] Adding menu item');
        }
        $component = $db->setQuery(
            $db->getQuery(true)
                ->select('e.extension_id')
                ->from('#__extensions AS e')
                ->where('e.element = ' . $db->quote('com_jinbound'))
        )
            ->loadResult();
        $data      = array(
            'menutype'     => 'main',
            'client_id'    => 1,
            'title'        => 'plg_system_jinboundleadmap_view_title',
            'alias'        => 'plg_system_jinboundleadmap_view_title',
            'type'         => 'component',
            'published'    => 0,
            'parent_id'    => $parentId,
            'component_id' => $component,
            'home'         => 0,
            'link'         => 'index.php?option=com_ajax&group=system&plugin=jinboundleadmapview&format=html'
        );

        try {
            // find the "leads" link in the existing menu
            $leadId = $db->setQuery(
                $db->getQuery(true)
                    ->select('id')
                    ->from('#__menu')
                    ->where(
                        array(
                            'client_id = 1',
                            'parent_id = ' . (int)$parentId,
                            'title = ' . $db->quote('COM_JINBOUND_LEADS')
                        )
                    )
            )
                ->loadResult();

            if ($leadId) {
                $table->setLocation($leadId, 'after');
            }

            if (!$table->bind($data) || !$table->check() || !$table->store()) {
                throw new Exception(
                    JText::sprintf('PLG_SYSTEM_JINBOUNDLEADMAP_ERROR_INSTALLING_MENU_ITEM', $table->getError()),
                    500
                );
            }

            $table->rebuild();

        } catch (Exception $e) {
            $app->enqueueMessage(
                JText::sprintf('PLG_SYSTEM_JINBOUNDLEADMAP_ERROR_INSTALLING_MENU_ITEM', $e->getMessage()),
                'error'
            );
            return;
        }
    }
}
