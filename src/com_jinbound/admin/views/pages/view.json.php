<?php
/**
 * @package		jInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundJsonListView', 'views/jsonviewlist');

class JInboundViewPages extends JInboundJsonListView
{
	public function display($tpl = null, $safeparams = null) {
		$this->items = $this->get('Items');
		if (!empty($this->items)) {
			foreach ($this->items as &$item) {
				$item->url = JInboundHelperUrl::edit('page', $item->id);
				$item->created = JInbound::userDate($item->created);
			}
		}
		parent::display($tpl, $safeparams);
	}
}
