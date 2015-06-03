<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
 @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JinboundTable', 'table');

/**
 * This is a base class for backwards compat
 */
class JInboundBaseAssetTable extends JInboundTable
{
	public $asset_id;
	
	/**
	 * Our compat method
	 *
	 * @param unknown_type $table
	 * @param unknown_type $id
	 */
	protected function _compat_getAssetParentId($table = null, $id = null) {
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_jinbound');
		return $asset->id;
	}
}

/**
 * Declare the shim class that defines _getAssetParentId in different ways based on version
 *
 */
if (JInbound::version()->isCompatible('3.2.0'))
{
	class JInboundAssetTable extends JInboundBaseAssetTable
	{
		protected function _getAssetParentId(JTable $table = null, $id = null)
		{
			return $this->_compat_getAssetParentId($table, $id);
		}
	}
}
else
{
	class JInboundAssetTable extends JInboundBaseAssetTable
	{
		protected function _getAssetParentId($table = null, $id = null)
		{
			return $this->_compat_getAssetParentId($table, $id);
		}
	}
}
