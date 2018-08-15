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

JFormHelper::loadFieldClass('GroupedList');

class JFormFieldJinboundMailchimpfields extends JFormFieldGroupedList
{
    protected $type = 'JinboundMailchimpfields';

    /**
     * @var object[]
     */
    protected static $fields = null;

    /**
     * @return array
     * @throws Exception
     */
    protected function getGroups()
    {
        if (static::$fields === null) {
            static::$fields = array();

            $plugin = JPluginHelper::getPlugin('system', 'jinboundmailchimp');

            require_once realpath(__DIR__ . '/../library/helper.php');
            $helper = new JinboundMailchimp(array('params' => $plugin->params));

            $listFields = $helper->getFields();
            foreach ($listFields as $listId => $fields) {
                $listName = $fields->list->name;

                foreach ($fields->fields as $field) {
                    if ($subTags = $helper->getSubTags($field->type)) {
                        foreach ($subTags as $subTag) {
                            static::$fields[$listName][] = JHtml::_(
                                'select.option',
                                $field->tag . ':' . $subTag,
                                JText::sprintf(
                                    sprintf('PLG_SYSTEM_JINBOUNDMAILCHIMP_SUBFIELD_%s_%s', $field->type, $subTag),
                                    $field->name
                                )
                            );
                        }

                    } else {
                        static::$fields[$listName][] = JHtml::_('select.option', $field->tag, $field->name);
                    }
                }
            }
        }

        return array_merge(parent::getGroups(), static::$fields);
    }
}
