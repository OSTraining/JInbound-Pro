<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	plg_content_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.plugin.plugin');
// we HAVE to force-load the helper here to prevent fatal errors!
$helper = JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php';
if (JFile::exists($helper)) require_once $helper;

class plgContentJInbound extends JPlugin
{
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
		}
		else {
			JFactory::getLanguage()->load('plg_content_jinbound.sys', JPATH_ADMINISTRATOR);
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_CONTENT_JINBOUND_COMPONENT_NOT_INSTALLED'));
		}
		parent::__construct($subject, $config);
	}
	
	
}
