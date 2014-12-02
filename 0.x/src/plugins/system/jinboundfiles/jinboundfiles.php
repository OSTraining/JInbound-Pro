<?php
/**
 * @package		JInbound
 * @subpackage	plg_system_jinboundfiles
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.plugin.plugin');

class plgSystemJInboundfiles extends JPlugin
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
		$this->loadLanguage('plg_system_jinboundfiles.sys', JPATH_ADMINISTRATOR);
	}
	
	public function onAfterInitialise()
	{
		if (JFactory::getApplication()->isSite())
		{
			return;
		}
		$option = array_key_exists('option', $_REQUEST) ? $_REQUEST['option'] : '';
		$view = array_key_exists('view', $_REQUEST) ? $_REQUEST['view'] : '';
		if ('plg_system_jinboundfiles' === $option && 'liveupdate' === $view)
		{
			require_once JPATH_ROOT . '/plugins/system/jinboundfiles/liveupdate/liveupdate.php';
			LiveUpdate::handleRequest();
		}
	}
	
	public function onJinboundFormbuilderView(&$view)
	{
		// add template path for captcha
		$view->addTemplatePath(dirname(__FILE__) . '/tmpl');
	}
	
	public function onJinboundFormbuilderFields(&$fields)
	{
		$fields[] = (object) array(
			'name'  => JText::_('PLG_SYSTEM_JINBOUNDFILES_FILE'),
			'id'    => 'file',
			'type'  => 'file',
			'multi' => 1
		);
	}
	
	public function onContentBeforeSave($context, &$conversion, $isNew)
	{
		$app = JFactory::getApplication();
		// only operate on jinbound conversion contexts
		if ('com_jinbound.conversion' !== $context)
		{
			return;
		}
		$storage_path = $this->params->get('storage_path', JPATH_ROOT . '/media/jinboundfiles');
		if (!JFolder::exists($storage_path))
		{
			JFolder::create($storage_path);
		}
		$contact_path = $storage_path . '/' . $conversion->contact_id;
		if (!JFolder::exists($contact_path))
		{
			JFolder::create($contact_path);
		}
		$formdata = json_decode($conversion->formdata);
		$files = $app->input->files->get('jform');
		while (list($key, $file) = each($files['lead']))
		{
			$filename = basename($file['name']);
			$filepath = "$contact_path/$filename";
			move_uploaded_file($file['tmp_name'], $filepath);
			$formdata->lead->$key = $filepath;
		}
		$conversion->formdata = json_encode($formdata);
		// TODO check if file is supposed to be here
		// TODO validate file upload
	}
}
