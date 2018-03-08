<?php
/**
 * @package             JInbound
 * @subpackage          mod_jinbound_cta
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

defined('_JEXEC') or die;

// check that jinbound is installed
$jinbound_base = JPATH_ADMINISTRATOR . '/components/com_jinbound';
if (!is_dir($jinbound_base)) {
    return false;
}

// include additional classes
require_once dirname(__FILE__) . '/adapter.php';
require_once dirname(__FILE__) . '/helper.php';

// this module requires the jinbound system plugin
if (!class_exists('plgSystemJInbound')) {
    return false;
}

// since this module renders other modules, don't let it render itself
if (ModJInboundCTAHelper::$running) {
    return false;
}
ModJInboundCTAHelper::$running = true;

$sfx = JFilterInput::getInstance()->clean($params->get('moduleclass_sfx', ''));

// load required classes
JLoader::register('JInbound', "$jinbound_base/libraries/jinbound.php");

// render module
require JModuleHelper::getLayoutPath('mod_jinbound_cta', $params->get('layout', 'default'));

// finished rendering
ModJInboundCTAHelper::$running = false;
