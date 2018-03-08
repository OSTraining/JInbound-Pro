<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$e = new Exception(__FILE__);
JLog::add('JInboundViewLeads is deprecated. ' . $e->getTraceAsString(), JLog::WARNING, 'deprecated');

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundJsonListView', 'views/jsonviewlist');

class JInboundViewLeads extends JInboundJsonListView
{
    public function display($tpl = null, $safeparams = null)
    {
        $this->items = $this->get('Items');
        if (!empty($this->items)) {
            foreach ($this->items as &$item) {
                $item->url      = JInboundHelperUrl::edit('lead', $item->id);
                $item->page_url = JInboundHelperUrl::edit('page', $item->page_id);
            }
        }
        parent::display($tpl, $safeparams);
    }
}
