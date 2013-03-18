<?php
/**
 * @version		$Id$
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
		
		$db = JFactory::getDbo();
		
		$db->setQuery($db->getQuery(true)
			->select('id AS value, name AS text')
			->from('#__jinbound_priorities')
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