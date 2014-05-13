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
			->where('Page.published = 1')
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
	 * Gets the number of active contacts
	 * 
	 * @return integer
	 */
	public function getContactsCount()
	{
		/*
	SELECT COUNT(Contact.id)
	FROM cxsgv_jinbound_contacts AS Contact
	LEFT JOIN ( SELECT s1.*
		FROM cxsgv_jinbound_contacts_statuses as s1
		LEFT JOIN cxsgv_jinbound_contacts_statuses AS s2
		     ON s1.contact_id = s2.contact_id AND s1.created < s2.created
		WHERE s2.contact_id IS NULL ) AS ContactStatus

    ON (ContactStatus.contact_id = Contact.id)
    LEFT JOIN cxsgv_jinbound_lead_statuses AS Status
    	ON ContactStatus.status_id = Status.id
    WHERE (Status.active = 1 OR Status.active IS NULL)
    AND Contact.published = 1
    */
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
				->where('(Status.active = 1 OR Status.active IS NULL)') // users with no status are probably new and something went wonky
				->where('Contact.published = 1')
			)->loadResult();
		}
		catch (Exception $e)
		{
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
		/*
	SELECT COUNT(Contact.id)
	FROM cxsgv_jinbound_contacts AS Contact
	LEFT JOIN ( SELECT s1.*
		FROM cxsgv_jinbound_contacts_statuses as s1
		LEFT JOIN cxsgv_jinbound_contacts_statuses AS s2
		     ON s1.contact_id = s2.contact_id AND s1.created < s2.created
		WHERE s2.contact_id IS NULL ) AS ContactStatus

    ON (ContactStatus.contact_id = Contact.id)
    LEFT JOIN cxsgv_jinbound_lead_statuses AS Status
    	ON ContactStatus.status_id = Status.id
    WHERE Status.final = 1
    AND Contact.published = 1
    */
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
		$input = JFactory::getApplication()->input;
		$start = $input->get('filter_begin', '', 'string');
		$end   = $input->get('filter_end', '', 'string');
		$query = $this->getDbo()->getQuery(true)
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
			}
		}
		
		try
		{
			$contacts = $this->getDbo()->setQuery($query)->loadObjectList();
		}
		catch (Exception $e)
		{
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
		$input = JFactory::getApplication()->input;
		$start = $input->get('filter_begin', '', 'string');
		$end   = $input->get('filter_end', '', 'string');
		$query = $this->getDbo()->getQuery(true)
			->select('Page.id AS id')
			->select('Page.name AS name')
			->select('Page.hits AS hits')
			->select('Campaign.name AS campaign_name')
			->select('COUNT(DISTINCT Contact.id) AS contact_submissions')
			->select('GROUP_CONCAT(DISTINCT Contact.id) AS contact_submission_ids')
			->select('COUNT(DISTINCT Submission.id) AS submissions')
			->select('GROUP_CONCAT(DISTINCT Submission.id) AS submission_ids')
			->select('COUNT(DISTINCT Conversion.contact_id) AS conversions')
			->select('GROUP_CONCAT(DISTINCT Conversion.contact_id) AS conversion_ids')
			->select('ROUND(IF(COUNT(DISTINCT Contact.id) > 0, (COUNT(DISTINCT Conversion.contact_id) / COUNT(DISTINCT Contact.id)) * 100, 0), 2) AS conversion_rate')
			->from('#__jinbound_pages AS Page')
			->leftJoin('#__categories AS Category ON Category.id = Page.category')
			->leftJoin('#__jinbound_campaigns AS Campaign ON Campaign.id = Page.campaign')
			->leftJoin('#__jinbound_contacts AS Contact ON Contact.id IN (('
				. $this->getDbo()->getQuery(true)
					->select('DISTINCT ContactConversion.contact_id')
					->from('#__jinbound_conversions AS ContactConversion')
					->where('ContactConversion.page_id = Page.id')
					->where('ContactConversion.published = 1')
			. '))')
			->leftJoin('#__jinbound_conversions AS Submission ON Submission.page_id = Page.id AND Submission.published = 1')
			->leftJoin('(' . $this->getDbo()->getQuery(true)
				->select('s1.*')
				->from('#__jinbound_contacts_statuses AS s1')
				->leftJoin('#__jinbound_contacts_statuses AS s2 ON s1.contact_id = s2.contact_id AND s1.campaign_id = s2.campaign_id AND s1.created < s2.created')
				->where('s2.contact_id IS NULL')
			. ') AS Conversion ON Conversion.campaign_id = Campaign.id AND Conversion.contact_id IN (('
				. $this->getDbo()->getQuery(true)
					->select('DISTINCT ConversionConversion.contact_id')
					->from('#__jinbound_conversions AS ConversionConversion')
					->where('ConversionConversion.page_id = Page.id')
					->where('ConversionConversion.published = 1')
			. ')) AND Conversion.status_id IN (('
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
			}
		}
		
		try
		{
			$pages = $this->getDbo()->setQuery($query)->loadObjectList();
		}
		catch (Exception $e)
		{
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
				->innerJoin('#__jinbound_lead_statuses AS Status ON Lead.status_id = Status.id AND Status.final = 1')
				->where('Lead.published = 1')
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
				// construct body
				// TODO
				$subject = JText::_('COM_JINBOUND_REPORTS_EMAIL_SUBJECT');
				$body    = JText::_('COM_JINBOUND_REPORTS_EMAIL_BODY');
				// send emails
				$mailer = JFactory::getMailer();
				$mailer->ClearAllRecipients();
				$mailer->addRecipient($sendto);
				$mailer->setSubject($subject);
				$mailer->setBody($body);
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
