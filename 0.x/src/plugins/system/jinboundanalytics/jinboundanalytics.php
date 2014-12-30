<?php
/**
 * @package		JInbound
 * @subpackage	plg_system_jinboundanalytics
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.plugin.plugin');

class plgSystemJInboundanalytics extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param unknown_type $subject
	 * @param unknown_type $config
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage('plg_system_jinboundanalytics.sys', JPATH_ADMINISTRATOR);
	}
	
	public function onAfterInitialise()
	{
		if (JFactory::getApplication()->isSite())
		{
			return;
		}
		$option = array_key_exists('option', $_REQUEST) ? $_REQUEST['option'] : '';
		$view = array_key_exists('view', $_REQUEST) ? $_REQUEST['view'] : '';
		if ('plg_system_jinboundanalytics' === $option && 'liveupdate' === $view)
		{
			require_once JPATH_ROOT . '/plugins/system/jinboundanalytics/liveupdate/liveupdate.php';
			LiveUpdate::handleRequest();
		}
	}
	
	public function onContentPrepareForm($form)
	{
		if (!($form instanceof JForm)) {
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}
		if ('com_jinbound.page' != $form->getName()) {
			return true;
		}
		JForm::addFormPath(dirname(__FILE__) . '/form');
		$result = $form->loadFile('jinboundanalytics', false);
		return $result;
	}
}
