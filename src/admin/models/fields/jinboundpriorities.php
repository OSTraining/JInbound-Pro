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

class JFormFieldJinboundPriorities extends JFormFieldList
{
    protected $type = 'JinboundPriorities';

    protected function getOptions()
    {

        if (!file_exists($file = JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/priority.php')) {
            return array();
        }
        require_once $file;

        return array_merge(parent::getOptions(), JInboundHelperPriority::getSelectOptions());
    }
}
