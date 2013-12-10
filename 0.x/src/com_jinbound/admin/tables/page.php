<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundTable', 'table');

class JInboundTablePage extends JInboundTable
{
	function __construct(&$db) {
		parent::__construct('#__jinbound_pages', 'id', $db);
	}
	
	function check() {
		// Check for valid name.
		if (trim($this->name) == '') {
			$this->setError(JText::_('COM_JINBOUND_WARNING_PROVIDE_VALID_NAME'));
			return false;
		}
	
		if (empty($this->alias)) {
			$this->alias = $this->name;
		}
		$this->alias = JApplication::stringURLSafe($this->alias);
		if (trim(str_replace('-', '', $this->alias)) == '') {
			$this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}
		
		return parent::check();
	}
	
	public function load($keys = null, $reset = true) {
		$load = parent::load($keys, $reset);
		if (is_string($this->formbuilder)) {
			$registry = new JRegistry;
			$registry->loadString($this->formbuilder);
			$this->formbuilder = $registry;
		}
		return $load;
	}
	
	/**
	 * overload bind
	 */
	public function bind($array, $ignore = '') {
		// parameters
		if (isset($array['formbuilder'])) {
			$registry = new JRegistry;
			if (is_array($array['formbuilder'])) {
				$registry->loadArray($array['formbuilder']);
			}
			else if (is_string($array['formbuilder'])) {
				$registry->loadString($array['formbuilder']);
			}
			else if (is_object($array['formbuilder'])) {
	
			}
			$array['formbuilder'] = (string) $registry;
		}
	
		return parent::bind($array, $ignore);
	}
	
	public function store($updateNulls = false) {
		// Verify that the alias is unique
		$table = JTable::getInstance('Page', 'JInboundTable');
		if ($table->load(array('alias'=>$this->alias, 'category'=>$this->category)) && ($table->id != $this->id || $this->id==0)) {
			$this->setError(JText::_('COM_JINBOUND_ERROR_UNIQUE_ALIAS'));
			return false;
		}
		// Attempt to store the user data.
		return parent::store($updateNulls);
	}
}
