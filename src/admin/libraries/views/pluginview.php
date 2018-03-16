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

class JInboundPluginView extends JInboundView
{
    /**
     * @var JForm
     */
    public $filterForm = null;

    /**
     * @var object
     */
    public $data = null;

    /**
     * @var string
     */
    public $download_url = null;

    /**
     * @param string $property
     * @param mixed  $default
     *
     * @return JObject|mixed
     */
    public function getState($property = null, $default = null)
    {
        if (!$this->state) {
            $this->state = new JObject();
        }

        if ($property) {
            return $this->state->get($property, $default);
        }

        return $this->state;
    }

    /**
     * @param string $property
     * @param mixed  $value
     *
     * @return mixed
     */
    public function setState($property, $value)
    {
        return $this->getState()->set($property, $value);
    }
}
