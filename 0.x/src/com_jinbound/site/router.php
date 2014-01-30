<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('_JEXEC') or die;

jimport('joomla.application.categories');

function JinboundBuildRoute(&$query) {
	$segments = array();
	
	$app    = JFactory::getApplication();
	$menu   = $app->getMenu();
	
	if (empty($query['Itemid'])) {
		$menuItem = $menu->getActive();
	}
	else {
		$menuItem = $menu->getItem($query['Itemid']);
	}
	$mView  = (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
	$mId    = (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];
	
	// we always want to use category/page alias and not menu item
	// but menu items send us only Itemid & option here
	// so what we need to do is simply use the menu item to determine defaults
	// for unset query items before proceeding
	
	// if the query has these, use them
	foreach (array('view', 'id') as $var) {
		$mVar = ${'m'.ucwords($var)};
		if (isset($query[$var])) {
			$$var = $query[$var];
			unset($query[$var]);
		}
		// otherwise use the one provided in the menu, if applicable
		else if (!empty($mVar)) {
			$$var = $mVar;
		}
	}
	// check page view
	if (isset($view) && $view == 'page') {
		if (false !== strpos($id, ':')) {
			list ($pid, $tmp) = explode(':', $id, 2);
		}
		else {
			$pid = (int) $id;
			$tmp = '';
		}
		$pid = (int) $pid;
		if ($pid) {
			$db = JFactory::getDbo();
			$db->setQuery($db->getQuery(true)
				->select($db->quoteName('category'))
				->select($db->quoteName('name'))
				->select($db->quoteName('alias'))
				->from('#__jinbound_pages')
				->where($db->quoteName('id') . ' = ' . $pid)
			);
			try {
				$record = $db->loadObject();
			}
			catch (Exception $e) {
				JError::raiseError('500', $e->getMessage());
				jexit();
			}
			if (empty($tmp)) {
				$id .= ':' . (empty($record->alias) ? JApplication::stringURLSafe($record->name) : $record->alias);
			}
			$categories = JCategories::getInstance('Jinbound');
			$category = $categories->get($record->category);
			if ($category) {
				$path = $category->getPath();
				$path = array_reverse($path);
				
				$array = array();
				foreach ($path as $cid) {
					//list ($cid, $tmp) = explode(':', $cid, 2);
					$array[] = $cid;
				}
				$segments = array_merge($segments, array_reverse($array));
			}
		}
		$segments[] = $id;
		unset($query['id']);
	}

	if (isset($query['layout'])) {
		if ($query['layout'] == 'default') {
			unset($query['layout']);
		}
 	}
	return $segments;
}

function JinboundParseRoute($segments) {
	$vars = array();
	
	//Get the active menu item.
	$app    = JFactory::getApplication();
	$menu   = $app->getMenu();
	$item   = $menu->getActive();
	
	// bugfix for sef
	if (is_object($item)) {
		$templateStyle = $item->template_style_id;
		if ($templateStyle) {
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_templates/tables');
			$template = JTable::getInstance('Style', 'TemplatesTable');
			$template->load($templateStyle);
			if ($template->id) {
				$app->setTemplate($template->template, $template->params);
			}
		}
	}
	
	// Count route segments
	$count = count($segments);
	
	// Standard routing for pages.
	if (!isset($item)) {
		$vars['view'] = $segments[0];
		$vars['id']   = $segments[$count - 1];
		return $vars;
	}
	else if ('page' == $item->query['view']) {
		$vars['view'] = 'page';
		$vars['id']   = $segments[$count - 1];
	}
	
	return $vars;
}
