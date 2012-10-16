<?php
/**
 * this file cleans up the test environment from previous test runs
 * 
 */

// go ahead and use JFile and JFolder
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/*
// clean up the component paths
JFolder::delete(JPATH_BASE . '/administrator/components/');
JFolder::delete(JPATH_BASE . '/components/');
JFolder::delete(JPATH_BASE . '/media/');
JFolder::delete(JPATH_BASE . '/modules/');
JFolder::delete(JPATH_BASE . '/plugins/');
*/