<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound

**********************************************
JInbound
Copyright (c) 2012 Anything-Digital.com
**********************************************
JInbound is some kind of marketing thingy

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

echo '<h2>Create A New Landing Page</h2>';

$buttons = array();
$buttons[] = JHtml::link('index.php?option=com_jinbound&task=page.add', '<div class="layout_select_button">100px x 160px \'Layout A\' Image</div>');
$buttons[] = JHtml::link('index.php?option=com_jinbound&task=page.add', '<div class="layout_select_button">100px x 160px \'Layout B\' Image</div>');
$buttons[] = JHtml::link('index.php?option=com_jinbound&task=page.add', '<div class="layout_select_button">100px x 160px \'Layout C\' Image</div>');
$buttons[] = JHtml::link('index.php?option=com_jinbound&task=page.add', '<div class="layout_select_button">100px x 160px \'Layout D\' Image</div>');
$buttons[] = JHtml::link('index.php?option=com_jinbound&task=page.add', '<div class="layout_select_button">100px x 160px \'Custom\' Image</div>');


?>

<div class="jinbound_listbuttons">
	<?php foreach ($buttons as $button) : ?>
	<div class="jinbound_listbutton">
		<?php echo $button; ?>
	</div>
	<?php endforeach; ?>
</div>


<form action="<?php echo JURI::base() . 'index.php?option=com_jinbound&view=pages'; ?>" method="post" name="adminForm" id="adminForm">

<?php

$floatButtons = JInbound::version()->isCompatible('3.0');

?>

<div class="clr"> </div>

<fieldset id="filter-bar">
	<div class="filter-search fltlft btn-group pull-left">
		<label class="filter-search-lbl element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
		<input type="text" name="filter_search" id="filter_search" value="" title="<?php echo JText::_('COM_JINBOUND_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_JINBOUND_FILTER_SEARCH_DESC'); ?>" />

		<select class="listbox  " title="<?php echo JText::_('JSEARCH_FILTER_PUBLISHED'); ?>">
			<option>Published</option>
		</select>

		<select class="listbox  " title="<?php echo JText::_('JSEARCH_FILTER_CATEGORY'); ?>">
			<option>Category</option>
		</select>

<?php if (!$floatButtons) : ?>
		<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
		<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
	<?php endif; ?>

	</div>

<?php if ($floatButtons) : ?>
	<div class="btn-group pull-left hidden-phone">
		<button type="submit" class="btn tip hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
		<button type="button" class="btn tip hasTooltip" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
	</div>
	<?php endif; ?>


</fieldset>





	<table class="adminlist table table-striped">
		<thead><?php echo $this->loadTemplate('head');?>
</thead>
		<tfoot></tfoot>
		<tbody><?php echo $this->loadTemplate('body');?></tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
