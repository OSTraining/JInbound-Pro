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
	
	private $_registryColumns = array('formbuilder');
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		// main query
		$query = $db->getQuery(true)
			// Select the required fields from the table.
			->select('Page.*, Category.title as category_name')
			->from('#__jinbound_pages AS Page')
			->leftJoin('#__categories AS Category ON Category.id = Page.category')
			->select('COUNT(Lead.id) AS submissions')
			->leftJoin('#__jinbound_leads AS Lead ON Lead.page_id = Page.id')
			->group('Page.id')
		;
		
		// add author to query
		$this->appendAuthorToQuery($query, 'Page');
		$this->filterSearchQuery($query, $this->getState('filter.search'), 'Page');
		
		// Add the list ordering clause.
		$orderCol = trim($this->state->get('list.ordering'));
		$orderDirn = trim($this->state->get('list.direction'));
		if (strlen($orderCol)) {
			$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		}
		
		return $query;
	}
	
	public function getCategoriesOptions() {
		$query = $this->getDbo()->getQuery(true)
		->select('Category.id AS value, Category.title as text')
		->from('#__categories AS Category')
		->where('Category.published = 1')
		->where('Category.extension = ' . $this->getDbo()->quote(JInbound::COM))
		->order('Category.lft ASC')
		->order('Category.title ASC')
		->group('Category.id')
		;
		return $this->getOptionsFromQuery($query, JText::_('COM_JINBOUND_SELECT_CATEGORY'));
	}
	
	public function getCampaignsOptions() {
		$query = $this->getDbo()->getQuery(true)
		->select('Campaign.id AS value, Campaign.name as text')
		->from('#__jinbound_campaigns AS Campaign')
		->where('Campaign.published = 1')
		->group('Campaign.id')
		;
		return $this->getOptionsFromQuery($query, JText::_('COM_JINBOUND_SELECT_CAMPAIGN'));
	}
}
