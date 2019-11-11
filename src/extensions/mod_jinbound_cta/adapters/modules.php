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
