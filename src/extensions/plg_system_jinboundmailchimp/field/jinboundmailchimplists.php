<?php
/**
 * @package             jInbound
 * @subpackage          plg_system_jinboundmailchimp
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

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldJinboundMailchimplists extends JFormFieldList
{
    protected $type = 'JinboundMailchimplists';

    /**
     * @var object[]
     */
    protected $mcOptions = null;

    protected function getOptions()
    {
        $plugin = JPluginHelper::getPlugin('system', 'jinboundmailchimp');
        require_once realpath(__DIR__ . '/../library/helper.php');

        $helper = new JinboundMailchimp(array('params' => $plugin->params));

        if ($this->mcOptions === null) {
            $lists = $helper->getLists();

            $this->mcOptions = array();
            foreach ($lists as $listId => $list) {
                $this->mcOptions[] = JHtml::_('select.option', $listId, $list->name);
            }
        }

        return array_merge(parent::getOptions(), $this->mcOptions);
    }
}
