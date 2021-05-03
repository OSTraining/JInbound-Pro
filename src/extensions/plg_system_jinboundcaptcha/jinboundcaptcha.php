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

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.plugin.plugin');

$db      = JFactory::getDbo();
$plugins = $db->setQuery($db->getQuery(true)
    ->select('extension_id')->from('#__extensions')
    ->where($db->qn('element') . ' = ' . $db->q('com_jinbound'))
    ->where($db->qn('enabled') . ' = 1')
)->loadColumn();
defined('PLG_SYSTEM_JINBOUNDCAPTCHA') or define('PLG_SYSTEM_JINBOUNDCAPTCHA', 1 === count($plugins));

class plgSystemJInboundcaptcha extends JPlugin
{
    protected $app;

    /**
     * Constructor
     *
     * @param unknown_type $subject
     * @param unknown_type $config
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage('plg_system_jinboundcaptcha.sys', JPATH_ADMINISTRATOR);
        $this->app = JFactory::getApplication();
    }

    public function onJinboundFormbuilderDisplay(&$xml)
    {
        if (!PLG_SYSTEM_JINBOUNDCAPTCHA) {
            return;
        }
        // add validate attribute to captcha
        $nodes = $xml->xpath("//field[@type='captcha']");
        foreach ($nodes as &$node) {
            $node['validate'] = 'captcha';
        }
    }

    public function onJinboundFormbuilderView(&$view)
    {
        if (!PLG_SYSTEM_JINBOUNDCAPTCHA) {
            return;
        }
        // add template path for captcha
        $view->addTemplatePath(dirname(__FILE__) . '/tmpl');
    }

    public function onJinboundFormbuilderFields(&$fields)
    {
        if (!PLG_SYSTEM_JINBOUNDCAPTCHA) {
            return;
        }
        // add captcha fields to list
        $fields[] = (object)array(
            'name'  => JText::_('PLG_SYSTEM_JINBOUNDCAPTCHA_CAPTCHA'),
            'id'    => 'captcha',
            'type'  => 'captcha',
            'multi' => 0
        );
    }

    public function onJInboundBeforeListFieldTypes(&$types, &$ignored, &$paths, &$files)
    {
        if (!PLG_SYSTEM_JINBOUNDCAPTCHA) {
            return;
        }
        $types[] = 'captcha';
    }
}
