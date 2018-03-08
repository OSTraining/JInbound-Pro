<?php
/**
 * @package   LiveUpdate
 * @copyright Copyright ©2011-2013 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license   GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Configuration class for your extension's updates. Override to your liking.
 */
class LiveUpdateConfig extends LiveUpdateAbstractConfig
{
    var $_extensionName         = 'mod_jinbound_cta';
    var $_extensionTitle        = 'jInbound CTA';
    var $_updateURL             = 'http://jinbound.com/index.php?option=com_ars&view=update&format=ini&id=11';
    var $_requiresAuthorization = true;
    var $_versionStrategy       = 'vcompare';
    var $_storageAdapter        = 'file';
    var $_storageConfig         = array(
        'extensionName' => 'mod_jinbound_cta',
        'key'           => 'liveupdate'
    );
    var $_xmlFilename           = 'mod_jinbound_cta.xml';

    public function __construct()
    {
        parent::__construct();
        $db     = JFactory::getDbo();
        $json   = $db->setQuery($db->getQuery(true)
            ->select('params')
            ->from('#__extensions')
            ->where($db->quoteName('type') . ' = ' . $db->quote('module'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('mod_jinbound_cta'))
        )->loadResult();
        $params = json_decode($json);
        if (is_object($params) && property_exists($params, 'downloadid')) {
            $this->_downloadID = $params->downloadid;
        }
    }
}
