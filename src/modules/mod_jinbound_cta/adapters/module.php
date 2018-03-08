<?php
/**
 * @package		JInbound
 * @subpackage	mod_jinbound_cta
@ant_copyright_header@
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
		$id       = $this->params->get($this->pfx . 'mode_module');
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
