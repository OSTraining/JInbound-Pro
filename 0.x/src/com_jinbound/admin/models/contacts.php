<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('contact');
JInbound::registerLibrary('JInboundListModel', 'models/basemodellist');

/**
 * This models supports retrieving lists of contacts.
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelContacts extends JInboundListModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $context  = 'com_jinbound.contacts';
	
	/**
	 * Constructor.
	 *
	 * @param       array   An optional associative array of configuration settings.
	 * @see         JController
	 */
	function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'Contact.published'
			,	'latest'
			);
		}
		
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);
		
		$app    = JFactory::getApplication();
		$format = $app->input->get('format', '', 'cmd');
		$end    = ('json' == $format ? '.json' : '');
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.start'.$end, 'filter_start', '', 'string');
		$this->setState('filter.start', $value);
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.end'.$end, 'filter_end', '', 'string');
		$this->setState('filter.end', $value);
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.priority'.$end, 'filter_priority', '', 'int');
		$this->setState('filter.priority', $value);
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.status'.$end, 'filter_status', '', 'int');
		$this->setState('filter.status', $value);
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
		$id	.= ':'.serialize($this->getState('filter.start'));
		$id	.= ':'.serialize($this->getState('filter.end'));
		$id	.= ':'.serialize($this->getState('filter.priority'));
		$id	.= ':'.serialize($this->getState('filter.status'));

		return parent::getStoreId($id);
	}
	
	public function getItems()
	{
		$items = parent::getItems();
		if (!empty($items))
		{
			$db = JFactory::getDbo();
			foreach ($items as &$item)
			{
				$item->conversions        = JInboundHelperContact::getContactConversions($item->id);
				$item->campaigns          = JInboundHelperContact::getContactCampaigns($item->id);
				$item->previous_campaigns = JInboundHelperContact::getContactCampaigns($item->id, true);
				$item->statuses           = JInboundHelperContact::getContactStatuses($item->id);
				$item->priorities         = JInboundHelperContact::getContactPriorities($item->id);
				// add forms
				$item->forms = array();
				if (!empty($item->conversions))
				{
					foreach ($item->conversions as $conversion)
					{
						$item->forms[$conversion->page_id] = $conversion->page_name;
					}
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
			}
		}
		return $items;
	}
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		
		// select columns
		$query = $db->getQuery(true)
			->select('Contact.*')
			->select('CONCAT_WS(' . $db->quote(' ') . ', Contact.first_name, Contact.last_name) AS full_name')
			->from('#__jinbound_contacts AS Contact')
			// get the latest form
			->select('Latest.created AS latest')
			->select('Latest.id AS latest_conversion_id')
			->select('Latest.page_id AS latest_conversion_page_id')
			->select('LatestPage.name AS latest_conversion_page_name')
			->select('LatestPage.formname AS latest_conversion_page_formname')
			->leftJoin('(' . $this->getDbo()->getQuery(true)
				->select('c1.*')
				->from('#__jinbound_conversions AS c1')
				->leftJoin('#__jinbound_conversions AS c2 ON c1.contact_id = c2.contact_id AND c1.created < c2.created')
				->where('c2.contact_id IS NULL')
			. ') AS Latest ON (Latest.contact_id = Contact.id)')
			->leftJoin('#__jinbound_pages AS LatestPage ON LatestPage.id = Latest.page_id')
			->group('Contact.id')
		;
		
		// add author to query
		//$this->appendAuthorToQuery($query, 'Contact');
		// filter query
		$this->filterSearchQuery($query, $this->getState('filter.search'), 'Contact', 'id', array(
			'first_name', 'last_name'
		));
		$this->filterPublished($query, $this->getState('filter.published'), 'Contact');
		
		$value = $this->getState('filter.start');
		if (!empty($value))
		{
			try
			{
				$date = new DateTime($value);
			}
			catch (Exception $e)
			{
				$date = false;
			}
			if ($date)
			{
				$query->where('Contact.created > ' . $db->quote($date->format('Y-m-d h:i:s')));
			}
		}
		
		$value = $this->getState('filter.end');
		if (!empty($value))
		{
			try
			{
				$date = new DateTime($value);
			}
			catch (Exception $e)
			{
				$date = false;
			}
			if ($date)
			{
				$query->where('Contact.created < ' . $db->quote($date->format('Y-m-d h:i:s')));
			}
		}
		
		// Add the list ordering clause.
		$listOrdering = $this->getState('list.ordering', 'Contact.created');
		$listDirn     = $db->escape($this->getState('list.direction', 'ASC'));
		$query->order($db->escape($listOrdering) . ' ' . $listDirn);

		return $query;
	}
	
	public function getCampaignsOptions() {
		$query = $this->getDbo()->getQuery(true)
		->select('Campaign.id AS value, Campaign.name as text')
		->from('#__jinbound_campaigns AS Campaign')
		->where('Campaign.published = 1')
		->group('Campaign.id')
		;
		return $this->getOptionsFromQuery($query, JText::_('COM_JINBOUND_SELECT_CAMPAIGN'));
	}
}
