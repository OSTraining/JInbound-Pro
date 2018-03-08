<?php
/**
 * @package             JInbound
 * @subpackage          files_jinbound
 * @ant_copyright_header@
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

// Force library to be in JError legacy mode, if possible
if (class_exists('JError')) {
    JError::$legacy = true;
}

// load plugins so system plugins (e.g., Mandrill) can do their thing
// TODO

// force jInbound
define('JPATH_COMPONENT', JPATH_ROOT . '/components/com_jinbound');
define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_jinbound');

/**
 * Cron jobs for jInbound
 *
 */
class JInboundCron extends JApplicationCli
{
    /**
     * Entry point for the script
     *
     * @return  void
     */
    public function doExecute()
    {
        echo "Starting jInbound cron task ...\n";

        JFactory::$application = $this;
        JFactory::getLanguage()->load('com_jinbound', JPATH_ADMINISTRATOR);

        if (jimport('joomla.application.component.controller')) {
            $controller = JController::getInstance('JInbound');
        } else {
            jimport('legacy.controllers.legacy');
            $controller = JControllerLegacy::getInstance('JInbound');
        }

        // exec task
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
