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

defined('_JEXEC') or die;

class JFormFieldJinboundcaptchaupdate extends JFormField
{
    protected function getInput()
    {
        // rewritten LiveUpdate code
        require_once JPATH_ROOT . '/plugins/system/jinboundcaptcha/liveupdate/liveupdate.php';
        $updateInfo = LiveUpdate::getUpdateInformation();
        if (!$updateInfo->supported) {
            return JText::_('PLG_SYSTEM_JINBOUNDCAPTCHA_UPDATE_UNSUPPORTED');
        } else {
            if ($updateInfo->stuck) {
                return JText::_('PLG_SYSTEM_JINBOUNDCAPTCHA_UPDATE_STUCK');
            } else {
                if ($updateInfo->hasUpdates) {
                    return JText::sprintf('PLG_SYSTEM_JINBOUNDCAPTCHA_UPDATE_HASUPDATES', $updateInfo->version);
                }
            }
        }
        return JText::_('PLG_SYSTEM_JINBOUNDCAPTCHA_UPDATE_OK');
    }
}
