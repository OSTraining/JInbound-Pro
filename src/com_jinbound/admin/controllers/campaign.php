<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundFormController', 'controllers/basecontrollerform');

class JInboundControllerCampaign extends JInboundFormController
{
    public function edit($key = 'id', $urlVar = 'id')
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_jinbound.campaign')) {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }
        return parent::edit($key, $urlVar);
    }
}
