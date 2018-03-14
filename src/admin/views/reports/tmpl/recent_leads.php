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

?>
<div class="row-fluid">
    <div class="span12">
        <h4><img alt="<?php echo JText::_('COM_JINBOUND_RECENT_LEADS'); ?>"
                 src="<?php echo JInboundHelperUrl::media() . '/images/recent_leads.png'; ?>"/>
            <span><?php echo JText::_('COM_JINBOUND_RECENT_LEADS'); ?></span></h4>
        <div id="reports_recent_leads"></div>
    </div>
</div>
