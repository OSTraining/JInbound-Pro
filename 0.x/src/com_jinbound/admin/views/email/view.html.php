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
		$icon = 'send';
		if (JInbound::version()->isCompatible('3.0.0'))
		{
			$icon = 'mail';
		}
		JToolbarHelper::custom('email.test', "{$icon}.png", "{$icon}_f2.png", 'COM_JINBOUND_EMAIL_TEST', false);
	}
}
