<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
		if ($this->is_alt)
		{
			$position = $this->params->get('cta_alt_mode_modules');
		}
		else
		{
			$position = $this->params->get('cta_mode_modules');
		}
		$renderer = JFactory::getDocument()->loadRenderer('module');
		$modules  = JModuleHelper::getModules($position);
		$params   = array('style' => 'none');
		foreach ($modules as $module)
		{
			echo $renderer->render($module, $params);
		}
	}
}
