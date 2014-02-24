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
		
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
		->select('id AS value, name AS text')
		->from('#__jinbound_lead_statuses')
		->where('published = 1')
		;
		
		if ($final) {
			$query->where('final = 1');
		}
		
		$db->setQuery($query);
		
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