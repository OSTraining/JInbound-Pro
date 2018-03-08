<?php
/**
 * @package             JInbound
 * @subpackage          plg_system_jinboundmailchimp
 * @ant_copyright_header@
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldJinboundMailchimpfields extends JFormFieldList
{
    protected $type = 'JinboundMailchimpfields';

    protected function getOptions()
    {
        $plugin = JPluginHelper::getPlugin('system', 'jinboundmailchimp');
        require_once realpath(dirname(__FILE__) . '/../library/helper.php');
        $helper = new JinboundMailchimp(array('params' => $plugin->params));
        // Put fields in select field
        $options = $helper->getMCMergeFieldsSelectOptions($this->form->getValue('id'));
        return array_merge(parent::getOptions(), $options);
    }
}
