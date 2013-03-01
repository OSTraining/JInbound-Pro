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
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');

?>

<div class="container-fluid" id="jinbound_component">

	<h2>Lead Manager</h2>

	<div class="m">

		Random Advice Text

	</div>

	<br />

	<div class="m">

	<form action="<?php echo JRoute::_('index.php?option=com_jinbound&view=lead&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="<?php echo $this->viewName; ?>-edit-form form-validate">

			<fieldset id="filter-bar">
		<div class="filter-search fltlft btn-group pull-left">
			<input type="text" name="filter_search" id="filter_search" value="" title="<?php echo JText::_('COM_JINBOUND_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_JINBOUND_FILTER_SEARCH_DESC'); ?>" />

		<?php if (!$floatButtons) : ?>
			<button type="submit" class="btn">Go</button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">Reset</button>
		<?php endif; ?>

		</div>


		<select class="listbox  " title="">
			<option>Form Converted On</option>
		</select>

		<select class="listbox  " title="Priority">
			<option>Priority</option>
		</select>

		<select class="listbox  " title="Priority">
			<option>Status</option>
		</select>


		</fieldset>
		<div class="clr"> </div>


		<table>
			<tr>
				<th width="1%" class="nowrap hidden-phone">
					<?php echo JText::_('COM_JINBOUND_ID'); ?>
				</th>
				<th width="1%" class="nowrap hidden-phone">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_LEAD_NAME', 'Lead.Name', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_LEAD_DATE', 'Lead.Date', $listDirn, $listOrder); ?>
				</th>
						<th>
					<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_LEAD_CONVERTED', 'Lead.Converted', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_LEAD_PRIORITY', 'Lead.Priority', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_LEAD_STATUS', 'Lead.Status', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_LEAD_NOTE', 'Lead.note', $listDirn, $listOrder); ?>
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
				<td class="hidden-phone">
					<?php echo $i; ?>
				</td>
				<td class="hidden-phone">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td class="nowrap has-context">
					Lead <?php echo $i; ?>
					<div class="pull-left">

						<?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->author_name, $item->checked_out_time, 'pages.', $canCheckin); ?>
						<?php endif; ?>
						<?php if ($canEdit || $canEditOwn) : ?>
							<a href="<?php echo JInboundHelperUrl::edit('page', $item->id); ?>">
								<?php echo $this->escape($item->user_name); ?>
							</a>
						<?php else : ?>
							<?php echo $this->escape($item->user_name); ?>
						<?php endif; ?>
					</div>
					<?php if (JInbound::version()->isCompatible('3.0')) : ?>
					<div class="pull-left"><?php

						JHtml::_('dropdown.edit', $item->id, 'page.');
						JHtml::_('dropdown.divider');
						JHtml::_('dropdown.' . ($item->published ? 'un' : '') . 'publish', 'cb' . $i, 'pages.');
						if ($item->checked_out) :
							JHtml::_('dropdown.checkin', 'cb' . $i, 'pages.');
							endif;
						JHtml::_('dropdown.' . ($trashed ? 'un' : '') . 'trash', 'cb' . $i, 'pages.');

						echo JHtml::_('dropdown.render');

						?></div>
					<?php endif; ?>
					</td>
					<td class="hidden-phone">
							&nbsp;
							<?php echo date("D M j G:i:s"); ?>
					</td>
					<td class="hidden-phone">
							&nbsp;
							<?php echo date("D M j G:i:s", strtotime("-1 day")); ?>
					</td>
					<td class="hidden-phone">
							&nbsp;
							Normal
					</td>
					<td class="hidden-phone">
							&nbsp;
							Active
					</td>
					<td class="hidden-phone">
							&nbsp;
							N/a
					</td>
				</tr>
				<?php endforeach;
						endif;
				?>

		</table>

	</div>
		<div>
			<input type="hidden" name="task" value="page.edit" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>

</div>

