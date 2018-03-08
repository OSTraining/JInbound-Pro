<?php
/**
 * @package             jInbound
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
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');
JInbound::registerHelper('form');

class JInboundViewForms extends JInboundListView
{
    /**
     * Default sorting column
     *
     * @var string
     */
    protected $_sortColumn = 'Form.title';

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     */
    protected function getSortFields()
    {
        return array(
            'Form.title'      => JText::_('COM_JINBOUND_TITLE')
        ,
            'Form.type'       => JText::_('COM_JINBOUND_FORM_TYPE_LABEL')
        ,
            'FormFieldCount'  => JText::_('COM_JINBOUND_FIELD_COUNT')
        ,
            'Form.created_by' => JText::_('COM_JINBOUND_CREATED_BY')
        ,
            'Form.published'  => JText::_('COM_JINBOUND_PUBLISHED')
        ,
            'Form.default'    => JText::_('COM_JINBOUND_DEFAULT')
        );
    }
}
