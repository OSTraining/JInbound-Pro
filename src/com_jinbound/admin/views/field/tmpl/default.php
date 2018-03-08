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

echo $this->loadTemplate('edit');
?>
<script type="text/javascript">
    (function($, d) {
        $(d).ready(function() {
            $(d).on('change', '#jform_type', function(e) {
                var o = $('div[data-id="jform_params_opts"]');
                if (o.length) {
                    switch ($(e.target).find(':selected').val()) {
                        case 'checkbox':
                        case 'checkboxes':
                        case 'list':
                        case 'radio':
                        case 'groupedlist':
                            o.show();
                            break;
                        default:
                            o.hide();
                            break;
                    }
                }
            });
            $('#jform_type').trigger('click');
        });
    })(jQuery, document);
</script>
