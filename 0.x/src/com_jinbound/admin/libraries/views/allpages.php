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


	function display($tpl = null, $echo = true) {
		$profiler = JProfiler::getInstance('Application');
		$profiler->mark('onJInboundViewDisplayStart');

		$app = JFactory::getApplication();

		$this->viewClass = 'jinbound_component';
		if (JInbound::version()->isCompatible('3.0')) {
			$this->viewClass .= ' jinbound_bootstrap';
		}


		$this->addMenuBar();

		JHtml::stylesheet('admin.stylesheet.css', 'media/jinbound/css/');

		$jversion = new JVersion();
		if( version_compare( $jversion->getShortVersion(), "3", 'lt' ) ) {
			//JHtml::stylesheet('bootstrap.legacy.css', 'media/jinbound/css/');
			JHtml::stylesheet('bootstrap-responsive.min.css', 'media/jinbound/css/');
			JHtml::script('bootstrap.min.js', 'media/jinbound/js/');
		}

		if (JFactory::getUser()->authorise('core.manage', self::$option)) {
			JToolBarHelper::preferences(self::$option);
		}

		JToolBarHelper::divider();
		// help!!!
		JToolBarHelper::help('COM_JINBOUND_HELP', false, JInbound::config('help_url'));



	}


	public function addMenuBar() {

		$app = JFactory::getApplication();


		$option = $app->input->get('option', '', 'cmd');

		JSubMenuHelper::addEntry(
			JText::_(self::$option.'_DASHBOARD'),
			'index.php?option=com_jinbound',
			false
			);

		JSubMenuHelper::addEntry(
			JText::_(self::$option.'_PAGES'),
			'index.php?option=com_jinbound&view=pages',
			false
			);
		JSubMenuHelper::addEntry(
			JText::_(self::$option.'_CAMPAIGNS'),
			'index.php?option=com_jinbound&view=campaigns',
			false
			);
		JSubMenuHelper::addEntry(
			JText::_(self::$option.'_LEADS'),
			'index.php?option=com_jinbound&view=leads',
			false
			);
		JSubMenuHelper::addEntry(
			JText::_(self::$option.'_REPORTS'),
			'index.php?option=com_jinbound&view=reports',
			false
			);
		JSubMenuHelper::addEntry(
			JText::_(self::$option.'_SETTINGS'),
			'index.php?option=com_jinbound&view=settings',
			false
			);


			parent::display($tpl);
	}


}
