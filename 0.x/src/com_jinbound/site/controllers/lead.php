<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundController', 'controllers/basecontrollerpage');

class JInboundControllerPage extends JInboundPageController
{
	public function savelead() {
		echo '<pre>' . htmlspecialchars(print_r($_REQUEST,1)) . '</pre>'; die;
	}
}
