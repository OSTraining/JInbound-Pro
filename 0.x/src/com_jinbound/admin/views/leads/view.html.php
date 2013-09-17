<?php
/**
 * @package		jInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');

class JInboundViewLeads extends JInboundListView
{
	/**
	 * Returns an array of fields the table can be sorted by
	 * 
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields() {
		return array(
			'User.name'        => JText::_('COM_JINBOUND_NAME')
		,	'Lead.published'   => JText::_('COM_JINBOUND_PUBLISHED')
		,	'Lead.created'     => JText::_('COM_JINBOUND_LEAD_DATE')
		,	'Page.formname'    => JText::_('COM_JINBOUND_LEAD_CONVERTED')
		,	'Priority.name'    => JText::_('COM_JINBOUND_LEAD_PRIORITY')
		,	'Status.name'      => JText::_('COM_JINBOUND_LEAD_STATUS')
		,	'Lead.note'        => JText::_('COM_JINBOUND_LEAD_NOTE')
		);
	}
}
