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
class JInboundModelCampaigns extends JInboundListModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_jinbound.campaigns';
	protected $context  = 'com_jinbound.campaigns';
	
	/**
	 * Constructor.
	 *
	 * @param       array   An optional associative array of configuration settings.
	 * @see         JController
	 */
	function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'Campaign.name'
			,	'Campaign.published'
			,	'Campaign.created'
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
		->select('Campaign.*')
		->from('#__jinbound_campaigns AS Campaign')
		;
		
		$this->appendAuthorToQuery($query, 'Campaign');
		$this->filterSearchQuery($query, $this->getState('filter.search'), 'Campaign', 'id', array('name'));
		$this->filterPublished($query, $this->getState('filter.published'), 'Campaign');
		
		// Add the list ordering clause.
		$listOrdering = $this->getState('list.ordering', 'Campaign.name');
		$listDirn     = $db->escape($this->getState('list.direction', 'ASC'));
		$query->order($db->escape($listOrdering) . ' ' . $listDirn);

		return $query;
	}
	
	public function getStatusOptions() {
		$query = $this->getDbo()->getQuery(true)
		->select('Status.name AS text, Status.id AS value')
		->from('#__jinbound_lead_statuses AS Status')
		->where('Status.published = 1')
		->order('Status.name ASC')
		;
		return $this->getOptionsFromQuery($query, JText::_('COM_JINBOUND_SELECT_STATUS'));
	}


}
