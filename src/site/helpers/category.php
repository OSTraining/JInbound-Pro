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

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * jInbound Component Category Tree
 *
 */
class JinboundCategories extends JCategories
{
    public function __construct($options = array())
    {
        $options['table']      = '#__jinbound_pages';
        $options['extension']  = 'com_jinbound';
        $options['field']      = 'category';
        $options['statefield'] = 'published';
        parent::__construct($options);
    }
}
