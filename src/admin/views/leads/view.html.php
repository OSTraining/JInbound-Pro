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

$e = new Exception(__FILE__);
JLog::add('JInboundViewLeads is deprecated. ' . $e->getTraceAsString(), JLog::WARNING, 'deprecated');

JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');

class JInboundViewLeads extends JInboundListView
{
    function display($tpl = null, $safeparams = false)
    {

        $state = $this->get('State');
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        $this->addFilter(JText::_('COM_JINBOUND_SELECT_PRIORITY'), 'filter_priority', $this->get('PriorityOptions'),
            $state->get('filter.priority'));
        $this->addFilter(JText::_('COM_JINBOUND_SELECT_LEAD_STATUS'), 'filter_status', $this->get('StatusOptions'),
            $state->get('filter.status'));

        return parent::display($tpl, $safeparams);
    }

    public function addToolBar()
    {
        // export icon
        JToolBarHelper::custom('reports.exportleads', 'export.png', 'export_f2.png', 'COM_JINBOUND_EXPORT_LEADS',
            false);
        parent::addToolBar();
    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     */
    protected function getSortFields()
    {
        return array(
            'User.name'      => JText::_('COM_JINBOUND_NAME')
        ,
            'Lead.published' => JText::_('COM_JINBOUND_PUBLISHED')
        ,
            'Lead.created'   => JText::_('COM_JINBOUND_LEAD_DATE')
        ,
            'Page.formname'  => JText::_('COM_JINBOUND_LEAD_CONVERTED')
        ,
            'Priority.name'  => JText::_('COM_JINBOUND_LEAD_PRIORITY')
        ,
            'Status.name'    => JText::_('COM_JINBOUND_LEAD_STATUS')
        ,
            'Lead.note'      => JText::_('COM_JINBOUND_LEAD_NOTE')
        );
    }
}
