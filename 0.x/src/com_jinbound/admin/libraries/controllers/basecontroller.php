<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

if (jimport('joomla.application.component.controller')) {
	class JInboundBaseController extends JController
	{
		
	}
}
else {
	jimport('legacy.controller.legacy');
	class JInboundBaseController extends JControllerLegacy
	{
		
	}
}