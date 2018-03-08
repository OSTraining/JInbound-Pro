<?php
/**
 * @package		JInbound
 * @subpackage	mod_jinbound_form_free
@ant_copyright_header@
 */

defined('_JEXEC') or die;

// get helper class
require_once dirname(__FILE__) . '/helper.php';

if (version_compare(JInbound::VERSION, '2.1.6', '<='))
{
	echo JText::_('MOD_JINBOUND_FORM_FREE_REQUIRES_JINBOUND_2_1_6');
	return;
}

// initialise
$form = modJinboundFormFreeHelper::getForm($module, $params);
$data = modJinboundFormFreeHelper::getFormData($module, $params);
$btn  = $params->get('submit_text', 'JSUBMIT');
$sfx  = $params->get('moduleclass_sfx', '');

if (false === $form || false === $data)
{
	return false;
}

// coerce empty button text
if (empty($btn))
{
	$btn = 'JSUBMIT';
}

// create data to store in the session in order to save form
$session_name = 'mod_jinbound_form_free.form.' . $module->id;
JFactory::getSession()->set($session_name, $data);
$form_url = JInboundHelperUrl::toFull(JInboundHelperUrl::task('lead.save', true, array(
	'token' => $session_name
)));

// render module
require JModuleHelper::getLayoutPath('mod_jinbound_form_free', $params->get('layout', 'default'));
