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
			,	'Email.name'
			,	'Email.published'
			,	'Email.sendafter'
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
			->select('Email.*')
			->select('Campaign.name AS campaign_name')
			->from('#__jinbound_emails AS Email')
			->leftJoin('#__jinbound_campaigns AS Campaign ON Email.campaign_id = Campaign.id')
			->group('Email.id')
			->order('Campaign.name ASC')
		;
		
		$this->appendAuthorToQuery($query, 'Email');
		$this->filterSearchQuery($query, $this->state->get('filter.search'), 'Email', 'id', array('name', 'subject', 'Campaign.name'));
		$this->filterPublished($query, $this->getState('filter.published'), 'Email');
		
		// Add the list ordering clause.
		$listOrdering = $this->getState('list.ordering', 'Email.sendafter');
		$listDirn     = $db->escape($this->getState('list.direction', 'ASC'));
		$query->order($db->escape($listOrdering) . ' ' . $listDirn);

		return $query;
	}
	
	/**
	 * Method to send all the emails that need to be sent
	 * 
	 */
	public function send() {
		JInbound::registerHelper('url');
		JPluginHelper::importPlugin('content');
		
		$db         = $this->getDbo();
		$out        = JInbound::config("debug", 0);
		$interval   = $out ? 'MINUTE' : 'DAY';
		$now        = JFactory::getDate();
		$params     = new JRegistry;
		$dispatcher = JDispatcher::getInstance();
		
		// NOTE: This query is kind of hairy and a little complicated, edit at your own risk!!!
		$db->setQuery($db->getQuery(true)
			->select('Lead.first_name AS first_name')
			->select('Lead.last_name AS last_name')
			->select('Lead.created AS created')
			->select('Lead.id AS lead_id')
			->select('Lead.formdata AS form')
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
			->leftJoin('#__jinbound_subscriptions AS Sub ON Contact.id = Sub.contact_id')
			->where('Record.id IS NULL')
			->where('DATE_ADD(Lead.created, INTERVAL Email.sendafter ' . $interval . ') < UTC_TIMESTAMP()')
			->where('Email.published = 1')
			->where('Page.published = 1')
			->where('Campaign.published = 1')
			->where('Sub.enabled <> 0')
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
		
		foreach ($results as $result) {
			// parse form data
			$reg = new JRegistry;
			$reg->loadString($result->form);
			$arr = $reg->toArray();
			$tags = array();
			foreach (array_keys($arr['lead']) as $tag) {
				$tags[] = 'email.lead.' . $tag;
			}
			$reg = $reg->toObject();
			// trigger an event before parsing
			$status = $dispatcher->trigger('onContentBeforeDisplay', array('com_jinbound.email', &$result, &$params, 0));
			//if (in_array(false, $status, true)) {
				//continue;
			//}
			// replace email tags
			$result->htmlbody  = $this->_replaceTags($result->htmlbody,  $reg, $tags);
			$result->plainbody = $this->_replaceTags($result->plainbody, $reg, $tags);
			// add unsubscribe link to email contents
			$unsubscribe       = JInboundHelperUrl::toFull(JInboundHelperUrl::task('unsubscribe', false, array('email' => $result->email)));
			$result->htmlbody  = $result->htmlbody  . JText::sprintf('COM_JINBOUND_UNSUBSCRIBE_HTML',  $unsubscribe);
			$result->plainbody = $result->plainbody . JText::sprintf('COM_JINBOUND_UNSUBSCRIBE_PLAIN', $unsubscribe);
			// trigger an event after parsing
			$dispatcher->trigger('onContentAfterDisplay', array('com_jinbound.email', &$result, &$params, 0));
			
			if ($out) {
				echo '<h3>Result</h3><pre>' . htmlspecialchars(print_r($result, 1)) . '</pre>';
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
	
	
	private static function _replaceTags($string, $object, $extra = false) {
		$out  = JInbound::config("debug", 0);
		if ($out) echo ('<h3>Email Tags</h3>');
		$tags = array(
			'email.lead.first_name'
		,	'email.lead.last_name'
		,	'email.lead.email'
		);
	
		if ($extra && is_array($extra)) {
			$tags = array_merge($tags, $extra);
		}
		array_unique($tags);
		
		if ($out) echo ('<h4>Tags</h4><pre>' . print_r($tags, 1) . '</pre>');
		if ($out) echo ('<h4>Object</h4><pre>' . print_r($object, 1) . '</pre>');
	
		if (!empty($tags)) foreach ($tags as $tag) {
			if (false === stripos($string, $tag)) {
				continue;
			}
			$parts   = explode('.', $tag);
			$context = array_shift($parts);
			$params  = false;
			$value   = false;
			if ($out) echo ('<h4>Context</h4><pre>' . print_r($context, 1) . '</pre>');
			if ($out) echo ('<h4>Parts</h4><pre>' . print_r($parts, 1) . '</pre>');
			while (!empty($parts)) {
				$part = array_shift($parts);
				if ($out) echo ('<h4>Part</h4><pre>' . print_r($part, 1) . '</pre>');
				// handle the value differently based on it's type
				if ($value) {
					// arrays should have the key available
					if (is_array($value) && array_key_exists($part, $value)) {
						$value = $value[$part];
					}
					// JRegistry uses get() for values
					else if (is_object($value) && $value instanceof JRegistry) {
						$value = $value->get($part);
					}
					// normal object
					else if (is_object($value) && property_exists($value, $part)) {
						$value = $value->{$part};
					}
					// object with this method
					else if (is_object($value) && method_exists($value, $part)) {
						$value = call_user_func(array($value, $part));
					}
					// don't know what to do here...
					else {
						$value = '';
						break;
					}
				}
				else {
					$value = $object->{$part};
				}
				if ($out) echo ('<h4>Value</h4><pre>' . print_r($value, 1) . '</pre>');
			}
			$string = str_ireplace("{%$tag%}", $value, $string);
		}
		if ($out) echo ('<h4>String</h4><pre>' . htmlspecialchars(print_r($string, 1)) . '</pre>');
		return $string;
	}
}
