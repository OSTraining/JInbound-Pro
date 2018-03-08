<?php
/**
 * @package             jInbound
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

$e = new Exception(__FILE__);
JLog::add('JInboundViewLead is deprecated. ' . $e->getTraceAsString(), JLog::WARNING, 'deprecated');

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundItemView', 'views/baseviewitem');

class JInboundViewLead extends JInboundItemView
{
    function display($tpl = null, $safeparams = false)
    {
        $this->notes    = $this->get('Notes');
        $this->page     = $this->get('Page');
        $this->records  = $this->get('Records');
        $this->campaign = $this->get('Campaign');
        return parent::display($tpl, $safeparams);
    }
}
