<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder = ($listOrder == 'Priority.ordering');
?>
<tr>
	<th width="1%" class="nowrap hidden-phone">
		<?php echo JText::_('COM_JINBOUND_ID'); ?>
	</th>
	<th width="1%" class="nowrap hidden-phone">
		<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_NAME', 'Priority.name', $listDirn, $listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_PUBLISHED', 'Priority.published', $listDirn, $listOrder); ?>
	</th>
	<th width="10%">
		<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'Priority.ordering', $listDirn, $listOrder); ?>
		<?php if ($saveOrder) :?>
			<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'priorities.saveorder'); ?>
		<?php endif; ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_DESCRIPTION', 'Priority.description', $listDirn, $listOrder); ?>
	</th>
</tr>