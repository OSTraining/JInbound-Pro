<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 **********************************************
 * JInbound
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

$listOrder = ''; //$this->state->get('list.ordering');
$listDirn  = ''; //$this->state->get('list.direction');
$saveOrder = ''; //($listOrder == 'Page.id');
?>
<tr>
    <th width="1%" class="nowrap hidden-phone">
        <?php echo JText::_('COM_JINBOUND_ID'); ?>
    </th>
    <th width="1%" class="nowrap hidden-phone">
        <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
    </th>
    <th>
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_CATEGORY_NAME', 'Status.name', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_PUBLISHED', 'Status.status', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_CATEGORY_DESCRIPTION', 'Status.description', $listDirn,
            $listOrder); ?>
    </th>
</tr>
