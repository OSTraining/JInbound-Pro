<?php
/**
 * @package   jInbound-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2015 Anything-Digital.com
 * @copyright 2016-2019 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of jInbound-Pro.
 *
 * jInbound-Pro is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * jInbound-Pro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with jInbound-Pro.  If not, see <http://www.gnu.org/licenses/>.
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
