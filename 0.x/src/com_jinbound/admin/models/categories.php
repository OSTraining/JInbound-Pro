<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundListModel', 'models/basemodellist');

/**
 * This models supports retrieving lists of locations.
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelCategories extends JInboundListModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $context  = 'com_jinbound.categories';
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);
		
		$app = JFactory::getApplication();
		$value = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$limit = $value;
		$this->setState('list.limit', $limit);
		
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $search);
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
		$id	.= ':'.$this->getState('filter.search');

		return parent::getStoreId($id);
	}

	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
	
		// main query
		$query = $db->getQuery(true)
			// Select the required fields from the table.
			->select($this->getState('list.select', 'Category.*'))
			->from('#__jinbound_categories AS Category')
		;
		// add author to query
		$this->appendAuthorToQuery($query, 'Category');
		$this->filterSearchQuery($query, $this->getState('filter.search'), 'Category');

		// Add the list ordering clause.
		$orderCol = trim($this->state->get('list.ordering'));
		$orderDirn = trim($this->state->get('list.direction'));
		if (strlen($orderCol)) {
			$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		}

		// Group by filter
		$query->group('Category.id');
		return $query;
	}
}
