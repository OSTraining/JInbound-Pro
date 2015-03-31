<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('_JEXEC') or die;

class ModJInboundCTAModuleAdapter extends ModJInboundCTAAdapter
{
	/**
	 * Renders a module
	 * @return string
	 */
	public function render()
	{
		if ($this->is_alt)
		{
			$id = $this->params->get('cta_alt_mode_module');
		}
		else
		{
			$id = $this->params->get('cta_mode_module');
		}
		$renderer = JFactory::getDocument()->loadRenderer('module');
		$module   = $this->getModule($id);
		$params   = array('style' => 'none');
		if (is_object($module))
		{
			echo $renderer->render($module, $params);
		}
	}
	
	protected function getModule($id)
	{
		if (!$id)
		{
			return false;
		}
		$db = JFactory::getDbo();
		return $db->setQuery($db->getQuery(true)
			->select('*')
			->from('#__modules')
			->where('id = ' . intval($id))
		)->loadObject();
	}
}
