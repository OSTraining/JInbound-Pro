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


class JInboundViewLead extends JInboundView
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{


		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		JToolBarHelper::title(JText::_(parent::$option . '_DASHBOARD_TITLE'), 'jinbound');


		$this->addToolbar();
		parent::display($tpl);

	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	public function addToolbar()
	{

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);




		// Build the actions for new and existing records.
		if ($isNew)  {
			// For new records, check the create permission.
			if ($isNew && (count($user->getAuthorisedCategories('com_jinbound', 'core.create')) > 0)) {
				JToolBarHelper::apply('campaign.apply');
				JToolBarHelper::save('campaign.save');
				JToolBarHelper::save2new('campaign.save2new');
			}

			JToolBarHelper::cancel('campaign.cancel');
		}
		else {
			// Can't save the record if it's checked out.
			if (!$checkedOut) {
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
					JToolBarHelper::apply('campaign.apply');
					JToolBarHelper::save('campaign.save');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create')) {
						JToolBarHelper::save2new('campaign.save2new');
					}
				}
			}

			// If checked out, we can still save
			if ($canDo->get('core.create')) {
				JToolBarHelper::save2copy('campaign.save2copy');
			}

			JToolBarHelper::cancel('campaign.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_COMPONENTS_JINBOUND');
	}
}
