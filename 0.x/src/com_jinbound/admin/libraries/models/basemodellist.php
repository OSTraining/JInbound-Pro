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
	
	function __construct($config = array()) {
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
}
