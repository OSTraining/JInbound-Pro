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

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');

abstract class JInboundHelperPath
{
	static private function _buildPath($root, $file = '') {
		return $root . (empty($file) ? '' : "/$file");
	}
	
	/**
	 * static method to get the media path
	 * 
	 * @return string
	 */
	static public function media($file = '') {
		return self::_buildPath(JPATH_ROOT . '/media/jinbound', $file);
	}
	
	/**
	 * static method to get the admin path
	 * 
	 * @return string
	 */
	static public function admin($file = '') {
		return self::_buildPath(JPATH_ADMINISTRATOR . '/components/' . JInbound::COM, $file);
	}
	
	/**
	 * static method to get the site path
	 * 
	 * @return string
	 */
	static public function site($file = '') {
		return self::_buildPath(JPATH_ROOT . '/components/' . JInbound::COM, $file);
	}
	
	/**
	 * static method to get the helper path
	 * 
	 * @return string
	 */
	static public function helper($helper = null) {
		static $base;
		
		if (empty($base)) {
			$base = self::admin('helpers');
		}
		
		$file = '';
		if (!empty($helper)) {
			jimport('joomla.filesystem.file');
			$file = preg_replace('/[^a-z]/', '', $helper) . '.php';
		}
		
		return self::_buildPath($base, $file);
	}
	
	/**
	 * static method to get the library path
	 * 
	 * @return string
	 */
	static public function library($file = '') {
		return self::_buildPath(self::admin('libraries'), $file);
	}
}
