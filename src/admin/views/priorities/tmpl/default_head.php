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

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$saveOrder = ($listOrder == 'Priority.ordering');
?>
<tr>
    <th width="1%" class="hidden-phone">
        <?php echo JHtml::_('searchtools.sort', '', 'Priority.ordering', $listDirn, $listOrder,
            null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
    </th>
    <th width="1%" class="hidden-phone">
        <?php echo JHtml::_('grid.checkall'); ?>
    </th>
    <th width="1%" class="nowrap center">
        <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'Priority.published', $listDirn,
            $listOrder); ?>
    </th>
    <th class="title">
        <?php echo JHtml::_('searchtools.sort', 'COM_JINBOUND_NAME', 'Priority.name', $listDirn,
            $listOrder); ?>
    </th>
    <th width="25%" class="hidden-phone">
        <?php echo JHtml::_('searchtools.sort', 'COM_JINBOUND_DESCRIPTION',
            'Priority.description', $listDirn, $listOrder); ?>
    </th>
    <th width="1%" class="nowrap hidden-phone">
        <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'Priority.id', $listDirn,
            $listOrder); ?>
    </th>
</tr>
