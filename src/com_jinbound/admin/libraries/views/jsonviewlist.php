<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.pane');

JLoader::register('JInboundListView',
    JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/views/baseviewlist.php');

class JInboundJsonListView extends JInboundListView
{
    public function display($tpl = null, $safeparams = null)
    {
        $data = array();
        foreach (array('items', 'pagination', 'state') as $var) {
            if (empty($this->$var)) {
                $$var = $this->get($var);
            } else {
                $$var = $this->$var;
            }
        }
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
        $data['items']      = $items;
        $data['pagination'] = $pagination;
        $data['state']      = $state;

        $this->data = $data;
        echo json_encode($data);
        jexit();
    }
}
