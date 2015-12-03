<?php
/**
 * @package		JInbound
 * @subpackage	mod_jinbound_form_free
@ant_copyright_header@
 */

defined('_JEXEC') or die;

// load required classes
JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/jinbound.php');
JInbound::registerHelper('form');
JInbound::registerHelper('module');
JInbound::registerHelper('url');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.form.form');
jimport('joomla.application.module.helper');

abstract class modJinboundFormFreeHelper
{	
	/**
	 * Fetches the module object needed to operate
	 * 
	 * This method is deprecated, use JInboundHelperModule::getModuleObject instead
	 * 
	 * @return stdClass
	 * @throws UnexpectedValueException
	 * @deprecated
	 */
	static public function getModuleObject($module_id = null)
	{
		return JInboundHelperModule::getModuleObject($module_id);
	}
	
	static public function getFormData(&$module, &$params)
	{
		// initialise
		$campaign_id = (int) $params->get('campaignid', 0);
		$form_id     = (int) $params->get('formid', 0);
		if (empty($form_id) || empty($campaign_id))
		{
			return false;
		}
		return (object) array(
			'campaign_id'         => $campaign_id,
			'form_id'             => $form_id,
			'page_name'           => $module->name,
			'notification_email'  => $params->get('notification_email', ''),
			'after_submit_sendto' => $params->get('after_submit_sendto', 'message'),
			'menu_item'           => $params->get('menu_item', ''),
			'send_to_url'         => $params->get('send_to_url', ''),
			'sendto_message'      => $params->get('sendto_message', ''),
			'return_url'          => $params->get('return_url', JUri::root(false))
		);
	}
	
	static public function getForm(&$module, &$params)
	{
		return JInboundHelperForm::getJinboundForm((int) $params->get('formid', 0), array('control' => 'mod_jinbound_form_free_' . $module->id));
	}
}
