<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
jimport('joomla.application.module.helper');

abstract class JInboundHelperModule
{
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
			throw new UnexpectedValueException('Module not found (' . $module->id . ', ' . $id . ')');
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
}
