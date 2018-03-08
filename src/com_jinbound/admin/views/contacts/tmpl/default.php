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

$this->cols = 11;

echo $this->loadTemplate('list');

?>
<script type="text/javascript">
    Joomla.submitbutton = function(task) {
        if ('reports.exportleads' === task) {
            setTimeout(function() {
                jQuery('#adminForm').find('input[name=\'task\']').val('');
            }, 3000);
        }
        Joomla.submitform(task, document.getElementById('adminForm'));
    };
</script>
