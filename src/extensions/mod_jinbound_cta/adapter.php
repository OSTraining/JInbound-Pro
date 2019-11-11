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

abstract class ModJInboundCTAAdapter
{
    /**
     * Parameter prefix, one of '', 'c1_', 'c2_', 'c3_'
     *
     * @var string
     */
    public $pfx = '';
    /**
     * Module parameters
     *
     * @var JRegistry
     */
    protected $params;

    /**
     * Constructor
     *
     * @param JRegistry $params
     */
    function __construct(JRegistry $params)
    {
        $this->params = $params;
    }

    /**
     * Adapters must have a render method that sends back the data to render
     *
     * @return string
     */
    abstract public function render();
}
