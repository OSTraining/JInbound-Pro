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
 * This models supports retrieving lists of locations.
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelPriorities extends JInboundListModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_jinbound.priorities';
	protected $context  = 'com_jinbound.priorities';
	
	/**
	 * Constructor.
	 *
	 * @param       array   An optional associative array of configuration settings.
	 * @see         JController
	 */
	function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'Priority.name'
			,	'Priority.status'
			,	'Priority.ordering'
			,	'Priority.description'
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
		// Select the required fields from the table.
		->select('Priority.*')
		->from('#__jinbound_priorities AS Priority')
		;
		// add author to query
		$this->appendAuthorToQuery($query, 'Priority');
		$this->filterSearchQuery($query, $this->getState('filter.search'), 'Priority', 'id', array('name', 'description'));
		$this->filterPublished($query, $this->getState('filter.published'), 'Priority');
		
		// Add the list ordering clause.
		$listOrdering = $this->getState('list.ordering', 'Priority.name');
		$listDirn     = $db->escape($this->getState('list.direction', 'ASC'));
		$query->order($db->escape($listOrdering) . ' ' . $listDirn);

		return $query;
	}


}
