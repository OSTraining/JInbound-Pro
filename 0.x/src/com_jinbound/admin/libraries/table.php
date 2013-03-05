<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
 @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.database.table');

class JInboundTable extends JTable
{
	/**
	 * Overload the store method
	 *
	 * @param       boolean Toggle whether null values should be updated.
	 * @return      boolean True on success, false on failure.
	 */
	public function store($updateNulls = false) {
		$date   = JFactory::getDate();
		$user   = JFactory::getUser();
		if ($this->id) {
			// Existing item
			$this->modified    = $date->toSql();
			$this->modified_by = $user->get('id');
		}
		else {
			// New item
			$this->created = $date->toSql();
			$this->created_by = $user->get('id');
		}
	
		return parent::store($updateNulls);
	}
}