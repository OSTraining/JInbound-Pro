<?php
/**
 * @package   jInbound-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2015 Anything-Digital.com
 * @copyright 2016-2021 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of jInbound-Pro.
 *
 * jInbound-Pro is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * jInbound-Pro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with jInbound-Pro.  If not, see <http://www.gnu.org/licenses/>.
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
