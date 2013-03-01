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

jimport('joomla.html.pane');

$base = JPATH_ADMINISTRATOR . '/components/com_jinbound';

JLoader::register('JInbound', "$base/helpers/jinbound.php");
JLoader::register('JInboundView', "$base/libraries/views/allpages.php");

class JInboundViewPages extends JInboundView
{

	protected $items;
	protected $pagination;
	protected $state;

	function display($tpl = null, $echo = true) {

		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');


		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$this->publishedList = $this->get('PublishedStatus');
		$this->addToolBar();

		JToolBarHelper::title(JText::_(parent::$option . '_DASHBOARD_TITLE'), 'jinbound');

		parent::display($tpl);
	}

	public function addToolBar() {
		// only fire in administrator
		if (!JFactory::getApplication()->isAdmin()) return;

		$single = preg_replace('/s$/', '', $this->_name);

		if (JFactory::getUser()->authorise('core.create')) {
			JToolBarHelper::addNew($single . '.add', 'JTOOLBAR_NEW');
		}
		if (JFactory::getUser()->authorise('core.edit') || JFactory::getUser()->authorise('core.edit.own')) {
			JToolBarHelper::editList($single . '.edit', 'JTOOLBAR_EDIT');
			JToolBarHelper::divider();
		}
		if (JFactory::getUser()->authorise('core.edit.state')) {
			JToolBarHelper::publish($this->_name . '.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish($this->_name . '.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::checkin($this->_name . '.checkin');
			JToolBarHelper::divider();
		}
		if ($this->state->get('filter.published') == -2 && JFactory::getUser()->authorise('core.delete', self::$option)) {
			JToolBarHelper::deleteList('', $this->_name . '.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		else if (JFactory::getUser()->authorise('core.edit.state')) {
			JToolBarHelper::trash($this->_name . '.trash');
			JToolBarHelper::divider();
		}

	}

}