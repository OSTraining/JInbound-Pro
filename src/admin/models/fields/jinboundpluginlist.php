<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
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

jimport('joomla.filesystem.folder');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

JFormHelper::loadFieldClass('list');
JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('path');

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
