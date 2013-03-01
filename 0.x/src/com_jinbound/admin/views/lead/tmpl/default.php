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


	<br />

	<div class="m">

	<form action="<?php echo JRoute::_('index.php?option=com_jinbound&view=lead&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="<?php echo $this->viewName; ?>-edit-form form-validate">


		<div class="filter-search fltlft btn-group pull-left">
			<input type="text" name="filter_search" id="filter_search" value="" title="<?php echo JText::_('COM_JINBOUND_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_JINBOUND_FILTER_SEARCH_DESC'); ?>" />

		<button type="submit" class="btn">Go</button>
		<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">Reset</button>

		<select class="listbox  " title="">
			<option>Form Converted On</option>
		</select>

		<select class="listbox  " title="Priority">
			<option>Priority</option>
		</select>

		<select class="listbox  " title="Priority">
			<option>Status</option>
		</select>

		</div>


		<div class="clr"> </div>

		</div>

		<br/>

		<div class="m">



		</div>
	</div>


