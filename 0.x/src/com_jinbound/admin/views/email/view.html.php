<?php
/**
 * @package		jInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('_JEXEC') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundItemView', 'views/baseviewitem');

class JInboundViewEmail extends JInboundItemView
{
	public function addToolBar()
	{
		parent::addToolBar();
		JToolbarHelper::custom('email.test', 'mail.png', 'mail_f2.png', 'COM_JINBOUND_EMAIL_TEST', false);
	}
}
