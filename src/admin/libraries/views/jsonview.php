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

jimport('joomla.html.pane');

JLoader::register('JInboundBaseView', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/views/baseview.php');

class JInboundJsonView extends JInboundBaseView
{
    public function display($tpl = null, $safeparams = null)
    {
        $data = array();
        if (property_exists($this, 'data')) {
            $data = $this->data;
        }
        echo json_encode($data);
        jexit();
    }
}
