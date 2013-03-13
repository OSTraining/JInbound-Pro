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
 * This models supports retrieving reports
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelReports extends JInboundListModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $context  = 'com_jinbound.reports';
	
	protected function getListQuery()
	{
		// Create a new query object.
		$query = $this->getDbo()->getQuery(true)
		// Select the required fields from the table.
		->select('1')
		->from('#__jinbound_pages AS Page')
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
	
	/**
	 * Gets the total number of hits for all landing pages
	 * 
	 * @return integer
	 */
	public function getVisitCount() {
		$this->getDbo()->setQuery($this->getDbo()->getQuery(true)
			->select('SUM(Page.hits)')
			->from('#__jinbound_pages AS Page')
		);
		
		try {
			$count = $this->getDbo()->loadResult();
		}
		catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage());
			$count = 0;
		}
		
		return (int) $count;
	}
	
	/**
	 * Gets the total number of leads
	 * 
	 * @return integer
	 */
	public function getLeadCount() {
		$this->getDbo()->setQuery($this->getDbo()->getQuery(true)
			->select('COUNT(Lead.id)')
			->from('#__jinbound_leads AS Lead')
		);
		
		try {
			$count = $this->getDbo()->loadResult();
		}
		catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage());
			$count = 0;
		}
		
		return (int) $count;
	}
	
	public function getRecentLeads() {
		$this->getDbo()->setQuery($this->getDbo()->getQuery(true)
			->select('Contact.name AS name, Lead.created AS date, Contact.webpage AS website')
			->from('#__jinbound_leads AS Lead')
			->leftJoin('#__contact_details AS Contact ON Contact.id = Lead.contact_id')
			->group('Lead.id')
			->order('Lead.created DESC')
		);
		
		try {
			$leads = $this->getDbo()->loadObjectList();
		}
		catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage());
			$leads = array();
		}
		
		return $leads;
	}
}
