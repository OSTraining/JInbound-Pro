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
JLoader::register('JInboundView', "$base/libraries/views/allpages.php");

class JInboundViewSettings extends JInboundView
{

	public function display($tpl = null)
	{

		JToolBarHelper::title(JText::_(parent::$option . '_DASHBOARD_TITLE'), 'jinbound');

		parent::display($tpl);
	}

}