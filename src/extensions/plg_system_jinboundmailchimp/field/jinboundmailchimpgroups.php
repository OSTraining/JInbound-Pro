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

JFormHelper::loadFieldClass('Groupedlist');

class JFormFieldJinboundMailchimpgroups extends JFormFieldGroupedList
{
    protected $type = 'JinboundMailchimpgroups';

    /**
     * @var JFormFieldJinboundMailchimplists
     */
    protected $listField = null;

    /**
     * @var object[]
     */
    protected $mcOptions = null;

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        if (parent::setup($element, $value, $group)) {
            if ($fieldName = (string)$element['listfield']) {
                $this->listField = $this->form->getField($fieldName, $group);

                return $this->listField instanceof JFormFieldJinboundMailchimplists;
            }
        }

        return false;
    }

    /**
     * @return object[]
     * @throws Exception
     */
    protected function getGroups()
    {
        $options = array();

        if ($listIds = $this->listField->value) {
            $plugin = JPluginHelper::getPlugin('system', 'jinboundmailchimp');
            require_once realpath(__DIR__ . '/../library/helper.php');

            $helper = new JinboundMailchimp(array('params' => $plugin->params));

            $lists  = $helper->getList();
            $groups = $helper->getGroups($listIds);

            foreach ($groups as $listId => $listGroups) {
                if (isset($lists[$listId])) {
                    $listName = $lists[$listId]->name;

                    $options[$listName] = array();
                    if ($listGroups) {
                        foreach ($listGroups as $group) {
                            $options[$listName][] = JHtml::_('select.option', $group->id, $group->name);
                        }

                    } else {
                        $options[$listName][] = JHtml::_(
                            'select.option',
                            '',
                            JText::_('PLG_SYSTEM_JINBOUNDMAILCHIMP_NONE'),
                            'value',
                            'text',
                            true
                        );
                    }
                }
            }
        }

        return array_merge(parent::getGroups(), $options);
    }
}
