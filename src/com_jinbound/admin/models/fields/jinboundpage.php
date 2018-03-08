<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldJinboundPage extends JFormFieldList
{
	protected $type = 'JinboundPage';
	
	protected function getOptions() {
		
		$db = JFactory::getDbo();
		
		$db->setQuery($db->getQuery(true)
			->select('id AS value, name AS text')
			->from('#__jinbound_pages')
			->where('published = 1')
			->order('name')
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