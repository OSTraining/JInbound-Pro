<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.pane');

JLoader::register('JInboundBaseView', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/views/baseview.php');

class JInboundJsonView extends JInboundBaseView
{
    public function display($tpl = null, $safeparams = null)
    {
        $data = array();
        if (property_exists($this, 'data')) {
            $data = $this->data;
        }
        echo json_encode($data);
        jexit();
    }
}
