<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundListModel', 'models/basemodellist');

JInbound::registerHelper('filter');
JInbound::registerHelper('path');
JInbound::registerHelper('url');

jimport('joomla.form.form');

require_once dirname(__FILE__) . '/contacts.php';
require_once dirname(__FILE__) . '/emails.php';
require_once dirname(__FILE__) . '/pages.php';

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
	
	protected $frequencies = array(
		'1 DAY', '1 WEEK', '2 WEEK', '1 MONTH', '2 MONTH', '3 MONTH', '6 MONTH', '1 YEAR'
	);

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);
		
		$app    = JFactory::getApplication();
		
		foreach (array('page', 'campaign', 'start', 'end', 'status', 'priority') as $var) {
			$this->setState('filter.' . $var, $this->getUserStateFromRequest($this->context.'.filter.'.$var, 'filter_'.$var, '', 'string'));
		}
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
		$id	.= ':'.serialize($this->getState('filter.campaign'));
		$id	.= ':'.serialize($this->getState('filter.page'));
		$id	.= ':'.serialize($this->getState('filter.start'));
		$id	.= ':'.serialize($this->getState('filter.end'));
		$id	.= ':'.serialize($this->getState('filter.status'));
		$id	.= ':'.serialize($this->getState('filter.priority'));

		return parent::getStoreId($id);
	}
	
	protected function getListQuery()
	{
		// Create a new query object.
		$query = $this->getDbo()->getQuery(true)
		// Select the required fields from the table.
		->select('1')
		->from('#__jinbound_pages AS Page')
		;
		
		return $query;
	}
	
	/**
	 * Gets the total number of hits for all landing pages
	 * 
	 * @return integer
	 */
	public function getVisitCount() {
		$query = $this->getDbo()->getQuery(true)
			->select('SUM(PageHits.hits)')
			->from('#__jinbound_landing_pages_hits AS PageHits')
			->leftJoin('#__jinbound_pages AS Page ON Page.id = PageHits.page_id')
			->where('Page.published = 1')
		;
		
		
		$this->getDbo()->setQuery($query);
		
		try {
			$count = $this->getDbo()->loadResult();
		}
		catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			$count = 0;
		}
		
		return (int) $count;
	}
	
	/**
	 * Gets the number of active contacts
	 * 
	 * @return integer
	 */
	public function getContactsCount()
	{
		$query = $this->getDbo()->getQuery(true)
			->select('COUNT(DISTINCT Contact.id)')
			->from('#__jinbound_contacts AS Contact')
			->leftJoin('(' . $this->getDbo()->getQuery(true)
				->select('s1.*')
				->from('#__jinbound_contacts_statuses AS s1')
				->leftJoin('#__jinbound_contacts_statuses AS s2 ON s1.contact_id = s2.contact_id AND s1.campaign_id = s2.campaign_id AND s1.created < s2.created')
				->where('s2.contact_id IS NULL')
			. ') AS ContactStatus ON (ContactStatus.contact_id = Contact.id)')
			->leftJoin('#__jinbound_lead_statuses AS Status ON ContactStatus.status_id = Status.id')
			->where('(Status.active = 1 OR Status.active IS NULL)') // users with no status are probably new and something went wonky
			->where('Contact.published = 1')
		;
		
		try
		{
			$count = $this->getDbo()->setQuery($query)->loadResult();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			$count = 0;
		}
		return (int) $count;
	}
	
	/**
	 * gets the number of active conversions
	 * 
	 * @return integer
	 */
	public function getConversionsCount()
	{
		$state = $this->getState();
		$start = $state->get('filter.start', null);
		$end   = $state->get('filter.end', null);
		$conversions = $this->getConversionsByDate($start, $end);
		$count = 0;
		foreach ($conversions as $conversion)
		{
			$count += (int) $conversion[1];
		}
		return $count;
	}
	
	/**
	 * Gets a list of recent contacts
	 * 
	 * @return array
	 */
	public function getRecentContacts()
	{
		$app       = JFactory::getApplication();
		$input     = $app->input;
		$start     = $input->get('filter_start', '', 'string');
		$end       = $input->get('filter_end', '', 'string');
		$campaign  = $input->get('filter_campaign', '', 'string');
		$page      = $input->get('filter_page', '', 'string');
		$status    = $input->get('filter_status', '', 'string');
		$priority  = $input->get('filter_priority', '', 'string');
		$published = $input->get('filter_published', '', 'string');
		$query     = $this->getDbo()->getQuery(true)
			->select('Contact.id AS id')
			->select('Contact.created AS date')
			->select('CONCAT_WS(' . $this->getDbo()->quote(' ') . ', Contact.first_name, Contact.last_name) AS name')
			->select('Conversion.formdata AS formdata')
			->select('Page.id AS page_id')
			->select('Page.formname AS formname')
			->select('Contact.website AS website')
			->from('#__jinbound_contacts AS Contact')
			->leftJoin('#__jinbound_conversions AS Conversion ON Contact.id = Conversion.contact_id')
			->leftJoin('#__jinbound_pages AS Page ON Conversion.page_id = Page.id')
			->where('Contact.published = 1')
			->where('Conversion.published = 1')
			->where('Page.published = 1')
			->group('Contact.id')
			->group('Conversion.id')
			->order('Contact.created ASC')
		;
		
		if (!empty($campaign))
		{
			$query->where('Contact.id IN (('
				. $this->getDbo()->getQuery(true)
				->select('ContactCampaign.contact_id')
				->from('#__jinbound_contacts_campaigns AS ContactCampaign')
				->where('ContactCampaign.campaign_id = ' . (int) $campaign)
				->where('ContactCampaign.enabled = 1')
				. '))');
		}
		
		if (!empty($page))
		{
			$query->where('Page.id = ' . (int) $page);
		}
		
		if (!empty($status))
		{
			// join in only the latest status
			$query->leftJoin('('
				. $this->getDbo()->getQuery(true)
					->select('s1.*')
					->from('#__jinbound_contacts_statuses AS s1')
					->leftJoin('#__jinbound_contacts_statuses AS s2 ON s1.contact_id = s2.contact_id AND s1.campaign_id = s2.campaign_id AND s1.created < s2.created')
					->where('s2.contact_id IS NULL')
				. ') AS ContactStatus ON ContactStatus.campaign_id = Page.campaign AND ContactStatus.contact_id = Contact.id'
			)->where('ContactStatus.status_id = ' . (int) $status);
		}
		
		if (!empty($priority))
		{
			// join in only the latest priority
			$query->leftJoin('('
				. $this->getDbo()->getQuery(true)
					->select('p1.*')
					->from('#__jinbound_contacts_priorities AS p1')
					->leftJoin('#__jinbound_contacts_priorities AS p2 ON p1.contact_id = p2.contact_id AND p1.campaign_id = p2.campaign_id AND p1.created < p2.created')
					->where('p2.contact_id IS NULL')
				. ') AS ContactPriority ON ContactPriority.campaign_id = Page.campaign AND ContactPriority.contact_id = Contact.id'
			)->where('ContactPriority.priority_id = ' . (int) $priority);
		}
		
		if (is_numeric($published))
		{
			$query->where('Contact.published = ' . (int) $published);
		}
		
		if (!empty($start))
		{
			try
			{
				$start = new DateTime($start);
				if ($start)
				{
					$query->where('Conversion.created > ' . $this->getDbo()->quote($start->format('Y-m-d h:i:s')));
				}
			}
			catch (Exception $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}
		}
		
		if (!empty($end))
		{
			try
			{
				$end = new DateTime($end);
				if ($end)
				{
					$query->where('Conversion.created > ' . $this->getDbo()->quote($end->format('Y-m-d h:i:s')));
				}
			}
			catch (Exception $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}
		}
		
		try
		{
			$contacts = $this->getDbo()->setQuery($query)->loadObjectList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
			$contacts = array();
		}
		return $contacts;
	}
	
	/**
	 * Gets a list of the top landing pages
	 * 
	 * @return array
	 */
	public function getTopPages()
	{
		$app      = JFactory::getApplication();
		$input    = $app->input;
		$start    = $input->get('filter_start', '', 'string');
		$end      = $input->get('filter_end', '', 'string');
		$campaign = $input->get('filter_campaign', '', 'string');
		$page     = $input->get('filter_page', '', 'string');
		$query    = $this->getDbo()->getQuery(true)
			->select('Page.id AS id')
			->select('Page.name AS name')
			->select('Page.hits AS hits')
			->select('Category.title AS category_name')
			->select('Campaign.name AS campaign_name')
			->select('COUNT(DISTINCT Submission.contact_id) AS contact_submissions')
			->select('GROUP_CONCAT(DISTINCT Submission.contact_id) AS contact_submission_ids')
			->select('COUNT(DISTINCT Submission.id) AS submissions')
			->select('GROUP_CONCAT(DISTINCT Submission.id) AS submission_ids')
			->select('COUNT(DISTINCT Conversion.contact_id) AS conversions')
			->select('GROUP_CONCAT(DISTINCT Conversion.contact_id) AS conversion_ids')
			->select('ROUND(IF(COUNT(DISTINCT Submission.contact_id) > 0, (COUNT(DISTINCT Conversion.contact_id) / COUNT(DISTINCT Submission.contact_id)) * 100, 0), 2) AS conversion_rate')
			->from('#__jinbound_pages AS Page')
			->leftJoin('#__categories AS Category ON Category.id = Page.category')
			->leftJoin('#__jinbound_campaigns AS Campaign ON Campaign.id = Page.campaign')
			->leftJoin('#__jinbound_conversions AS Submission ON Submission.page_id = Page.id AND Submission.published = 1')
			->leftJoin('('
				. $this->getDbo()->getQuery(true)
					->select('s1.*')
					->from('#__jinbound_contacts_statuses AS s1')
					->leftJoin('#__jinbound_contacts_statuses AS s2 ON s1.contact_id = s2.contact_id AND s1.campaign_id = s2.campaign_id AND s1.created < s2.created')
					->where('s2.contact_id IS NULL')
				. ') AS Conversion ON Conversion.campaign_id = Campaign.id AND Conversion.contact_id = Submission.contact_id AND Conversion.status_id IN (('
				. $this->getDbo()->getQuery(true)
					->select('Status.id')
					->from('#__jinbound_lead_statuses AS Status')
					->where('Status.final = 1')
					->where('Status.published = 1')
			. '))')
			->group('Page.id')
			->order('conversion_rate DESC')
			->order('Page.hits DESC')
		;
		
		if (!empty($campaign))
		{
			$query->where('Campaign.id = ' . (int) $campaign);
		}
		
		if (!empty($page))
		{
			$query->where('Page.id = ' . (int) $page);
		}
		
		if (!empty($start))
		{
			try
			{
				$start = new DateTime($start);
				if ($start)
				{
					$query->where('Contact.created > ' . $this->getDbo()->quote($start->format('Y-m-d h:i:s')));
				}
			}
			catch (Exception $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}
		}
		
		if (!empty($end))
		{
			try
			{
				$end = new DateTime($end);
				if ($end)
				{
					$query->where('Contact.created > ' . $this->getDbo()->quote($end->format('Y-m-d h:i:s')));
				}
			}
			catch (Exception $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}
		}
		
		try
		{
			$pages = $this->getDbo()->setQuery($query)->loadObjectList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
			$pages = array();
		}
		return $pages;
	}
	
	/**
	 * Gets the total number of active leads
	 * 
	 * @return integer
	 */
	public function getLeadCount() {
		$this->getDbo()->setQuery($this->getDbo()->getQuery(true)
			->select('COUNT(Lead.id)')
			->from('#__jinbound_leads AS Lead')
			->leftJoin('#__jinbound_lead_statuses AS Status ON Lead.status_id = Status.id')
			->where('(Status.active = 1 OR Status.active IS NULL)') // users with no status are probably new and something went wonky
			->where('Lead.published = 1')
		);
		
		try {
			$count = $this->getDbo()->loadResult();
		}
		catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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
			->where('Lead.published = 1')
			->where('Page.published = 1')
			->group('Contact.id')
			->group('Lead.id')
			->order('Lead.created ASC')
		);
		
		try {
			$leads = $this->getDbo()->loadObjectList();
		}
		catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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
			->leftJoin('#__jinbound_statuses AS Status ON Status.final = 1')
			->leftJoin('#__jinbound_leads AS Lead ON Lead.page_id = Lead.id AND Lead.status_id = Status.id')
			->where('Lead.published = 1')
			->where('Page.published = 1')
			->group('Page.id')
			->order('conversion_rate DESC')
			->order('Page.hits DESC')
		);
		
		try {
			$pages = $this->getDbo()->loadObjectList();
		}
		catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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
				->innerJoin('#__jinbound_lead_statuses AS Status ON Lead.status_id = Status.id AND Status.final = 1')
				->where('Lead.published = 1')
			);
			
			try {
				$count = $this->getDbo()->loadResult();
			}
			catch (Exception $e) {
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				$count = 0;
			}
		}
		
		return (int) $count;
	}
	
	public function getConversionRate() {
		static $rate;
		
		if (is_null($rate)) {
			$count = (int) $this->getConversionsCount(); //$this->getConversionCount();
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
	
	public function getViewsToLeads() {
		static $rate;
		
		if (is_null($rate)) {
			$count = (int) $this->getContactsCount(); //$this->getLeadCount();
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
	
	private function _getDateRanges($start = null, $end = null)
	{
		static $range;
		if (is_null($range))
		{
			$range = $this->getDbo()->setQuery($this->getDbo()->getQuery(true)
				->select('MIN(t.created) AS start, NOW() AS end')
				->from('(' . $this->getDbo()->getQuery(true)
					->select('day AS created')
					->from('#__jinbound_landing_pages_hits')
					. ' UNION ' . $this->getDbo()->getQuery(true)
					->select('created')
					->from('#__jinbound_contacts')
					. ' UNION ' . $this->getDbo()->getQuery(true)
					->select('created')
					->from('#__jinbound_conversions')
					. ' UNION ' . $this->getDbo()->getQuery(true)
					->select('created')
					->from('#__jinbound_contacts_priorities')
					. ' UNION ' . $this->getDbo()->getQuery(true)
					->select('created')
					->from('#__jinbound_contacts_statuses')
					. ') as t'
					)
			)->loadObject();
		}
		$tz   = new DateTimeZone('UTC');
		$date = new stdClass;
		$date->start = new DateTime((empty($start) ? $range->start : $start), $tz);
		$date->end   = new DateTime((empty($end) ? $range->end : $end), $tz);
		$dates       = array();
		$date->end->modify('+1 day');
		while ($date->start < $date->end)
		{
			$dates[] = $date->start->format('Y-m-d');
			$date->start->modify('+1 day');
		}
		return $dates;
	}
	
	public function getLandingPageHits($start = null, $end = null)
	{
		$dates = $this->_getDateRanges($start, $end);
		$query = $this->getDbo()->getQuery(true)
			->select('PageHit.day, SUM(PageHit.hits) AS hits')
			->from('#__jinbound_landing_pages_hits AS PageHit')
			->group('PageHit.day')
		;
		if (!empty($start))
		{
			$query->where('PageHit.day >= ' . $this->getDbo()->quote($start));
		}
		if (!empty($end))
		{
			$query->where('PageHit.day <= ' . $this->getDbo()->quote($end));
		}
		$campaign = $this->getState('filter.campaign');
		if (!empty($campaign))
		{
			$query->where('PageHit.page_id IN (('
				. $this->getDbo()->getQuery(true)
				->select('Page.id')
				->from('#__jinbound_pages AS Page')
				->where('Page.campaign = ' . (int) $campaign)
				. '))'
			);
		}
		$page = $this->getState('filter.page');
		if (!empty($page))
		{
			$query->where('PageHit.page_id = ' . (int) $page);
		}
		$days = $this->getDbo()->setQuery($query)->loadObjectList();
		$data = array();
		foreach ($dates as $date)
		{
			$entry = array($date . ' 00:00:00');
			$count = 0;
			foreach ($days as $day)
			{
				if ($day->day == $date)
				{
					$count = (int) $day->hits;
					break;
				}
			}
			reset($days);
			$entry[] = $count;
			$data[]  = $entry;
		}
		return $data;
	}
	
	public function getLeadsByCreationDate($start = null, $end = null)
	{
		$dates = $this->_getDateRanges($start, $end);
		$query = $this->getDbo()->getQuery(true)
			->select('DATE(Contact.created) AS day, COUNT(Contact.id) AS total')
			->from('#__jinbound_contacts AS Contact')
			->group('day')
		;
		if (!empty($start))
		{
			try {
				$startdate = new DateTime($start);
				$query->where('Contact.created > ' . $this->getDbo()->quote($startdate->format('Y-m-d H:i:s')));
			}
			catch (Exception $ex) {
				// nothing
			}
		}
		if (!empty($end))
		{
			try {
				$enddate = new DateTime($end);
				$query->where('Contact.created < ' . $this->getDbo()->quote($enddate->format('Y-m-d H:i:s')));
			}
			catch (Exception $ex) {
				// nothing
			}
		}
		$campaign = $this->getState('filter.campaign');
		if (!empty($campaign))
		{
			$query->where('Contact.id IN (('
				. $this->getDbo()->getQuery(true)
				->select('ContactCampaign.contact_id')
				->from('#__jinbound_contacts_campaigns AS ContactCampaign')
				->where('ContactCampaign.campaign_id = ' . (int) $campaign)
				->where('ContactCampaign.enabled = 1')
				. '))'
			);
		}
		$page = $this->getState('filter.page');
		if (!empty($page))
		{
			$query->where('Contact.id IN (('
				. $this->getDbo()->getQuery(true)
				->select('Conversion.contact_id')
				->from('#__jinbound_conversions AS Conversion')
				->where('Conversion.page_id = ' . (int) $page)
				. '))'
			);
		}
		$days = $this->getDbo()->setQuery($query)->loadObjectList();
		$data = array();
		foreach ($dates as $date)
		{
			$entry = array($date . ' 00:00:00');
			$count = 0;
			foreach ($days as $day)
			{
				if ($day->day == $date)
				{
					$count += (int) $day->total;
					break;
				}
			}
			reset($days);
			$entry[] = $count;
			$data[]  = $entry;
		}
		return $data;
	}
	
	public function getConversionsByDate($start = null, $end = null)
	{
		/*
		 * 
			select DATE(created) as day, count(contact_id) from (
		 * select s1.*,s.final from jos_jinbound_contacts_statuses as s1 
		 * left join jos_jinbound_contacts_statuses AS s2 
		 * ON s1.contact_id = s2.contact_id 
		 * AND s1.campaign_id = s2.campaign_id 
		 * AND s1.created < s2.created 
		 * inner join jos_jinbound_lead_statuses as s 
		 * on s.id = s1.status_id 
		 * where s2.contact_id is null) as t where final = 1 group by day;

		 */
		$dates = $this->_getDateRanges($start, $end);
		$inner = $this->getDbo()->getQuery(true)
			->select('a.*,c.final')
			->from('#__jinbound_contacts_statuses AS a')
			->leftJoin('#__jinbound_contacts_statuses AS b ON a.contact_id = b.contact_id AND a.campaign_id = b.campaign_id AND a.created < b.created')
			->innerJoin('#__jinbound_lead_statuses AS c ON c.id = a.status_id')
			->where('b.contact_id IS NULL')
		;
		$query = $this->getDbo()->getQuery(true)
			->select('DATE(created) AS day, COUNT(contact_id) AS num')->where('final = 1')->group('day')
		;
		if (!empty($start))
		{
			try {
				$startdate = new DateTime($start);
				$query->where('created > ' . $this->getDbo()->quote($startdate->format('Y-m-d H:i:s')));
			}
			catch (Exception $ex) {
				// nothing
			}
		}
		if (!empty($end))
		{
			try {
				$enddate = new DateTime($end);
				$query->where('created < ' . $this->getDbo()->quote($enddate->format('Y-m-d H:i:s')));
			}
			catch (Exception $ex) {
				// nothing
			}
		}
		$campaign = $this->getState('filter.campaign');
		if (!empty($campaign))
		{
			$inner->where('a.campaign_id = ' . (int) $campaign);
		}
		$page = $this->getState('filter.page');
		if (!empty($page))
		{
			$inner->where('a.campaign_id IN (('
				. $this->getDbo()->getQuery(true)
				->select('p.campaign')
				->from('#__jinbound_pages AS p')
				->where('p.id = ' . (int) $page)
				. '))'
			);
		}
		$days = $this->getDbo()->setQuery($query->from('(' . $inner . ') AS t'))->loadObjectList();
		$data = array();
		foreach ($dates as $date)
		{
			$entry = array($date . ' 00:00:00');
			$count = 0;
			foreach ($days as $day)
			{
				if ($day->day == $date)
				{
					$count += (int) $day->num;
					break;
				}
			}
			reset($days);
			$entry[] = $count;
			$data[]  = $entry;
		}
		return $data;
	}
	
	public function send()
	{
		$out = JInbound::config("debug", 0);
		// only send if configured to
		if (!JInbound::config('send_reports', 1))
		{
			if ($out)
			{
				echo "<p>Not sending reports - disabled in config</p>";
			}
			return;
		}
		// init
		$db = JFactory::getDbo();
		// fetch the existing report emails
		try
		{
			$emailrecords = $db->setQuery($db->getQuery(true)
				->select('*')
				->from('#__jinbound_emails')
				->where('published = 1')
				->where($db->quoteName('type') . ' = ' . $db->quote('report'))
			)->loadObjectList();
		}
		catch (Exception $e)
		{
			if ($out)
			{
				echo "<p>" . $e->getMessage() . "</p><pre>" . $e->getTraceAsString() . "</pre>";
			}
			return;
		}
		if (empty($emailrecords))
		{
			if ($out)
			{
				echo "<p>No report emails found</p>";
			}
			return;
		}
		foreach ($emailrecords as $idx => $emailrecord)
		{
			if (empty($emailrecord->params))
			{
				if ($out)
				{
					echo "<p>Email has no params...</p>";
				}
				continue;
			}
			$params = json_decode($emailrecord->params);
			$emailrecords[$idx]->params = $params;
			if (!(
				is_object($params) && 
				property_exists($params, 'reports_frequency') && 
				property_exists($params, 'recipients')
				))
			{
				if ($out)
				{
					echo "<p>Email is missing params...</p>";
				}
				continue;
			}
			$emails = $emailrecord->params->recipients;
			// only send if there are emails
			if (empty($emails))
			{
				if ($out)
				{
					echo "<p>Email has no recipients...</p>";
				}
				continue;
			}
			// convert emails to an array
			if (false !== strpos($emails, ','))
			{
				$emails = explode(',', $emails);
			}
			else
			{
				$emails = array($emails);
			}
			// quote emails for use in db query
			$quoted = array();
			foreach ($emails as $idx => $email)
			{
				$emails[$idx] = trim($email);
				$quoted[] = $db->quote($email);
			}
			$frequency = $params->reports_frequency;
			if (!in_array($frequency, $this->frequencies))
			{
				if ($out)
				{
					echo "<p>Invalid frequency...</p>";
				}
				continue;
			}
			$records = $db->setQuery($db->getQuery(true)
				->select('email')
				->from('#__jinbound_reports_emails')
				->where('email_id = ' . intval($emailrecord->id))
				->where("created > (NOW() - INTERVAL $frequency)")
			)->loadColumn();
			// if there are no records, we can send
			// otherwise skip
			$sendto = array();
			foreach ($emails as $email)
			{
				if (in_array($email, $records))
				{
					continue;
				}
				$sendto[] = $email;
			}
			// only send if sendto isn't empty
			if (empty($sendto))
			{
				if ($out)
				{
					echo "<p>Cannot send to any recipients yet...</p>";
				}
				continue;
			}
			
			$data      = $this->getReportEmailData($emailrecord);
			$tags      = $this->getReportEmailTags($emailrecord);
			$subject   = $emailrecord->subject;
			
			$htmlbody  = JInboundModelEmails::_replaceTags($emailrecord->htmlbody, $data, $tags);
			$plainbody = JInboundModelEmails::_replaceTags($emailrecord->plainbody, $data, $tags);
			
			// send emails
			$mailer = JFactory::getMailer();
			$mailer->ClearAllRecipients();
			$mailer->addRecipient($sendto);
			$mailer->setSubject($subject);
			$mailer->setBody($htmlbody);
			$mailer->IsHtml(true);
			$mailer->AltBody = $plainbody;
			
			if ($out)
			{
				echo "<p>Attempting to send the following email:</p>\n";
				echo "<h4>$subject</h4>";
				echo $htmlbody;
			}
			
			$send_response = $mailer->send();
			if (false !== $send_response && !($send_response instanceof JError) && !($send_response instanceof Exception))
			{
				$query = $db->getQuery(true)
					->insert('#__jinbound_reports_emails')
					->columns(array('email', 'email_id', 'created'))
				;
				foreach ($sendto as $email)
				{
					$query->values($db->quote($email) . ', ' . intval($emailrecord->id) . ', NOW()');
				}
				try
				{
					$db->setQuery($query)->query();
				}
				catch (Exception $e)
				{
					if ($out)
					{
						echo "<p>" . $e->getMessage() . "</p><pre>" . $e->getTraceAsString() . "</pre>";
					}
					return;
				}
			}
			else if (false === $send_response && $out)
			{
				echo "<p>Unspecified error sending mail!</p>";
			}
			else if ($send_response instanceof Exception && $out)
			{
				echo "<p>" . $e->getMessage() . "</p>";
			}
		}
	}
	
	public function getReportEmailData($email)
	{
		$out        = JInbound::config("debug", 0);
		$dispatcher = JDispatcher::getInstance();
		$start_date = new DateTime();
		$end_date   = new DateTime();
		$start_date->modify("-{$email->params->reports_frequency}");
		$start      = $start_date->format('Y-m-d H:i:s');
		$end        = $end_date->format('Y-m-d H:i:s');
		$campaigns  = $email->params->campaigns;
		if (!is_array($campaigns))
		{
			$campaigns = explode(',', $campaigns);
		}
		if ($out)
		{
			echo "<p>Reporting on data from '$start' to '$end'...</p>";
		}
		$lead_filters = array(
			'filter'          => array('start' => $start, 'end' => $end, 'campaign' => $campaigns)
		,	'filter.start'    => $start
		,	'filter.end'      => $end
		,	'filter.campaign' => $campaigns
		,	'list'            => array('limit' => 10, 'ordering' => 'Contact.created', 'direction' => 'DESC')
		,	'list.limit'      => 10
		,	'list.ordering'   => 'Contact.created'
		,	'list.direction'  => 'DESC'
		);
		$page_filters = array_merge($lead_filters, array());
		$page_filters['list.ordering'] = $page_filters['list']['ordering'] = 'hits';
		
		$page_list_data = $this->getPagesArrayForEmail($page_filters);
		$lead_list_data = $this->getLeadsArrayForEmail($lead_filters);
		
		$top_pages_data = array_merge(array(), $page_list_data);
		$top = array(
			'name' => ''
		,	'url' => ''
		);
		if (!empty($top_pages_data))
		{
			$toppage     = array_shift($top_pages_data);
			$top['name'] = $toppage->name;
			$top['url']  = JInboundHelperUrl::toFull(JInboundHelperUrl::view('page', true, array('id' => $toppage->id)));
		}
		$lowest = array_merge(array(), $top);
		if (!empty($top_pages_data))
		{
			$lowestpage     = array_pop($top_pages_data);
			$lowest['name'] = $lowestpage->name;
			$lowest['url']  = JInboundHelperUrl::toFull(JInboundHelperUrl::view('page', true, array('id' => $lowestpage->id)));
		}
		
		$hits_data = $this->getLandingPageHits($start, $end);
		$lead_data = $this->getLeadsByCreationDate($start, $end);
		$conv_data = $this->getConversionsByDate($start, $end);
		$views = 0;
		$leads = 0;
		$conversions     = 0;
		$views_to_leads  = 0;
		$conversion_rate = 0;
		// add values
		foreach ($hits_data as $hit)
		{
			$views += (int) $hit[1];
		}
		foreach ($lead_data as $lead)
		{
			$leads += (int) $lead[1];
		}
		foreach ($conv_data as $conversion)
		{
			$conversions += (int) $conversion[1];
		}
		// calc percents
		if (0 < $views) {
			$views_to_leads  = ($leads / $views) * 100;
			$conversion_rate = ($conversions / $views) * 100;
		}
		$views_to_leads  = number_format($views_to_leads, 2) . '%';
		$conversion_rate = number_format($conversion_rate, 2) . '%';
		$debug = array('filters' => '', 'pagestate' => 'null', 'leadstate' => 'null');
		if (property_exists($this, '_pageModelState'))
		{
			$debug['pagestate'] = json_encode($this->_pageModelState);
		}
		if (property_exists($this, '_contactModelState'))
		{
			$debug['leadstate'] = json_encode($this->_contactModelState);
		}
		$data = array(
			'goals' => array(
				'count' => $conversions
			,	'percent' => $conversion_rate
			)
		,	'leads' => array(
				'count' => $leads
			,	'list' => $this->getEmailLeadsList(is_array($lead_list_data) ? $lead_list_data : array())
			,	'percent' => $views_to_leads
			)
		,	'pages' => array(
				'hits' => $views
			,	'list' => $this->getEmailPagesList(is_array($page_list_data) ? $page_list_data : array())
			,	'top' => $top
			,	'lowest' => $lowest
			)
		,	'debug' => $debug
		);
		
		$dispatcher->trigger('onJInboundReportEmailData', array(&$data));
		// send back data
		return json_decode(json_encode($data));
	}
	
	public function getPagesArrayForEmail(array $filters = array())
	{
		$model = new JInboundModelPages();
		$model->getState();
		if (!empty($filters))
		{
			foreach ($filters as $filter => $value)
			{
				$model->getState($filter, $value);
				$model->setState($filter, $value);
			}
		}
		$this->_pageModelState = $model->getState();
		return $model->getItems();
	}
	
	public function getLeadsArrayForEmail(array $filters = array())
	{
		$model = new JInboundModelContacts();
		$model->getState();
		if (!empty($filters))
		{
			foreach ($filters as $filter => $value)
			{
				$model->getState($filter, $value);
				$model->setState($filter, $value);
			}
		}
		$this->_contactModelState = $model->getState();
		return $model->getItems();
	}
	
	public function getEmailPagesList(array $pages = array())
	{
		$table = array();
		$table[] = '<table>';
		$table[] = '<thead>';
		$table[] = '<tr>';
		$table[] = '<th>' . JText::_('COM_JINBOUND_LANDING_PAGE_NAME') . '</th>';
		$table[] = '<th>' . JText::_('COM_JINBOUND_VISITS') . '</th>';
		$table[] = '<th>' . JText::_('COM_JINBOUND_SUBMISSIONS') . '</th>';
		$table[] = '<th>' . JText::_('COM_JINBOUND_LEADS') . '</th>';
		$table[] = '<th>' . JText::_('COM_JINBOUND_GOAL_COMPLETIONS') . '</th>';
		$table[] = '<th>' . JText::_('COM_JINBOUND_GOAL_COMPLETION_RATE') . '</th>';
		$table[] = '</tr>';
		$table[] = '</thead>';
		$table[] = '<tbody>';
		if (empty($pages))
		{
			$table[] = '<tr><td colspan="6">' . JText::_('COM_JINBOUND_NOT_FOUND') . '</td></tr>';
		}
		else
		{
			foreach ($pages as $page)
			{
				$table[] = '<tr>';
				// name
				$table[] = '<td>';
				if (!empty($page->name))
				{
					$table[] = JInboundHelperFilter::escape($page->name);
				}
				$table[] = '</td>';
				
				$table[] = '<td>' . $page->hits . '</td>';
				$table[] = '<td>' . $page->submissions . '</td>';
				$table[] = '<td>' . $page->contact_submissions . '</td>';
				$table[] = '<td>' . $page->conversions . '</td>';
				$table[] = '<td>' . $page->conversion_rate . '</td>';
				
				$table[] = '</tr>';
			}
		}
		$table[] = '</tbody>';
		$table[] = '</table>';
		return implode("\n", $table);
	}
	
	public function getEmailLeadsList(array $leads = array())
	{
		$table = array();
		$table[] = '<table>';
		$table[] = '<thead>';
		$table[] = '<tr>';
		$table[] = '<th>' . JText::_('COM_JINBOUND_NAME') . '</th>';
		$table[] = '<th>' . JText::_('COM_JINBOUND_DATE') . '</th>';
		$table[] = '<th>' . JText::_('COM_JINBOUND_FORM_CONVERTED_ON') . '</th>';
		$table[] = '<th>' . JText::_('COM_JINBOUND_LANDING_PAGE_NAME') . '</th>';
		$table[] = '</tr>';
		$table[] = '</thead>';
		$table[] = '<tbody>';
		if (empty($leads))
		{
			$table[] = '<tr><td colspan="4">' . JText::_('COM_JINBOUND_NOT_FOUND') . '</td></tr>';
		}
		else
		{
			foreach ($leads as $lead)
			{
				$table[] = '<tr>';
				// name
				$table[] = '<td>';
				$table[] = JInboundHelperFilter::escape($lead->first_name . ' ' . $lead->last_name);
				$table[] = '</td>';
				
				$table[] = '<td>';
				$table[] = $lead->latest ? $lead->latest : $lead->created;
				$table[] = '</td>';
				
				$table[] = '<td>';
				if (!empty($lead->latest_conversion_page_formname))
				{
					$table[] = JInboundHelperFilter::escape($lead->latest_conversion_page_formname);
				}
				$table[] = '</td>';
				
				$table[] = '<td>';
				if (!empty($lead->latest_conversion_page_name))
				{
					$table[] = JInboundHelperFilter::escape($lead->latest_conversion_page_name);
				}
				$table[] = '</td>';
				
				$table[] = '</tr>';
			}
		}
		$table[] = '</tbody>';
		$table[] = '</table>';
		return implode("\n", $table);
	}
	
	public function getReportEmailTags($email = null)
	{
		$dispatcher = JDispatcher::getInstance();
		$tags = array(
			'reports.goals.count', 'reports.goals.percent'
		,	'reports.leads.count', 'reports.leads.list', 'reports.leads.percent'
		,	'reports.pages.hits', 'reports.pages.list'
		,	'reports.pages.top.name', 'reports.pages.top.url'
		,	'reports.pages.lowest.name', 'reports.pages.lowest.url'
		,	'reports.debug.filters', 'reports.debug.leadstate', 'reports.debug.pagestate'
		);
		$dispatcher->trigger('onJInboundReportEmailTags', array(&$tags, $email));
		return $tags;
	}
	
	public function getPermissions()
	{
		$db = JFactory::getDbo();
		$id = $db->setQuery($db->getQuery(true)
			->select('id')->from('#__assets')->where('name = ' . $db->quote(JInbound::COM . '.report'))
		)->loadResult();
		$modelpath = JInboundHelperPath::admin('models');
		$formname  = 'report_rules';
		if (!file_exists("$modelpath/forms/$formname.xml"))
		{
			return false;
		}
		JForm::addFormPath("$modelpath/forms");
		JForm::addFieldPath("$modelpath/fields");
		$form = $this->loadForm(JInbound::COM . '.' . $formname, $formname, array('control' => '', 'load_data' => false));
		if (empty($form))
		{
			return false;
		}
		$form->bind(array('asset_id' => $id));
		return $form;
	}
}
