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
 * This models supports retrieving lists of leads.
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelLeads extends JModelList
{
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();

		// main query
		$query = $db->getQuery(true)
			// Select the required fields from the table.
			->select('Lead.*')
			->from('#__jinbound_leads AS Lead')
			->select('User.email AS email')
			->select('User.name AS name')
			->select('User.username AS username')
			->leftJoin('#__users AS User ON User.id = Lead.user_id')
			->group('Lead.id')
		;

		return $query;
	}
}
