<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('_JEXEC') or die;

// initialise
$form_id = (int) $params->get('formid', 0);
if (empty($form_id))
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

// render module
require JModuleHelper::getLayoutPath('mod_jinbound_form', $params->get('layout', 'default'));
