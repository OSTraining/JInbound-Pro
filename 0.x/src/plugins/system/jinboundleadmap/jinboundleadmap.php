<?php
/**
 * @package		JInbound
 * @subpackage	plg_system_jinboundleadmap
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

require_once dirname(__FILE__) . '/lib/geoip2.phar';
use GeoIp2\Database\Reader;

class PlgSystemJinboundleadmap extends JPlugin
{
	/**
	 * Loads the language, if in admin
	 */
	public function onAfterInitialise()
	{
		if (JFactory::getApplication()->isAdmin())
		{
			// load language files
			$this->loadLanguage('com_jinbound', JPATH_ADMINISTRATOR);
			$this->loadLanguage('plg_system_jinboundleadmap.sys', JPATH_ROOT);
		}
	}
	
	/**
	 * Rebuilds this plugin's menu item
	 * 
	 * @throws Exception
	 */
	public function onJinboundRebuildMenu()
	{
		$app   = JFactory::getApplication();
		$db    = JFactory::getDbo();
		$table = JTable::getInstance('menu');
		// load the parent menu item
		$parent_id = $db->setQuery($db->getQuery(true)
			->select('m.id')
			->from('#__menu AS m')
			->leftJoin('#__extensions AS e ON m.component_id = e.extension_id')
			->where('m.parent_id = 1')
			->where("m.client_id = 1")
			->where('e.element = ' . $db->quote('com_jinbound'))
		)->loadResult();
		// if the parent is missing, stop unless uninstalling
		if (empty($parent_id))
		{
			if (JDEBUG)
			{
				$app->enqueueMessage('[' . __METHOD__ . '] Parent menu is empty');
			}
			return;
		}
		// load the existing plg_system_jinboundleadmap menu item
		$existing = $db->setQuery($db->getQuery(true)
			->select('m.id')
			->from('#__menu AS m')
			->where('m.parent_id = ' . (int) $parent_id)
			->where("m.client_id = 1")
			->where("link LIKE " . $db->quote('%jinboundleadmap%'))
		)->loadResult();
		// if there is an existing menu, remove it
		if ($existing)
		{
			if (JDEBUG)
			{
				$app->enqueueMessage('[' . __METHOD__ . '] Removing existing menu item ' . $existing);
			}
			if (!$table->delete((int) $existing))
			{
				$app->enqueueMessage($table->getError(), 'error');
			}
			$table->rebuild();
		}
		if (JDEBUG)
		{
			$app->enqueueMessage('[' . __METHOD__ . '] Adding menu item');
		}
		$component = $db->setQuery($db->getQuery(true)
			->select('e.extension_id')
			->from('#__extensions AS e')
			->where('e.element = ' . $db->quote('com_jinbound'))
		)->loadResult();
		$data = array();
		$data['menutype'] = 'main';
		$data['client_id'] = 1;
		$data['title'] = 'plg_system_jinboundleadmap_view_title';
		$data['alias'] = 'plg_system_jinboundleadmap_view_title';
		$data['type'] = 'component';
		$data['published'] = 0;
		$data['parent_id'] = $parent_id;
		$data['component_id'] = $component;
		$data['img'] = 'class:component';
		$data['home'] = 0;
		$data['link'] = 'index.php?option=com_'
			. (version_compare(JVERSION, '3.0.0', '>=') ? '' : 'jinbound&task=')
			. 'ajax&group=system&plugin=jinboundleadmapview&format=html'
		;
		try
		{
			$location = $table->setLocation($parent_id, 'last-child');
			if (false === $location)
			{
				throw new Exception(JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_ERROR_LOCATION_NOT_SET'));
			}
		}
		catch (Exception $ex)
		{
			$app->enqueueMessage(JText::sprintf('PLG_SYSTEM_JINBOUNDLEADMAP_ERROR_INSTALLING_MENU_ITEM', $ex->getMessage()), 'error');
			return;
		}
		if (!$table->bind($data) || !$table->check() || !$table->store())
		{
			$app->enqueueMessage(JText::sprintf('PLG_SYSTEM_JINBOUNDLEADMAP_ERROR_INSTALLING_MENU_ITEM', $table->getError()), 'error');
		}
		$table->rebuild();
	}
	
	/**
	 * Adds menu item to submenu
	 * 
	 * @param type $view
	 */
	public function onJinboundBeforeMenuBar(&$view)
	{
		// init
		$input  = JFactory::getApplication()->input;
		$url    = $this->getUrl();
		if (JInbound::version()->isCompatible('3.0.0'))
		{
			$active = 'jinboundleadmapview' == $input->get('plugin')
				&& 'html' == $input->get('format')
				&& 'com_ajax' == $input->get('option')
			;
		}
		else
		{
			$active = 'jinboundleadmapview' == $input->get('plugin')
				&& 'html' == $input->get('format')
				&& 'com_jinbound' == $input->get('option')
				&& 'ajax' == $input->get('task')
			;
		}
		$label = JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_VIEW_TITLE');
		$view->addSubMenuEntry($label, $url, $active);
	}
	
	/**
	 * Display lead map view
	 * 
	 * @return mixed
	 * @throws RuntimeException
	 */
	public function onAjaxJinboundleadmapview()
	{
		$app = JFactory::getApplication();
		// only allow in admin
		if (!$app->isAdmin())
		{
			throw new RuntimeException(JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_NOT_FOUND'), 404);
		}
		// get the map data
		$data = $this->getData();
		if ('json' == $app->input->get('format'))
		{
			return $data;
		}
		// render the view
		$view = $this->getView();
		$view->data = $data;
		$view->addMenuBar();
		$view->addToolBar();
		if (class_exists('JToolBarHelper'))
		{
			JToolBarHelper::title(JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_VIEW_TITLE'), 'jinbound-leadmap');
		}
		return $view->loadTemplate();
	}
	
	public function onAjaxJinboundleadmapdownload()
	{
		$app     = JFactory::getApplication();
		$url     = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz';
		$parts   = parse_url($url);
		$options = array(
			'http' => array(
				'method' => 'GET'
			,	'request_fulluri' => true
			,	'header' => array(
					"Host: {$parts['host']}"
				,	"Connection: keep-alive"
				,	"Cache-Control: max-age=0"
				,	"User-Agent: Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)"
				,	"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"
				,	"Referer: {$parts['scheme']}://{$parts['host']}"
				,	"Accept-Language: en"
				,	"Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7"
				)
			)
		);
		$context  = stream_context_create($options);
		$response = file_get_contents($url, false, $context);
		$config   = new JConfig;
		$destfile = $this->getMaxmindDbFileName();
		$tmpfile  = rtrim($config->tmp_path, '/') . '/' . basename($destfile) . '.gz';
		file_put_contents($tmpfile, $response);
		jimport('joomla.filesystem.archive');
		JArchive::extract($tmpfile, dirname($destfile));
		unlink($tmpfile);
		$app->redirect($this->getUrl('jinboundleadmapview', array(), false));
	}
	
	protected function getUrl($plugin = 'jinboundleadmapview', $params = array(), $sef = true, $escape = true)
	{
		$parts = array('group' => 'system', 'format' => 'html', 'plugin' => $plugin);
		if (JInbound::version()->isCompatible('3.0.0'))
		{
			$parts['option'] = 'com_ajax';
		}
		else
		{
			$parts['option'] = 'com_jinbound';
			$parts['task']   = 'ajax';
		}
		$url = 'index.php?' . http_build_query(array_merge($parts, $params));
		if ($sef)
		{
			$url = JRoute::_($url, $escape);
		}
		return $url;
	}


	protected function getMaxmindDbFileName()
	{
		return dirname(__FILE__) . '/lib/geolite2-city.mmdb';
	}
	
	protected function getData()
	{
		$app  = JFactory::getApplication();
		$mmdb = $this->getMaxmindDbFileName();
		$data = new stdClass();
		$data->locations = array();
		if (!file_exists($mmdb))
		{
			return new Exception(JText::sprintf('PLG_SYSTEM_JINBOUNDLEADMAP_NO_MAXMIND_DB', $this->getUrl('jinboundleadmapdownload')));
		}
		$reader    = new Reader($mmdb);
		$patterns  = array('/^127\.0\.0\.1$/', '/^10\./', '/^192\.168\./');
		$db        = JFactory::getDbo();
		$records   = $db->setQuery($db->getQuery(true)
			->select('t.ip, GROUP_CONCAT(l.id) AS lead_id')
			->from('#__jinbound_tracks AS t')
			->leftJoin('#__jinbound_contacts AS l ON l.cookie = t.cookie')
			->group('t.ip')
		)->loadObjectList();
		if (JDEBUG)
		{
			$app->enqueueMessage('[' . __METHOD__ . '] IPs: ' . implode("<br>", $records));
		}
		if (!empty($records))
		{
			foreach ($records as $record)
			{
				if (empty($record->ip))
				{
					continue;
				}
				$check = true;
				foreach ($patterns as $pattern)
				{
					$check = $check && !preg_match($pattern, $record->ip);
				}
				if (!$check)
				{
					if (JDEBUG)
					{
						$app->enqueueMessage('[' . __METHOD__ . '] IP does not qualify: "' . $record->ip . '"');
					}
					continue;
				}
				$lead_ids = explode(',', $record->lead_id);
				$islead   = false;
				if (!empty($lead_ids))
				{
					foreach ($lead_ids as $lead_id)
					{
						if (($islead = (int) $lead_id))
						{
							break;
						}
					}
				}
				try
				{
					$record = $reader->city($record->ip);
					$data->locations[] = (object) array(
						'latitude'  => $record->location->latitude
					,	'longitude' => $record->location->longitude
					,	'city'      => $record->city->name
					,	'lead'      => $islead
					);
				}
				catch (Exception $ex)
				{
					continue;
				}
			}
		}
		return $data;
	}
	
	/**
	 * Get view class instance
	 * 
	 * @return \JInboundPluginView
	 */
	protected function getView()
	{
		JLoader::register('JInboundPluginView', JPATH_ADMINISTRATOR.'/components/com_jinbound/libraries/views/pluginview.php');
		$viewConfig = array('template_path' => dirname(__FILE__) . '/tmpl');
		$view = new JInboundPluginView($viewConfig);
		return $view;
	}
}
