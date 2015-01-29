<?php
/**
 * @package LiveUpdate
 * @copyright Copyright ©2011-2013 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Configuration class for your extension's updates. Override to your liking.
 */
class LiveUpdateConfig extends LiveUpdateAbstractConfig
{
	var $_extensionName			= 'plg_system_jinboundacymailing';
	var $_extensionTitle		= 'System - JInbound AcyMailing';
	var $_updateURL				= 'http://jinbound.com/index.php?option=com_ars&view=update&format=ini&id=7';
	var $_requiresAuthorization	= true;
	var $_versionStrategy		= 'vcompare';
	var $_storageAdapter		= 'file';
	var $_storageConfig			= array(
		'extensionName'	=> 'plg_system_jinboundacymailing',
		'key'			=> 'liveupdate'
	);
	var $_xmlFilename       = 'jinboundacymailing.xml';
	
	public function __construct()
	{
		parent::__construct();
		$db = JFactory::getDbo();
		$json = $db->setQuery($db->getQuery(true)
			->select('params')
			->from('#__extensions')
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
			->where($db->quoteName('element') . ' = ' . $db->quote('jinboundacymailing'))
		)->loadResult();
		$params = json_decode($json);
		if (is_object($params) && property_exists($params, 'downloadid'))
		{
			$this->_downloadID = $params->downloadid;
		}
	}
}
