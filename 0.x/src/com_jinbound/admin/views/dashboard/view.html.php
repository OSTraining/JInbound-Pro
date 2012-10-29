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

jimport('joomla.html.pane');

$base = JPATH_ADMINISTRATOR . '/components/com_jinbound';

JLoader::register('JInbound', "$base/helpers/jinbound.php");
JLoader::register('JInboundView', "$base/libraries/views/baseview.php");

class JInboundViewDashboard extends JInboundView
{

	public function display($tpl = null)
	{

		JHtml::stylesheet('admin.stylesheet.css', 'media/jinbound/css/');

		$jversion = new JVersion();
		if( version_compare( $jversion->getShortVersion(), "3", 'lt' ) ) {
			JHtml::stylesheet('bootstrap.legacy.css', 'media/jinbound/css/');
			JHtml::stylesheet('bootstrap-responsive.min.css', 'media/jinbound/css/');
			JHtml::script('bootstrap.min.js', 'media/jinbound/js/');
		}

		JSubMenuHelper::addEntry(
				JText::_(parent::$option.'_DASHBOARD'),
				'index.php?option=com_jinbound',
				false
			);

		JSubMenuHelper::addEntry(
				JText::_(parent::$option.'_PAGES'),
				'index.php?option=com_jinbound&view=pages',
				false
			);
		JSubMenuHelper::addEntry(
				JText::_(parent::$option.'_CAMPAIGNS'),
				'index.php?option=com_jinbound&view=campaigns',
				false
			);
		JSubMenuHelper::addEntry(
				JText::_(parent::$option.'_LEADS'),
				'index.php?option=com_jinbound&view=leads',
				false
			);
		JSubMenuHelper::addEntry(
				JText::_(parent::$option.'_REPORTS'),
				'index.php?option=com_jinbound&view=reports',
				false
			);

		$this->addToolbar();
		parent::display($tpl);
	}

	function addToolBar() {
		JToolBarHelper::title(JText::_(parent::$option . '_DASHBOARD_TITLE'), 'jinbound');
		parent::addToolBar();
	}
}
