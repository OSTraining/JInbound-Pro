<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
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
				if (!empty($mods)) {
					$db->setQuery('UPDATE #__modules SET position = "jinbound_social", publish_up = "0000-00-00 00:00:00", publish_down = "0000-00-00 00:00:00", access = 1, published = 1 WHERE module = "mod_jinbound_social_bookmark"');
					$db->query();
					$db->setQuery('DELETE FROM #__modules_menu WHERE moduleid IN(' . implode(',', $mods) . ')');
					$db->query();
					$insert = $db->getQuery(true)->insert('#__modules_menu')->columns(array('moduleid', 'menuid'));
					foreach ($mods as $mod) {
						$insert->values($mod . ',0');
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
}