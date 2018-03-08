<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.application.component.controlleradmin');
JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('url');

class JInboundControllerReports extends JControllerAdmin
{
    public function permissions()
    {
        JInbound::registerHelper('access');
        JInboundHelperAccess::saveRulesWithRedirect('report');
    }

    public function getModel($name = 'Reports', $prefix = 'JInboundModel')
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }

    public function exportleads()
    {
        $this->export('leads');
    }

    protected function export($layout)
    {
        $input  = JFactory::getApplication()->input;
        $params = array(
            'format'           => 'csv'
        ,
            'layout'           => $layout
        ,
            'filter_start'     => $input->get('filter_start', '', 'string')
        ,
            'filter_end'       => $input->get('filter_end', '', 'string')
        ,
            'filter_campaign'  => $input->get('filter_campaign', '', 'string')
        ,
            'filter_page'      => $input->get('filter_page', '', 'string')
        ,
            'filter_status'    => $input->get('filter_status', '', 'string')
        ,
            'filter_priority'  => $input->get('filter_priority', '', 'string')
        ,
            'filter_published' => $input->get('filter_published', '', 'string')
        );
        $this->setRedirect(JInboundHelperUrl::view('reports', false, $params));
    }

    public function exportpages()
    {
        $this->export('pages');
    }
}
