<?php
/**
 * @package		jInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');
JInbound::registerHelper('form');

class JInboundViewFields extends JInboundListView
{
	/**
	 * Default sorting column
	 * 
	 * @var string
	 */
	protected $_sortColumn = 'Field.title';
	
	/**
	 * Returns an array of fields the table can be sorted by
	 * 
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields() {
		return array(
			'Field.title'      => JText::_('COM_JINBOUND_TITLE')
		,	'Field.type'       => JText::_('COM_JINBOUND_FIELD_TYPE_LABEL')
		,	'Field.formtype'   => JText::_('COM_JINBOUND_FIELD_FORMTYPE_LABEL')
		,	'Field.created_by' => JText::_('COM_JINBOUND_CREATED_BY')
		,	'Field.published'  => JText::_('COM_JINBOUND_PUBLISHED')
		);
	}
}
