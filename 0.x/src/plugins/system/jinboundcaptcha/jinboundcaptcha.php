<?php
/**
 * @package		JInbound
 * @subpackage	plg_system_jinboundcaptcha
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.plugin.plugin');

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
		if (JFactory::getApplication()->isSite())
		{
			return;
		}
		$option = array_key_exists('option', $_REQUEST) ? $_REQUEST['option'] : '';
		$view = array_key_exists('view', $_REQUEST) ? $_REQUEST['view'] : '';
		if ('plg_system_jinboundcaptcha' === $option && 'liveupdate' === $view)
		{
			require_once JPATH_ROOT . '/plugins/system/jinboundcaptcha/liveupdate/liveupdate.php';
			LiveUpdate::handleRequest();
		}
	}
	
	public function onJinboundFormbuilderDisplay(&$xml)
	{
		// add validate attribute to captcha
		$nodes = $xml->xpath("//field[@name='captcha']");
		foreach ($nodes as &$node)
		{
			$node['validate'] = 'captcha';
		}
	}
	
	public function onJinboundFormbuilderView(&$view)
	{
		// add template path for captcha
		$view->addTemplatePath(dirname(__FILE__) . '/tmpl');
	}
	
	public function onJinboundFormbuilderFields(&$fields)
	{
		// add captcha fields to list
		$fields[] = (object) array(
			'name'  => JText::_('PLG_SYSTEM_JINBOUNDCAPTCHA_CAPTCHA'),
			'id'    => 'captcha',
			'type'  => 'captcha',
			'multi' => 0
		);
	}
}
