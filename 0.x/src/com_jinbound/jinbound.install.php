<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

class com_JInboundInstallerScript
{
	public function postflight($type, $parent) {
		switch ($type) {
			case 'install':
			case 'discover_install':
				// fix component config
				$this->_saveDefaults($parent);
				break;
		}
	}
	
	private function _saveDefaults(&$parent) {
		jimport('joomla.filesystem.file');
		jimport('joomla.form.form');
		
		if (method_exists($parent, 'extension_root')) {
			$configfile = $parent->getPath('extension_root') . '/config.xml';
		}
		else {
			$configfile = $parent->getParent()->getPath('extension_root') . '/config.xml';
		}
		
		if (!JFile::exists($configfile)) {
			return;
		}
		
		$xml       = JFile::read($configfile);
		$form      = JForm::getInstance('installer', $xml, array(), false, '/config');
		$params    = array();
		$fieldsets = $form->getFieldsets();
		
		if (!empty($fieldsets)) {
			foreach ($fieldsets as $fieldset) {
				$fields = $form->getFieldset($fieldset->name);
				if (!empty($fields)) {
					foreach ($fields as $name => $field) {
						$params[$field->__get('name')] = $field->__get('value');
					}
				}
			}
		}
		
		$db = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->update('#__extensions')
			->set('params = ' . $db->quote(json_encode($params)))
			->where('element = ' . $db->quote($parent->get('element')))
		);
		try {
			$db->query();
		}
		catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		
	}
}