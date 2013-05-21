<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	plg_system_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.plugin.plugin');
// we HAVE to force-load the helper here to prevent fatal errors!
$helper = JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php';
if (JFile::exists($helper)) require_once $helper;

class plgSystemJInbound extends JPlugin
{
	private static $_run;
	
	private static $_debug;
	
	/**
	 * Constructor
	 * 
	 * @param unknown_type $subject
	 * @param unknown_type $config
	 */
	public function __construct(&$subject, $config) {
		// if something happens & the helper class can't be found, we don't want a fatal error here
		if (class_exists('JInbound')) {
			JInbound::language(JInbound::COM, JPATH_ADMINISTRATOR);
			self::$_run = true;
		}
		else {
			$this->loadLanguage();
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_JINBOUND_COMPONENT_NOT_INSTALLED'));
			self::$_run = false;
		}
		parent::__construct($subject, $config);
	}
	
	public function loadLanguage($extension = 'plg_system_jinbound.sys', $basePath = JPATH_ADMINISTRATOR) {
		parent::loadLanguage($extension, $basePath);
	}
	
	/**
	 * onAfterInitialise
	 * 
	 * forces template
	 */
	public function onAfterInitialise() {
		$app = JFactory::getApplication();
		
		if (!self::$_run) {
			return;
		}
		if (JInbound::COM != JFactory::getApplication()->input->get('option')) {
			return;
		}
		
		$app->input->set('template', 'jinbound');
	}
	
	/**
	 * onAfterDispatch
	 * 
	 * handles flair after dispatch
	 */
	public function onAfterDispatch() {
		if (!self::$_run) {
			return;
		}
		$app = JFactory::getApplication();
		// we want to add some extras to com_categories
		if ($app->isAdmin() && 'com_categories' == $app->input->get('option', '', 'cmd') && class_exists('JInbound') && JInbound::COM == $app->input->get('extension', '', 'cmd')) {
			// UPDATE: don't do this in edit layout in 3.0+
			if (JInbound::version()->isCompatible('3.0') && 'edit' == $app->input->get('layout')) {
				return;
			}
			// add submenu to categories
			JInbound::registerLibrary('JInboundView', 'views/baseview');
			$comView = new JInboundView();
			$comView->addMenuBar();
		}
	}
}
