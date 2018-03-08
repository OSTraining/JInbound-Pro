<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

if (jimport('joomla.application.component.model')) {
	class JInboundBaseModelCommon extends JModel
	{
		static public function addIncludePath($path = '', $prefix = '') {
			return parent::addIncludePath($path, $prefix);
		}
	}
}
else {
	jimport('legacy.model.legacy');
	class JInboundBaseModelCommon extends JModelLegacy
	{
		static public function addIncludePath($path = '', $prefix = '') {
			return parent::addIncludePath($path, $prefix);
		}
	}
}

class JInboundBaseModel extends JInboundBaseModelCommon
{
}
