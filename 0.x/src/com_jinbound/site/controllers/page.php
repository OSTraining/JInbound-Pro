<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundPageController', 'controllers/basecontrollerpage');

class JInboundControllerPage extends JInboundPageController
{
	function __construct($config = array())
	{
		$app = JFactory::getApplication();
		$pop = $app->input->get('pop', array(), 'array');
		if (is_array($pop) && !empty($pop))
		{
			$app->setUserState('com_jinbound.page.data', $pop);
		}
		parent::__construct($config);
	}
}
