<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.application.component.controlleradmin');

class JInboundControllerCampaigns extends JControllerAdmin
{
    public function permissions()
    {
        JInbound::registerHelper('access');
        JInboundHelperAccess::saveRulesWithRedirect('campaign');
    }

    public function getModel($name = 'Campaign', $prefix = 'JInboundModel')
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }
}
