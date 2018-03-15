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

JFormHelper::loadFieldClass('text');

class JFormFieldJinboundTimespan extends JFormFieldText
{
    protected $type = 'JinboundTimespan';

    /**
     * Builds the input element
     *
     * (non-PHPdoc)
     * @see JFormField::getInput()
     */
    protected function getInput()
    {
        $view        = $this->getView();
        $view->input = parent::getInput();
        return $view->loadTemplate();
    }

    /**
     * gets a new instance of the base field view
     *
     * @return JInboundFieldView
     */
    protected function getView()
    {
        $viewConfig = array('template_path' => dirname(__FILE__) . '/timespan');
        $view       = new JInboundFieldView($viewConfig);
        return $view;
    }
}
