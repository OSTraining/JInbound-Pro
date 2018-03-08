<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundAssetTable', 'tables/asset');

class JInboundTableField extends JInboundAssetTable
{
	function __construct(&$db) {
		parent::__construct('#__jinbound_fields', 'id', $db);
	}

	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_jinbound.field.'.(int) $this->$k;
	}

	protected function _getAssetTitle() {
		return $this->title;
	}
	
	protected function _compat_getAssetParentId($table = null, $id = null) {
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_jinbound.fields');
		if (empty($asset->id))
		{
			JInbound::registerHelper('access');
			JInboundHelperAccess::saveRules('fields', array('core.dummy' => array()), false);
			$asset->loadByName('com_jinbound.fields');
		}
		return $asset->id;
	}
	
	/**
	 * Overload the bind method to store params
	 * 
	 * @param $array
	 * @param $ignore
	 */
	function bind($array, $ignore = '')
	{
		if (array_key_exists('params', $array) && is_array($array['params']))
		{
			// we have to do some extra crap here, to avoid a lot of extra coding in other parts of the application
			// due to the way the UI is coded (can't be helped without massive amounts of js)
			// we get a pretty funky array for attrs and opts
			// to "fix" this, we convert those here into simple key => value pairs
			// instead of having 2 arrays, one for keys & one for values
			// now, we could very well use php functions to accomplish this, but we want to ensure
			// that the data is preserved correctly for later use
			foreach (array('attrs', 'opts') as $param)
			{
				// make sure that this key exists & isn't empty
				if (!array_key_exists($param, $array['params']) || empty($array['params'][$param]))
				{
					continue;
				}
				// make sure the array is actually an array
				if (!is_array($array['params'][$param]))
				{
					continue;
				}
				// this array stores the actual values
				$realValues = array();
				// make sure we have the necessary keys
				if (array_key_exists('key', $array['params'][$param]) && !empty($array['params'][$param]['key']) && array_key_exists('value', $array['params'][$param]))
				{
					foreach ($array['params'][$param]['key'] as $i => $key)
					{
						// make sure we have a corresponding value
						if (!array_key_exists($i, $array['params'][$param]['value']))
						{
							continue;
						}
						$value = $array['params'][$param]['value'][$i];
						// if key is empty, bail
						if ('' === $key)
						{
							continue;
						}
						// woohoo! nailed it! add it to our array
						$realValues[$key] = $value;
					}
				}
				// reset the array
				$array['params'][$param] = $realValues;
			}
			// convert to string
			$array['params'] = json_encode($array['params']);
		}
		// Attempt to bind the data.
		return parent::bind($array, $ignore);
	}
	
	/**
	 * Overload the store method for the Fields table.
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
			// New field
			$this->created = $date->toSql();
			$this->created_by = $user->get('id');
		}
		
		// Attempt to store the user data.
		return parent::store($updateNulls);
	}
}
