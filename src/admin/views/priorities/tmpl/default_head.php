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
    <th width="1%" class="nowrap hidden-phone">
        <?php echo JText::_('COM_JINBOUND_ID'); ?>
    </th>
    <th width="1%" class="nowrap hidden-phone">
        <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
    </th>
    <th>
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_NAME', 'Priority.name', $listDirn, $listOrder); ?>
    </th>
    <th width="1%">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_PUBLISHED', 'Priority.published', $listDirn,
            $listOrder); ?>
    </th>
    <th width="12%">
        <?php echo JHtml::_($this->sortFunction, 'JGRID_HEADING_ORDERING', 'Priority.ordering', $listDirn,
            $listOrder); ?>
        <?php if ($saveOrder) : ?>
            <?php echo JHtml::_('grid.order', $this->items, 'filesave.png', 'priorities.saveorder'); ?>
        <?php endif; ?>
    </th>
    <th>
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_DESCRIPTION', 'Priority.description', $listDirn,
            $listOrder); ?>
    </th>
</tr>
