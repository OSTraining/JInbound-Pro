<?php
/**
 * @package   jInbound-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2015 Anything-Digital.com
 * @copyright 2016-2020 Joomlashack.com. All rights reserved
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

            // Create desired groupings
            $lists = $helper->getLists($listIds);
            foreach ($lists as $listId => $list) {
                $options[$list->name] = array();
            }

            // Add category groups
            $groups = $helper->getGroupsByList($listIds);
            foreach ($groups as $groupId => $group) {
                $listName = $group->category->list->name;


                $options[$listName][] = JHtml::_(
                    'select.option',
                    $group->id,
                    sprintf('%s/%s', $group->category->title, $group->name)
                );
            }

            foreach ($options as $listName => $groups) {
                if (!$groups) {
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

        return array_merge(parent::getGroups(), $options);
    }
}
