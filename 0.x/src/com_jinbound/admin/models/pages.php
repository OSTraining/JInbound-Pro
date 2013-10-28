<?php
/**
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
	
	/**
	 * Constructor.
	 *
	 * @param       array   An optional associative array of configuration settings.
	 * @see         JController
	 */
	function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'Page.name'
			,	'Page.published'
			,	'Page.category'
			,	'Page.hits'
			,	'submissions'
			,	'conversions'
			);
		}
		
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);
		
		$app    = JFactory::getApplication();
		$format = $app->input->get('format', '', 'cmd');
		
		foreach (array('category', 'campaign') as $var) {
			$this->setState('filter.' . $var, $this->getUserStateFromRequest($this->context.'.filter.'.$var, 'filter_'.$var, '', 'string'));
		}
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.start', 'filter_start', '', 'string');
		if ('json' != $format) $value = '';
		$this->setState('filter.start', $value);
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.end', 'filter_end', '', 'string');
		if ('json' != $format) $value = '';
		$this->setState('filter.end', $value);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.category');
		$id	.= ':'.$this->getState('filter.campaign');
		$id	.= ':'.$this->getState('filter.start');
		$id	.= ':'.$this->getState('filter.end');

		return parent::getStoreId($id);
	}
	
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
			// add on the conversion count by rejoining the leads based on final status
			->select('COUNT(DISTINCT Conversion.id) AS conversions')
			->select('GROUP_CONCAT(Conversion.id) AS conversion_ids')
			->innerJoin('#__jinbound_leads AS Conversion ON Conversion.page_id = Page.id AND Conversion.status_id IN ((SELECT Status.id FROM #__jinbound_lead_statuses AS Status WHERE Status.final = 1))')
			// add on the total submissions by counting leads
			->select('COUNT(DISTINCT Lead.id) AS submissions')
			->select('GROUP_CONCAT(Lead.id) AS submission_ids')
			->innerJoin('#__jinbound_leads AS Lead ON Lead.page_id = Page.id AND (Conversion.status_id IN ((SELECT Status.id FROM #__jinbound_lead_statuses AS Status WHERE Status.active = 1)) OR Conversion.status_id = 0)')
			// add on the conversion rate based on submissions
			->select('ROUND(IF(COUNT(DISTINCT Lead.id) > 0, (COUNT(DISTINCT Conversion.id) / COUNT(DISTINCT Lead.id)) * 100, 0), 2) AS conversion_rate')
			// group by page
			->group('Page.id')
		;
		
		// add author to query
		$this->appendAuthorToQuery($query, 'Page');
		$this->filterSearchQuery($query, $this->getState('filter.search'), 'Page', 'id', array('Page.name', 'Category.title'));
		$this->filterPublished($query, $this->getState('filter.published'), 'Page');
		// other filters
		foreach (array('category', 'campaign') as $column) {
			$filter = $this->getState('filter.' . $column);
			if (!empty($filter)) {
				$query->where('Page.' . $column . ' = ' . (int) $filter);
			}
		}
		
		$value = $this->getState('filter.start');
		if (!empty($value)) {
			try {
				$date = new DateTime($value);
			}
			catch (Exception $e) {
				$date = false;
			}
			if ($date) {
				$query->where('Lead.created > ' . $db->quote($date->format('Y-m-d h:i:s')));
				$query->where('Conversion.created > ' . $db->quote($date->format('Y-m-d h:i:s')));
			}
		}
		
		$value = $this->getState('filter.end');
		if (!empty($value)) {
			try {
				$date = new DateTime($value);
			}
			catch (Exception $e) {
				$date = false;
			}
			if ($date) {
				$query->where('Lead.created < ' . $db->quote($date->format('Y-m-d h:i:s')));
				$query->where('Conversion.created < ' . $db->quote($date->format('Y-m-d h:i:s')));
			}
		}
		
		// Add the list ordering clause.
		$listOrdering = $this->getState('list.ordering', 'Page.name');
		$listDirn     = $db->escape($this->getState('list.direction', 'ASC'));
		$query->order($db->escape($listOrdering) . ' ' . $listDirn);
		
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
