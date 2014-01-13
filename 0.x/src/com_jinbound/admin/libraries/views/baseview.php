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
	jimport('legacy.view.legacy');
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
		
		$app = JFactory::getApplication();
		
		// add the common tmpl path so we can load our commonly shared files
		$templateBase = ($app->isAdmin() ? JInboundHelperPath::admin() : JInboundHelperPath::site()) . '/views';
		foreach (array('_common', $this->_name . '/tmpl') as $path) {
			if (JFolder::exists("$templateBase/$path")) {
				$this->addTemplatePath("$templateBase/$path");
			}
		}
		
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
		
		$app = JFactory::getApplication();
		
		$this->viewClass = 'jcl_component';
		if (JInbound::version()->isCompatible('3.0.0')) {
			$this->viewClass .= ' jcl_bootstrap';
		}
		// add the view as a class as well
		$this->viewClass   .= ' jcl_view_' . JInboundHelperFilter::escape($this->_name);
		$this->viewName     = $this->_name;
		
		// are we in component view?
		$this->tpl = 'component' == $app->input->get('tmpl', '', 'cmd');
		
		if ($app->isAdmin()) {
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
			$this->raw = ('raw' == $app->input->get('format', '', 'cmd'));
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
		JToolBarHelper::title(JText::_(strtoupper(JInbound::COM.'_'.$this->_name)), 'jinbound-'.strtolower($this->_name));
		
		// only fire in administrator
		if (!JFactory::getApplication()->isAdmin()) return;
		
		if (JFactory::getUser()->authorise('core.manage', JInbound::COM)) {
			JToolBarHelper::preferences(JInbound::COM);
		}
		
		JToolBarHelper::divider();
		// help!!!
		//JToolBarHelper::help('COM_JINBOUND_HELP', false, JInboundHelperUrl::help());
		
	}
	
	public function addMenuBar() {
		
		$app = JFactory::getApplication();
		
		// only fire in administrator
		if (!$app->isAdmin()) return;
		
		$vName  = $app->input->get('view', '', 'cmd');
		$option = $app->input->get('option', '', 'cmd');
		// Dashboard
		JSubMenuHelper::addEntry(JText::_(strtoupper(JInbound::COM)), JInboundHelperUrl::_(), $option == JInbound::COM && in_array($vName, array('', 'dashboard')));
		// the rest
		$subMenuItems = array(
			'pages'     => 'PAGES',
			'emails'    => 'LEAD_NURTURING_MANAGER',
			'leads'     => 'LEADS',
			'reports'   => 'REPORTS',
			'utilities' => 'UTILITIES'
		);
		foreach ($subMenuItems as $sub => $txt) {
			$label = JText::_(strtoupper(JInbound::COM . "_$txt"));
			$href = JInboundHelperUrl::_(array('view' => $sub));
			$active = ($vName == $sub);
			JSubMenuHelper::addEntry($label, $href, $active);
		}
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument() {
		
		$app    = JFactory::getApplication();
		$doc    = JFactory::getDocument();
		$canAdd = method_exists($doc, 'addStyleSheet');
		$ext    = (JInbound::config("debug", 0) ? '.min' : '');
		$sfx    = $app->isAdmin() ? 'back' : 'front';
		if ($canAdd) {
			if (JInbound::version()->isCompatible('3.0.0')) {
				JHtml::_('behavior.framework', true);
				JHtml::_('jquery.ui', array('core', 'sortable'));
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
		if ($app->isAdmin()) {
			if (method_exists($doc, 'addStyleSheet')) {
				$doc->addStyleSheet(JInboundHelperUrl::media() . '/css/admin.stylesheet.css');
			}
			return;
		}
		
		$doc->addStyleSheet(JInboundHelperUrl::media() . '/css/stylesheet.css');
		
		$menus   = $app->getMenu();
		$pathway = $app->getPathway();
		$title   = null;
		$layout  = $app->input->get('layout', '', 'cmd');

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
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
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
