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
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.start', 'filter_start', '', 'string');
		$this->setState('filter.start', $value);
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.end', 'filter_end', '', 'string');
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

		// main query
		$query = $db->getQuery(true)
			// main table
			->select('Lead.*')
			->from('#__jinbound_leads AS Lead')
			// join the contact
			->select('Contact.email_to AS email')
			->select('Contact.name AS name')
			->leftJoin('#__contact_details AS Contact ON Contact.id = Lead.contact_id')
			// join the form data
			->select('Page.formbuilder')
			->select('Page.formname')
			->leftJoin('#__jinbound_pages AS Page ON Page.id = Lead.page_id')
			// join the priority
			->select('Priority.name AS priority_name')
			->leftJoin('#__jinbound_priorities AS Priority ON Priority.id = Lead.priority_id')
			// join the status
			->select('Status.name AS status_name')
			->leftJoin('#__jinbound_lead_statuses AS Status ON Status.id = Lead.status_id')
			// join the campaign
			->select('Campaign.name AS campaign_name')
			->leftJoin('#__jinbound_campaigns AS Campaign ON Campaign.id = Lead.campaign_id')
			// group by lead
			->group('Lead.id')
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
