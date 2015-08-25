<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

abstract class JInboundHelperAccess
{
	private static $_actions = array(
		'core.admin', 'core.manage', 'core.create',
		'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
	);
	
	/**
	 * Gets an object containing id & level of parent asset 
	 * 
	 * @param string $name Name of section, blank for none
	 * @return object
	 */
	static public function getParent($name = '') {
		$db = JFactory::getDbo();
		return $db->setQuery($db->getQuery(true)
			->select('id, level')
			->from('#__assets')
			->where('name = ' . $db->Quote(JInbound::COM . (empty($name) ? '' : '.' . $name)))
		)->loadObject();
	}
	
	/**
	 * Creates an asset JTable and saves the bind data
	 * 
	 * @param mixed $bind data to bind to JTable
	 * 
	 * @throws Exception
	 */
	static public function saveAsset($bind) {
		$asset = JTable::getInstance('Asset');
		$bind  = (array) $bind;
		// save our asset
		if (!$asset->bind($bind)) {
			throw new Exception(JText::_('COM_JINBOUND_PERMISSIONS_ERROR_BIND'));
		}
		if (!$asset->check()) {
			throw new Exception(JText::_('COM_JINBOUND_PERMISSIONS_ERROR_CHECK'));
		}
		if (!$asset->store()) {
			throw new Exception(JText::_('COM_JINBOUND_PERMISSIONS_ERROR_STORE'));
		}
		if (!$asset->moveByReference($bind['parent_id'], 'last-child')) {
			throw new Exception(JText::_('COM_JINBOUND_PERMISSIONS_ERROR_MOVE'));
		}
	}
	
	static public function sanitizeRules($rules) {
		$saferules = array();
		// sanitize the rules
		foreach ($rules as $action => $identities) {
			if (!array_key_exists($action, $saferules)) {
				$saferules[$action] = array();
			}
			if (!empty($identities)) {
				foreach ($identities as $group => $permission) {
					if ('' == $permission) {
						continue;
					}
					$saferules[$action][$group] = (int) ((bool) $permission);
				}
			}
		}
		return $saferules;
	}
	
	static public function saveRules($name, $rules = null, $checktoken = true) {
		if ($checktoken) {
			JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		}
		if (!JFactory::getUser()->authorise('core.admin', JInbound::COM)) {
			JError::raiseError(403, JText::_('COM_JINBOUND_PERMISSIONS_SAVE_NOT_AUTH'));
			jexit();
		}
		// pull rules from request if needed
		if (is_null($rules)) {
			$rules = JFactory::getApplication()->input->post->get('rules', array(), 'array');
		}
		// if we have no rules, then there's something amiss
		if (empty($rules)) {
			throw new Exception(JText::_('COM_JINBOUND_PERMISSIONS_NO_RULES'));
		}
		// save our rules to the assets table
		else {
			$saferules = self::sanitizeRules($rules);
			// find our parent asset
			$parent = self::getParent();
			// create our bind data
			$bind = array(
				'rules' => json_encode($saferules)
			,	'name' => JInbound::COM . '.' . $name
			,	'title' => JText::_('COM_JINBOUND_' . $name . '_PERMISSIONS')
			,	'level' => ((int) $parent->level) + 1
			,	'parent_id' => $parent->id
			,	'id' => self::getParent($name)->id
			);
			// save our asset
			self::saveAsset($bind);
		}
		return true;
	}
	
	static public function saveRulesWithRedirect($name) {
		JInbound::registerHelper('url');
		JInbound::registerLibrary('JInboundInflector', 'inflector');
		$plural = JInboundInflector::pluralize($name);
		$app    = JFactory::getApplication();
		$url    = JInboundHelperUrl::view($plural, false);
		$msg    = JText::_('COM_JINBOUND_PERMISSIONS_SAVE_SUCCESS');
		$type   = 'message';
	
		try {
			JInboundHelperAccess::saveRules($name);
		}
		catch (Exception $e) {
			$msg  = $e->getMessage();
			$type = 'error';
		}
		$app->redirect($url, $msg, $type);
		jexit();
	}
}
