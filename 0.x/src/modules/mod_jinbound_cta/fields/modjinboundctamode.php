<?php
/**
 * @package		jInbound
 * @subpackage	com_jinbound
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
		static $isset;
		if (is_null($isset))
		{
			JFactory::getDocument()->addScript(JUri::root() . 'media/mod_jinbound_cta/js/admin.js');
			$isset = true;
		}
	}
}
