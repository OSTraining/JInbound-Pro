<?php
/**
 * @package		JInbound
 * @subpackage	plg_content_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.plugin.plugin');
// we HAVE to force-load the helper here to prevent fatal errors!
$helper = JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php';
if (JFile::exists($helper)) {
	require_once $helper;
}

class plgContentJInbound extends JPlugin
{
	private static $_run;
	
	/**
	 * Constructor
	 * 
	 * @param unknown_type $subject
	 * @param unknown_type $config
	 */
	public function __construct(&$subject, $config) {
		// if something happens & the helper class can't be found, we don't want a fatal error here
		if (class_exists('JInbound')) {
			JInbound::language('plg_content_jinbound.sys', JPATH_ADMINISTRATOR);
			self::$_run = true;
		}
		else {
			JFactory::getLanguage()->load('plg_content_jinbound.sys', JPATH_ADMINISTRATOR);
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_CONTENT_JINBOUND_COMPONENT_NOT_INSTALLED'));
			self::$_run = false;
		}
		parent::__construct($subject, $config);
	}
	
	/**
	 * onContentBeforeSave event - dummy for now
	 * 
	 * @param unknown_type $context
	 * @param unknown_type $table
	 * @param unknown_type $isNew
	 */
	public function onContentBeforeSave($context, &$table, $isNew) {
		if (!self::$_run || 'com_jinbound.lead' != $context) {
			return true;
		}
		if (defined('JDEBUG') && JDEBUG) {
			JFactory::getApplication()->enqueueMessage(__METHOD__);
		}
		return true;
	}
	
	/**
	 * onContentAfterSave event - dummy for now
	 * 
	 * @param unknown_type $context
	 * @param unknown_type $table
	 * @param unknown_type $isNew
	 */
	public function onContentAfterSave($context, &$table, $isNew) {
		if (!self::$_run || 'com_jinbound.lead' != $context) {
			return true;
		}
		if (defined('JDEBUG') && JDEBUG) {
			JFactory::getApplication()->enqueueMessage(__METHOD__);
		}
		return true;
	}
	
	/**
	 * onContentAfterDelete event - dummy for now
	 * 
	 * @param unknown_type $context
	 * @param unknown_type $item
	 */
	public function onContentAfterDelete($context, $item) {
		if (!self::$_run || 'com_jinbound.lead' != $context) {
			return true;
		}
		if (defined('JDEBUG') && JDEBUG) {
			JFactory::getApplication()->enqueueMessage(__METHOD__);
		}
		return true;
	}
	
	/**
	 * onContentChangeState event - dummy for now
	 * 
	 * @param unknown_type $context
	 * @param unknown_type $item
	 */
	public function onContentChangeState($context, $id, $value) {
		if (!self::$_run) {
			return true;
		}
		switch ($context) {
			case 'com_jinbound.lead.status':
			case 'com_jinbound.lead.priority':
				break;
			default:
				return true;
		}
		if (defined('JDEBUG') && JDEBUG) {
			JFactory::getApplication()->enqueueMessage(__METHOD__);
		}
		return true;
	}
}
