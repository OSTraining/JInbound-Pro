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

/**
 * This model supports retrieving lists of forms.
 *
 * @package        jInbound
 * @subpackage     com_jinbound
 */
class JInboundModelForms extends JInboundListModel
{
    /**
     * Model context string.
     *
     * @var        string
     */
    protected $context = 'com_jinbound.forms';

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
                'Form.id'
            ,
                'Form.title'
            ,
                'Form.type'
            ,
                'FormFieldCount'
            ,
                'Form.created_by'
            ,
                'Form.published'
            ,
                'Form.default'
            );
        }

        parent::__construct($config);
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
        $id .= ':' . $this->getState('filter.formtype');
        $id .= ':' . $this->getState('filter.default');
        $id .= ':' . $this->getState('filter.search');

        return parent::getStoreId($id);
    }

    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();

        // main query
        $query = $db->getQuery(true)
            // Select the required fields from the table.
            ->select($this->getState('list.select', 'Form.*'))
            ->from('#__jinbound_forms AS Form')
            // join the counts from the xref table
            ->select('COUNT(Xref.field_id) AS FormFieldCount')
            ->leftJoin('#__jinbound_form_fields AS Xref ON Form.id=Xref.form_id');
        // add author to query
        $this->appendAuthorToQuery($query, 'Form');

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('Form.published = ' . (int)$published);
        } else {
            if ($published === '') {
                $query->where('(Form.published = 0 OR Form.published = 1)');
            }
        }

        // Filter by published state
        $formtype = $this->getState('filter.formtype');
        if (is_numeric($formtype)) {
            $query->where('Form.type = ' . (int)$formtype);
        } else {
            if ($formtype === '') {
                $query->where('(Form.type = 0 OR Form.type = 1)');
            }
        }

        // Filter by default
        $default = $this->getState('filter.default');
        if (is_numeric($default)) {
            $query->where('Form.default = ' . (int)$default);
        } else {
            if ($default === '') {
                $query->where('(Form.default = 0 OR Form.default = 1)');
            }
        }

        // Filter by search.
        $this->filterSearchQuery($query, $this->getState('filter.search'), 'Form', 'id', array('title'));

        // Add the list ordering clause.
        $listOrdering = $this->getState('list.ordering', 'Form.title');
        $listDirn     = $db->escape($this->getState('list.direction', 'ASC'));
        $query->order($db->escape($listOrdering) . ' ' . $listDirn);

        // Group by filter
        $query->group('Form.id');
        return $query;
    }

}
