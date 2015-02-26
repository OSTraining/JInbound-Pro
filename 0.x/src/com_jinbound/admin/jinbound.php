<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$app   = JFactory::getApplication();
$input = $app->input;

if (!JFactory::getUser()->authorise('core.manage', 'com_jinbound')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// rewritten LiveUpdate code
if ('liveupdate' == $input->get('view', '')) {
	// check which liveupdate to load
	$ext    = $input->get('ext', '');
	$type   = $input->get('type', '');
	$folder = $input->get('folder', '');
	$base   = JPATH_COMPONENT_ADMINISTRATOR;
	if (!empty($ext))
	{
		switch ($type)
		{
			case 'mod':
				$base = JPATH_ROOT . '/modules/mod_' . $ext;
				break;
			case 'plg':
				$base = JPATH_ROOT . '/plugins/' . $folder . '/' . $ext;
				break;
			default:
				throw new Exception('Unknown type');
		}
	}
	require_once $base . '/liveupdate/liveupdate.php';
	LiveUpdate::handleRequest();
	return;
}

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('form');
JInbound::registerHelper('url');

if (JInboundHelperForm::needsMigration() && 'json' !== $input->get('format') && 'forms.migrate' !== $input->get('task'))
{
	$app->enqueueMessage(JInboundHelperForm::getMigrationWarning(), 'warning');
}
else if (JInboundHelperForm::needsDefaultFields())
{
	JInboundHelperForm::installDefaultFields();
	JInboundHelperForm::installDefaultForms();
}

if (jimport('joomla.application.component.controller')) {
	$controller = JController::getInstance('JInbound');
}
else {
	jimport('legacy.controllers.legacy');
	$controller = JControllerLegacy::getInstance('JInbound');
}

// exec task
$controller->execute($input->get('task'));
$controller->redirect();
