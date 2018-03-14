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

?>
<tr>
    <th width="1%" class="nowrap hidden-phone">
        <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
    </th>
    <th width="1%" class="nowrap hidden-phone">
        <?php echo JText::_('COM_JINBOUND_ID'); ?>
    </th>
    <th style="min-width:24px;">
        &nbsp;
    </th>
    <th>
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_LANDINGPAGE_NAME', 'Page.name', $listDirn,
            $listOrder); ?>
    </th>
    <th class="hidden-phone">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_PUBLISHED', 'Page.published', $listDirn, $listOrder); ?>
    </th>
    <th class="hidden-phone">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_CAMPAIGN', 'campaign_name', $listDirn, $listOrder); ?>
    </th>
    <th class="hidden-phone">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_CATEGORY', 'Page.category', $listDirn, $listOrder); ?>
    </th>
    <th class="hidden-phone">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_LAYOUT', 'Page.layout', $listDirn, $listOrder); ?>
    </th>
    <th class="hidden-phone">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_VIEWS', 'Page.hits', $listDirn, $listOrder); ?>
    </th>
    <th class="hidden-phone">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_SUBMISSIONS', 'submissions', $listDirn, $listOrder); ?>
    </th>
    <th class="hidden-phone">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_LEADS', 'contact_submissions', $listDirn, $listOrder); ?>
    </th>
    <th class="hidden-phone">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_CONVERSIONS', 'conversions', $listDirn, $listOrder); ?>
    </th>
</tr>
