<?php
/**
 * @package             JInbound
 * @subpackage          plg_system_jinboundacymailing
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

defined('_JEXEC') or die;

class JFormFieldJinboundacymailingcontactinfo extends JFormField
{
    protected function getInput()
    {
        $email  = $this->form->getValue('email');
        $plugin = JPluginHelper::getPlugin('system', 'jinboundacymailing');
        require_once realpath(dirname(__FILE__) . '/../helper/helper.php');
        $helper = new JinboundAcymailing(array('params' => $plugin->params));
        $table  = $helper->getListTable($email, $this->id . '_table');
        $script = $this->getScript();
        return $table . $script;
    }

    protected function getScript()
    {
        $id = $this->id . '_table';
        return <<<SCRIPT
<script type="text/javascript">
	(function($,d){
		$(d.body).on('jinboundleadupdate', function(e,response){
			if ('undefined' === typeof response.plugin)
			{
				return;
			}
			$.each(response.plugin, function(idx, el) {
				if ('undefined' === typeof el.acymailing)
				{
					return;
				}
				$('#$id').parent().empty().append($(el.acymailing));
			});
		});
	})(jQuery,document);
</script>
SCRIPT
            ;
    }

    protected function getLabel()
    {
        return '';
    }
}
