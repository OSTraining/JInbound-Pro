<?php
/**
 * @package             JInbound
 * @subpackage          plg_system_jinboundacymailing
 * @ant_copyright_header@
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
