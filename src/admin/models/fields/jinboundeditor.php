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

if (!defined('JINP_LOADED')) {
    $path = JPATH_ADMINISTRATOR . '/components/com_jinbound/include.php';
    if (is_file($path)) {
        require_once $path;
    }
}

JLoader::register('JInboundFieldView', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/views/fieldview.php');

JFormHelper::loadFieldClass('editor');

class JFormFieldJinboundEditor extends JFormFieldEditor
{
    public $type = 'JinboundEditor';

    public function getTags()
    {
        // Initialize variables.
        $tags = array();

        foreach ($this->element->children() as $tag) {
            // Only add <tag /> elements.
            if ($tag->getName() != 'tag') {
                continue;
            }

            // Create a new option object based on the <option /> element.
            $tmp        = new stdClass;
            $tmp->value = (string)$tag['value'];

            // Set some option attributes.
            $tmp->class = (string)$tag['class'];

            // Add the option object to the result set.
            $tags[] = $tmp;
        }

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onJinboundEditorTags', array(&$this, &$tags));

        reset($tags);

        return $tags;
    }

    /**
     * This method is used in the form display to show extra data
     *
     */
    public function getSidebar()
    {
        $view = $this->getView();
        // set data
        $view->input = $this;
        // return template html
        return $view->loadTemplate();
    }

    /**
     * gets a new instance of the base field view
     *
     * @return JInboundFieldView
     */
    protected function getView()
    {
        $viewConfig = array('template_path' => dirname(__FILE__) . '/editor');
        $view       = new JInboundFieldView($viewConfig);
        return $view;
    }
}
