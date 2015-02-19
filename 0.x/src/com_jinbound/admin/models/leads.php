<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$e = new Exception(__FILE__);
JLog::add('JInboundModelLeads is deprecated. ' . $e->getTraceAsString(), JLog::WARNING, 'deprecated');

JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundListModel', 'models/basemodellist');

/**
 * This models supports retrieving lists of leads.
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelLeads extends JInboundListModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_jinbound.leads';
	protected $context  = 'com_jinbound.leads';
	
	/**
	 * Constructor.
	 *
	 * @param       array   An optional associative array of configuration settings.
	 * @see         JController
	 */
	function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'Contact.name'
			,	'Lead.published'
			,	'Lead.created'
			,	'Page.formname'
			,	'Priority.name'
			,	'Campaign.name'
			,	'Status.name'
			,	'Lead.note'
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
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		
		// select columns
		$query = $db->getQuery(true)
			->select('Lead.*')
			->select('Contact.email_to AS email')
			->select('Contact.name AS name')
			->select('Page.formbuilder')
			->select('Page.formname')
			->select('Priority.name AS priority_name')
			->select('Status.name AS status_name')
			->select('Campaign.name AS campaign_name')
		;
		
		// group by contact but only on the main leads page
		if ('leads' == JFactory::getApplication()->input->get('view')) {
			$query
			->select('GROUP_CONCAT(Lead.id) AS lead_ids')
			->from('#__contact_details AS Contact')
			->leftJoin('#__jinbound_leads AS Lead ON Contact.id = Lead.contact_id')
			->group('Contact.id')
			;
		}
		// group by lead
		else {
			$query
			->select('Lead.id AS lead_ids') // just to keep columns the same
			->from('#__jinbound_leads AS Lead')
			->leftJoin('#__contact_details AS Contact ON Contact.id = Lead.contact_id')
			->group('Lead.id')
			;
		}
		
		// join in the remaining tables
		// this has to be done after the initial Lead/Contact from/join to prevent sql errors
		$query
		->leftJoin('#__jinbound_pages AS Page ON Page.id = Lead.page_id')
		->leftJoin('#__jinbound_priorities AS Priority ON Priority.id = Lead.priority_id')
		->leftJoin('#__jinbound_lead_statuses AS Status ON Status.id = Lead.status_id')
		->leftJoin('#__jinbound_campaigns AS Campaign ON Campaign.id = Lead.campaign_id')
		;
		
		// add author to query
		$this->appendAuthorToQuery($query, 'Lead');
		// filter query
		$this->filterSearchQuery($query, $this->getState('filter.search'), 'Lead', 'id', array(
			'Contact.email_to', 'Contact.name', 'first_name', 'last_name'
		));
		$this->filterPublished($query, $this->getState('filter.published'), 'Lead');
		
		$value = $this->getState('filter.start');
		if (!empty($value)) {
			try {
				$date = new DateTime($value);
			}
			catch (Exception $e) {
				$date = false;
			}
			if ($date) {
				$query->where('Lead.created > ' . $db->quote($date->format('Y-m-d h:i:s')));
			}
		}
		
		$value = $this->getState('filter.end');
		if (!empty($value)) {
			try {
				$date = new DateTime($value);
			}
			catch (Exception $e) {
				$date = false;
			}
			if ($date) {
				$query->where('Lead.created < ' . $db->quote($date->format('Y-m-d h:i:s')));
			}
		}
		
		$value = $this->getState('filter.priority');
		if (!empty($value)) {
			$query->where('Lead.priority_id = ' . (int) $value);
		}
		
		$value = $this->getState('filter.status');
		if (!empty($value)) {
			$query->where('Lead.status_id = ' . (int) $value);
		}
		
		// Add the list ordering clause.
		$listOrdering = $this->getState('list.ordering', 'Lead.created');
		$listDirn     = $db->escape($this->getState('list.direction', 'ASC'));
		$query->order($db->escape($listOrdering) . ' ' . $listDirn);

		return $query;
	}
	
	
	/**
	 *
	 */
	public function getPriorityOptions() {
		$db = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->select($db->quoteName('id'))
			->select($db->quoteName('name'))
			->from('#__jinbound_priorities')
			->where($db->quoteName('published') . ' = 1')
		);
	
		try {
			$options = $db->loadObjectList();
		}
		catch (Exception $e) {
			// don't bother if there's an issue
		}
	
		$list = array(JHtml::_('select.option', '', JText::_('COM_JINBOUND_SELECT_PRIORITY')));
	
		if (!empty($options)) {
			foreach ($options as $option) {
				$list[] = JHtml::_('select.option', $option->id, $option->name);
			}
		}
	
		return $list;
	}
	
	/**
	 *
	 */
	public function getStatusOptions() {
		$db = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->select($db->quoteName('id'))
			->select($db->quoteName('name'))
			->from('#__jinbound_lead_statuses')
			->where($db->quoteName('published') . ' = 1')
		);
		
		try {
			$options = $db->loadObjectList();
		}
		catch (Exception $e) {
			// don't bother if there's an issue
		}
		
		$list = array(JHtml::_('select.option', '', JText::_('COM_JINBOUND_SELECT_LEAD_STATUS')));
		
		if (!empty($options)) {
			foreach ($options as $option) {
				$list[] = JHtml::_('select.option', $option->id, $option->name);
			}
		}
		
		return $list;
	}
}
