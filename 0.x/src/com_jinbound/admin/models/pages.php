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
			,	'conversion_rate'
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
		
		$this->setState('layout.json', 'json' == $format);
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
		$id	.= ':'.$this->getState('layout.json');

		return parent::getStoreId($id);
	}
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		// main query
		$query = $db->getQuery(true)
			->select('Page.*')
			->select('Category.title AS category_name')
			->select('Campaign.name AS campaign_name')
			->select('COUNT(DISTINCT Contact.id) AS contact_submissions')
			->select('GROUP_CONCAT(DISTINCT Contact.id) AS contact_submission_ids')
			->select('COUNT(DISTINCT Submission.id) AS submissions')
			->select('GROUP_CONCAT(DISTINCT Submission.id) AS submission_ids')
			->select('COUNT(DISTINCT Conversion.contact_id) AS conversions')
			->select('GROUP_CONCAT(DISTINCT Conversion.contact_id) AS conversion_ids')
			->select('ROUND(IF(COUNT(DISTINCT Contact.id) > 0, (COUNT(DISTINCT Conversion.contact_id) / COUNT(DISTINCT Contact.id)) * 100, 0), 2) AS conversion_rate')
			->from('#__jinbound_pages AS Page')
			->leftJoin('#__categories AS Category ON Category.id = Page.category')
			->leftJoin('#__jinbound_campaigns AS Campaign ON Campaign.id = Page.campaign')
			->leftJoin('#__jinbound_contacts AS Contact ON Contact.id IN (('
				. $db->getQuery(true)
					->select('DISTINCT ContactConversion.contact_id')
					->from('#__jinbound_conversions AS ContactConversion')
					->where('ContactConversion.page_id = Page.id')
					->where('ContactConversion.published = 1')
			. '))')
			->leftJoin('#__jinbound_conversions AS Submission ON Submission.page_id = Page.id AND Submission.published = 1')
			->leftJoin('#__jinbound_contacts_statuses AS Conversion ON Conversion.campaign_id = Campaign.id AND Conversion.contact_id IN (('
				. $db->getQuery(true)
					->select('DISTINCT ConversionConversion.contact_id')
					->from('#__jinbound_conversions AS ConversionConversion')
					->where('ConversionConversion.page_id = Page.id')
					->where('ConversionConversion.published = 1')
			. ')) AND Conversion.status_id IN (('
				. $db->getQuery(true)
					->select('Status.id')
					->from('#__jinbound_lead_statuses AS Status')
					->where('Status.final = 1')
					->where('Status.published = 1')
			. '))')
			->group('Page.id')
		;
		/*
SELECT Page.*, Category.title AS category_name, Campaign.name AS campaign_name
, COUNT(DISTINCT Contact.id) AS contact_submissions
, GROUP_CONCAT(DISTINCT Contact.id) AS contact_submission_ids
, COUNT(DISTINCT Submission.id) AS submissions
, GROUP_CONCAT(DISTINCT Submission.id) AS submission_ids
, COUNT(DISTINCT Conversion.contact_id) AS conversions
, GROUP_CONCAT(DISTINCT Conversion.contact_id) AS conversion_ids
, ROUND(IF(COUNT(DISTINCT Contact.id) > 0, (COUNT(DISTINCT Conversion.contact_id) / COUNT(DISTINCT Contact.id)) * 100, 0), 2) AS conversion_rate
FROM cxsgv_jinbound_pages AS Page
LEFT JOIN cxsgv_categories AS Category
	ON Category.id = Page.category
LEFT JOIN cxsgv_jinbound_campaigns AS Campaign
	ON Campaign.id = Page.campaign
LEFT JOIN cxsgv_jinbound_contacts AS Contact
	ON Contact.id IN ((
		SELECT DISTINCT ContactConversion.contact_id
		FROM cxsgv_jinbound_conversions AS ContactConversion
		WHERE ContactConversion.page_id = Page.id
		AND ContactConversion.published = 1
	))
LEFT JOIN cxsgv_jinbound_conversions AS Submission
	ON Submission.page_id = Page.id
	AND Submission.published = 1
LEFT JOIN cxsgv_jinbound_contacts_statuses AS Conversion
	ON Conversion.campaign_id = Campaign.id
	AND Conversion.contact_id IN ((
		SELECT DISTINCT ConversionConversion.contact_id
		FROM cxsgv_jinbound_conversions AS ConversionConversion
		WHERE ConversionConversion.page_id = Page.id
		AND ConversionConversion.published = 1
	))
	AND Conversion.status_id IN ((
		SELECT Status.id
		FROM cxsgv_jinbound_lead_statuses AS Status
		WHERE Status.final = 1
		AND Status.published = 1
	))
GROUP BY Page.id
		 */
		
		// add author to query
		$this->appendAuthorToQuery($query, 'Page');
		$this->filterSearchQuery($query, $this->getState('filter.search'), 'Page', 'id', array('name', 'Category.title'));
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
				$query->where('Submission.created > ' . $db->quote($date->format('Y-m-d h:i:s')));
				$query->where('Contact.created > ' . $db->quote($date->format('Y-m-d h:i:s')));
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
				$query->where('Submission.created < ' . $db->quote($date->format('Y-m-d h:i:s')));
				$query->where('Contact.created < ' . $db->quote($date->format('Y-m-d h:i:s')));
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
