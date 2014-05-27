<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('contact');
JInbound::registerLibrary('JInboundAdminModel', 'models/basemodeladmin');

/**
 * This models supports retrieving a contact.
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelContact extends JInboundAdminModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $context  = 'com_jinbound.contact';

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm($this->option.'.'.$this->name, $this->name, array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		$campaigns = JInboundHelperContact::getContactCampaigns($form->getValue('id'));
		$value = array();
		
		if (is_array($campaigns) && !empty($campaigns))
		{
			foreach ($campaigns as $campaign)
			{
				$value[] = $campaign->id;
			}
		}
		
		$form->setValue('_campaigns', null, $value);
		// return the form
		return $form;
	}
	
	public function getItem($id = null)
	{
		$item = parent::getItem($id);
		$db   = JFactory::getDbo();
		
		$item->conversions        = array();
		$item->campaigns          = array();
		$item->statuses           = array();
		$item->previous_campaigns = array();
		$item->priorities         = array();
		
		if ($item->id)
		{
			$item->conversions        = JInboundHelperContact::getContactConversions($item->id);
			$item->campaigns          = JInboundHelperContact::getContactCampaigns($item->id);
			$item->previous_campaigns = JInboundHelperContact::getContactCampaigns($item->id, true);
			$item->statuses           = JInboundHelperContact::getContactStatuses($item->id);
			$item->priorities         = JInboundHelperContact::getContactPriorities($item->id);
		}
		
		// add tracks
		try
		{
			$item->tracks = $db->setQuery($db->getQuery(true)
				->select('Track.*')
				->from('#__jinbound_tracks AS Track')
				->where('Track.cookie = ' . $db->quote($item->cookie))
				->order('Track.created DESC')
			)->loadObjectList();
		}
		catch (Exception $e)
		{
			$item->tracks = array();
		}
		
		return $item;
	}
	
	/**
	 * set the lead status details for an item
	 * 
	 * @param unknown_type $contact_id
	 * @param unknown_type $campaign_id
	 * @param unknown_type $status_id
	 * @return mixed
	 */
	public function status($contact_id, $campaign_id, $status_id, $creator = null) {
		//$dispatcher = JDispatcher::getInstance();
		//JPluginHelper::importPlugin('content');
		
		$db = JFactory::getDbo();
		
		// some info for the status and priority
		$date    = new DateTime();
		$created = $date->format('Y-m-d H:i:s');
		$creator = JFactory::getUser($creator)->get('id');
		// save the status
		$return = $db->setQuery($db->getQuery(true)
			->insert('#__jinbound_contacts_statuses')
			->columns(array(
				'status_id'
			,	'campaign_id'
			,	'contact_id'
			,	'created'
			,	'created_by'
			))
			->values($db->quote($status_id)
			. ', ' . $db->quote($campaign_id)
			. ', ' . $db->quote($contact_id)
			. ', ' . $db->quote($created)
			. ', ' . $db->quote($creator)
			)
		)->query();
		
		/*
		 * TODO fix this for campaign id
		 * 
		// Trigger the onContentChangeState event.
		$result = $dispatcher->trigger('onContentChangeState', array(
			'com_jinbound.contact.status'
		,	array($contact_id), $status_id));
		
		if (in_array(false, $result, true)) {
			return false;
		}
		*/
		
		return $return;
	}
	
	/**
	 * set the lead priority details
	 * 
	 * @param unknown_type $id
	 * @param unknown_type $value
	 * @return mixed
	 */
	public function priority($contact_id, $campaign_id, $priority_id, $creator = null) {
		//$dispatcher = JDispatcher::getInstance();
		//JPluginHelper::importPlugin('content');
		
		$db = JFactory::getDbo();
		
		// some info for the status and priority
		$date    = new DateTime();
		$created = $date->format('Y-m-d H:i:s');
		$creator = JFactory::getUser($creator)->get('id');
		// save the status
		$return = $db->setQuery($db->getQuery(true)
			->insert('#__jinbound_contacts_priorities')
			->columns(array(
				'priority_id'
			,	'campaign_id'
			,	'contact_id'
			,	'created'
			,	'created_by'
			))
			->values($db->quote($priority_id)
			. ', ' . $db->quote($campaign_id)
			. ', ' . $db->quote($contact_id)
			. ', ' . $db->quote($created)
			. ', ' . $db->quote($creator)
			)
		)->query();
		
		/*
		 * TODO fix this for campaign id
		 * 
		// Trigger the onContentChangeState event.
		$result = $dispatcher->trigger('onContentChangeState', array(
			'com_jinbound.contact.priority'
		,	array($contact_id), $priority_id));
		
		if (in_array(false, $result, true)) {
			return false;
		}
		*/
		
		return $return;
	}
	
	/**
	 * get lead notes
	 * 
	 * @param unknown_type $id
	 */
	public function getNotes($id = null) {
		if (property_exists($this, 'item') && $this->item && $this->item->id == $id) {
			$item = $this->item;
		}
		else {
			$item = $this->getItem($id);
		}
		$db = JFactory::getDbo();
		
		try {
			$notes = $db->setQuery($db->getQuery(true)
				->select('Note.id, Note.created, Note.text, User.name AS author')
				->from('#__jinbound_notes AS Note')
				->leftJoin('#__users AS User ON User.id = Note.created_by')
				->where('Note.published = 1')
				->where('Note.lead_id = ' . (int) $item->id)
				->group('Note.id')
			)->loadObjectList();
			if (!is_array($notes) || empty($notes)) {
				throw new Exception('Empty');
			}
		}
		catch (Exception $e) {
			$notes = array();
		}
		
		return $notes;
	}
}
