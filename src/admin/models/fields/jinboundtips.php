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

JLoader::register('JInboundFieldView', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/views/fieldview.php');

JFormHelper::loadFieldClass('hidden');

class JFormFieldJinboundTips extends JFormFieldHidden
{
    protected $type = 'JinboundTips';

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
        $viewConfig = array('template_path' => dirname(__FILE__) . '/tips');
        $view       = new JInboundFieldView($viewConfig);
        return $view;
    }

    /**
     * don't output an input
     *
     * (non-PHPdoc)
     * @see JFormFieldHidden::getInput()
     */
    protected function getInput()
    {
        return '';
    }

    /**
     * don't output a label
     *
     * (non-PHPdoc)
     * @see JFormFieldHidden::getLabel()
     */
    protected function getLabel()
    {
        return '';
    }
}
