<?php
/**
 * @package		JInbound
 * @subpackage	mod_jinbound_cta
@ant_copyright_header@
 */

defined('_JEXEC') or die;

class ModJInboundCTAModulesAdapter extends ModJInboundCTAAdapter
{
	/**
	 * Renders a module position
	 * @return string
	 */
	public function render()
	{
		$position = $this->params->get($this->pfx . 'mode_modules');
		$renderer = JFactory::getDocument()->loadRenderer('module');
		$modules  = JModuleHelper::getModules($position);
		$params   = array('style' => 'none');
		foreach ($modules as $module)
		{
			echo $renderer->render($module, $params);
		}
	}
}
