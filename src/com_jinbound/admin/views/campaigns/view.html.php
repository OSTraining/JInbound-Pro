<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');

class JInboundViewCampaigns extends JInboundListView
{
    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     */
    protected function getSortFields()
    {
        return array(
            'Campaign.name'      => JText::_('COM_JINBOUND_CAMPAIGN_NAME')
        ,
            'Campaign.published' => JText::_('JPUBLISHED')
        ,
            'Campaign.created'   => JText::_('JGLOBAL_CREATED')
        );
    }
}
