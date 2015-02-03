<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldJinboundStatuses extends JFormFieldList
{
	protected $type = 'JinboundStatuses';
	
	protected function getOptions() {
		
		$final = $this->element['final'] ? ('true' === strtolower($this->element['final'])) : false;
		
		if (!file_exists($file = JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/status.php'))
		{
			return array();
		}
		require_once $file;
		
		return array_merge(parent::getOptions(), JInboundHelperStatus::getSelectOptions($final));
	}
}