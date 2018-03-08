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

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = ''; //$this->state->get('list.ordering');
$listDirn  = ''; //$this->state->get('list.direction');
$saveOrder = ''; //($listOrder == 'Page.id');
$trashed   = ''; //(-2 == $this->state->get('filter.published'));

if (JInbound::version()->isCompatible('3.0')) {
    JHtml::_('dropdown.init');
}


if (!empty($this->items)) :
    foreach ($this->items as $i => $item) :
        $this->_itemNum = $i;

        $canEdit    = $user->authorise('core.edit', JInbound::COM . '.stage.' . $item->id);
        $canEditOwn = $user->authorise('core.edit.own',
                JInbound::COM . '.stage.' . $item->id) && $item->created_by == $userId;
        $canChange  = $user->authorise('core.edit.state', JInbound::COM . '.stage.' . $item->id);
        ?>
        <tr class="row<?php echo $i % 2; ?>">
            <td class="hidden-phone">
                <?php echo $item->id; ?>
            </td>
            <td class="hidden-phone">
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
            </td>
            <td class="nowrap has-context">
                <div class="pull-left">
                    <?php if ($item->checked_out) : ?>
                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->author_name, $item->checked_out_time,
                            'stages.', $canCheckin); ?>
                    <?php endif; ?>
                    <?php if ($canEdit || $canEditOwn) : ?>
                        <a href="<?php echo JInboundHelperUrl::edit('stage', $item->id); ?>">
                            <?php echo $this->escape($item->user_name); ?>
                        </a>
                    <?php else : ?>
                        <?php echo $this->escape($item->user_name); ?>
                    <?php endif; ?>
                    <?php echo $item->name ?>
                </div>
                <?php $this->currentItem = $item;
                echo $this->loadTemplate('list_dropdown'); ?>
            </td>
            <td class="hidden-phone">
                &nbsp;<?php echo JHtml::_('jgrid.published', $item->published, $i, 'stages.', $canChange, 'cb'); ?>
            </td>
            <td class="hidden-phone">
                &nbsp;<?php echo $item->description; ?>
            </td>
        </tr>
    <?php endforeach;
endif;
