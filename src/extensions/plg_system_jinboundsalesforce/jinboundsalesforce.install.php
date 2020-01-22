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

defined('JPATH_PLATFORM') or die;

class plgSystemJinboundsalesforceInstallerScript
{
    /**
     * @var string
     */
    protected $wsdlLegacy = '/wsdl';

    /**
     * @var string
     */
    protected $wsdl = '/library/wsdl';

    /**
     * @param string            $type
     * @param JInstallerAdapter $parent
     *
     * @return bool
     */
    public function preflight($type, $parent)
    {
        if ($type == 'update') {
            $path = $parent->getParent()->getPath('extension_root');

            if (is_dir($path . $this->wsdlLegacy)) {
                JFolder::move($path . $this->wsdlLegacy, $path . $this->wsdl);
            }
        }

        return true;
    }

    /**
     * @param string            $type
     * @param JInstallerAdapter $parent
     */
    public function postflight($type, $parent)
    {
        $path = $parent->getParent()->getPath('extension_root');

        if (!is_dir($path . $this->wsdl)) {
            JFolder::create($path . $this->wsdl);
        }
    }
}
