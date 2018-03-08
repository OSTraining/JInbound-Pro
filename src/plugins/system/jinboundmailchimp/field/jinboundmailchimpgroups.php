<?php
/**
 * @package             JInbound
 * @subpackage          plg_system_jinboundmailchimp
 * @ant_copyright_header@
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldJinboundMailchimpgroups extends JFormFieldList
{
    protected $type = 'JinboundMailchimpgroups';

    protected function getOptions()
    {
        $plugin = JPluginHelper::getPlugin('system', 'jinboundmailchimp');
        require_once realpath(dirname(__FILE__) . '/../library/helper.php');
        $helper = new JinboundMailchimp(array('params' => $plugin->params));
        // Put groups in select field
        $options = $helper->getMCGroupSelectOptions($this->form->getValue('id'));
        return array_merge(parent::getOptions(), $options);
    }

}
