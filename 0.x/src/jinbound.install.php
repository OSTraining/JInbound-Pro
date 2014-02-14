<?php
/**
 * @package		JInbound
 * @subpackage	pkg_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

class pkg_JInboundInstallerScript
{
	public function postflight($type, $parent) {
		if ('uninstall' == $type) {
			return;
		}
		$debug = defined('JDEBUG') && JDEBUG;
		$app   = JFactory::getApplication();
		$db    = JFactory::getDbo();
		// enable the plugins
		$db->setQuery('UPDATE `#__extensions` SET `enabled`=1 WHERE `element`="jinbound" AND `folder` IN ("system", "content", "user") AND `type`="plugin"');
		try {
			$db->query();
			if ($debug) {
				$app->enqueueMessage('Enabled plugins...');
			}
		}
		catch (Exception $e) {
			$app->enqueueMessage($e->getMessage(), 'error');
		}
		// enable the social bookmarks module
		// search the module table to see if any modules are already assigned to the module position
		// if not, find our default module and enable it
		$db->setQuery('SELECT id FROM #__modules WHERE position = "jinbound_social" AND published = 1');
		try {
			$ids = $db->loadColumn();
			if ($debug) {
				$app->enqueueMessage('Modules found: ' . implode(', ', $ids));
			}
		}
		catch (Exception $e) {
			$ids = false;
			$app->enqueueMessage($e->getMessage(), 'error');
		}
		
		// none published - update the default if possible
		if (empty($ids)) {
			// get the default module ids
			$db->setQuery('SELECT id FROM #__modules WHERE module = "mod_jinbound_social_bookmark"');
			try {
				$mods = $db->loadColumn();
				// handle mods (TODO check if there's only one? limit?)
				if (!empty($mods)) {
					// update all the publication columns for the module
					$db->setQuery('UPDATE #__modules SET position = "jinbound_social", publish_up = "0000-00-00 00:00:00", publish_down = "0000-00-00 00:00:00", access = 1, published = 1 WHERE module = "mod_jinbound_social_bookmark"');
					$db->query();
					// get rid of the page associations
					$db->setQuery('DELETE FROM #__modules_menu WHERE moduleid IN(' . implode(',', $mods) . ')');
					$db->query();
					// get ready to insert new page associations
					$insert = $db->getQuery(true)->insert('#__modules_menu')->columns(array('moduleid', 'menuid'));
					foreach ($mods as $mod) {
						// add association
						$insert->values($mod . ',0');
						// go ahead and fix the params in this loop
						$this->_saveModuleDefaults($mod, $parent);
					}
					$db->setQuery($insert);
					$db->query();
				}
			}
			catch (Exception $e) {
				$app->enqueueMessage($e->getMessage(), 'error');
			}
		}
		
	}
	
	
	
	private function _saveModuleDefaults($modid, &$parent) {
		jimport('joomla.filesystem.file');
		jimport('joomla.form.form');
		
		$configfile = JPATH_ROOT . '/modules/mod_jinbound_social_buttons/mod_jinbound_social_buttons.xml';
	
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
				->update('#__modules')
				->set('params = ' . $db->quote(json_encode($params)))
				->where('id = ' . (int) $modid)
		);
		try {
			$db->query();
		}
		catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	
	}
}