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

class JInboundControllerContact extends JInboundBaseController
{
    public function status()
    {
        $this->_changeContact('status');
    }

    private function _changeContact($how)
    {
        $app      = JFactory::getApplication();
        $id       = $app->input->get('id');
        $campaign = $app->input->get('campaign_id');
        $value    = $app->input->get('value');
        $model    = $this->getModel('Contact', 'JInboundModel', array('ignore_request' => true));
        $result   = $model->$how($id, $campaign, $value);
        $list     = array();
        if ('priority' == $how) {
            $list = JInboundHelperContact::getContactPriorities($id);
        } else {
            if ('status' == $how) {
                $statuses  = JInboundHelperContact::getContactStatuses($id);
                $campaigns = JInboundHelperContact::getContactCampaigns($id);
                $list      = array();
                if (!empty($campaigns)) {
                    foreach ($campaigns as $c) {
                        if (array_key_exists($c->id, $statuses)) {
                            $list[$c->id] = $statuses[$c->id];
                        }
                    }
                }
            }
        }
        $plugin_results = JDispatcher::getInstance()
            ->trigger('onJInboundAfterJsonChangeState', array(
                $how,
                $id,
                $campaign,
                $value,
                $result
            ));
        echo json_encode(array(
            'success' => $result
        ,
            'list'    => $list
        ,
            'request' => array(
                'contact_id'  => $id
            ,
                'campaign_id' => $campaign
            ,
                "{$how}_id"   => $value
            )
        ,
            'plugin'  => $plugin_results
        ));
        jexit();
    }

    public function priority()
    {
        $this->_changeContact('priority');
    }
}
