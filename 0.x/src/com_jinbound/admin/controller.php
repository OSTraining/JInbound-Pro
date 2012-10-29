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

JLoader::register('JInboundBaseController', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/controllers/basecontroller.php');
JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');

class JInboundController extends JInboundBaseController
{
	function display($cachable = false, $urlparams = false) {
		$app        = JFactory::getApplication();
		$view       = $app->input->get('view', 'Dashboard', 'cmd');
		$helpurl    = JInbound::config('help_url');
		$configured = preg_match('/^https?\:\/{2}/', $helpurl);
		if (!$configured) {
			$app->enqueuemessage(JText::_('COM_JINBOUND_SAVE_CONFIG_WARNING'), 'warning');
		}
		// the help view acts as a redirect to the REAL help page
		// we only really use this in the main component submenu,
		// as any link we can handle via code will just use the config option
		if ('help' == strtolower($view)) {
			$app->redirect($configured ? JInbound::config('help_url') : 'index.php?option=com_jinbound');
			// tear down the application
			jexit();
		}
		$app->input->set('view', $view);
		parent::display($cachable);
	}
}
