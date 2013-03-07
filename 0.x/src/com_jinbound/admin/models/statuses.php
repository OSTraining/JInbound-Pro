<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.application.component.modellist');
JLoader::register('JInboundListModel', JPATH_ADMINISTRATOR.'/components/com_jinbound/libraries/models/basemodellist.php');

/**
 * This models supports retrieving lists of statuses.
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelStatuses extends JModelList
{
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		// main query
		$query = $db->getQuery(true)
			// Select the required fields from the table.
			->select('Status.*')
			->from('#__jinbound_lead_statuses AS Status')
		;
		return $query;
	}
}
