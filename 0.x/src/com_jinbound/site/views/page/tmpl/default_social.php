<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

// render a module position here
$modules = JModuleHelper::getModules('jinbound_social');

if (!empty($modules)) {
	foreach ($modules as $module) {
		$mparams = new JRegistry;
		$mparams->loadString($module->params);
		echo JModuleHelper::renderModule($module, $mparams->toArray());
	}
}

