<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');

?>
<tr>
	<th width="1%" class="nowrap hidden-phone">
		<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_NAME', 'User.name', $listDirn, $listOrder); ?>
	</th>
	<th width="5%">
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_PUBLISHED', 'Lead.published', $listDirn, $listOrder); ?>
	</th>
	<th width="10%">
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_LEAD_DATE', 'Lead.date', $listDirn, $listOrder); ?>
	</th>
	<th width="15%">
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_LEAD_CONVERTED', 'Page.formname', $listDirn, $listOrder); ?>
	</th>
	<th width="10%">
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_LEAD_PRIORITY', 'Priority.name', $listDirn, $listOrder); ?>
	</th>
	<th width="10%">
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_LEAD_STATUS', 'Lead.status_id', $listDirn, $listOrder); ?>
	</th>
	<th width="10%">
		<?php echo JText::_('COM_JINBOUND_LEAD_NOTE'); ?>
	</th>
	<th width="1%" class="nowrap hidden-phone">
		<?php echo JText::_('COM_JINBOUND_ID'); ?>
	</th>
</tr>