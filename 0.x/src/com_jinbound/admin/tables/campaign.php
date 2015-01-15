<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundTable', 'table');

class JInboundTableCampaign extends JInboundTable
{

	function __construct(&$db) {
		parent::__construct('#__jinbound_campaigns', 'id', $db);
	}
	
	/**
	 * Redefined asset name, as we support action control
	 */
	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_jinbound.campaign.'.(int) $this->$k;
	}
	
	/**
	 * We provide our global ACL as parent
	 * @see JTable::_getAssetParentId()
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_jinbound.campaign');
		return $asset->id;
	}
	
	public function load($keys = null, $reset = true) {
		$load = parent::load($keys, $reset);
		if (is_string($this->params))
		{
			$registry = new JRegistry;
			$registry->loadString($this->params);
			$this->params = $registry;
		}
		return $load;
	}
	
	public function bind($array, $ignore = '') {
		if (isset($array['params'])) {
			$registry = new JRegistry;
			if (is_array($array['params'])) {
				$registry->loadArray($array['params']);
			}
			else if (is_string($array['params'])) {
				$registry->loadString($array['params']);
			}
			else if (is_object($array['params'])) {
				$registry->loadArray((array) $array['params']);
			}
			$array['params'] = (string) $registry;
		}
		return parent::bind($array, $ignore);
	}
}
