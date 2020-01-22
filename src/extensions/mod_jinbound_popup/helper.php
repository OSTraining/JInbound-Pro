<?php
/**
 * @package   jInbound-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2015 Anything-Digital.com
 * @copyright 2016-2020 Joomlashack.com. All rights reserved
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

defined('_JEXEC') or die;

if (!defined('JINB_LOADED')) {
    $path = JPATH_ADMINISTRATOR . '/components/com_jinbound/include.php';
    if (is_file($path)) {
        require_once $path;
    }
}

abstract class modJinboundPopupHelper
{
    static $assets_set = false;

    public static function getFormData(&$module, &$params)
    {
        // initialise
        $campaign_id = (int)$params->get('campaignid', 0);
        $form_id     = (int)$params->get('formid', 0);
        if (empty($form_id) || empty($campaign_id)) {
            return false;
        }
        return (object)array(
            'campaign_id'         => $campaign_id,
            'form_id'             => $form_id,
            'page_name'           => preg_replace('/^mod_/', '', $module->module),
            'notification_email'  => $params->get('notification_email', ''),
            'after_submit_sendto' => $params->get('after_submit_sendto', 'message'),
            'menu_item'           => $params->get('menu_item', ''),
            'send_to_url'         => $params->get('send_to_url', ''),
            'sendto_message'      => $params->get('sendto_message', ''),
            'return_url'          => $params->get('return_url', JUri::root(false))
        );
    }

    public static function getForm(&$module, &$params)
    {
        return JInboundHelperForm::getJinboundForm((int)$params->get('formid', 0),
            array('control' => 'mod_jinbound_popup_' . $module->id));
    }

    public static function addHtmlAssets()
    {
        if (!static::$assets_set) {
            $document = JFactory::getDocument();
            $script   = 'popup.js';
            if (method_exists($document, 'addScript')) {
                $document->addScript(JUri::root() . 'media/mod_jinbound_popup/js/' . $script);
            }
            static::$assets_set = true;
        }
    }

    public static function showForm()
    {
        $show = false;
        if (class_exists('plgSystemJinbound')) {
            $cookie = plgSystemJInboundHelper::getCookieUser();

        }
        return $show;
    }
}
