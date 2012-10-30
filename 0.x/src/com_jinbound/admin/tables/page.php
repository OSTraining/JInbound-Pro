<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.database.table');

class JInboundTablePage extends JTable
{
	public $id;
	public $catid;
	public $title;
	public $alias;
	public $heading;
	public $subheading;
	public $buttons;
	public $maintext;
	public $image;
	public $imagealt;
	public $metatitle;
	public $metadesc;
	public $created;
	public $created_by;
	public $modified;
	public $modified_by;
	public $published;
	public $checked_out;
	public $checked_out_time;

	function __construct(&$db) {
		parent::__construct('#__jinbound_pages', 'id', $db);
	}

	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_jinbound.page.'.(int) $this->$k;
	}

	protected function _getAssetTitle() {
		return $this->title;
	}

	protected function _getAssetParentId($table = null, $id = null) {
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_jinbound');
		return $asset->id;
	}
	
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
