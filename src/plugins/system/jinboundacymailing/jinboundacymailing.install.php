<?php
/**
 * @package             JInbound
 * @subpackage          plg_system_jinboundacymailing
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

class plgSystemJinboundacymailingInstallerScript
{
    public function postflight($type, $parent)
    {
        $app = JFactory::getApplication();
        $db  = JFactory::getDbo();

        // find this plugin in the database, if possible...
        $db->setQuery($db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where($db->quoteName('element') . ' = ' . $db->quote('jinboundacymailing'))
            ->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
        );

        try {
            $eid = $db->loadResult();
            if (!$eid) {
                throw new Exception('Could not enable plugin! ' . __METHOD__);
            }
        } catch (Exception $e) {
            if (defined('JDEBUG') && JDEBUG) {
                $app->enqueueMessage(htmlspecialchars($e->getMessage()));
            }
            return;
        }

        // force-enable this plugin
        $db->setQuery($db->getQuery(true)
            ->update('#__extensions')
            ->set($db->quoteName('enabled') . ' = 1')
            ->where($db->quoteName('extension_id') . ' = ' . (int)$eid)
        );

        try {
            $db->query();
        } catch (Exception $e) {
            if (defined('JDEBUG') && JDEBUG) {
                $app->enqueueMessage(htmlspecialchars($e->getMessage()));
            }
            return;
        }

    }
}
