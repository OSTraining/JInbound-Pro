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
	var $_extensionName			= 'plg_system_jinboundanalytics';
	var $_extensionTitle		= 'System - JInbound Analytics';
	var $_updateURL				= 'http://jinbound.com/index.php?option=com_ars&view=update&format=ini&id=5';
	var $_requiresAuthorization	= true;
	var $_versionStrategy		= 'vcompare';
	var $_storageAdapter		= 'file';
	var $_storageConfig			= array(
		'extensionName'	=> 'plg_system_jinboundanalytics',
		'key'			=> 'liveupdate'
	);
	var $_xmlFilename       = 'jinboundanalytics.xml';
	
	public function __construct()
	{
		parent::__construct();
		$db = JFactory::getDbo();
		$json = $db->setQuery($db->getQuery(true)
			->select('params')
			->from('#__extensions')
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
			->where($db->quoteName('element') . ' = ' . $db->quote('jinboundanalytics'))
		)->loadResult();
		$params = json_decode($json);
		if (is_object($params) && property_exists($params, 'downloadid'))
		{
			$this->_downloadID = $params->downloadid;
		}
	}
}
