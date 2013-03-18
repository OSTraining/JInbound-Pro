<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.application.component.modellist') or jimport('legacy.model.list');
JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');

/**
 * Base list model class
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundListModel extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = JInbound::COM;

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = JInbound::COM;

	private $_parent = null;

	private $_items = null;
	
	private $_registryColumns = null;

	function __construct($config = array()) {
		parent::__construct($config);
	}
	
	public function getItems() {
		$items = parent::getItems();
		// if we have no columns to alter, we're done
		if (!is_array($this->_registryColumns) || empty($this->_registryColumns)) {
			return $items;
		}
		// alter items, if any, to convert json data to registries
		if (is_array($items) && !empty($items)) {
			foreach ($items as &$item) {
				foreach ($this->_registryColumns as $col) {
					if (!property_exists($item, $col)) {
						continue;
					}
					$registry = new JRegistry();
					$registry->loadString($item->$col);
					$item->$col = $registry;
				}
			}
		}
		return $items;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);

		$this->setState('filter.extension', $this->_extension);

		$user = JFactory::getUser();
		// get published status
		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '', 'string');
		if ((!$user->authorise('core.edit.state', JInbound::COM)) &&  (!$user->authorise('core.edit', JInbound::COM))) {
			// filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', 1);
		}
		else {
			$this->setState('filter.published', $published);
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $search);

		$params = JInbound::config();
		$this->setState('params', $params);
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
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.search');

		return parent::getStoreId($id);
	}

	/**
	 * The core "state" jhtml crap uses "archive" and we don't need that
	 *
	 */
	public function getPublishedStatus() {
		$list = array(
			JHtml::_('select.option',  '',  JText::_('COM_JINBOUND_SELECT_PUBLISHED'))
		,	JHtml::_('select.option',  '1', JText::_('COM_JINBOUND_SELECT_PUBLISHED_OPTION_PUBLISHED'))
		,	JHtml::_('select.option',  '0', JText::_('COM_JINBOUND_SELECT_PUBLISHED_OPTION_UNPUBLISHED'))
		,	JHtml::_('select.option', '-2', JText::_('COM_JINBOUND_SELECT_PUBLISHED_OPTION_TRASHED'))
		);
		return $list;
	}
	
	public function getOptionsFromQuery($query, $defaultText) {
		$default = json_decode(json_encode(array(
			'value' => ''
		,	'text' => $defaultText
		)));
		// Create a new query object.
		$db = $this->getDbo();
		$db->setQuery($query);
		try {
			$options = $db->loadObjectList();
			array_unshift($options, $default);
			return $options;
		}
		catch (Exception $e) {
			return array($default);
		}
	}

	/**
	 * give public read access to the model's context
	 *
	 */
	public function getContext() {
		return (string) $this->_context;
	}

	public function appendAuthorToQuery(&$query, $tablename, $created_by = 'created_by') {
		// clean our table name
		$tablename = JFilterInput::getInstance()->clean($tablename, 'cmd');
		// get our dbo for cleaning texts
		$db = JFactory::getDbo();
		// clean the created_by column
		$created_by = $db->quoteName($created_by);
		// our default names for guest & system
		$guest  = JText::_('COM_JINBOUND_AUTHOR_GUEST');
		$system = JText::_('COM_JINBOUND_AUTHOR_SYSTEM');
		// full column selection
		$column = $tablename . '.' . $created_by;
		// join over author
		$query
			->select('IF(' . $column . '=0,' . $db->Quote($guest) . ',IF(' . $column . '=-1,' . $db->Quote($system) . ',Author.name)) AS author_name')
			->select('IF(' . $column . '=0,' . $db->Quote(strtolower($guest)) . ',IF(' . $column . '=-1,' . $db->Quote(strtolower($system)) . ',Author.username)) AS author_username')
			->leftJoin("#__users AS Author ON Author.id = {$column}")
		;
	}
	
	public function filterSearchQuery(&$query, $search, $tablename, $pk = 'id', $columns = array('name')) {
		// clean our variables
		$filter    = JFilterInput::getInstance();
		$tablename = $filter->clean($tablename, 'cmd');
		$pk        = $filter->clean($pk, 'cmd');
		// get our dbo for cleaning texts
		$db = JFactory::getDbo();
		if (!empty($search)) {
			if (stripos($search, $pk . ':') === 0) {
				$query->where($tablename . '.' . $pk . ' = ' . (int) substr($search, 3));
			}
			else {
				$search = $db->Quote('%' . $db->getEscaped($search, true) . '%');
				$where = array();
				if (!empty($columns)) {
					foreach ($columns as &$column) {
						$column  = $filter->clean($column, 'cmd');
						$where[] = $tablename . '.' . $column . ' LIKE ' . $search;
					}
					$db->where('(' . implode(' OR ', $wheres) . ')');
				}
			}
		}
	}
	
}
