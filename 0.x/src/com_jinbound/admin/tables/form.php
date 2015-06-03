<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundAssetTable', 'tables/asset');

class JInboundTableForm extends JInboundAssetTable
{	
	private $_formfields = null;

	function __construct(&$db) {
		parent::__construct('#__jinbound_forms', 'id', $db);
	}

	protected function _compat_getAssetName() {
		$k = $this->_tbl_key;
		return 'com_jinbound.form.'.(int) $this->$k;
	}

	protected function _getAssetTitle() {
		return $this->title;
	}
	
	protected function _compat_getAssetParentId($table = null, $id = null) {
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_jinbound.forms');
		if (empty($asset->id))
		{
			JInbound::registerHelper('access');
			JInboundHelperAccess::saveRules('forms', array('core.dummy' => array()), false);
			$asset->loadByName('com_jinbound.forms');
		}
		return $asset->id;
	}
	
	/**
	 * Overload the store method for the JInbound Fields table.
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
		
		// force formfields
		if (property_exists($this, 'formfields')) {
			$this->_formfields = $this->formfields;
			unset($this->formfields);
		}
		
		// go ahead and store now, as we'll need it later
		$store = parent::store($updateNulls);
		// handle xref but only if we have an id already ;)
		// if store was successful, there should now be an assigned id
		if ($store) {
			if (empty($this->_formfields)) {
				// go ahead and fetch these from the request
				// we currently have no need to get this data from anywhere else
				// but eventually we may need to
				// unfortunately, we have to extract this data from JForm, so we have to fetch via JForm array
				$jform = JFactory::getApplication()->input->get('jform', array(), null);
				// if for some dumb reason jform isn't an array we should account for this
				// as well, this variable may not be set
				if (is_array($jform) && array_key_exists('formfields', $jform)) {
					// we may receive an array from the request
					// this is doubtful, but let's go ahead and account for it anyways
					$this->_formfields = $jform['formfields'];
				}
				else {
					return $store;
				}
			}
			// we want the formfields value to be a string
			if (is_array($this->_formfields)) {
				$this->_formfields = implode('|', $this->_formfields);
			}
			// account for non-string variables by making it empty,
			// but only if the passed variable cannot be converted to a string
			else if (!is_string($this->_formfields)) {
				try {
					$this->_formfields = (string) $this->_formfields;
				}
				catch (Exception $e) {
					// ouch, failed converting to string - just make it blank
					$this->_formfields = '';
				}
			}
			// now we need to convert our string back into an array and inject the records
			$formfields = explode('|', $this->_formfields);
			if (!empty($formfields)) {
				// go ahead and purge the existing records
				$this->_db->setQuery($this->_db->getQuery(true)
					->delete('#__jinbound_form_fields')
					->where('form_id=' . intval($this->id))
				)->query();
				// go ahead & force our fields to be integers, unique, and only values
				JArrayHelper::toInteger($formfields);
				$formfields = array_unique($formfields);
				$formfields = array_values($formfields);
				$insert     = $this->_db->getQuery(true)
					->insert('#__jinbound_form_fields')
					->columns(array('form_id', 'field_id', 'ordering'))
				;
				$query      = false;
				// walk the array and convert to INSERT snippets
				// we're using for() instead of foreach() here so we have proper ordering :)
				for ($i = 0; $i < count($formfields); $i++) {
					$query = true;
					$insert->values(intval($this->id) . ", " . $formfields[$i] . ", $i");
				}
				if ($query) {
					// inject the new records
					$this->_db->setQuery($insert)->query();
				}
			}
		}
		// return the store value
		return $store;
	}
	
	public function bind($array, $ignore = '') {
		// make sure fields get set
		if (array_key_exists('formfields', $array)) {
			$this->_formfields = $array['formfields'];
			unset($array['formfields']);
		}
		return parent::bind($array, $ignore);
	}
	
}
