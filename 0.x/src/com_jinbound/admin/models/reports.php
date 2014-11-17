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
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);
		
		$app    = JFactory::getApplication();
		
		foreach (array('page', 'campaign', 'start', 'end') as $var) {
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
		$id	.= ':'.$this->getState('filter.campaign');
		$id	.= ':'.$this->getState('filter.page');

		return parent::getStoreId($id);
	}
	
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
		try
		{
			$count = $this->getDbo()->setQuery($this->getDbo()->getQuery(true)
				->select('COUNT(Contact.id)')
				->from('#__jinbound_contacts AS Contact')
				->leftJoin('(' . $this->getDbo()->getQuery(true)
					->select('s1.*')
					->from('#__jinbound_contacts_statuses AS s1')
					->leftJoin('#__jinbound_contacts_statuses AS s2 ON s1.contact_id = s2.contact_id AND s1.campaign_id = s2.campaign_id AND s1.created < s2.created')
					->where('s2.contact_id IS NULL')
				. ') AS ContactStatus ON (ContactStatus.contact_id = Contact.id)')
				->leftJoin('#__jinbound_lead_statuses AS Status ON ContactStatus.status_id = Status.id')
				->where('Status.final = 1')
				->where('Contact.published = 1')
			)->loadResult();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			$count = 0;
		}
		return (int) $count;
	}
	
	/**
	 * Gets a list of recent contacts
	 * 
	 * @return array
	 */
	public function getRecentContacts()
	{
		$app      = JFactory::getApplication();
		$input    = $app->input;
		$start    = $input->get('filter_start', '', 'string');
		$end      = $input->get('filter_end', '', 'string');
		$campaign = $input->get('filter_campaign', '', 'string');
		$page     = $input->get('filter_page', '', 'string');
		$query    = $this->getDbo()->getQuery(true)
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
				->select('MIN(day) AS start, MAX(day) AS end')
				->from('#__jinbound_landing_pages_hits')
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
		$dates = $this->_getDateRanges($start, $end);
		$query = $this->getDbo()->getQuery(true)
			->select('DATE(a.created) AS day, COUNT(a.created)')
			->from('#__jinbound_contacts_statuses AS a')
			->innerJoin('#__jinbound_lead_statuses AS s ON s.id = a.status_id AND s.final = 1')
			->group('day')
		;
		if (!empty($start))
		{
			try {
				$startdate = new DateTime($start);
				$query->where('a.created > ' . $this->getDbo()->quote($startdate->format('Y-m-d H:i:s')));
			}
			catch (Exception $ex) {
				// nothing
			}
		}
		if (!empty($end))
		{
			try {
				$enddate = new DateTime($end);
				$query->where('a.created < ' . $this->getDbo()->quote($enddate->format('Y-m-d H:i:s')));
			}
			catch (Exception $ex) {
				// nothing
			}
		}
		$campaign = $this->getState('filter.campaign');
		if (!empty($campaign))
		{
			$query->where('a.campaign_id = ' . (int) $campaign);
		}
		$page = $this->getState('filter.page');
		if (!empty($page))
		{
			$query->where('a.campaign_id IN (('
				. $this->getDbo()->getQuery(true)
				->select('p.campaign')
				->from('#__jinbound_pages AS p')
				->where('p.id = ' . (int) $page)
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
					$count++;
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
		$dbg = JInbound::config("debug", 0);
		if ($dbg) echo "<h3>Sending reports</h3>";
		// only send if configured to
		if (!JInbound::config('send_reports', 1))
		{
			if ($dbg) echo "<p>Reports not enabled!</p>";
			return;
		}
		// init
		$db     = JFactory::getDbo();
		$emails = JInbound::config('report_recipients', '');
		// only send if there are emails
		if (empty($emails))
		{
			if ($dbg) echo "<p>No emails provided!</p>";
			return;
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
		if ($dbg) echo "<p>Found emails: " . implode(', ', $emails) . "</p>";
		// quote emails for use in db query
		$quoted = array();
		foreach ($emails as &$email)
		{
			$email = trim($email);
			$quoted[] = $db->quote($email);
		}
		unset($email);
		// get the interval
		$freq = (int) JInbound::config('report_frequency', 0);
		switch ($freq)
		{
			// no interval, no emails
			case 0:
				if ($dbg) echo "<p>No interval!</p>";
				return;
			// all others will be in hours
			default:
				// get the previous records
				try
				{
					$interval = JInbound::config("debug", 0) ? 'MINUTE' : 'HOUR';
					if ($dbg) echo "<p>Sending at interval $freq $interval</p>";
					$records = $db->setQuery($db->getQuery(true)
						->select('email')
						->from('#__jinbound_reports_emails')
						->where('email IN(' . implode(',', $quoted) . ')')
						->where("created > (NOW() - INTERVAL $freq $interval)")
					)->loadColumn();
				}
				catch (Exception $e)
				{
					if ($dbg) echo "<p>" . $e->getMessage() . "</p>";
					return;
				}
				// if there are no records, we can send
				// otherwise skip
				$sendto = array();
				foreach ($emails as $email)
				{
					if (in_array($email, $records))
					{
						if ($dbg) echo "<p>skipping " . $email . "</p>";
						continue;
					}
					$sendto[] = $email;
				}
				// only send if sendto isn't empty
				if (empty($sendto))
				{
					if ($dbg) echo "<p>No emails are being sent</p>";
					return;
				}
				// construct subject and body
				require_once dirname(__FILE__) . '/pages.php';
				$config     = new JConfig;
				$model      = new JInboundModelPages();
				$pages      = $model->getItems();
				$subject    = JText::sprintf('COM_JINBOUND_REPORTS_EMAIL_SUBJECT', $config->sitename);
				$htmltable  = '<table>';
				$plaintable = "\n\n";
				// add the headers
				$headers = array(
					JText::_('COM_JINBOUND_LANDING_PAGE_NAME')
				,	JText::_('COM_JINBOUND_VISITS')
				,	JText::_('COM_JINBOUND_SUBMISSIONS')
				,	JText::_('COM_JINBOUND_LEADS')
				,	JText::_('COM_JINBOUND_CONVERSIONS')
				,	JText::_('COM_JINBOUND_CONVERSION_RATE')
				);
				$htmltable  .= sprintf('<thead><tr><th>%s</th></tr></thead><tbody>', implode('</th><th>', $headers));
				$plaintable .= implode("\t", $headers) . "\n";
				// add the data
				foreach ($pages as $page) {
					$data = array(
						htmlspecialchars($page->name)
					,	htmlspecialchars($page->hits)
					,	htmlspecialchars($page->submissions)
					,	htmlspecialchars($page->contact_submissions)
					,	htmlspecialchars($page->conversions)
					,	htmlspecialchars($page->conversion_rate)
					);
					$htmltable  .= sprintf('<tr><td>%s</td></tr>', implode('</td><td>', $data));
					$plaintable .= implode("\t", $data) . "\n";
				}
				$htmltable .= '</tbody></table>';
				// build the body
				$htmlbody  = JText::sprintf('COM_JINBOUND_REPORTS_EMAIL_HTMLBODY', $config->sitename, $htmltable);
				$plainbody = JText::sprintf('COM_JINBOUND_REPORTS_EMAIL_PLAINBODY', $config->sitename, $plaintable);
				// send emails
				$mailer = JFactory::getMailer();
				$mailer->ClearAllRecipients();
				$mailer->addRecipient($sendto);
				$mailer->setSubject($subject);
				$mailer->setBody($htmlbody);
				$mailer->IsHtml(true);
				$mailer->AltBody = $plainbody;
				if ($mailer->send())
				{
					$query = $db->getQuery(true)
						->insert('#__jinbound_reports_emails')
						->columns(array('email', 'created'))
					;
					foreach ($sendto as $email)
					{
						$query->values($db->quote($email) . ', NOW()');
					}
					try
					{
						$db->setQuery($query)->query();
					}
					catch (Exception $e)
					{
						if ($dbg) echo "<p>" . $e->getMessage() . "</p>";
						return;
					}
					if ($dbg) echo "<p>Mailer succeeded!</p>";
				}
				else
				{
					if ($dbg) echo "<p>Mailer failed!</p>";
				}
				// done
				return;
		}
	}
}
