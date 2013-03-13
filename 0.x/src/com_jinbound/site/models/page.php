<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;


JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundAdminModel', 'models/basemodeladmin');

class JInboundModelPage extends JInboundAdminModel
{
	public $_context = 'com_jinbound.page';
}
