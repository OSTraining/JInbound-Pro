<?php
/**
 * @package             JInbound
 * @subpackage          plg_system_jinboundsalesforce
 * @ant_copyright_header@
 */

defined('_JEXEC') or die;

class JFormFieldJinboundsalesforceupdate extends JFormField
{
    protected function getInput()
    {
        // rewritten LiveUpdate code
        require_once JPATH_ROOT . '/plugins/system/jinboundsalesforce/liveupdate/liveupdate.php';
        $updateInfo = LiveUpdate::getUpdateInformation();
        if (!$updateInfo->supported) {
            return JText::_('PLG_SYSTEM_JINBOUNDSALESFORCE_UPDATE_UNSUPPORTED');
        } else {
            if ($updateInfo->stuck) {
                return JText::_('PLG_SYSTEM_JINBOUNDSALESFORCE_UPDATE_STUCK');
            } else {
                if ($updateInfo->hasUpdates) {
                    return JText::sprintf('PLG_SYSTEM_JINBOUNDSALESFORCE_UPDATE_HASUPDATES', $updateInfo->version);
                }
            }
        }
        return JText::_('PLG_SYSTEM_JINBOUNDSALESFORCE_UPDATE_OK');
    }
}
