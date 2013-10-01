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
 * This models supports retrieving lists of statuses.
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelStatuses extends JInboundListModel
{
	/**
	 * Constructor.
	 *
	 * @param       array   An optional associative array of configuration settings.
	 * @see         JController
	 */
	function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'Status.name'
			,	'Status.published'
			,	'Status.default'
			,	'Status.final'
			,	'Status.ordering'
			,	'Status.description'
			);
		}
		
		parent::__construct($config);
	}
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $context  = 'com_jinbound.statuses';
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		// main query
		$query = $db->getQuery(true)
			// Select the required fields from the table.
			->select('Status.*')
			->from('#__jinbound_lead_statuses AS Status')
		;
		// add author to query
		$this->appendAuthorToQuery($query, 'Status');
		$this->filterSearchQuery($query, $this->getState('filter.search'), 'Status');
		$this->filterPublished($query, $this->getState('filter.published'), 'Status');
		
		// Add the list ordering clause.
		$listOrdering = $this->getState('list.ordering', 'Status.name');
		$listDirn     = $db->escape($this->getState('list.direction', 'ASC'));
		$query->order($db->escape($listOrdering) . ' ' . $listDirn);
		
		return $query;
	}
}
