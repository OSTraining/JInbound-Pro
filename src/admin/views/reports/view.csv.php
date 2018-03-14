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

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundCsvView', 'views/csvview');
JInbound::registerLibrary('JInboundBaseModel', 'models/basemodel');

class JInboundViewReports extends JInboundCsvView
{
    public function display($tpl = null, $safeparams = null)
    {
        if (!JFactory::getUser()->authorise('core.create', 'com_jinbound.report')) {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }
        $model = JInboundBaseModel::getInstance('Reports', 'JInboundModel');
        $state = $this->get('State');
        if (is_array($state) && !empty($state)) {
            foreach ($state as $key => $value) {
                $model->setState($key, $value);
            }
        }
        switch ($this->getLayout()) {
            case 'leads':
                $leads = $model->getRecentContacts(); //$model->getRecentLeads();
                $data  = array();
                $extra = array();
                if (!empty($leads)) {
                    foreach ($leads as $idx => $lead) {
                        $formdata = new JRegistry();
                        $formdata->loadString($lead->formdata);
                        $leads[$idx]->formdata = $formdata->toArray();
                        if (array_key_exists('lead', $lead->formdata) && is_array($lead->formdata['lead'])) {
                            $extra = array_values(array_unique(array_merge($extra,
                                array_keys($lead->formdata['lead']))));
                        }
                    }
                    if (!empty($extra)) {
                        foreach ($leads as $idx => $lead) {
                            foreach ($extra as $col) {
                                $value = '';
                                if (array_key_exists('lead',
                                        $lead->formdata) && is_array($lead->formdata['lead']) && array_key_exists($col,
                                        $lead->formdata['lead'])) {
                                    $value = $lead->formdata['lead'][$col];
                                }
                                $leads[$idx]->$col = $value;
                            }
                            unset($leads[$idx]->formdata);
                            $data[] = $lead;
                        }
                    }
                }
                $this->data = $data;
                break;
            case 'pages':
                $this->data = $model->getTopPages(); // $model->getTopLandingPages();
                break;
            default:
                JError::raiseError(400, JText::_('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND'));
                jexit();
        }
        $this->filename = $this->getLayout() . '-report';

        parent::display($tpl, $safeparams);
    }
}
