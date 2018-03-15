<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
 **********************************************
 * jInbound
 * Copyright (c) 2013 Anything-Digital.com
 * Copyright (c) 2018 Open Source Training, LLC
 **********************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.n *
 * This header must not be removed. Additional contributions/changes
 * may be added to this header as long as no information is deleted.
 */

defined('JPATH_PLATFORM') or die;

if (!defined('JINB_LOADED')) {
    $path = JPATH_ADMINISTRATOR . '/components/com_jinbound/include.php';
    if (is_file($path)) {
        require_once $path;
    }
}

JFormHelper::loadFieldClass('list');

class JFormFieldJInboundFieldType extends JFormFieldList
{
    public $type = 'Jinboundfieldtype';

    protected function getOptions()
    {
        $dispatcher = JDispatcher::getInstance();
        // initialize our array for the field types
        // we're holding them here because we want to be able to sort them later
        $types = array();
        // ignored field types
        $ignored = array();
        if ($this->element['ignoredfields']) {
            $ignored = explode(',', (string)$this->element['ignoredfields']);
        }
        // paths to search for files
        $paths = array(
            JPATH_LIBRARIES . '/joomla/form/fields'
        ,
            JInboundHelperPath::library() . '/fields'
        );
        // files containing field classes
        $files = array();
        // trigger plugins
        $dispatcher->trigger('onJInboundBeforeListFieldTypes', array(&$types, &$ignored, &$paths, &$files));
        // get files from the paths
        foreach ($paths as $path) {
            if (!JFolder::exists($path)) {
                continue;
            }
            $search = JFolder::files($path, '.php$');
            if (is_array($search)) {
                $files = array_merge($files, $search);
            }
        }
        // go ahead & loop through our found fields, and add them to the stack if they're not being ignored
        if (!empty($files)) {
            foreach ($files as $filename) {
                $name = preg_replace('/^(.*?)\.(.*)$/', '\1', $filename);
                if (in_array($name, $ignored)) {
                    continue;
                }
                $types[] = $name;
            }
        }
        // sort field types alphabetically
        asort($types);
        $types = array_values($types);
        $list  = array();
        // loop these types & create options
        for ($i = 0; $i < count($types); $i++) {
            $list[] = JHtml::_('select.option', $types[$i], $types[$i]);
        }
        // send back our select list
        return array_merge(parent::getOptions(), $list);
    }
}
