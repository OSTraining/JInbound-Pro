<?php
/**
 * @package             JInbound
 * @subpackage          plg_system_jinboundsalesforce
 * @ant_copyright_header@
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldJinboundsalesforcefields extends JFormFieldList
{
    public $type = 'Jinboundsalesforcefields';

    protected function getOptions()
    {
        $options = array();
        JFactory::getLanguage()->load('plg_system_jinboundsalesforce.sys', JPATH_ADMINISTRATOR);
        JDispatcher::getInstance()->trigger('onJInboundSalesforceFields', array(&$options));
        return array_merge(parent::getOptions(), $options);
    }
}
