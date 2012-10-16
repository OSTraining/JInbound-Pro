<?php
/**
 * Prepares a minimalist framework for unit testing.
 *
 * Joomla is assumed to include the /unittest/ directory.
 * eg, /path/to/joomla/unittest/
 *
 * @package     Joomla.UnitTest
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

define('_JEXEC', 1);

// Fix magic quotes.
@ini_set('magic_quotes_runtime', 0);

// Maximise error reporting.
@ini_set('zend.ze1_compatibility_mode', '0');
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
 * Ensure that required path constants are defined.  These can be overriden within the phpunit.xml file
 * if you chose to create a custom version of that file.
 */
if (!defined('JPATH_TESTS'))
{
	define('JPATH_TESTS', realpath(__DIR__));
}
if (!defined('JPATH_PLATFORM'))
{
	define('JPATH_PLATFORM', realpath(JPATH_TESTS . '/platform/libraries'));
}
if (!defined('JPATH_BASE'))
{
	define('JPATH_BASE', realpath(JPATH_TESTS . '/platform'));
}
if (!defined('JPATH_ROOT'))
{
	define('JPATH_ROOT', realpath(JPATH_BASE));
}
if (!defined('JPATH_CACHE'))
{
	define('JPATH_CACHE', JPATH_BASE . '/cache');
}
if (!defined('JPATH_CONFIGURATION'))
{
	define('JPATH_CONFIGURATION', JPATH_BASE);
}
if (!defined('JPATH_MANIFESTS'))
{
	define('JPATH_MANIFESTS', JPATH_BASE . '/manifests');
}
if (!defined('JPATH_PLUGINS'))
{
	define('JPATH_PLUGINS', JPATH_BASE . '/plugins');
}
if (!defined('JPATH_THEMES'))
{
	define('JPATH_THEMES', JPATH_BASE . '/themes');
}
if (!defined('JPATH_JINBOUNDSRC'))
{
	define('JPATH_JINBOUNDSRC', realpath(dirname(dirname(__FILE__)) . '/src'));
}
if (!defined('JPATH_ADMINISTRATOR'))
{
	define('JPATH_ADMINISTRATOR', JPATH_BASE . '/administrator');
}
if (!defined('JPATH_SITE'))
{
	define('JPATH_SITE', JPATH_BASE);
}
@define('JPATH_INSTALLATION', JPATH_ROOT . '/installation');

// Load a configuration file for the tests.
if (file_exists(JPATH_TESTS . '/config.php'))
{
	include_once JPATH_TESTS . '/config.php';
}
else
{
	require_once JPATH_TESTS . '/config.dist.php';
}

// Import the platform.
require_once JPATH_PLATFORM . '/import.php';

// build the environment
require JPATH_TESTS . '/includes/env/teardown.php';
require JPATH_TESTS . '/includes/env/buildup.php';

// Include the base test cases.
require_once JPATH_TESTS . '/includes/JoomlaTestCase.php';
require_once JPATH_TESTS . '/includes/JoomlaDatabaseTestCase.php';
require_once JPATH_TESTS . '/includes/JInboundTestCase.php';
