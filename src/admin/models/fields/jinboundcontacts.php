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

JFormHelper::loadFieldClass('list');

class JFormFieldJinboundContacts extends JFormFieldList
{
    protected $type = 'JinboundContacts';

    protected function getOptions()
    {
        $db = JFactory::getDbo();

        try {
            $options = $db->setQuery($db->getQuery(true)
                ->select('id AS value, name AS text')
                ->from('#__contact_details')
                ->where('published = 1')
                ->order('name DESC')
            )->loadObjectList();
        } catch (Exception $e) {
            $options = array();
        }

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
