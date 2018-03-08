<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundView', 'views/baseview');

class JInboundViewUtilities extends JInboundView
{
	function display($tpl = null, $safeparams = false) {
		return parent::display($tpl, $safeparams);
	}
}