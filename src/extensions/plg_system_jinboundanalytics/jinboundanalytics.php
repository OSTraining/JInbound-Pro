<?php
/**
 * @package             JInbound
 * @subpackage          plg_system_jinboundanalytics
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
defined('PLG_SYSTEM_JINBOUNDANALYTICS') or define('PLG_SYSTEM_JINBOUNDANALYTICS', 1 === count($plugins));

class plgSystemJInboundanalytics extends JPlugin
{
    /**
     * Constructor
     *
     * @param unknown_type $subject
     * @param unknown_type $config
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage('plg_system_jinboundanalytics.sys', JPATH_ADMINISTRATOR);
    }

    public function onContentPrepareForm($form)
    {
        if (!PLG_SYSTEM_JINBOUNDANALYTICS) {
            return true;
        }
        if (!($form instanceof JForm)) {
            $this->_subject->setError('JERROR_NOT_A_FORM');
            return false;
        }
        if ('com_jinbound.page' != $form->getName()) {
            return true;
        }
        JForm::addFormPath(dirname(__FILE__) . '/form');
        $result = $form->loadFile('jinboundanalytics', false);
        return $result;
    }
}
