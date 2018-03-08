<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');

class JInboundViewStatuses extends JInboundListView
{
    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     */
    protected function getSortFields()
    {
        return array(
            'Status.name'        => JText::_('COM_JINBOUND_NAME')
        ,
            'Status.published'   => JText::_('COM_JINBOUND_PUBLISHED')
        ,
            'Status.default'     => JText::_('COM_JINBOUND_DEFAULT')
        ,
            'Status.active'      => JText::_('COM_JINBOUND_ACTIVE')
        ,
            'Status.final'       => JText::_('COM_JINBOUND_FINAL')
        ,
            'Status.ordering'    => JText::_('JGRID_HEADING_ORDERING')
        ,
            'Status.description' => JText::_('COM_JINBOUND_DESCRIPTION')
        );
    }
}
