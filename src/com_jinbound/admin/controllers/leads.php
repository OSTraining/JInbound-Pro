<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$e = new Exception(__FILE__);
JLog::add('JInboundControllerLeads is deprecated. ' . $e->getTraceAsString(), JLog::WARNING, 'deprecated');

jimport('joomla.application.component.controlleradmin');

class JInboundControllerLeads extends JControllerAdmin
{
    public function getModel($name = 'Lead', $prefix = 'JInboundModel')
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }
}
