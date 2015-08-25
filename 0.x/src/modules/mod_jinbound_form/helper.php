<?php
/**
 * @package		JInbound
 * @subpackage	mod_jinbound_form
@ant_copyright_header@
 */

defined('_JEXEC') or die;

// load required classes
JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/jinbound.php');
JInbound::registerHelper('form');
JInbound::registerHelper('url');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.form.form');
jimport('joomla.application.module.helper');

abstract class modJinboundFormHelper
{
	static public function getFormAjax()
	{
		$input = JFactory::getApplication()->input;
		$id    = $input->getInt('id', 0);
		if (!headers_sent())
		{
			$origin = filter_input(INPUT_SERVER, 'HTTP_ORIGIN', FILTER_SANITIZE_URL);
			header('Access-Control-Allow-Origin: ' . $origin);
			header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
			header('Access-Control-Max-Age: 1000');
			header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
		}
		$module  = static::getModuleObject();
		$attribs = array('style' => 'none');
		return array(
			'form'    => JModuleHelper::renderModule($module, $attribs),
			'style'   => false,
			'session' => JFactory::getSession()->get('mod_jinbound_form.form.' . $id)
		);
	}
	
	/**
	 * Fetches the module object needed to operate
	 * 
	 * @return stdClass
	 * @throws UnexpectedValueException
	 */
	static public function getModuleObject($module_id = null)
	{
		// init
		$input  = JFactory::getApplication()->input;
		$db     = JFactory::getDbo();
		$id     = is_null($module_id) ? $input->getInt('id', 0) : $module_id;
		$return = base64_decode($input->getBase64('return_url', base64_encode(JUri::root(true))));
		// there must be a module id to continue
		if (empty($id))
		{
			throw new UnexpectedValueException('Module not found');
		}
		// load the module by title
		$title = $db->setQuery($db->getQuery(true)
			->select('title')
			->from('#__modules')
			->where('id = ' . $id)
		)->loadResult();
		if (empty($title))
		{
			throw new UnexpectedValueException('Module not found');
		}
		// use the module helper to load the module object
		$module = JModuleHelper::getModule('mod_jinbound_form', $title);
		if ($module->id != $id)
		{
			throw new UnexpectedValueException('Module not found');
		}
		// fix the params
		if (!is_a($module->params, 'Registry'))
		{
			$registry = JInbound::registry($module->params);
			$module->params = $registry;
		}
		// set return url if desired
		if (!empty($return))
		{
			$module->params->set('return_url', $return);
		}
		return $module;
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
	
	static public function getForm(&$params)
	{
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
		
		return $form;
	}
}
