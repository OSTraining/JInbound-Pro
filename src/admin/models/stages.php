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

JInbound::registerLibrary('JInboundListModel', 'models/basemodellist');

/**
 * This models supports retrieving lists of locations.
 *
 * @package        jInbound
 * @subpackage     com_jinbound
 */
class JInboundModelStages extends JInboundListModel
{
    /**
     * Model context string.
     *
     * @var        string
     */
    public    $_context = 'com_jinbound.stages';
    protected $context  = 'com_jinbound.stages';

    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();

        // main query
        $query = $db->getQuery(true)
            // Select the required fields from the table.
            ->select('Stage.*')
            ->from('#__jinbound_stages AS Stage');
        // add author to query
        $this->appendAuthorToQuery($query, 'Stage');
        $this->filterSearchQuery($query, $this->getState('filter.search'), 'Stage', 'id', array('name', 'description'));
        $this->filterPublished($query, $this->getState('filter.published'), 'Stage');

        // Add the list ordering clause.
        $orderCol  = trim($this->state->get('list.ordering'));
        $orderDirn = trim($this->state->get('list.direction'));
        if (strlen($orderCol)) {
            $query->order((method_exists($db, 'escape') ? $db->escape($orderCol . ' ' . $orderDirn,
                true) : $db->getEscaped($orderCol . ' ' . $orderDirn, true)));
        }

        return $query;
    }


}
