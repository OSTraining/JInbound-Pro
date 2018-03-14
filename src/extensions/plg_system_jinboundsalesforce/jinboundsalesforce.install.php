<?php
/**
 * @package             jInbound
 * @subpackage          plg_system_jinboundsalesforce
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
