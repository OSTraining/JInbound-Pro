<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

if (!JFactory::getUser()->authorise('core.manage', 'com_jinbound')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

if (jimport('joomla.application.component.controller')) {
	$controller = JController::getInstance('JInbound');
}
else {
	jimport('legacy.controllers.legacy');
	$controller = JControllerLegacy::getInstance('JInbound');
}

// exec task
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
