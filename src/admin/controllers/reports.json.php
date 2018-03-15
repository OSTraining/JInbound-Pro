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

class JInboundControllerReports extends JInboundBaseController
{
    public function plot()
    {
        $data  = array();
        $model = $this->getModel('Reports');
        try {
            $state               = $model->getState();
            $start               = $state->get('filter.start', null);
            $end                 = $state->get('filter.end', null);
            $data['tick']        = $model->getTickString($start, $end);
            $data['hits']        = $model->getLandingPageHits($start, $end);
            $data['leads']       = $model->getLeadsByCreationDate($start, $end);
            $data['conversions'] = $model->getConversionsByDate($start, $end);
            foreach ($data['leads'] as $i => $lead) {
                unset($data['leads'][$i]->tracks);
            }
        } catch (Exception $e) {
            $this->send403($e);
        }
        // TODO the rest
        $this->_json($data);
    }

    private function send403(Exception $exception)
    {
        if (!headers_sent()) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' 403 Forbidden');
        }
        $this->_json(array('error' => $exception->getMessage()));
    }

    private function _json($data, $headers = true)
    {
        if ($headers) {
            header('Content-Type: application/json');
        }
        echo json_encode($data);
        die;
    }

    public function glance()
    {
        $model = $this->getModel('Reports');
        try {
            $state       = $model->getState();
            $start       = $state->get('filter.start', null);
            $end         = $state->get('filter.end', null);
            $hits        = $model->getLandingPageHits($start, $end);
            $leads       = $model->getLeadsByCreationDate($start, $end);
            $conversions = $model->getConversionsByDate($start, $end);
            // initial data
            $data = array(
                'views'            => 0
            ,
                'leads'            => 0
            ,
                'views-to-leads'   => 0
            ,
                'conversion-count' => 0
            ,
                'conversion-rate'  => 0
            ,
                '__raw'            => array(
                    'hits'        => $hits
                ,
                    'leads'       => $leads
                ,
                    'conversions' => $conversions
                ,
                    'start'       => $start
                ,
                    'end'         => $end
                )
            );
            // add values
            foreach ($hits as $hit) {
                $data['views'] += (int)$hit[1];
            }
            foreach ($leads as $lead) {
                $data['leads'] += (int)$lead[1];
            }
            foreach ($conversions as $conversion) {
                $data['conversion-count'] += (int)$conversion[1];
            }
            // calc percents
            if (0 < $data['views']) {
                $data['views-to-leads']  = ($data['leads'] / $data['views']) * 100;
                $data['conversion-rate'] = ($data['conversion-count'] / $data['views']) * 100;
            }
            $data['views-to-leads']  = number_format($data['views-to-leads'], 2) . '%';
            $data['conversion-rate'] = number_format($data['conversion-rate'], 2) . '%';
        } catch (Exception $e) {
            $this->send403($e);
        }
        $this->_json($data);
    }

    private function _getDateTimeFromInput($string)
    {
        try {
            $date = new DateTime(JFactory::getApplication()->input->get($string, ''));
        } catch (Exception $e) {
            return false;
        }
        return $date;
    }
}
