<?php
/**
 * this file simply copies the various sources into their proper places
 * 
 */

// go ahead and use JFile and JFolder
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

// copy the component
JFolder::create(JPATH_BASE . '/components');
JFolder::copy(JPATH_JINBOUNDSRC . '/com_jinbound', JPATH_BASE . '/components');
JFolder::create(JPATH_BASE . '/administrator/components');
JFolder::copy(JPATH_JINBOUNDSRC . '/com_jinbound/admin', JPATH_BASE . '/administrator/components/com_jinbound');

// make a media folder
JFolder::create(JPATH_BASE . '/media');
JFolder::copy(JPATH_JINBOUNDSRC . '/com_jinbound/media', JPATH_BASE . '/media/jinbound');

// make a modules folder
JFolder::create(JPATH_BASE . '/modules');

// make a plugins folder
JFolder::create(JPATH_BASE . '/plugins');