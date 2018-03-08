<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 **********************************************
 * JInbound
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

jimport('joomla.application.component.controlleradmin');

class JInboundControllerStatuses extends JControllerAdmin
{
    public function permissions()
    {
        JInbound::registerHelper('access');
        JInboundHelperAccess::saveRulesWithRedirect('status');
    }

    public function setDefault()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $pks = JRequest::getVar('cid', array(), 'post', 'array');

        try {
            if (empty($pks)) {
                throw new Exception(JText::_('COM_JINBOUND_NO_STATUS_SELECTED'));
            }

            JArrayHelper::toInteger($pks);

            // Pop off the first element.
            $id    = array_shift($pks);
            $model = $this->getModel();
            $model->setDefault($id);
            $this->setMessage(JText::_('COM_JINBOUND_SUCCESS_DEFAULT_STATUS_SET'));

        } catch (Exception $e) {
            JError::raiseWarning(500, $e->getMessage());
        }

        $this->setRedirect('index.php?option=com_jinbound&view=statuses');
    }

    public function getModel($name = 'Status', $prefix = 'JInboundModel')
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }

    public function unsetDefault()
    {
        $this->setMessage(JText::_('COM_JINBOUND_SUCCESS_SEFAULT_CANNOT_BE_UNSET'));
        $this->setRedirect('index.php?option=com_jinbound&view=statuses');
    }

    public function setFinal()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $pks = JRequest::getVar('cid', array(), 'post', 'array');

        try {
            if (empty($pks)) {
                throw new Exception(JText::_('COM_JINBOUND_NO_STATUS_SELECTED'));
            }

            JArrayHelper::toInteger($pks);

            // Pop off the first element.
            $id    = array_shift($pks);
            $model = $this->getModel();
            $model->setFinal($id);
            $this->setMessage(JText::_('COM_JINBOUND_SUCCESS_FINAL_STATUS_SET'));

        } catch (Exception $e) {
            JError::raiseWarning(500, $e->getMessage());
        }

        $this->setRedirect('index.php?option=com_jinbound&view=statuses');
    }

    public function unsetFinal()
    {
        $this->setMessage(JText::_('COM_JINBOUND_SUCCESS_FINAL_CANNOT_BE_UNSET'));
        $this->setRedirect('index.php?option=com_jinbound&view=statuses');
    }

    public function setActive()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $pks = JRequest::getVar('cid', array(), 'post', 'array');

        try {
            if (empty($pks)) {
                throw new Exception(JText::_('COM_JINBOUND_NO_STATUS_SELECTED'));
            }

            JArrayHelper::toInteger($pks);

            // Pop off the first element.
            $id    = array_shift($pks);
            $model = $this->getModel();
            $model->setActive($id);
            $this->setMessage(JText::_('COM_JINBOUND_SUCCESS_ACTIVE_STATUS_SET'));

        } catch (Exception $e) {
            JError::raiseWarning(500, $e->getMessage());
        }

        $this->setRedirect('index.php?option=com_jinbound&view=statuses');
    }

    public function unsetActive()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $pks = JRequest::getVar('cid', array(), 'post', 'array');

        try {
            if (empty($pks)) {
                throw new Exception(JText::_('COM_JINBOUND_NO_STATUS_SELECTED'));
            }

            JArrayHelper::toInteger($pks);

            // Pop off the first element.
            $id    = array_shift($pks);
            $model = $this->getModel();
            $model->unsetActive($id);
            $this->setMessage(JText::_('COM_JINBOUND_SUCCESS_ACTIVE_STATUS_SET'));

        } catch (Exception $e) {
            JError::raiseWarning(500, $e->getMessage());
        }

        $this->setRedirect('index.php?option=com_jinbound&view=statuses');
    }
}
