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

class JFormFieldModJInboundCTAModule extends JFormFieldList
{
	public $type = 'ModJInboundCTAModule';
	
	protected function getOptions()
	{
		$db = JFactory::getDbo();
		try
		{
			$options = $db->setQuery($db->getQuery(true)
				->select('id AS value, title AS text')
				->from('#__modules')
				->where('published = 1')
				->order('title ASC')
			)->loadObjectList();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		if (!is_array($options))
		{
			$options = array();
		}
		return array_merge(parent::getOptions(), $options);
	}
}
