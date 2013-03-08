<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundListModel', 'models/basemodellist');

/**
 * This models supports retrieving lists of pages.
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelPages extends JInboundListModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $context  = 'com_jinbound.pages';
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		// main query
		$query = $db->getQuery(true)
		// Select the required fields from the table.
		->select('Page.*, Category.name as category_name')
		->from('#__jinbound_pages AS Page')
		->leftJoin('#__jinbound_categories AS Category ON Category.id = Page.category')
		;
		return $query;
	}
	
	public function getCategoryOptions() {
		$query = $this->getDbo()->getQuery(true)
		->select('Category.id AS value, Category.name as text')
		->from('#__jinbound_categories AS Category')
		->where('Category.published = 1')
		->order('Category.name ASC')
		;
		return $this->getOptionsFromQuery($query, JText::_('COM_JINBOUND_SELECT_CATEGORY'));
	}
}
