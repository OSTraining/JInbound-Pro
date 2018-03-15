<?php
/**
 * @package             jInbound
 * @subpackage          files_jinbound
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

// Make sure we're being called from the command line, not a web interface
if (array_key_exists('REQUEST_METHOD', $_SERVER)) {
    die();
}

// Initialize Joomla framework
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);

// Load system defines
if (file_exists(dirname(dirname(__FILE__)) . '/defines.php')) {
    require_once dirname(dirname(__FILE__)) . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', dirname(dirname(__FILE__)));
    require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
if (file_exists(JPATH_LIBRARIES . '/import.legacy.php')) {
    require_once JPATH_LIBRARIES . '/import.legacy.php';
} else {
    require_once JPATH_LIBRARIES . '/import.php';
}

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

// force jInbound
define('JPATH_COMPONENT', JPATH_ROOT . '/components/com_jinbound');
define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_jinbound');

require_once JPATH_ADMINISTRATOR . '/components/com_jinbound/include.php';

/**
 * Cron jobs for jInbound
 *
 */
class JInboundCron extends JApplicationCli
{
    /**
     * Entry point for the script
     *
     * @return void
     * @throws Exception
     */
    public function doExecute()
    {
        echo "Starting jInbound cron task ...\n";

        JFactory::$application = $this;
        JFactory::getLanguage()->load('com_jinbound', JPATH_ADMINISTRATOR . '/components/com_jinbound');

        $controller = JControllerLegacy::getInstance('JInbound');
        $controller->execute('cron');
    }

    /**
     * STUBS needed by jInbound calls to the application
     *
     */

    public function getMenu()
    {
        return array();
    }

    public function isAdmin()
    {
        return false;
    }

    public function isSite()
    {
        return true;
    }

    public function getClientId()
    {
        return 'site';
    }
}

JApplicationCli::getInstance('JInboundCron')->execute();
