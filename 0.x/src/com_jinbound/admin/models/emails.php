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
 * This models supports retrieving lists of emails.
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelEmails extends JInboundListModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $context  = 'com_jinbound.emails';
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();

		// main query
		$query = $db->getQuery(true)
			// Select the required fields from the table.
			->select('Email.*')
			->select('Campaign.name AS campaign_name')
			->from('#__jinbound_emails AS Email')
			->leftJoin('#__jinbound_campaigns AS Campaign ON Email.campaign_id = Campaign.id')
			->group('Email.id')
			->order('Campaign.name')
		;
		
		$this->appendAuthorToQuery($query, 'Email');
		$this->filterSearchQuery($query, $this->state->get('filter.search'), 'Email', 'id', array('Email.name', 'Email.subject', 'Campaign.name'));

		// Add the list ordering clause.
		$orderCol = trim($this->state->get('list.ordering'));
		$orderDirn = trim($this->state->get('list.direction'));
		if (strlen($orderCol)) {
			$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		}

		return $query;
	}
	
	/**
	 * Method to send all the emails that need to be sent
	 * 
	 */
	public function send() {
		$db       = $this->getDbo();
		$out      = defined('JDEBUG') && JDEBUG;
		$interval = $out ? 'MINUTE' : 'DAY';
		
		// NOTE: This query is kind of hairy and a little complicated, edit at your own risk!!!
		$db->setQuery($db->getQuery(true)
			->select('Lead.first_name AS first_name')
			->select('Lead.last_name AS last_name')
			->select('Lead.created AS created')
			->select('Lead.id AS lead_id')
			->select('Contact.email_to AS email')
			->select('Contact.id AS contact_id')
			->select('Page.id AS page_id')
			->select('Campaign.id AS campaign_id')
			->select('Email.id AS email_id')
			->select('Email.sendafter AS sendafter')
			->select('Email.fromname AS fromname')
			->select('Email.fromemail AS fromemail')
			->select('Email.subject AS subject')
			->select('Email.htmlbody AS htmlbody')
			->select('Email.plainbody AS plainbody')
			->select('Record.id AS record_id')
			->from('#__contact_details AS Contact')
			->leftJoin('#__jinbound_leads AS Lead ON Lead.contact_id = Contact.id')
			->leftJoin('#__jinbound_pages AS Page ON Lead.page_id = Page.id')
			->leftJoin('#__jinbound_campaigns AS Campaign ON Page.campaign = Campaign.id')
			->leftJoin('#__jinbound_emails AS Email ON Email.campaign_id = Campaign.id')
			->leftJoin('#__jinbound_emails_records AS Record ON Record.lead_id = Lead.id AND Record.email_id = Email.id')
			->where('Record.id IS NULL')
			->where('DATE_ADD(Lead.created, INTERVAL Email.sendafter ' . $interval . ') < UTC_TIMESTAMP()')
			// NOTE: Grouping order is VERY important here!!!!!
			// the query has to be grouped FIRST by emails, THEN by contacts
			// otherwise we don't get the correct data!!!!!!
			->group('Email.id')
			->group('Contact.id')
		);
		
		if ($out) {
			echo '<h3>Query</h3><pre>' . print_r((string) $db->getQuery(false), 1) . '</pre>';
		}
		
		try {
			$results = $db->loadObjectList();
			if (empty($results)) {
				throw new Exception('No records found');
			}
		}
		catch (Exception $e) {
			if ($out) {
				echo $e->getMessage() . "\n<pre>" . $e->getTraceAsString() . "</pre>";
			}
			jexit();
		}
		
		$now = JFactory::getDate();
		
		foreach ($results as $result) {
			if ($out) {
				echo '<h3>Result</h3><pre>' . print_r($result, 1) . '</pre>';
			}
			
			$mailer = JFactory::getMailer();
			$mailer->ClearAllRecipients();
			$mailer->AddBCC($result->email, $result->first_name . ' ' . $result->last_name);
			$mailer->SetFrom($result->fromemail, $result->fromname);
			$mailer->setSubject($result->subject);
			$mailer->setBody($result->htmlbody);
			$mailer->IsHTML(true);
			$mailer->AltBody = $result->plainbody;
			
			if ($out) {
				echo ('<h3>Mailer</h3><pre>' . print_r($mailer, 1) . '</pre>');
			}
			
			$sent = $mailer->Send();
			
			if (!$sent) {
				if ($out) {
					echo ('<h3>COULD NOT SEND MAIL!!!!</h3>');
				}
				continue;
			}
			$object = new stdClass;
			$object->email_id = $result->email_id;
			$object->lead_id  = $result->lead_id;
			$object->sent     = $now->toSql();
			try {
				$db->insertObject('#__jinbound_emails_records', $object);
			}
			catch (Exception $e) {
				if ($out) {
					echo $e->getMessage() . "\n" . $e->getTraceAsString();
				}
				continue;
			}
		}
		
		echo "\n";
		jexit();
	}
}
