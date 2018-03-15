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

class JInboundControllerPriority extends JInboundFormController
{
    /**
     * @param string $key
     * @param string $urlVar
     *
     * @return bool
     * @throws Exception
     */
    public function edit($key = 'id', $urlVar = 'id')
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_jinbound.priority')) {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
        }
        return parent::edit($key, $urlVar);
    }
}
