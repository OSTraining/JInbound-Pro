<?php
/**
 * @version		$Id$
 * @package		JCalPro
 * @subpackage	com_jcalpro

**********************************************
JCal Pro
Copyright (c) 2006-2011 Anything-Digital.com
**********************************************
JCalPro is a native Joomla! calendar component for Joomla!

JCal Pro was once a fork of the existing Extcalendar component for Joomla!
(com_extcal_0_9_2_RC4.zip from mamboguru.com).
Extcal (http://sourceforge.net/projects/extcal) was renamed
and adapted to become a Mambo/Joomla! component by
Matthew Friedman, and further modified by David McKinnis
(mamboguru.com) to repair some security holes.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This header must not be removed. Additional contributions/changes
may be added to this header as long as no information is deleted.
**********************************************
Get the latest version of JCal Pro at:
http://anything-digital.com/
**********************************************

 */

defined('JPATH_PLATFORM') or die;
jimport('joomla.application.component.modellist');
JLoader::register('JInboundListModel', JPATH_ADMINISTRATOR.'/components/com_jinbound/libraries/models/basemodellist.php');

/**
 * This models supports retrieving lists of locations.
 *
 * @package		JCalPro
 * @subpackage	com_jcalpro
 */
class JInboundModelStages extends JModelList
{

	public function __construct($config = array())
	{

		parent::__construct($config);
	}


	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();

		// main query
		$query = $db->getQuery(true)
		// Select the required fields from the table.
		->select('*')
		->from('#__jinbound_stages AS Stage')
		;

		return $query;
	}



}
