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

jimport('joomla.form.formfield');
jimport('joomla.form.helper');

class JFormFieldJinboundPublished extends JFormField
{
	public $type = 'Jinboundpublished';

	protected function getInput() {
		// get class for this element
		$class = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		// list of published types
		$list = array();
		$list[] = JHtml::_('select.option', 0, JText::_('COM_JCALPRO_UNPUBLISHED'), '_id', '_name');
		$list[] = JHtml::_('select.option', 1, JText::_('COM_JCALPRO_PUBLISHED'), '_id', '_name');
    return JHtml::_('select.genericlist', $list, $this->name, $class . ' size="1"', '_id', '_name', $this->value);
	}
}
