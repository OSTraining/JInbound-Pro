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

JFormHelper::loadFieldClass('list');

class JFormFieldJinboundStatuses extends JFormFieldList
{
    protected $type = 'JinboundStatuses';

    protected function getOptions()
    {

        $final = $this->element['final'] ? ('true' === strtolower($this->element['final'])) : false;

        if (!file_exists($file = JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/status.php')) {
            return array();
        }
        require_once $file;

        return array_merge(parent::getOptions(), JInboundHelperStatus::getSelectOptions($final));
    }
}
