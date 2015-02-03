<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldJinboundPriorities extends JFormFieldList
{
	protected $type = 'JinboundPriorities';
	
	protected function getOptions() {
		
		if (!file_exists($file = JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/priority.php'))
		{
			return array();
		}
		require_once $file;
		
		return array_merge(parent::getOptions(), JInboundHelperPriority::getSelectOptions());
	}
}