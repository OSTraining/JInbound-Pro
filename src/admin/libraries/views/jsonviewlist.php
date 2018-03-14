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

jimport('joomla.html.pane');

JLoader::register('JInboundListView',
    JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/views/baseviewlist.php');

class JInboundJsonListView extends JInboundListView
{
    public function display($tpl = null, $safeparams = null)
    {
        $data = array();
        foreach (array('items', 'pagination', 'state') as $var) {
            if (empty($this->$var)) {
                $$var = $this->get($var);
            } else {
                $$var = $this->$var;
            }
        }
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
        $data['items']      = $items;
        $data['pagination'] = $pagination;
        $data['state']      = $state;

        $this->data = $data;
        echo json_encode($data);
        jexit();
    }
}
