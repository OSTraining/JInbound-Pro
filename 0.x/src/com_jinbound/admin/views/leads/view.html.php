<?php
/**
 * @version		$Id$
 * @package		jInbound
 * @subpackage	com_jinbound

**********************************************
jInbound
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

jimport('joomla.html.pane');

$base = JPATH_ADMINISTRATOR . '/components/com_jinbound';

JLoader::register('JInbound', "$base/helpers/jinbound.php");
JLoader::register('JInboundView', "$base/libraries/views/allpages.php");
JLoader::register('JInboundHelperPath', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/path.php');


class JInboundViewLeads extends JInboundView
{

	protected $items;

	public function display($tpl = null)
	{

		JToolBarHelper::title(JText::_(parent::$option . '_DASHBOARD_TITLE'), 'jinbound');

		$this->items[] = '';
		$this->items[] = '';
		$this->items[] = '';
		$this->items[] = '';
		$this->items[] = '';
		$this->items[] = '';


		$this->addToolbar();
		parent::display($tpl);
	}

	function addToolBar() {


		if (JFactory::getUser()->authorise('core.create')) {
			JToolBarHelper::addNew($single . '.add', 'JTOOLBAR_NEW');
		}
		if (JFactory::getUser()->authorise('core.edit') || JFactory::getUser()->authorise('core.edit.own')) {
			JToolBarHelper::editList($single . '.edit', 'JTOOLBAR_EDIT');
			JToolBarHelper::addNew($single . '.copy', 'COM_JINBOUND_COPY');
			JToolBarHelper::trash($single . '.trash', 'JTOOLBAR_TRASH');
			JToolBarHelper::divider();
		}


	}
}
