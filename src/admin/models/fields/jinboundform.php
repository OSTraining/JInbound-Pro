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
JInbound::registerLibrary('JInboundBaseModel', 'models/basemodel');

JFormHelper::loadFieldClass('list');

class JFormFieldJInboundForm extends JFormFieldList
{
    public $type = 'Jinboundform';

    protected function getOptions()
    {
        // get our form model
        JInboundBaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jinbound/models');
        $model = JInboundBaseModel::getInstance('Forms', 'JInboundModel');
        // fetch the list of available, published forms from the model
        $model->getState('filter.published');
        $model->setState('filter.published', '1');
        $forms = $model->getItems();
        // list of available forms
        $list = array();
        // loop available forms & add to the list
        if (!empty($forms)) {
            foreach ($forms as $form) {
                $list[] = JHtml::_('select.option', $form->id, $form->title);
            }
        }
        // send back all options
        return array_merge(parent::getOptions(), $list);
    }
}
