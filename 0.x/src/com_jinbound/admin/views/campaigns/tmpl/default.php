<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound

 **********************************************
 jInbound
 Copyright (c) 2012 Anything-Digital.com
 **********************************************
 jInbound is some kind of marketing thingy

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This header must not be removed. Additional contributions/changes
 may be added to this header as long as no information is deleted.
 **********************************************
 Get the latest version of JInbound at:
 http://anything-digital.com/
 **********************************************

 */

defined('JPATH_PLATFORM') or die;
$user      = JFactory::getUser();

?>

<div class="container-fluid" id="jinbound_component">

	<h2>Lead Nurturing</h2>
		<div class="row-fluid">
    		<div class="span12 m" style="height:50px;">
				<div style="position:relative; top:50%; text-align:center;">
					Random Advice text
				</div>
			</div>
		</div>

	<h3>Create A New Email</h3>

	<?php
		$buttons = array();

		$buttons[] = JHtml::link('index.php?option=com_jinbound&view=campaign', '<div class="layout_select_button">100px x 160px \'Layout A\' Image</div>');
		$buttons[] = JHtml::link('index.php?option=com_jinbound&view=campaign', '<div class="layout_select_button">100px x 160px \'Layout B\' Image</div>');
		$buttons[] = JHtml::link('index.php?option=com_jinbound&view=campaign', '<div class="layout_select_button">100px x 160px \'Layout C\' Image</div>');
		$buttons[] = JHtml::link('index.php?option=com_jinbound&view=campaign', '<div class="layout_select_button">100px x 160px \'Layout D\' Image</div>');
		$buttons[] = JHtml::link('index.php?option=com_jinbound&view=campaign', '<div class="layout_select_button">100px x 160px \'Custom\' Image</div>');


	?>

<div class="jinbound_listbuttons">
<?php foreach ($buttons as $button) : ?>
	<div class="jinbound_listbutton">
		<?php echo $button; ?>
	</div>
	<?php endforeach; ?>
</div>


	<br />

	<div class="m">

			<fieldset id="filter-bar">
		<div class="filter-search fltlft btn-group pull-left">
			<label class="filter-search-lbl element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="" title="<?php echo JText::_('COM_JINBOUND_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_JINBOUND_FILTER_SEARCH_DESC'); ?>" />


		<?php if (!$floatButtons) : ?>
			<button type="submit" class="btn">Go</button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">Reset</button>
		<?php endif; ?>

		</div>

		<?php if ($floatButtons) : ?>
		<div class="btn-group pull-left hidden-phone">
			<button type="submit" class="btn tip hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
			<button type="button" class="btn tip hasTooltip" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
		</div>
		<?php endif; ?>

			<select class="listbox  " title="Campaign Name">
				<option>Campaign Name</option>
			</select>

			<select class="listbox  " title="Active">
				<option>Active</option>
			</select>

		</fieldset>
		<div class="clr"> </div>

<form action="<?php echo JURI::base() . 'index.php?option=com_jinbound&view=campaigns'; ?>" method="post" name="adminForm" id="adminForm">

<?php

$floatButtons = JInbound::version()->isCompatible('3.0');

?>


		<table>
			<tr>
				<th width="1%" class="nowrap hidden-phone">
					<?php echo JText::_('COM_JINBOUND_ID'); ?>
				</th>
				<th width="1%" class="nowrap hidden-phone">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_CAMPAIGN_NAME', 'Campaign.Name', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_EMAIL_NAME', 'Campaign.EmailName', $listDirn, $listOrder); ?>
				</th>
						<th>
					<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_CAMPAIGN_ACTIVE', 'Campaign.Active', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_CAMPAIGN_SCHEDULE', 'Campaign.Schedule', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_CAMPAIGN_USERS', 'Campaign.Users', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_CAMPAIGN_OPEN', 'Campaign.Open', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_CAMPAIGN_CLICK', 'Campaign.Click', $listDirn, $listOrder); ?>
				</th>
			</tr>


			<?php

		if (!empty($this->items)) :
			foreach($this->items as $i => $item):
				$canEdit    = $user->authorise('core.edit', JInbound::COM.'.page.'.$item->id);
				$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
				$canEditOwn = $user->authorise('core.edit.own', JInbound::COM.'.page.'.$item->id) && $item->created_by == $userId;
				$canChange  = $user->authorise('core.edit.state', JInbound::COM.'.page.'.$item->id) && $canCheckin;
				?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="hidden-phone" valign="top">
					<?php echo $item->id; ?>
				</td>
				<td class="hidden-phone"valign="top">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td class="nowrap has-context"valign="top">
					<div class="pull-left">
						&nbsp;<?php  echo $item->name;   ?>
					</div>
					</td>
					<td class="hidden-phone" valign="top">
							&nbsp;<?php  echo @$item->emails;   ?>
					</td>
					<td class="hidden-phone" valign="top">
							&nbsp;Yes
					</td>
					<td class="hidden-phone" valign="top">
							&nbsp;No
					</td>
					<td class="hidden-phone" valign="top">
							&nbsp;15
					</td>
					<td class="hidden-phone" valign="top">
							&nbsp;18%
					</td>
					<td class="hidden-phone" valign="top">
							&nbsp;20%
					</td>
				</tr>
				<?php endforeach;
						endif;
				?>

		</table>

	</div>

			<div>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>


</div>

