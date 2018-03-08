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

JLoader::register('JInboundAdminModel',
    JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/models/basemodeladmin.php');

/**
 * This models supports retrieving a location.
 *
 * @package        JInbound
 * @subpackage     com_jinbound
 */
class JInboundModelCampaign extends JInboundAdminModel
{
    /**
     * Model context string.
     *
     * @var        string
     */
    public $_context = 'com_jinbound.campaign';

    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.' . $this->name, $this->name,
            array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        // check published permissions
        if (!JFactory::getUser()->authorise('core.edit.state', 'com_jinbound.campaign')) {
            $form->setFieldAttribute('published', 'readonly', 'true');
        }
        return $form;
    }
}
