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

        if (JDEBUG) {
            $app->enqueueMessage(sprintf('[%s] Type "%s"', __METHOD__, $type));
        }

        try {
            $this->checkMenu();

        } catch (Exception $e) {
            $app->enqueueMessage(
                JText::sprintf('PLG_SYSTEM_JINBOUNDLEADMAP_ERROR_INSTALLING_MENU_ITEM', $e->getMessage()),
                'error'
            );

        } catch (Throwable $e) {
            $app->enqueueMessage(
                JText::sprintf('PLG_SYSTEM_JINBOUNDLEADMAP_ERROR_INSTALLING_MENU_ITEM', $e->getMessage()),
                'error'
            );
        }
    }

    public function uninstall()
    {
        $this->removeMenu();
    }

    /**
     * Check that the menu item for this plugin is installed
     *
     * @return void
     * @throws Exception
     */
    protected function checkMenu()
    {
        $app = JFactory::getApplication();

        if (JDEBUG) {
            $app->enqueueMessage('[' . __METHOD__ . '] Adding menu item');
        }

        $jinbound = JComponentHelper::getComponent('com_jinbound');
        if ($jinbound->id) {
            /** @var JTableMenu $parent */
            $parent = JTable::getInstance('Menu');
            $parent->load(
                array(
                    'component_id' => $jinbound->id,
                    'level'        => 1,
                    'client_id'    => 1
                )
            );

            /** @var JTableMenu $leads */
            $leads = JTable::getInstance('Menu');
            $leads->load(
                array(
                    'component_id' => $jinbound->id,
                    'title'        => 'COM_JINBOUND_LEADS_MANAGER',
                    'level'        => 2,
                    'client_id'    => 1
                )
            );

            /** @var JTableMenu $leadmap */
            $leadmap = JTable::getInstance('Menu');
            $leadmap->load(
                array(
                    'component_id' => $jinbound->id,
                    'title'        => 'plg_system_jinboundleadmap_view_title',
                    'level'        => 2,
                    'client_id'    => 1
                )
            );

            if ($parent->id && $leads->id && !$leadmap->id) {
                $newMenu = array(
                    'parent_id'    => $parent->id,
                    'level'        => 2,
                    'menutype'     => $leads->menutype,
                    'title'        => 'plg_system_jinboundleadmap_view_title',
                    'link'         => 'index.php?option=com_ajax&group=system&plugin=jinboundleadmapview&format=html',
                    'type'         => 'component',
                    'published'    => 1,
                    'component_id' => $jinbound->id,
                    'access'       => $leads->access,
                    'client_id'    => $parent->client_id
                );

                if ($leadmap->bind($newMenu) && $leadmap->check()) {
                    $leadmap->setLocation($leads->id, 'after');
                    if (!$leadmap->store()) {
                        throw new Exception($leadmap->getError());
                    }

                } else {
                    throw new Exception($leadmap->getError());
                }
            }
        }
    }

    /**
     * Removes the admin menu item we originally installed here
     */
    protected function removeMenu()
    {
        $jinbound = JComponentHelper::getComponent('com_jinbound');

        if ($jinbound->id) {
            /** @var JTableMenu $leadmap */
            $leadmap = JTable::getInstance('Menu');
            $leadmap->load(
                array(
                    'component_id' => $jinbound->id,
                    'title'        => 'plg_system_jinboundleadmap_view_title',
                    'level'        => 2,
                    'client_id'    => 1
                )
            );

            if ($leadmap->id) {
                $leadmap->delete();
            }
        }
    }
}
