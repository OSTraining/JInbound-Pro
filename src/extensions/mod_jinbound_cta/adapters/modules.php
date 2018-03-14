<?php
/**
 * @package             jInbound
 * @subpackage          mod_jinbound_cta
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

defined('_JEXEC') or die;

class ModJInboundCTAModulesAdapter extends ModJInboundCTAAdapter
{
    /**
     * Renders a module position
     *
     * @return string
     */
    public function render()
    {
        $position = $this->params->get($this->pfx . 'mode_modules');
        $renderer = JFactory::getDocument()->loadRenderer('module');
        $modules  = JModuleHelper::getModules($position);
        $params   = array('style' => 'none');
        foreach ($modules as $module) {
            echo $renderer->render($module, $params);
        }
    }
}
