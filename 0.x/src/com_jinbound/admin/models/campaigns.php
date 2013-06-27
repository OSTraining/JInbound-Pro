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
	protected $context  = 'com_jinbound.campaigns';
	
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
		$this->filterSearchQuery($query, $this->getState('filter.search'), 'Campaign', 'id', array('Campaign.name'));

		// Add the list ordering clause.
		$orderCol = trim($this->state->get('list.ordering'));
		$orderDirn = trim($this->state->get('list.direction'));
		if (strlen($orderCol)) {
			$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		}

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
