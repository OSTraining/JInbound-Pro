<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound

**********************************************
JInbound
Copyright (c) 2012 Anything-Digital.com
**********************************************
JInbound is some kind of marketing thingy

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This header must not be removed. Additional contributions/changes
may be added to this header as long as no information is deleted.
**********************************************
Get the latest version of JInbound at:
http://anything-digital.com/
**********************************************

 */

defined('JPATH_PLATFORM') or die;

// include the path helper
JLoader::register('JInboundHelperPath', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/path.php');
// include the html helper here
//jimport('joomla.html.html');
//JHtml::addIncludePath(JInboundHelperPath::site('helpers/html'));
// include core libs
jimport('joomla.error.profiler');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
// include other helpers
JLoader::register('JInbound', JInboundHelperPath::helper('jinbound'));
JLoader::register('JInboundHelperFilter', JInboundHelperPath::helper('filter'));
JLoader::register('JInboundHelperUrl', JInboundHelperPath::helper('url'));
// we have to always load the language file for com_categories
JInbound::language('com_categories', JPATH_ADMINISTRATOR);

// create an intermediary dummy class
if (jimport('joomla.application.component.view')) {
	class JInboundBaseView extends JView
	{
		
	}
}
else {
	jimport('legacy.view.legacy');
	class JInboundBaseView extends JViewLegacy
	{
		
	}
}


class JInboundView extends JInboundBaseView
{
	public static $option = JInbound::COM;
	
	protected $_filters;

	function display($tpl = null, $echo = true) {
		$profiler = JProfiler::getInstance('Application');
		$profiler->mark('onJInboundViewDisplayStart');
		
		$app = JFactory::getApplication();
		
		$this->viewClass = 'jinbound_component';
		if (JInbound::version()->isCompatible('3.0')) {
			$this->viewClass .= ' jinbound_bootstrap';
		}
		
		// add the common tmpl path so we can load our commonly shared files
		$this->addTemplatePath(($app->isAdmin() ? JInboundHelperPath::admin() : JInboundHelperPath::site()) . '/views/common/tmpl');
		
		// are we in component view?
		$this->tpl = 'component' == $app->input->get('tmpl', '', 'cmd');
		
		if (!$app->isAdmin()) {
			// Initialise variables
			$state		 = $this->get('State');
			$context   = $this->get('Context');
			// these are page params only... ?
			$params    = $state->params;
			// are we in a raw view?
			$this->raw = ('raw' == $app->input->get('format', '', 'cmd'));
			// component params
			$cparams   = JInbound::config();
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
			
			// add debug info
			JInbound::debugger('Context', $this->context);
			JInbound::debugger('State', $this->state);
			JInbound::debugger('Params', $this->params);
			
		}
		// prepare the document and display
		$this->_prepareDocument();
		
		$profiler->mark('onJInboundViewDisplayEnd');
		
		if ($echo) {
			parent::display($tpl);
		}
		else {
			return $this->loadTemplate($tpl);
		}
	}
	
	/**
	 * used to add administrator toolbar
	 */
	public function addToolBar() {
		
		// only fire in administrator
		if (!JFactory::getApplication()->isAdmin()) return;
		
		if (JFactory::getUser()->authorise('core.manage', self::$option)) {
			JToolBarHelper::preferences(self::$option);
		}
		
		JToolBarHelper::divider();
		// help!!!
		JToolBarHelper::help('COM_JINBOUND_HELP', false, JInbound::config('help_url'));
		
	}
	
	public function addMenuBar() {
		
		$app = JFactory::getApplication();
		
		// only fire in administrator
		if (!$app->isAdmin()) return;
		
		$vName  = $app->input->get('view', '', 'cmd');
		$option = $app->input->get('option', '', 'cmd');
		// Dashboard
		JSubMenuHelper::addEntry(JText::_(strtoupper(self::$option)), JInboundHelperUrl::_(), $option == self::$option && in_array($vName, array('', 'dashboard')));
		// the rest
		$subMenuItems = array('help');
		foreach ($subMenuItems as $sub) {
			$label = JText::_(strtoupper(self::$option . "_$sub"));
			$href = JInboundHelperUrl::_(array('view' => $sub));
			$active = ($vName == $sub);
			JSubMenuHelper::addEntry($label, $href, $active);
		}
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument() {
		
		$app = JFactory::getApplication();
		
		// load js framework
		JHtml::_('behavior.framework', true);
		
		// we don't want to run this whole function in admin,
		// but there's still a bit we need - specifically, styles for header icons
		// if we're in admin, just load the stylesheet and bail
		if ($app->isAdmin()) {
			$this->document->addStyleSheet(JInboundHelperUrl::media('css/admin.css'));
			// grab the modal styles if necessary
			if ($this->tpl && 'modal' == $app->input->get('layout', '', 'cmd')) {
				$this->document->addStyleSheet(JInboundHelperUrl::media('css/modal.css'));
			}
			return;
		}
		
		jimport('joomla.filesystem.file');
		
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;
		
		// load common css
		$this->document->addStyleSheet(JInboundHelperUrl::media('css/common.css'));

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
}
