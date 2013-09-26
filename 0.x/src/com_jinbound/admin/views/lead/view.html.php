<?php
/**
 * @package		jInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundItemView', 'views/baseviewitem');

class JInboundViewLead extends JInboundItemView
{
	function display($tpl = null, $safeparams = false) {
		$this->notes    = $this->get('Notes');
		$this->page     = $this->get('Page');
		$this->records  = $this->get('Records');
		$this->campaign = $this->get('Campaign');
		return parent::display($tpl, $safeparams);
	}
}
