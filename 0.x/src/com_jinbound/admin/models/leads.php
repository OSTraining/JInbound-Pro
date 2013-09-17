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
			,	'Status.name'
			,	'Lead.note'
			);
		}
		
		parent::__construct($config);
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
			// group by lead
			->group('Lead.id')
		;
		
		// add author to query
		$this->appendAuthorToQuery($query, 'Lead');
		$this->filterSearchQuery($query, $this->getState('filter.search'), 'Lead');
		
		// Add the list ordering clause.
		$listOrdering = $this->getState('list.ordering', 'Lead.created');
		$listDirn     = $db->escape($this->getState('list.direction', 'ASC'));
		$query->order($db->escape($listOrdering) . ' ' . $listDirn);

		return $query;
	}
}
