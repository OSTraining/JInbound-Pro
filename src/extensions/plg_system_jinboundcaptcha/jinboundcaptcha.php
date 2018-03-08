<?php
/**
 * @package             JInbound
 * @subpackage          plg_system_jinboundcaptcha
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
