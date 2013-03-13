<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

class pkg_JInboundInstallerScript
{
	public function postflight($type, $parent) {
		if ('uninstall' == $type) {
			return;
		}
		$db  = JFactory::getDbo();
		// enable the plugins
		$db->setQuery('UPDATE `#__extensions` SET `enabled`=1 WHERE `element`="jinbound" AND `folder` IN ("system", "content", "user") AND `type`="plugin"');
		$db->query();
	}
}