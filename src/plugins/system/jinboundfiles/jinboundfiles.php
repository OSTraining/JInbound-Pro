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

$db = JFactory::getDbo();
$plugins = $db->setQuery($db->getQuery(true)
	->select('extension_id')->from('#__extensions')
	->where($db->qn('element') . ' = ' . $db->q('com_jinbound'))
	->where($db->qn('enabled') . ' = 1')
)->loadColumn();
defined('PLG_SYSTEM_JINBOUNDFILES') or define('PLG_SYSTEM_JINBOUNDFILES', 1 === count($plugins));

class plgSystemJInboundfiles extends JPlugin
{
	protected $app;
	
	protected $session;
	
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
		$this->loadLanguage('lib_joomla', JPATH_ADMINISTRATOR);
		$this->app = JFactory::getApplication();
		$this->session = JFactory::getSession();
	}
	
	public function onAfterInitialise()
	{
		if ($this->app->isSite() || !PLG_SYSTEM_JINBOUNDFILES || JFactory::getUser()->guest)
		{
			return;
		}
		$this->session->clear('jinboundfiles.files');
		$option = array_key_exists('option', $_REQUEST) ? $_REQUEST['option'] : '';
		$view = array_key_exists('view', $_REQUEST) ? $_REQUEST['view'] : '';
		if ('plg_system_jinboundfiles' === $option && 'liveupdate' === $view)
		{
			require_once JPATH_ROOT . '/plugins/system/jinboundfiles/liveupdate/liveupdate.php';
			$updateInfo = LiveUpdate::getUpdateInformation();
			if ($updateInfo->hasUpdates) {
				echo JText::sprintf('PLG_SYSTEM_JINBOUNDFILES_UPDATE_HASUPDATES', $updateInfo->version);
			}
			jexit();
		}
		if ('plg_system_jinboundfiles' === $option && 'file' === $view)
		{
			$file = array_key_exists('file', $_REQUEST) ? $_REQUEST['file'] : '';
			$contact = array_key_exists('contact', $_REQUEST) ? $_REQUEST['contact'] : '';
			if (empty($file) || empty($contact))
			{
				throw new Exception('File Not Found', 404);
			}
			$filename = basename($file) . '.file';
			$filepath = $contact_path = $this->getStoragePath($contact) . '/' . $filename;
			if (!JFile::exists($filepath))
			{
				throw new Exception('File Not Found', 404);
			}
			if (headers_sent())
			{
				throw new Exception('Headers already sent', 500);
			}
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($file).'"');
			header('Content-Transfer-Encoding: binary');
			header('Connection: Keep-Alive');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filepath));
			echo file_get_contents($filepath);
			die;
		}
	}
	
	public function onJinboundDashboardUpdate()
	{
		return "index.php?option=plg_system_jinboundfiles&view=liveupdate";
	}
	
	public function onJinboundFormbuilderView(&$view)
	{
		if (!PLG_SYSTEM_JINBOUNDFILES)
		{
			return;
		}
		// add template path
		$view->addTemplatePath(dirname(__FILE__) . '/tmpl');
	}
	
	public function onJinboundFormbuilderFields(&$fields)
	{
		if (!PLG_SYSTEM_JINBOUNDFILES)
		{
			return;
		}
		$fields[] = (object) array(
			'name'  => JText::_('PLG_SYSTEM_JINBOUNDFILES_FILE'),
			'id'    => 'file',
			'type'  => 'file',
			'multi' => 1
		);
	}
	
	public function onContentBeforeSave($context, $conversion, $isNew)
	{
		// only operate on jinbound conversion contexts
		if ('com_jinbound.conversion' !== $context || !PLG_SYSTEM_JINBOUNDFILES)
		{
			return;
		}
		if (JDEBUG)
		{
			$this->app->enqueueMessage(__METHOD__);
		}
		$fileinputs = $this->getFileInputs($conversion->page_id);
		if (empty($fileinputs))
		{
			if (JDEBUG)
			{
				$this->app->enqueueMessage('No file input in this form');
			}
			return;
		}
		// get extensions
		$extensions = explode(',', $this->params->get('extensions', 'bmp,csv,doc,gif,ico,jpg,jpeg,odg,odp,ods,odt,pdf,png,ppt,swf,txt,xcf,xls,BMP,CSV,DOC,GIF,ICO,JPG,JPEG,ODG,ODP,ODS,ODT,PDF,PNG,PPT,SWF,TXT,XCF,XLS'));
		// store the files
		$contact_path = $this->getStoragePath($conversion->contact_id);
		$formdata = json_decode($conversion->formdata);
		$files = $this->app->input->files->get('jform', null, 'raw');
		$tosave = array();
		if (!is_array($files))
		{
			if (JDEBUG)
			{
				$this->app->enqueueMessage('No files uploaded');
			}
		}
		else
		{
			while (list($key, $file) = each($files['lead']))
			{
				if (!in_array($key, array_keys($fileinputs)))
				{
					continue;
				}
				if (!in_array(JFile::getExt($file['name']), $extensions))
				{
					continue;
				}
				$pfx  = date('YmdHis') . '-';
				$base = basename($file['name']);
				$filename = $pfx . $base . '.file'; // force non-executing extension
				$filepath = "$contact_path/$filename";
				if (method_exists('JFilterInput', 'isSafeFile'))
				{
					$descriptor = array(
						'tmp_name' => $file['tmp_name'],
						'name'     => $filename,
						'type'     => '',
						'error'    => '',
						'size'     => '',
					);
					$isSafeOptions = array('php_ext_content_extensions' => array());
					foreach (array('zip', 'rar', 'tar', 'gz', 'tgz', 'bz2', 'tbz', 'jpa') as $blockMe)
					{
						if (in_array($blockMe, $extensions))
						{
							continue;
						}
						$isSafeOptions['php_ext_content_extensions'][] = $blockMe;
					}
					$isSafe = JFilterInput::isSafeFile($descriptor, $isSafeOptions);
					if (!$isSafe)
					{
						$this->app->enqueueMessage(JText::sprintf('JLIB_FILESYSTEM_ERROR_WARNFS_ERR03', $filename), 'error');
						continue;
					}
				}
				try
				{
					JFile::upload($file['tmp_name'], $filepath, false, true);
				}
				catch (Exception $e)
				{
					$error = JText::_($e->getMessage());
					if (false !== strpos($error, '%s'))
					{
						$error = sprintf($error, JFilterInput::clean($base, 'string'));
					}
					if (false !== strpos($error, $contact_path))
					{
						$error = str_replace($contact_path, '', $error);
					}
					$this->app->enqueueMessage($error, 'error');
					continue;
				}
				$url = JUri::root(false) . 'administrator/' . sprintf('index.php?option=plg_system_jinboundfiles&view=file&file=%1$s&contact=%2$d', htmlspecialchars($pfx . $base, ENT_QUOTES, 'UTF-8'), $conversion->contact_id);
				$formdata->lead->$key = sprintf('<a href="%1$s">%1$s</a>', $url);
				$tosave[] = array('field' => $key, 'value' => $formdata->lead->$key);
			}
		}
		$this->session->set('jinboundfiles.files', json_encode($tosave));
		$conversion->formdata = json_encode($formdata);
	}
	
	protected function getStoragePath($contact = null)
	{
		$storage_path = $this->params->get('storage_path', JPATH_ROOT . '/media/jinboundfiles');
		if (!JFolder::exists($storage_path))
		{
			JFolder::create($storage_path);
		}
		if (is_null($contact))
		{
			return $storage_path;
		}
		$contact_path = $storage_path . '/' . $contact;
		if (!JFolder::exists($contact_path))
		{
			JFolder::create($contact_path);
		}
		return $contact_path;
	}
	
	protected function getFileInputs($page_id)
	{
		$db = JFactory::getDbo();
		$rows = $db->setQuery($db->getQuery(true)
			->select('Field.title')
			->select('Field.name')
			->from('#__jinbound_fields AS Field')
			->leftJoin('#__jinbound_form_fields AS FormFields ON FormFields.field_id = Field.id')
			->leftJoin('#__jinbound_pages AS Page ON FormFields.form_id = Page.formid AND Page.id = ' . (int) $page_id)
			->where('Field.published = 1')
			->where('Field.type = ' . $db->quote('file'))
			->group('Field.id')
		)->loadObjectList();
		$result = array();
		foreach ($rows as $row)
		{
			$result[$row->name] = (array) $row;
		}
		return $result;
	}
	
	public function onJinboundFormbuilderRenderValue(&$output, $page_id, $field, $value)
	{
		if (!PLG_SYSTEM_JINBOUNDFILES)
		{
			return;
		}
		$fileinputs = $this->getFileInputs($page_id);
		if (!in_array($field, array_keys($fileinputs)))
		{
			return;
		}
		$output = "$value";
	}
	
	public function onJinboundBeforeNotificationEmail(&$emails, &$subject, &$html, $contact, $conversion)
	{
		if (!PLG_SYSTEM_JINBOUNDFILES)
		{
			return;
		}
		$fileinputs = $this->getFileInputs($conversion->page_id);
		$data = $this->session->get('jinboundfiles.files');
		$files = json_decode($data);
		if (empty($files))
		{
			return;
		}
		$result = array();
		foreach ($html as $line)
		{
			if ('</table>' == $line)
			{
				foreach ($files as $file)
				{
					if (!array_key_exists($file->field, $fileinputs))
					{
						continue;
					}
					$link = '';
					$this->onJinboundFormbuilderRenderValue($link, $conversion->page_id, $file->field, $file->value);
					$result[] = '	<tr>';
					$result[] = '		<td>';
					$result[] = '			' . htmlspecialchars($fileinputs[$file->field], ENT_QUOTES, 'UTF-8');
					$result[] = '		</td>';
					$result[] = '		<td>';
					$result[] = '			' . $link;
					$result[] = '		</td>';
					$result[] = '	</tr>';
				}
			}
			$result[] = $line;
		}
		$html = $result;
		$this->session->clear('jinboundfiles.files');
	}
	
	public function onJInboundBeforeListFieldTypes(&$types, &$ignored, &$paths, &$files)
	{
		if (!PLG_SYSTEM_JINBOUNDFILES)
		{
			return;
		}
		$ignored = array_values(array_diff($ignored, array('file')));
	}
}
