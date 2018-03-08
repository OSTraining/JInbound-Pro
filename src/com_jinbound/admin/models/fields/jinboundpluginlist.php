<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

JFormHelper::loadFieldClass('list');
JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('path');

class JFormFieldJInboundPluginList extends JFormFieldList
{
    public $type = 'Jinboundpluginlist';

    protected function getOptions()
    {
        $dispatcher = JDispatcher::getInstance();
        // get options
        $options = parent::getOptions();
        // trigger plugins
        $dispatcher->trigger('onJInboundPluginList', array($this->type, &$options));
        // all done
        return $options;
    }
}
