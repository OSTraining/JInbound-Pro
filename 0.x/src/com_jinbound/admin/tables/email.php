<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundTable', 'table');

class JInboundTableEmail extends JInboundTable
{

	function __construct(&$db) {
		parent::__construct('#__jinbound_emails', 'id', $db);
	}
	
	/**
	 * Redefined asset name, as we support action control
	 */
	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_jinbound.email.'.(int) $this->k;
	}
	
	/**
	 * We provide our global ACL as parent
	 * @see JTable::_getAssetParentId()
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_jinbound');
		return $asset->id;
	}
	
	/**
	 * override to save versions
	 *
	 * (non-PHPdoc)
	 * @see JInboundTable::store()
	 */
	public function store($updateNulls = false) {
		$app = JFactory::getApplication();
		// we have to determine if this email is new or not
		$isNew = empty($this->id);
		// if it is new, we can simply save it and insert a new record into the versions table
		if ($isNew) {
			// save this email first
			$store = parent::store($updateNulls);
			// now run a query to insert the values from this email into the versions table
			$this->_db->setQuery('INSERT INTO #__jinbound_emails_versions (email_id, subject, htmlbody, plainbody) SELECT id, subject, htmlbody, plainbody FROM #__jinbound_emails WHERE id = ' . $this->id);
			try {
				$this->_db->query();
			}
			catch (Exception $e) {
				$app->enqueueMessage($e->getMassage(), 'error');
				return $store;
			}
			// now return the store result
			return $store;
		}
		// if it isn't new, we have to pull the previous version and check the texts
		else {
			// if any of the texts in this version of the email differ
			// we have to insert a new version of the email
			
			// pull the original from the database
			$this->_db->setQuery('SELECT subject, htmlbody, plainbody FROM #__jinbound_emails WHERE id = ' . $this->id);
			try {
				$original = $this->_db->loadObject();
			}
			catch (Exception $e) {
				$app->enqueueMessage($e->getMassage(), 'error');
				return parent::store($updateNulls);
			}
			// go ahead and store the new, then update versions to reflect
			$store = parent::store($updateNulls);
			// compare the original to the new
			// if the old matches, just store & bail
			if ($original->subject == $this->subject && $original->htmlbody == $this->htmlbody && $original->plainbody == $this->plainbody) {
				return $store;
			}
			// there is a difference - insert a new version record before store
			$this->_db->setQuery('INSERT INTO #__jinbound_emails_versions (email_id, subject, htmlbody, plainbody) SELECT id, subject, htmlbody, plainbody FROM #__jinbound_emails WHERE id = ' . $this->id);
			try {
				$this->_db->query();
			}
			catch (Exception $e) {
				$app->enqueueMessage($e->getMassage(), 'error');
				return $store;
			}
			// now return the store result
			return $store;
		}
	}
}
