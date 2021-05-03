<?php
/**
 * @package   jInbound-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2015 Anything-Digital.com
 * @copyright 2016-2021 Joomlashack.com. All rights reserved
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

// lighten the load by returning an empty module if this user has already seen it
if (filter_input(INPUT_COOKIE, 'mod_' . $module->id)) {
    return;
}

// get helper class
require_once dirname(__FILE__) . '/helper.php';

modJinboundPopupHelper::addHtmlAssets();

// initialise
$form      = modJinboundPopupHelper::getForm($module, $params);
$data      = modJinboundPopupHelper::getFormData($module, $params);
$sfx       = $params->get('moduleclass_sfx', '');
$btn       = $params->get('submit_text', 'JSUBMIT');
$introtext = $params->get('introtext', '');
$stripped  = strip_tags($introtext);
$showintro = !empty($stripped);

if (false === $form || false === $data) {
    return false;
}

// coerce empty button text
if (empty($btn)) {
    $btn = 'JSUBMIT';
}

// create data to store in the session in order to save form
$session_name = 'mod_jinbound_popup.form.' . $module->id;
JFactory::getSession()->set($session_name, $data);
$form_url = JInboundHelperUrl::toFull(JInboundHelperUrl::task('lead.save', true, array(
    'token' => $session_name
)));

// render module
require JModuleHelper::getLayoutPath('mod_jinbound_popup', $params->get('layout', 'default'));
