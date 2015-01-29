<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldJinboundPublished extends JFormFieldList
{
	public $type = 'Jinboundpublished';

	protected function getOptions() {
		// list of published types
		$list = array();
		$list[] = JHtml::_('select.option', 0, JText::_('COM_JINBOUND_UNPUBLISHED'));
		$list[] = JHtml::_('select.option', 1, JText::_('COM_JINBOUND_PUBLISHED'));
		return array_merge(parent::getOptions(), $list);
	}
}
