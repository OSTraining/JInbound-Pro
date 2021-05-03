<?php
/**
 * @package   jInbound-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2015 Anything-Digital.com
 * @copyright 2016-2021 Joomlashack.com. All rights reserved
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

JFormHelper::loadFieldClass('list');

class JFormFieldJinboundMailchimplists extends JFormFieldList
{
    protected $type = 'JinboundMailchimplists';

    /**
     * @var object[]
     */
    protected $mcOptions = null;

    /**
     * @return object[]
     * @throws Exception
     */
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
