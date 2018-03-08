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

// load required classes
JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/jinbound.php');
JInbound::registerHelper('url');

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
			$document = JFactory::getDocument();
			if (!JInbound::version()->isCompatible('3.0.0'))
			{
				// force jQuery
				$document->addScript(JInboundHelperUrl::media() . '/js/jquery-1.9.1.min.js');
			}
			$document->addScript(JUri::root() . 'media/mod_jinbound_cta/js/admin.js');
			$mod_jinbound_cta_script_loaded = true;
		}
	}
}
