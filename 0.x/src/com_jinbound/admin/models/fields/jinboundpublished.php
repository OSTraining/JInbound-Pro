<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');
jimport('joomla.form.helper');

class JFormFieldJinboundPublished extends JFormField
{
	public $type = 'Jinboundpublished';

	protected function getInput() {
		// get class for this element
		$class = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		// list of published types
		$list = array();
		$list[] = JHtml::_('select.option', 0, JText::_('COM_JINBOUND_UNPUBLISHED'), '_id', '_name');
		$list[] = JHtml::_('select.option', 1, JText::_('COM_JINBOUND_PUBLISHED'), '_id', '_name');
    return JHtml::_('select.genericlist', $list, $this->name, $class . ' size="1"', '_id', '_name', $this->value);
	}
}
