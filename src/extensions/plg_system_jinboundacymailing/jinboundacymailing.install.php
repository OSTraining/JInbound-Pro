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

defined('JPATH_PLATFORM') or die;

class plgSystemJinboundacymailingInstallerScript
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

        $db->setQuery(
            $db->getQuery(true)
                ->select('extension_id')
                ->from('#__extensions')
                ->where(
                    array(
                        $db->quoteName('element') . ' = ' . $db->quote('jinboundacymailing'),
                        $db->quoteName('folder') . ' = ' . $db->quote('system'),
                        $db->quoteName('type') . ' = ' . $db->quote('plugin')
                    )
                )
        );

        try {
            $eid = $db->loadResult();
            if (!$eid) {
                throw new Exception('Could not enable plugin! ' . __METHOD__);
            }

        } catch (Exception $e) {
            if (defined('JDEBUG') && JDEBUG) {
                $app->enqueueMessage(htmlspecialchars($e->getMessage()), 'error');
            }
            return;
        }

        // force-enable this plugin
        $db->setQuery(
            $db->getQuery(true)
                ->update('#__extensions')
                ->set($db->quoteName('enabled') . ' = 1')
                ->where($db->quoteName('extension_id') . ' = ' . (int)$eid)
        );

        try {
            $db->execute();

        } catch (Exception $e) {
            if (defined('JDEBUG') && JDEBUG) {
                $app->enqueueMessage(htmlspecialchars($e->getMessage()), 'error');
            }

            return;
        }
    }
}
