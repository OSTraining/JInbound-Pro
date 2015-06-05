<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('_JEXEC') or die;

// initialise
$campaign_id = (int) $params->get('campaignid', 0);
$form_id     = (int) $params->get('formid', 0);
if (empty($form_id) || empty($campaign_id))
{
	return false;
}

// check that jinbound is installed
$jinbound_base = JPATH_ADMINISTRATOR . '/components/com_jinbound';
if (!JFolder::exists($jinbound_base))
{
	return false;
}

// load required classes
JLoader::register('JInbound', "$jinbound_base/libraries/jinbound.php");
JInbound::registerHelper('form');
JInbound::registerHelper('url');
jimport('joomla.form.form');

// set up JForm
JForm::addFormPath("$jinbound_base/models/forms");
try
{
	$form = JForm::getInstance('jinbound_form_module', '<form><!-- --></form>', array('control' => 'jform'));
}
catch (Exception $e)
{
	return false;
}

// get the model
JModelLegacy::addIncludePath(JPATH_ROOT . '/components/com_jinbound/models', 'JInboundModel');
$model  = JModelLegacy::getInstance('Page', 'JInboundModel');

// add fields to form
$fields = JInboundHelperForm::getFields($form_id);
$model->addFieldsToForm($fields, $form, JText::_('COM_JINBOUND_FIELDSET_LEAD'));

// sanity checks
if (empty($fields) || !($form instanceof JForm))
{
	return false;
}

// create data to store in the session in order to save form
$session_name = 'mod_jinbound_form.form.' . $module->id;
JFactory::getSession()->set($session_name, (object) array(
	'campaign_id'         => $campaign_id,
	'form_id'             => $form_id,
	'page_name'           => $module->name,
	'notification_email'  => $params->get('notification_email', ''),
	'after_submit_sendto' => $params->get('after_submit_sendto', 'message'),
	'menu_item'           => $params->get('menu_item', ''),
	'send_to_url'         => $params->get('send_to_url', ''),
	'sendto_message'      => $params->get('sendto_message', ''),
	'return_url'          => JUri::root(false)
));
$form_url = JInboundHelperUrl::task('lead.save', true, array(
	'token' => $session_name
));

// render module
require JModuleHelper::getLayoutPath('mod_jinbound_form', $params->get('layout', 'default'));
