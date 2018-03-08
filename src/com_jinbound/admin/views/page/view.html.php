<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 **********************************************
 * JInbound
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

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundItemView', 'views/baseviewitem');
JInbound::registerLibrary('JInboundBaseModel', 'models/basemodel');
JInbound::registerHelper('filter');
JInbound::registerHelper('form');

class JInboundViewPage extends JInboundItemView
{
    function display($tpl = null, $safeparams = false)
    {
        $model    = JInboundBaseModel::getInstance('Forms', 'JInboundModel');
        $forms    = $model->getItems();
        $tags     = array();
        $defaults = '';
        foreach (array(
                     'heading',
                     'subheading',
                     'maintext',
                     'sidebartext',
                     'image',
                     'form',
                     'form:open',
                     'form:close'
                 ) as $default) {
            $defaults .= "<li>{{$default}}</li>";
        }

        if (!empty($forms)) {
            foreach ($forms as $form) {
                // start the list
                $out = '<ul>';
                // add the defaults
                $out    .= $defaults;
                $fields = JInboundHelperForm::getFields($form->id);
                if (!empty($fields)) {
                    foreach ($fields as $field) {
                        $out .= '<li>{form:' . JInboundHelperFilter::escape($field->name) . '}</li>';
                    }
                }
                $out             .= '<li>{submit}</li></ul>';
                $tags[$form->id] = $out;
            }
        }

        $this->layouttags = $tags;

        return parent::display($tpl, $safeparams);
    }
}
