<?php
/**
 * @package		JInbound
 * @subpackage	plg_akeebasubs_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.plugin.plugin');
// we HAVE to force-load the helper here to prevent fatal errors!
$helper = JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php';
if (JFile::exists($helper)) require_once $helper;

class plgAkeebasubsJInbound extends JPlugin
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
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_AKEEBASUBS_JINBOUND_COMPONENT_NOT_INSTALLED'));
			self::$_run = false;
		}
		parent::__construct($subject, $config);
	}
	
	public function loadLanguage($extension = 'plg_akeebasubs_jinbound.sys', $basePath = JPATH_ADMINISTRATOR) {
		parent::loadLanguage($extension, $basePath);
	}
	
	/**
	 * onAKSubscriptionChange
	 * 
	 */
	public function onAKSubscriptionChange($row, $info) {
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();
		
		if (!self::$_run) {
			return;
		}
		
		// No payment has been made yet
		if ('N' == $row->state) {
			return;
		}
		
		// Did the payment status just change to C or P? It's a new subscription
		if (array_key_exists('state', (array) $info['modified']) && in_array($row->state, array('P','C'))) {
			// Check for the lead by comparing the email from the akeeba subs user with the contact table (and cross reference by lead)
			// TODO
		}
		
		$app->enqueueMessage('<pre>' . print_r($row, 1) . '</pre>');
		$app->enqueueMessage('<pre>' . print_r($info, 1) . '</pre>');
	}
}
