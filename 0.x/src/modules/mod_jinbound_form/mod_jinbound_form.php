<?php
/**
 * @package		JInbound
 * @subpackage	mod_jinbound_form
@ant_copyright_header@
 */

defined('_JEXEC') or die;

// get helper class
require_once dirname(__FILE__) . '/helper.php';

// initialise
$form = modJinboundFormHelper::getForm($params);
$data = modJinboundFormHelper::getFormData($module, $params);

if (false === $form || false === $data)
{
	return false;
}

// create data to store in the session in order to save form
$session_name = 'mod_jinbound_form.form.' . $module->id;
JFactory::getSession()->set($session_name, $data);
$form_url = JInboundHelperUrl::toFull(JInboundHelperUrl::task('lead.save', true, array(
	'token' => $session_name
)));

// render module
require JModuleHelper::getLayoutPath('mod_jinbound_form', $params->get('layout', 'default'));
