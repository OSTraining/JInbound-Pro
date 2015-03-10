<?php
/**
 * @package		JInbound
 * @subpackage	plg_system_jinboundcaptcha
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.plugin.plugin');

$db = JFactory::getDbo();
$plugins = $db->setQuery($db->getQuery(true)
	->select('extension_id')->from('#__extensions')
	->where($db->qn('element') . ' = ' . $db->q('com_jinbound'))
	->where($db->qn('enabled') . ' = 1')
)->loadColumn();
defined('PLG_SYSTEM_JINBOUNDCAPTCHA') or define('PLG_SYSTEM_JINBOUNDCAPTCHA', 1 === count($plugins));

class plgSystemJInboundcaptcha extends JPlugin
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
		$this->loadLanguage('plg_system_jinboundcaptcha.sys', JPATH_ADMINISTRATOR);
	}
	
	public function onAfterInitialise()
	{
		if (JFactory::getApplication()->isSite() || !PLG_SYSTEM_JINBOUNDCAPTCHA)
		{
			return;
		}
		$option = array_key_exists('option', $_REQUEST) ? $_REQUEST['option'] : '';
		$view = array_key_exists('view', $_REQUEST) ? $_REQUEST['view'] : '';
		if ('plg_system_jinboundcaptcha' === $option && 'liveupdate' === $view)
		{
			require_once JPATH_ROOT . '/plugins/system/jinboundcaptcha/liveupdate/liveupdate.php';
			$updateInfo = LiveUpdate::getUpdateInformation();
			if ($updateInfo->hasUpdates) {
				echo JText::sprintf('PLG_SYSTEM_JINBOUNDCAPTCHA_UPDATE_HASUPDATES', $updateInfo->version);
			}
			jexit();
		}
	}
	
	public function onJinboundDashboardUpdate()
	{
		return 'index.php?option=plg_system_jinboundcaptcha&view=liveupdate';
	}
	
	public function onJinboundFormbuilderDisplay(&$xml)
	{
		if (!PLG_SYSTEM_JINBOUNDCAPTCHA)
		{
			return;
		}
		// add validate attribute to captcha
		$nodes = $xml->xpath("//field[@name='captcha']");
		foreach ($nodes as &$node)
		{
			$node['validate'] = 'captcha';
		}
	}
	
	public function onJinboundFormbuilderView(&$view)
	{
		if (!PLG_SYSTEM_JINBOUNDCAPTCHA)
		{
			return;
		}
		// add template path for captcha
		$view->addTemplatePath(dirname(__FILE__) . '/tmpl');
	}
	
	public function onJinboundFormbuilderFields(&$fields)
	{
		if (!PLG_SYSTEM_JINBOUNDCAPTCHA)
		{
			return;
		}
		// add captcha fields to list
		$fields[] = (object) array(
			'name'  => JText::_('PLG_SYSTEM_JINBOUNDCAPTCHA_CAPTCHA'),
			'id'    => 'captcha',
			'type'  => 'captcha',
			'multi' => 0
		);
	}
	
	public function onJInboundBeforeListFieldTypes(&$types, &$ignored, &$paths, &$files)
	{
		if (!PLG_SYSTEM_JINBOUNDCAPTCHA)
		{
			return;
		}
		$types[] = 'captcha';
	}
}
