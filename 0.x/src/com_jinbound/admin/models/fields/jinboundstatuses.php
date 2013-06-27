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
		
		$db = JFactory::getDbo();
		
		$db->setQuery($db->getQuery(true)
			->select('id AS value, name AS text')
			->from('#__jinbound_lead_statuses')
			->where('published = 1')
		);
		
		try {
			$options = $db->loadObjectList();
		}
		catch (Exception $e) {
			$options = array();
		}
		
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}