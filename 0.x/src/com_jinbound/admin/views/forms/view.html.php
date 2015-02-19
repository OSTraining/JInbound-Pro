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

class JInboundViewForms extends JInboundListView
{
	/**
	 * Default sorting column
	 * 
	 * @var string
	 */
	protected $_sortColumn = 'Form.title';
	
	/**
	 * Returns an array of fields the table can be sorted by
	 * 
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields() {
		return array(
			'Form.title'      => JText::_('COM_JINBOUND_TITLE')
		,	'Form.type'       => JText::_('COM_JINBOUND_FORM_TYPE_LABEL')
		,	'FormFieldCount'  => JText::_('COM_JINBOUND_FIELD_COUNT')
		,	'Form.created_by' => JText::_('COM_JINBOUND_CREATED_BY')
		,	'Form.published'  => JText::_('COM_JINBOUND_PUBLISHED')
		,	'Form.default'    => JText::_('COM_JINBOUND_DEFAULT')
		);
	}
}
