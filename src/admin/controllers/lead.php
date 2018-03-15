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
JLog::add('JInboundControllerLead is deprecated. ' . $e->getTraceAsString(), JLog::WARNING, 'deprecated');

JInbound::registerLibrary('JInboundFormController', 'controllers/basecontrollerform');

class JInboundControllerLead extends JInboundFormController
{
    public function save($key = null, $urlVar = null)
    {
        $app  = JFactory::getApplication();
        $data = $app->input->post->get('jform', array(), 'array');
        if (array_key_exists('formdata', $data)) {
            unset($formdata);
        }
        $data['formdata'] = json_encode($data);
        return parent::save($key, $urlVar);
    }

    public function status()
    {
        $this->_changeLead('status');
    }

    private function _changeLead($how)
    {
        $app   = JFactory::getApplication();
        $id    = $app->input->get('id');
        $value = $app->input->get('value');
        $model = $this->getModel();
        $model->$how($id, $value);
    }

    public function priority()
    {
        $this->_changeLead('priority');
    }
}
