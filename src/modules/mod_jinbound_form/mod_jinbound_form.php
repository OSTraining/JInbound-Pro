<?php
/**
 * @package		JInbound
 * @subpackage	mod_jinbound_form
@ant_copyright_header@
 */

defined('_JEXEC') or die;

// get helper class
require_once dirname(__FILE__) . '/helper.php';

if (version_compare(JInbound::VERSION, '2.1.6', '<='))
{
	echo JText::_('MOD_JINBOUND_FORM_REQUIRES_JINBOUND_2_1_6');
	return;
}

// initialise
$form = modJinboundFormHelper::getForm($module, $params);
$data = modJinboundFormHelper::getFormData($module, $params);
$btn  = $params->get('submit_text', 'JSUBMIT');
$sfx  = $params->get('moduleclass_sfx', '');
$css  = $params->get('extra_css', '');
$doc  = JFactory::getDocument();

if (false === $form || false === $data)
{
	return false;
}

// coerce empty button text
if (empty($btn))
{
	$btn = 'JSUBMIT';
}

// add extra css
if (method_exists($doc, 'addStyleDeclaration') && !empty($css))
{
	$doc->addStyleDeclaration($css);
}

// create data to store in the session in order to save form
$session_name = 'mod_jinbound_form.form.' . $module->id;
JFactory::getSession()->set($session_name, $data);
$form_url = JInboundHelperUrl::toFull(JInboundHelperUrl::task('lead.save', true, array(
	'token' => $session_name
)));

// render module
require JModuleHelper::getLayoutPath('mod_jinbound_form', $params->get('layout', 'default'));
