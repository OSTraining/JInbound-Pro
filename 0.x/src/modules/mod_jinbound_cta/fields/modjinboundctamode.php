<?php
/**
 * @package		JInbound
 * @subpackage	mod_jinbound_cta
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

JFormHelper::loadFieldClass('list');

class JFormFieldModJInboundCTAMode extends JFormFieldList
{
	public $type = 'ModJInboundCTAMode';

	protected function getInput()
	{
		$this->insertScript();
		return parent::getInput();
	}
	
	protected function insertScript()
	{
		global $mod_jinbound_cta_script_loaded;
		if (is_null($mod_jinbound_cta_script_loaded))
		{
			JFactory::getDocument()->addScript(JUri::root() . 'media/mod_jinbound_cta/js/admin.js');
			$mod_jinbound_cta_script_loaded = true;
		}
	}
}
