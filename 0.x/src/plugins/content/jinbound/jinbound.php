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
	public function onContentBeforeSave($context, $table, $isNew) {
		if (!self::$_run || 0 !== strpos($context, 'com_jinbound')) {
			return true;
		}
		if (JInbound::config("debug", 0)) {
			JFactory::getApplication()->enqueueMessage(__METHOD__ . ' ' . $context);
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
	public function onContentAfterSave($context, $table, $isNew) {
		if (!self::$_run || 0 !== strpos($context, 'com_jinbound')) {
			return true;
		}
		if (JInbound::config("debug", 0)) {
			JFactory::getApplication()->enqueueMessage(__METHOD__ . ' ' . $context);
		}
		return true;
	}
	
	/**
	 * onContentBeforeDelete event - dummy for now
	 * 
	 * @param unknown_type $context
	 * @param unknown_type $item
	 */
	public function onContentBeforeDelete($context, $item) {
		if (!self::$_run || 0 !== strpos($context, 'com_jinbound')) {
			return true;
		}
		if (JInbound::config("debug", 0)) {
			JFactory::getApplication()->enqueueMessage(__METHOD__ . ' ' . $context);
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
		if (!self::$_run || 0 !== strpos($context, 'com_jinbound')) {
			return true;
		}
		if (JInbound::config("debug", 0)) {
			JFactory::getApplication()->enqueueMessage(__METHOD__ . ' ' . $context);
		}
		return true;
	}
	
	/**
	 * onContentChangeState event - dummy for now
	 * 
	 * @param unknown_type $context
	 * @param unknown_type $pks
	 * @param unknown_type $value
	 */
	public function onContentChangeState($context, $pks, $value) {
		if (!self::$_run || 0 !== strpos($context, 'com_jinbound')) {
			return true;
		}
		if (JInbound::config("debug", 0)) {
			JFactory::getApplication()->enqueueMessage(__METHOD__ . ' ' . $context);
		}
		return true;
	}
	
	/**
	 * onContentBeforeDisplay event
	 * 
	 * forces URLs in emails to be absolute no matter what
	 * 
	 * @param unknown_type $context
	 * @param unknown_type $table
	 * @param unknown_type $params
	 * @param unknown_type $offset
	 */
	public function onContentBeforeDisplay($context, &$table, &$params, $offset = 0) {
		if (!self::$_run || 0 !== strpos($context, 'com_jinbound')) {
			return;
		}
		if (JInbound::config("debug", 0)) {
			JFactory::getApplication()->enqueueMessage(__METHOD__ . ' ' . $context);
		}
		// handle known contexts
		switch ($context) {
			case 'com_jinbound.email':
				// handle html attributes
				preg_match_all('#(?P<attr>src|href)\=(?P<qte>\"|\\\')(?P<url>.*?)(?P=qte)#Di', $table->htmlbody, $matches, PREG_SET_ORDER);
				// empty match array is an error
				if (!is_array($matches) || empty($matches)) {
					return false;
				}
				// empty first match, however, is not
				if (empty($matches[0])) {
					return;
				}
				// "fix" the urls
				JInbound::registerHelper('url');
				foreach ($matches as $match) {
					$table->htmlbody = str_replace($match[0], $match['attr'] . '=' . $match['qte'] . JInboundHelperUrl::toFull($match['url']) . $match['qte'], $table->htmlbody);
				}
				return;
				// TODO plaintext emails?
			default:
				break;
		}
	}
	
	/**
	 * onContentAfterDisplay event - dummy for now
	 * 
	 * @param unknown_type $context
	 * @param unknown_type $table
	 * @param unknown_type $params
	 * @param unknown_type $offset
	 */
	public function onContentAfterDisplay($context, &$table, &$params, $offset = 0) {
		if (!self::$_run || 0 !== strpos($context, 'com_jinbound')) {
			return;
		}
		if (JInbound::config("debug", 0)) {
			JFactory::getApplication()->enqueueMessage(__METHOD__ . ' ' . $context);
		}
	}
}
