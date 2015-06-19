<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JFactory::getLanguage()->load('com_jinbound', JPATH_ADMINISTRATOR);

$app = JFactory::getApplication();
$pop = $app->input->get('pop', array(), 'array');
if (is_array($pop) && !empty($pop))
{
	$app->setUserState('com_jinbound.page.data', $pop);
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
