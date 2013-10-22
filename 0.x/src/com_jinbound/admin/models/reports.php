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
			->select('Lead.id AS id')
			->select('Contact.id AS contact_id')
			->select('Page.id AS page_id')
			->select('Contact.name AS name')
			->select('Lead.created AS date')
			->select('Lead.formdata AS formdata')
			->select('Page.formname AS formname')
			->select('Contact.webpage AS website')
			->from('#__jinbound_leads AS Lead')
			->leftJoin('#__contact_details AS Contact ON Contact.id = Lead.contact_id')
			->leftJoin('#__jinbound_pages AS Page ON Page.id = Lead.page_id')
			->group('Contact.id')
			->group('Lead.id')
			->order('Lead.created ASC')
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
	
	public function getTopLandingPages() {
		$this->getDbo()->setQuery($this->getDbo()->getQuery(true)
			->select('Page.id AS id')
			->select('Lead.id AS lead_id')
			->select('Page.name AS name')
			->select('Page.hits AS hits')
			->select('COUNT(Lead.id) AS conversions')
			->select('IF(Page.hits > 0, (COUNT(Lead.id) / Page.hits) * 100, 0) AS conversion_rate')
			->from('#__jinbound_pages AS Page')
			->leftJoin('#__jinbound_leads AS Lead ON Lead.page_id = Lead.id')
			//->leftJoin('#__jinbound_statuses AS Status ON Status.id = Lead.status_id AND Status.final = 1')
			->group('Page.id')
			->order('Page.hits DESC')
		);
		
		try {
			$pages = $this->getDbo()->loadObjectList();
		}
		catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage());
			$pages = array();
		}
		
		return $pages;
	}
	
	public function getConversionCount() {
		static $count;
		
		if (is_null($count)) {
			$this->getDbo()->setQuery($this->getDbo()->getQuery(true)
				->select('COUNT(Lead.id) AS conversions')
				->from('#__jinbound_leads AS Lead')
				->leftJoin('#__jinbound_lead_statuses AS Status ON Lead.status_id = Status.id')
				->where('Status.final = 1')
				->group('Lead.id')
			);
			
			try {
				$count = $this->getDbo()->loadResult();
			}
			catch (Exception $e) {
				JFactory::getApplication()->enqueueMessage($e->getMessage());
				$count = 0;
			}
		}
		
		return (int) $count;
	}
	
	public function getConversionRate() {
		static $rate;
		
		if (is_null($rate)) {
			$count = (int) $this->getConversionCount();
			$hits  = (int) $this->getVisitCount();
			if (0 < $hits) {
				$rate = ($count / $hits) * 100; 
			}
			else {
				$rate = 0;
			}
			
			$rate = number_format($rate, 2);
		}
		
		return $rate;
	}
	
	public function getPublishedStatus() {
		return false;
	}
	
}
