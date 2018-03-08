<?php
/**
 * @package             JInbound
 * @subpackage          plg_system_jinboundacymailing
 * @ant_copyright_header@
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldJinboundAcymailinglists extends JFormFieldList
{
    protected $type = 'JinboundAcymailinglists';

    protected function getOptions()
    {
        $plugin = JPluginHelper::getPlugin('system', 'jinboundacymailing');
        require_once realpath(dirname(__FILE__) . '/../helper/helper.php');
        $helper = new JinboundAcymailing(array('params' => $plugin->params));
        // Put groups in select field
        $options = $helper->getListSelectOptions($this->form->getValue('id'));
        return array_merge(parent::getOptions(), $options);
    }
}
