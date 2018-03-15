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

class JInboundViewStatuses extends JInboundListView
{
    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     */
    protected function getSortFields()
    {
        return array(
            'Status.name'        => JText::_('COM_JINBOUND_NAME'),
            'Status.published'   => JText::_('COM_JINBOUND_PUBLISHED'),
            'Status.default'     => JText::_('COM_JINBOUND_DEFAULT'),
            'Status.active'      => JText::_('COM_JINBOUND_ACTIVE'),
            'Status.final'       => JText::_('COM_JINBOUND_FINAL'),
            'Status.ordering'    => JText::_('JGRID_HEADING_ORDERING'),
            'Status.description' => JText::_('COM_JINBOUND_DESCRIPTION')
        );
    }
}
