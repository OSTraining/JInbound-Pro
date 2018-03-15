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
 * This model supports retrieving lists of fields.
 *
 * @package        jInbound
 * @subpackage     com_jinbound
 */
class JInboundModelFields extends JInboundListModel
{
    /**
     * Model context string.
     *
     * @var        string
     */
    protected $context = 'com_jinbound.fields';

    /**
     * The category context (allows other extensions to derived from this model).
     *
     * @var        string
     */
    protected $_extension = 'com_jinbound';

    private $_parent = null;

    private $_items = null;

    /**
     * Constructor.
     *
     * @param       array   An optional associative array of configuration settings.
     *
     * @see         JController
     */
    function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'Field.id'
            ,
                'Field.title'
            ,
                'Field.type'
            ,
                'Field.formtype'
            ,
                'Field.created_by'
            ,
                'Field.published'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState($ordering = null, $direction = null)
    {
        parent::populateState($ordering, $direction);

        // force only published fields on frontend
        if (!JFactory::getApplication()->isAdmin()) {
            $this->setState('filter.published', 1);
        }

        $this->setState('filter.access', true);
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param    string $id A prefix for the store id.
     *
     * @return    string        A store id.
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.extension');
        $id .= ':' . $this->getState('filter.published');
        $id .= ':' . $this->getState('filter.access');
        $id .= ':' . $this->getState('filter.parentId');

        return parent::getStoreId($id);
    }

    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();

        // main query
        $query = $db->getQuery(true)
            // Select the required fields from the table.
            ->select($this->getState('list.select', 'Field.*'))
            ->from('#__jinbound_fields AS Field');
        // add author to query
        $this->appendAuthorToQuery($query, 'Field');

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('Field.published = ' . (int)$published);
        } else {
            if ($published == '') {
                $query->where('(Field.published = 0 OR Field.published = 1)');
            }
        }

        // Filter by search.
        $this->filterSearchQuery($query, $this->getState('filter.search'), 'Field', 'id', array('title', 'name'));

        $type = $this->getState('filter.formtype');
        if (is_numeric($type)) {
            $query->where('Field.formtype = ' . (int)$type);
        }

        // Add the list ordering clause.
        $listOrdering = $this->getState('list.ordering', 'Field.title');
        $listDirn     = $db->escape($this->getState('list.direction', 'ASC'));
        $query->order($db->escape($listOrdering) . ' ' . $listDirn);

        // Group by filter
        $query->group('Field.id');
        return $query;
    }

}
