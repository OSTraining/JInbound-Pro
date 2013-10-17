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
 * This models supports retrieving lists of leads.
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelLeads extends JInboundListModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $context  = 'com_jinbound.leads';
	
	/**
	 * Constructor.
	 *
	 * @param       array   An optional associative array of configuration settings.
	 * @see         JController
	 */
	function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'Contact.name'
			,	'Lead.published'
			,	'Lead.created'
			,	'Page.formname'
			,	'Priority.name'
			,	'Campaign.name'
			,	'Status.name'
			,	'Lead.note'
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
		$id	.= ':'.$this->getState('filter.start');
		$id	.= ':'.$this->getState('filter.end');

		return parent::getStoreId($id);
	}
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		
		// select columns
		$query = $db->getQuery(true)
			->select('Lead.*')
			->select('Contact.email_to AS email')
			->select('Contact.name AS name')
			->select('Page.formbuilder')
			->select('Page.formname')
			->select('Priority.name AS priority_name')
			->select('Status.name AS status_name')
			->select('Campaign.name AS campaign_name')
		;
		
		// group by contact but only on the main leads page
		if ('leads' == JFactory::getApplication()->input->get('view')) {
			$query
			->select('GROUP_CONCAT(Lead.id) AS lead_ids')
			->from('#__contact_details AS Contact')
			->leftJoin('#__jinbound_leads AS Lead ON Contact.id = Lead.contact_id')
			->group('Contact.id')
			;
		}
		// group by lead
		else {
			$query
			->select('Lead.id AS lead_ids') // just to keep columns the same
			->from('#__jinbound_leads AS Lead')
			->leftJoin('#__contact_details AS Contact ON Contact.id = Lead.contact_id')
			->group('Lead.id')
			;
		}
		
		// join in the remaining tables
		// this has to be done after the initial Lead/Contact from/join to prevent sql errors
		$query
		->leftJoin('#__jinbound_pages AS Page ON Page.id = Lead.page_id')
		->leftJoin('#__jinbound_priorities AS Priority ON Priority.id = Lead.priority_id')
		->leftJoin('#__jinbound_lead_statuses AS Status ON Status.id = Lead.status_id')
		->leftJoin('#__jinbound_campaigns AS Campaign ON Campaign.id = Lead.campaign_id')
		;
		
		// add author to query
		$this->appendAuthorToQuery($query, 'Lead');
		// filter query
		$this->filterSearchQuery($query, $this->getState('filter.search'), 'Lead');
		$this->filterPublished($query, $this->getState('filter.published'), 'Lead');
		
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
			}
		}
		
		// Add the list ordering clause.
		$listOrdering = $this->getState('list.ordering', 'Lead.created');
		$listDirn     = $db->escape($this->getState('list.direction', 'ASC'));
		$query->order($db->escape($listOrdering) . ' ' . $listDirn);

		return $query;
	}
}
