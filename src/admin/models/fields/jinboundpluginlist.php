<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
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

if (!defined('JINB_LOADED')) {
    $path = JPATH_ADMINISTRATOR . '/components/com_jinbound/include.php';
    if (is_file($path)) {
        require_once $path;
    }
}

JFormHelper::loadFieldClass('list');

class JFormFieldJInboundPluginList extends JFormFieldList
{
    public $type = 'Jinboundpluginlist';

    protected function getOptions()
    {
        $dispatcher = JDispatcher::getInstance();
        // get options
        $options = parent::getOptions();
        // trigger plugins
        $dispatcher->trigger('onJInboundPluginList', array($this->type, &$options));
        // all done
        return $options;
    }
}
