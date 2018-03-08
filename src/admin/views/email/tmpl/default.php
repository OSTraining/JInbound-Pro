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

JHtml::_('behavior.formvalidation');

JText::script('COM_JINBOUND_ENTER_EMAIL_RECIPIENT');
JText::script('COM_JINBOUND_EMAIL_NOT_SENT');
JText::script('COM_JINBOUND_EMAIL_SENT');
JText::script('JGLOBAL_VALIDATION_FORM_FAILED');

?>
<script type="text/javascript">
    window.jinboundemailtags = {
        campaign: '<?php echo JInboundHelperFilter::escape_js($this->emailtags->campaign); ?>',
        report  : '<?php echo JInboundHelperFilter::escape_js($this->emailtags->report); ?>'
    };
    Joomla.emailtest = function(form) {
        <?php echo $this->form->getField('htmlbody')->save(); ?>
        var sendto = prompt(Joomla.JText._('COM_JINBOUND_ENTER_EMAIL_RECIPIENT'));
        var url = 'index.php?option=com_jinbound&task=email.test';
        var token = '<?php echo JSession::getFormToken(); ?>';
        var data = {
            to         : sendto
            , fromname : document.getElementById('jform_fromname').value
            , fromemail: document.getElementById('jform_fromemail').value
            , subject  : document.getElementById('jform_subject').value
            , htmlbody : document.getElementById('jform_htmlbody').value
            , plainbody: document.getElementById('jform_plainbody').value
            , type     : jQuery('#jform_type').find(':selected').val()
        };
        data[token] = 1;
        var success = function(response) {
            alert(Joomla.JText._('Done' == response ? 'COM_JINBOUND_EMAIL_SENT' : 'COM_JINBOUND_EMAIL_NOT_SENT'));
        };
        if ('undefined' === typeof jQuery) {
            var r = new Request.HTML({
                url        : url
                , method   : 'POST'
                , data     : data
                , onSuccess: success
            }).send();
        }
        else {
            jQuery.ajax(url, {
                type     : 'POST'
                , data   : data
                , success: success
            });
        }
    };
    Joomla.submitbutton = function(task) {
        var form = document.getElementById('adminForm');
        if ('email.cancel' === task) {
            Joomla.submitform(task, form);
        }
        else if ('email.test' === task) {
            Joomla.emailtest(form);
        }
        else if (!document.formvalidator.isValid(form)) {
            alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED'));
        }
        else {
            Joomla.submitform(task, form);
        }
    };
    (function($) {
        $(document).ready(function() {
            $('#jform_type').change(function() {
                var t  = $(this),
                    v  = t.find(':selected').val(),
                    s  = $('#jform_sendafter'),
                    t1 = $('.reports_tab'),
                    t2 = $('#jinbound_default_tabsTabs li a[href=\'#reports_tab\']'),
                    c  = $('#jform_campaign_id'),
                    l  = $('#jform_email_tips');
                if ('campaign' === v) {
                    s.removeAttr('disabled');
                    c.removeAttr('disabled');
                    t1.hide();
                    t2.hide();
                    l.empty().html(window.jinboundemailtags.campaign);
                }
                else if ('report' === v) {
                    s.attr('disabled', 'disabled');
                    c.attr('disabled', 'disabled');
                    t1.show();
                    t2.show();
                    l.empty().html(window.jinboundemailtags.report);
                }
            });
            $('#jform_type').trigger('change');
        });
    })(jQuery);
</script>
