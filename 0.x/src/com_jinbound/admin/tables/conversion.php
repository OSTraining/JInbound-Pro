<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundTable', 'table');

class JInboundTableConversion extends JInboundTable
{
	function __construct(&$db) {
		parent::__construct('#__jinbound_conversions', 'id', $db);
	}
	
	/**
	 * Override to handle formdata
	 * 
	 * (non-PHPdoc)
	 * @see JTable::load()
	 */
	public function load($keys = null, $reset = true) {
		// load
		$load = parent::load($keys, $reset);
		// convert formdata to an object
		$registry = new JRegistry;
		if (is_string($this->formdata)) {
			$registry->loadString($this->formdata);
		}
		else if (is_array($this->formdata)) {
			$registry->loadArray($this->formdata);
		}
		else if (is_object($this->formdata)) {
			$registry->loadObject($this->formdata);
		}
		$this->formdata = $registry;
		return $load;
	}
	
	/**
	 * Override to handle formdata
	 * 
	 * (non-PHPdoc)
	 * @see JTable::bind()
	 */
	public function bind($array, $ignore = '') {
		if (isset($array['formdata'])) {
			$registry = new JRegistry;
			if (is_array($array['formdata'])) {
				$registry->loadArray($array['formdata']);
			}
			else if (is_string($array['formdata'])) {
				$registry->loadString($array['formdata']);
			}
			else if (is_object($array['formdata'])) {
				
			}
			$array['formdata'] = (string) $registry;
		}
		return parent::bind($array, $ignore);
	}
}
