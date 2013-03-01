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

defined('_JEXEC') or die;

jimport('joomla.html.pane');

$base = JPATH_ADMINISTRATOR . '/components/com_jinbound';

JLoader::register('JInbound', "$base/helpers/jinbound.php");
JLoader::register('JInboundView', "$base/libraries/views/allpages.php");
JLoader::register('JInboundHelperPath', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/path.php');


class JInboundViewCampaign extends JInboundView
{

	protected $form = null;

	public function display($tpl = null)
	{

		$form = & $this->get('Form');

		$this->form = $form;

		$this->item = $this->get('Item');

		JToolBarHelper::title(JText::_(parent::$option . '_DASHBOARD_TITLE'), 'jinbound');

		$this->addToolBar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	public function addToolBar() {
		// only fire in administrator
		if (!JFactory::getApplication()->isAdmin()) return;
		JRequest::setVar('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = (@$this->item->id == 0);
		$checkedOut = false;
		if ($this->item && property_exists($this->item, 'checked_out')) {
			$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		}
		$canDo = Jinbound::getActions();

		if ($isNew) {
			if ($canDo->get('core.create')) {
				JToolBarHelper::apply(strtolower($this->_name).'.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save(strtolower($this->_name).'.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom(strtolower($this->_name).'.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel(strtolower($this->_name).'.cancel', 'JTOOLBAR_CANCEL');
		} else {
			// Can't save the record if it's checked out.
			if (!$checkedOut) {
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
					JToolBarHelper::apply(strtolower($this->_name).'.apply', 'JTOOLBAR_APPLY');
					JToolBarHelper::save(strtolower($this->_name).'.save', 'JTOOLBAR_SAVE');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create')) {
						JToolBarHelper::custom(strtolower($this->_name).'.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
					}
				}
			}

			// If checked out, we can still save
			if ($canDo->get('core.create')) {
				JToolBarHelper::custom(strtolower($this->_name).'.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::cancel(strtolower($this->_name).'.cancel', 'JTOOLBAR_CLOSE');
		}
	}

}
