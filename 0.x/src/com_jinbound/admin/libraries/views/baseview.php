<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

// include the helpers
JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('filter');
JInbound::registerHelper('path');
JInbound::registerHelper('toolbar');
JInbound::registerHelper('url');
JInbound::registerLibrary('JInboundInflector', 'inflector');
// include the html helper here
jimport('joomla.html.html');
JHtml::addIncludePath(JInboundHelperPath::admin() . '/helpers/html');
// include core libs
jimport('joomla.error.profiler');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
// include other helpers
// we have to always load the language file for com_categories
JInbound::language('com_categories', JPATH_ADMINISTRATOR);

// create an intermediary dummy class
if (jimport('joomla.application.component.view')) {
	class JInboundBaseCompatView extends JView
	{
		
	}
}
else {
	if (!class_exists('JViewLegacy', false)) {
		jimport('cms.view.legacy') or jimport('legacy.view.legacy');
	}
	class JInboundBaseCompatView extends JViewLegacy
	{
		
	}
}

class JInboundBaseView extends JInboundBaseCompatView
{
	function __construct($config = array()) {
		parent::__construct($config);
		
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		$this->app = JFactory::getApplication();
		
		// set the layout paths, in order of importance
		$root = $this->app->isAdmin() ? JInboundHelperPath::admin() : JInboundHelperPath::site();
		$layout_override = JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/com_jinbound/' . $this->getName();
		$common_override = JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/com_jinbound/_common';
		if (JFolder::exists($layout_override)) {
			$this->addTemplatePath($layout_override);
		}
		if (JFolder::exists($common_override)) {
			$this->addTemplatePath($common_override);
		}
		$this->addTemplatePath($root . '/views/_common');
		$this->addTemplatePath($root . '/views/' . $this->getName() . '/tmpl');
	}
	
	/**
	 * overload this with an extra param to choose layout
	 * 
	 * (non-PHPdoc)
	 * @see JView::loadTemplate()
	 */
	public function loadTemplate($tpl = null, $layout = null) {
		$oldLayout = $this->_layout;
		if (!is_null($layout)) {
			$this->_layout = $layout;
		}
		$return = parent::loadTemplate($tpl);
		$this->_layout = $oldLayout;
		return $return;
	}
	
}


class JInboundView extends JInboundBaseView
{
	public static $option = 'com_jinbound';
	
	public $viewItemName = '';
	
	protected $_filters;

	function display($tpl = null, $safeparams = false) {
		$profiler = JProfiler::getInstance('Application');
		$profiler->mark('onJInboundViewDisplayStart');
		
		$this->viewClass = 'jinbound_component';
		if (JInbound::version()->isCompatible('3.0.0')) {
			$this->viewClass .= ' jinbound_bootstrap';
		}
		else {
			$this->viewClass .= ' jinbound_legacy';
		}
		// add the view as a class as well
		$this->viewClass   .= ' jinbound_view_' . JInboundHelperFilter::escape($this->_name);
		$this->viewName     = $this->_name;
		
		// are we in component view?
		$this->tpl = 'component' == $this->app->input->get('tmpl', '', 'cmd');
		
		if ($this->app->isAdmin()) {
			$this->addToolbar();
			$this->addMenuBar();
		}
		// not in admin
		else {
			// Initialise variables
			$state		 = $this->get('State');
			$context   = $this->get('Context');
			// these are page params only... ?
			if (is_object($state) && property_exists($state, 'params')) {
				$params  = $state->params;
			}
			else {
				$params  = new JRegistry();
			}
			// are we in a raw view?
			$this->raw = ('raw' == $this->app->input->get('format', '', 'cmd'));
			// component params
			$cparams   = JComponentHelper::getParams(JInbound::COM);
			// Escape strings for HTML output
			$this->pageclass_sfx = JInboundHelperFilter::escape($params->get('pageclass_sfx'));
			
			// assign variables to the view
			$this->cparams    = $cparams;
			$this->params     = $params;
			$this->state      = $state;
			$this->context    = $context;
			
			// show heading?
			$this->show_page_heading = false;
			if (is_object($this->state) && method_exists($this->state, 'get')) {
				$menuparams = $this->state->get('parameters.menu');
				if (is_object($menuparams) && method_exists($menuparams, 'get')) {
					$this->show_page_heading = $this->state->get('parameters.menu')->get('show_page_heading');
				}
				else {
					$this->show_page_heading = $this->state->get('show_page_heading');
				}
			}
		}
		// prepare the document and display
		$this->_prepareDocument();
		
		$profiler->mark('onJInboundViewDisplayEnd');
		
		return parent::display($tpl, $safeparams);
	}
	
	/**
	 * used to add administrator toolbar
	 */
	public function addToolBar() {
		// set the default title
		$name = ('contacts' === $this->_name ? 'leads' : $this->_name);
		JToolBarHelper::title(JText::_(strtoupper(JInbound::COM.'_'.$name)), 'jinbound-'.strtolower($name));
		
		// only fire in administrator
		if (!$this->app->isAdmin()) return;
		
		if (JFactory::getUser()->authorise('core.manage', JInbound::COM)) {
			JToolBarHelper::preferences(JInbound::COM);
		}
		
		JToolBarHelper::divider();
		// help!!!
		//JToolBarHelper::help('COM_JINBOUND_HELP', false, JInboundHelperUrl::help());
		
	}
	
	public function addMenuBar() {
		
		// only fire in administrator
		if (!$this->app->isAdmin())
		{
			return;
		}
		
		$vName  = $this->app->input->get('view', '', 'cmd');
		$option = $this->app->input->get('option', '', 'cmd');
		
		if (empty($vName)) {
			$vName = 'dashboard';
		}
		
		$vName = strtolower($vName);
		// Dashboard
		$this->addSubMenuEntry(JText::_(strtoupper(JInbound::COM) . '_DASHBOARD'), JInboundHelperUrl::_(), $option == JInbound::COM && in_array($vName, array('', 'dashboard')));
		// the rest
		$subMenuItems = array(
			'campaigns'   => 'CAMPAIGNS_MANAGER',
			'emails'      => 'LEAD_NURTURING_MANAGER',
			'pages'       => 'PAGES',
			'contacts'    => 'LEADS',
			'reports'     => 'REPORTS',
			'statuses'    => 'STATUSES',
			'priorities'  => 'PRIORITIES',
			'forms'       => 'FORMS',
			'fields'      => 'FIELDS'
		);
		
		if (defined('JDEBUG') && JDEBUG)
		{
			$subMenuItems['tracks'] = 'TRACKS';
		}
		
		foreach ($subMenuItems as $sub => $txt)
		{
			$single = JInboundInflector::singularize($sub);
			if (!JFactory::getUser()->authorise('core.manage', JInbound::COM . '.' . $single))
			{
				continue;
			}
			$label = JText::_(strtoupper(JInbound::COM . "_$txt"));
			$href = JInboundHelperUrl::_(array('view' => $sub));
			$active = ($vName == $sub && JInbound::COM == $option);
			if ('statuses' === $sub)
			{
				if (JInbound::version()->isCompatible('3.0.0'))
				{
					$this->addSubMenuEntry('<hr style="padding:0;margin:0"/>', 'javascript:', false);
				}
				$this->addSubMenuEntry(JText::_('JCATEGORIES'), JInboundHelperUrl::_(array(
					'option' => 'com_categories'
				,	'view' => 'categories'
				,	'extension' => JInbound::COM
				)), ('categories' == $vName && 'com_categories' == $option));
			}
			$this->addSubMenuEntry($label, $href, $active);
		}
		
		$this->sidebar = false;
		if (class_exists('JHtmlSidebar')) {
			$this->renderFilters();
			$this->sidebar = JHtmlSidebar::render();
		}
	}
	
	protected function addSubMenuEntry($label, $href, $active) {
		if (class_exists('JHtmlSidebar')) {
			return JHtmlSidebar::addEntry($label, $href, $active);
		}
		return JSubMenuHelper::addEntry($label, $href, $active);
	}
	
	public function renderFilters() {
		// STUB
		return '';
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument() {
		
		$doc    = JFactory::getDocument();
		$canAdd = method_exists($doc, 'addStyleSheet');
		$ext    = (JInbound::config("debug", 0) ? '.min' : '');
		$sfx    = $this->app->isAdmin() ? 'back' : 'front';
		if ($canAdd) {
			if (JInbound::version()->isCompatible('3.0.0')) {
				JHtml::_('behavior.framework', true);
				JHtml::_('jquery.ui', array('core', 'sortable', 'tabs'));
				JHtml::_('bootstrap.tooltip');
			}
			else {
				JHtml::_('behavior.tooltip', '.hasTip');
			}
			if (JInbound::config("load_jquery_$sfx", 1)) {
				$doc->addScript(JInboundHelperUrl::media() . '/js/jquery-1.9.1.min.js');
			}
			if (JInbound::config("load_jquery_ui_$sfx", 1)) {
				$doc->addStyleSheet(JInboundHelperUrl::media() . '/ui/css/jinbound_component/jquery-ui-1.10.1.custom' . $ext . '.css');
				$doc->addScript(JInboundHelperUrl::media() . '/ui/js/jquery-ui-1.10.1.custom' . $ext . '.js');
			}
			if (JInbound::config("load_bootstrap_$sfx", 1)) {
				$doc->addStyleSheet(JInboundHelperUrl::media() . '/bootstrap/css/bootstrap.css');
				$doc->addStyleSheet(JInboundHelperUrl::media() . '/bootstrap/css/bootstrap-responsive.css');
				$doc->addScript(JInboundHelperUrl::media() . '/bootstrap/js/bootstrap' . $ext . '.js');
			}
		}
		
		// we don't want to run this whole function in admin,
		// but there's still a bit we need - specifically, styles for header icons
		// if we're in admin, just load the stylesheet and bail
		if ($this->app->isAdmin()) {
			if (method_exists($doc, 'addStyleSheet')) {
				$doc->addStyleSheet(JInboundHelperUrl::media() . '/css/admin.stylesheet.css');
			}
			return;
		}
		
		$doc->addStyleSheet(JInboundHelperUrl::media() . '/css/stylesheet.css');
		
		$menus   = $this->app->getMenu();
		$pathway = $this->app->getPathway();
		$title   = null;
		$layout  = $this->app->input->get('layout', '', 'cmd');

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else {
			$this->params->def('page_heading', JText::_('COM_JINBOUND_DEFAULT_PAGE_TITLE'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $this->app->getCfg('sitename');
		}
		elseif ($this->app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $this->app->getCfg('sitename'), $title);
		}
		elseif ($this->app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $this->app->getCfg('sitename'));
		}
		$this->document->setTitle($title);
		
		// set the path using another class method so we can override in each view
		$path = $this->getBreadcrumbs($menu);
		// add the crumbs, if there are any
		if (!empty($path)) foreach ($path as $item) {
			$pathway->addItem($item['title'], $item['url']);
		}
		
		/*
		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		*/
	}
	
	/**
	 * This should be overridden in each parent class!
	 * 
	 * @param array
	 * 
	 * @return array
	 */
	public function getBreadcrumbs(&$menu) {
		return array();
	}
	
	public function getCrumb($title, $url = '') {
		return array('title' => $title, 'url' => $url);
	}
}
