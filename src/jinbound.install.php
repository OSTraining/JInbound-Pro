<?php
/**
 * @package             JInbound
 * @subpackage          pkg_jinbound
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

class pkg_JInboundInstallerScript
{
    public function postflight($type, $parent)
    {
        if ('uninstall' == $type) {
            return;
        }
        $debug = defined('JDEBUG') && JDEBUG;
        $app   = JFactory::getApplication();
        $db    = JFactory::getDbo();
        // bugfixes
        $this->runBugFixes();
        // enable the plugins
        $db->setQuery('UPDATE `#__extensions` SET `enabled`=1 WHERE `element`="jinbound" AND `folder` IN ("system", "content", "user") AND `type`="plugin"');
        try {
            $db->query();
            if ($debug) {
                $app->enqueueMessage('Enabled plugins...');
            }
        } catch (Exception $e) {
            $app->enqueueMessage($e->getMessage(), 'error');
        }
        // enable the social bookmarks module
        // search the module table to see if any modules are already assigned to the module position
        // if not, find our default module and enable it
        $db->setQuery('SELECT id FROM #__modules WHERE position = "jinbound_social" AND published = 1');
        try {
            $ids = $db->loadColumn();
            if ($debug) {
                $app->enqueueMessage('Modules found: ' . implode(', ', $ids));
            }
        } catch (Exception $e) {
            $ids = false;
            $app->enqueueMessage($e->getMessage(), 'error');
        }

        // none published - update the default if possible
        if (empty($ids)) {
            // get the default module ids
            $db->setQuery('SELECT id FROM #__modules WHERE module = "mod_jinbound_social_bookmark"');
            try {
                $mods = $db->loadColumn();
                // handle mods (TODO check if there's only one? limit?)
                if (!empty($mods)) {
                    // update all the publication columns for the module
                    $db->setQuery('UPDATE #__modules SET position = "jinbound_social", publish_up = "0000-00-00 00:00:00", publish_down = "0000-00-00 00:00:00", access = 1, published = 1 WHERE module = "mod_jinbound_social_bookmark"');
                    $db->query();
                    // get rid of the page associations
                    $db->setQuery('DELETE FROM #__modules_menu WHERE moduleid IN(' . implode(',', $mods) . ')');
                    $db->query();
                    // get ready to insert new page associations
                    $insert = $db->getQuery(true)->insert('#__modules_menu')->columns(array('moduleid', 'menuid'));
                    foreach ($mods as $mod) {
                        // add association
                        $insert->values($mod . ',0');
                        // go ahead and fix the params in this loop
                        $this->_saveModuleDefaults($mod, $parent);
                    }
                    $db->setQuery($insert);
                    $db->query();
                }
            } catch (Exception $e) {
                $app->enqueueMessage($e->getMessage(), 'error');
            }
        }

    }

    protected function runBugFixes()
    {
        $this->fixAssets();
    }

    protected function fixAssets()
    {
        $parent = JTable::getInstance('Asset');
        $parent->loadByName('com_jinbound');
        $db = JFactory::getDbo();
        $db->setQuery($db->getQuery(true)
            ->delete('#__assets')
            ->where('name LIKE "%__jinbound_leads.%"')
        )->query();
        $assets = $db->setQuery($db->getQuery(true)
            ->select('id,name')->from('#__assets')
            ->where('name LIKE "%_jinbound%"')
            ->where('parent_id <> ' . (int)$parent->id)
        )->loadObjectList();
        if (empty($assets)) {
            return;
        }
        $map = array(
            '#__jinbound_campaigns'     => 'com_jinbound.campaign'
        ,
            '#__jinbound_contacts'      => 'com_jinbound.contact'
        ,
            '#__jinbound_conversions'   => 'com_jinbound.conversion'
        ,
            '#__jinbound_emails'        => 'com_jinbound.email'
        ,
            '#__jinbound_notes'         => 'com_jinbound.note'
        ,
            '#__jinbound_pages'         => 'com_jinbound.page'
        ,
            '#__jinbound_priorities'    => 'com_jinbound.priority'
        ,
            '#__jinbound_stages'        => 'com_jinbound.stage'
        ,
            '#__jinbound_lead_statuses' => 'com_jinbound.status'
        );
        foreach ($assets as $asset) {
            $table = JTable::getInstance('Asset');
            $table->load($asset->id);
            $table->name  = str_replace(array_keys($map), array_values($map), $table->name);
            $table->title = $table->name;
            $table->check() && $table->store() && $table->moveByReference($parent->id, 'last-child');
        }
    }

    private function _saveModuleDefaults($modid, &$parent)
    {
        jimport('joomla.filesystem.file');
        jimport('joomla.form.form');

        $configfile = JPATH_ROOT . '/modules/mod_jinbound_social_buttons/mod_jinbound_social_buttons.xml';

        if (!JFile::exists($configfile)) {
            return;
        }

        $xml       = JFile::read($configfile);
        $form      = JForm::getInstance('installer', $xml, array(), false, '/config');
        $params    = array();
        $fieldsets = $form->getFieldsets();

        if (!empty($fieldsets)) {
            foreach ($fieldsets as $fieldset) {
                $fields = $form->getFieldset($fieldset->name);
                if (!empty($fields)) {
                    foreach ($fields as $name => $field) {
                        $params[$field->__get('name')] = $field->__get('value');
                    }
                }
            }
        }

        $db = JFactory::getDbo();
        $db->setQuery($db->getQuery(true)
            ->update('#__modules')
            ->set('params = ' . $db->quote(json_encode($params)))
            ->where('id = ' . (int)$modid)
        );
        try {
            $db->query();
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }

    }
}
