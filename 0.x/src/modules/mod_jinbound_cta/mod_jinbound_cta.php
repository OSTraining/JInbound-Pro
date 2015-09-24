<?php
/**
 * @package		JInbound
 * @subpackage	mod_jinbound_cta
@ant_copyright_header@
 */

defined('_JEXEC') or die;

// check that jinbound is installed
$jinbound_base = JPATH_ADMINISTRATOR . '/components/com_jinbound';
if (!is_dir($jinbound_base))
{
	return false;
}

// include additional classes
require_once dirname(__FILE__) . '/adapter.php';
require_once dirname(__FILE__) . '/helper.php';

// this module requires the jinbound system plugin
if (!class_exists('plgSystemJInbound'))
{
	return false;
}

// since this module renders other modules, don't let it render itself
if (ModJInboundCTAHelper::$running)
{
	return false;
}
ModJInboundCTAHelper::$running = true;

$sfx = JFilterInput::getInstance()->clean($params->get('moduleclass_sfx', ''));

// load required classes
JLoader::register('JInbound', "$jinbound_base/libraries/jinbound.php");

// render module
require JModuleHelper::getLayoutPath('mod_jinbound_cta', $params->get('layout', 'default'));

// finished rendering
ModJInboundCTAHelper::$running = false;
