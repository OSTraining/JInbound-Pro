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


if (jimport('joomla.application.component.model')) {
	class JInboundBaseModelCommon extends JModel
	{
		static public function addIncludePath($path = '', $prefix = '') {
			return parent::addIncludePath($path, $prefix);
		}
	}
}
else {
	jimport('legacy.model.legacy');
	class JInboundBaseModelCommon extends JModelLegacy
	{
		static public function addIncludePath($path = '', $prefix = '') {
			return parent::addIncludePath($path, $prefix);
		}
	}
}

class JInboundModelPage extends JInboundBaseModelCommon
{

	public $_context = 'com_jinbound.page';

	public function &getItem()
	{
		// Initialise variables.
				$id = JRequest::getInt('id');

				$db = $this->getDbo();
				$query = $db->getQuery(true);

				$query->select('*');
				$query->from('#__jinbound_pages AS Page');
				$query->where('Page.id = ' . (int) $id);

				$db->setQuery($query);

				$data = $db->loadObject();

				return $data;
	}


}
