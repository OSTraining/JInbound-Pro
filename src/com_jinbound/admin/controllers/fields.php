<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('path');
JInbound::registerHelper('access');

jimport('joomla.application.component.controlleradmin');

class JInboundControllerFields extends JControllerAdmin
{
    public function getModel($name = 'Field', $prefix = 'JInboundModel')
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }

    public function permissions()
    {
        JInbound::registerHelper('access');
        JInboundHelperAccess::saveRulesWithRedirect('field');
    }
}
