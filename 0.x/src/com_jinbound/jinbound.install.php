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
			case 'update':
				$this->_checkContactCategory();
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
	
	private function _checkContactCategory() {
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->select('id')
			->from('#__categories')
			->where($db->quoteName('extension') . ' = ' . $db->quote('com_contact'))
			->where($db->quoteName('published') . ' = 1')
			->where($db->quoteName('note') . ' = ' . $db->quote('com_jinbound'))
		);
		try {
			$categories = $db->loadColumn();
		}
		catch (Exception $e) {
			$app->enqueueMessage($e->getMessage());
			return;
		}
		if (is_array($categories) && !empty($categories)) {
			$app->enqueueMessage(JText::_('COM_JINBOUND_CONTACT_CATEGORIES_FOUND'));
			return;
		}
		jimport('joomla.database.table');
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_contact/tables');
		$table = JTable::getInstance('Category');
		$bind = array(
			'parent_id'   => $table->getRootId(),
			'extension'   => 'com_contact',
			'title'       => JText::_('COM_JINBOUND_DEFAULT_CONTACT_CATEGORY_TITLE'),
			'note'        => 'com_jinbound',
			'description' => JText::_('COM_JINBOUND_DEFAULT_CONTACT_CATEGORY_DESCRIPTION'),
			'published'   => 1,
			'language'    => '*'
		);
		if (!$table->bind($bind)) {
			$app->enqueueMessage('bind error');
			return;
		}
		if (!$table->check()) {
			$app->enqueueMessage('check error');
			return;
		}
		if (!$table->store()) {
			$app->enqueueMessage('store error');
			return;
		}
		$table->moveByReference(0, 'last-child', $table->id);
	}
}
