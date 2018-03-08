<?php
/**
 * @package		jInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundJsonListView', 'views/jsonviewlist');

class JInboundViewContacts extends JInboundJsonListView
{
	public function display($tpl = null, $safeparams = null) {
		$this->items = $this->get('Items');
		if (!empty($this->items)) {
			foreach ($this->items as &$item) {
				$item->url = JInboundHelperUrl::edit('contact', $item->id);
				$item->page_url = JInboundHelperUrl::edit('page', $item->latest_conversion_page_id);
				$item->created = JInbound::userDate($item->created);
				$item->latest = JInbound::userDate($item->latest);
			}
			// do not send track info in json format
			// TODO just don't pull the data in the model
			unset($item->tracks);
		}
		parent::display($tpl, $safeparams);
	}
}
