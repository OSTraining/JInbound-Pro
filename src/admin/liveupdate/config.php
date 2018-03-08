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
    var $_extensionName         = 'com_jinbound';
    var $_extensionTitle        = 'JInbound';
    var $_updateURL             = 'http://jinbound.com/index.php?option=com_ars&view=update&format=ini&id=2';
    var $_requiresAuthorization = true;
    var $_versionStrategy       = 'vcompare';
    var $_storageAdapter        = 'component';
    var $_storageConfig         = array(
        'extensionName' => 'com_jinbound',
        'key'           => 'liveupdate'
    );

}
