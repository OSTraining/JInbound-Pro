<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
 **********************************************
 * jInbound
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

JLoader::register('JInboundView', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/views/baseview.php');

class JInboundItemView extends JInboundView
{
    function display($tpl = null, $safeparams = false)
    {
        $form = $this->get('Form');
        $item = $this->get('Item');
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
        // quickfix
        if (is_object($item) && !property_exists($item, 'id')) {
            $item->id = 0;
        }
        // Assign the Data
        $this->form  = $form;
        $this->item  = $item;
        $this->canDo = JInbound::getActions();

        $this->prepareItem();

        parent::display($tpl, $safeparams);
        $this->setDocument();
    }

    public function prepareItem()
    {
        // stub
    }

    public function setDocument()
    {
        jimport('joomla.filesystem.file');
        $isNew    = ($this->item->id < 1);
        $document = JFactory::getDocument();
        $title    = strtoupper(JInbound::COM . '_' . $this->_name);
        if ('contact' === $this->_name) {
            $title = strtoupper(JInbound::COM . '_LEAD');
        }
        $title .= '_' . ($isNew ? 'CREATING' : 'EDITING');
        $document->setTitle(JText::_($title));
    }

    public function addToolBar()
    {
        // only fire in administrator
        $app = JFactory::getApplication();
        if (!$app->isAdmin()) {
            return;
        }
        $app->input->set('hidemainmenu', true);
        $user       = JFactory::getUser();
        $userId     = $user->id;
        $isNew      = (@$this->item->id == 0);
        $checkedOut = false;
        $name       = strtolower($this->_name);
        if ($this->item && property_exists($this->item, 'checked_out')) {
            $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
        }
        $canCreate  = $user->authorise('core.create', JInbound::COM . ".$name");
        $canEdit    = $user->authorise('core.edit', JInbound::COM . ".$name");
        $canEditOwn = $user->authorise('core.edit.own', JInbound::COM . ".$name");

        // set the toolbar title
        $title = strtoupper(JInbound::COM . '_' . $this->_name . '_MANAGER');
        $class = 'jinbound-' . strtolower($this->_name);
        if ('contact' === $this->_name) {
            $title = strtoupper(JInbound::COM . '_LEAD_MANAGER');
            $class = 'jinbound-contact';
        }
        $title .= '_' . ($checkedOut ? 'VIEW' : ($isNew ? 'ADD' : 'EDIT'));
        JToolBarHelper::title(JText::_($title), $class);

        if ($isNew) {
            if ($canCreate) {
                JToolBarHelper::apply($name . '.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save($name . '.save', 'JTOOLBAR_SAVE');
                JToolBarHelper::custom($name . '.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW',
                    false);
            }
            JToolBarHelper::cancel($name . '.cancel', 'JTOOLBAR_CANCEL');
        } else {
            // Can't save the record if it's checked out.
            if (!$checkedOut) {
                // Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
                if ($canEdit || ($canEditOwn && $this->item->created_by == $userId)) {
                    JToolBarHelper::apply($name . '.apply', 'JTOOLBAR_APPLY');
                    JToolBarHelper::save($name . '.save', 'JTOOLBAR_SAVE');

                    // We can save this record, but check the create permission to see if we can return to make a new one.
                    if ($canCreate) {
                        JToolBarHelper::custom($name . '.save2new', 'save-new.png', 'save-new_f2.png',
                            'JTOOLBAR_SAVE_AND_NEW', false);
                    }
                }
            }

            // If checked out, we can still save
            if ($canCreate) {
                JToolBarHelper::custom($name . '.save2copy', 'save-copy.png', 'save-copy_f2.png',
                    'JTOOLBAR_SAVE_AS_COPY', false);
            }
            JToolBarHelper::cancel($name . '.cancel', 'JTOOLBAR_CLOSE');
        }
    }

    public function addMenuBar()
    {
        if ('edit' == JFactory::getApplication()->input->get('layout')) {
            return;
        }
        parent::addMenuBar();
    }
}
