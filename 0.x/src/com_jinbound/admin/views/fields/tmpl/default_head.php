<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder = ($listOrder == 'Field.id');
?>
<tr>
	<th width="1%" class="hidden-phone">
		<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
	</th>
	<th width="1%" class="hidden-phone">
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_PUBLISHED', 'Field.published', $listDirn, $listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_TITLE', 'Field.title', $listDirn, $listOrder); ?>
	</th>
	<th width="10%" class="nowrap">
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_FIELD_TYPE_LABEL', 'Field.type', $listDirn, $listOrder); ?>
	</th>
	<th width="1%" class="nowrap hidden-phone hidden-tablet">
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_ID', 'Field.id', $listDirn, $listOrder); ?>
	</th>
</tr>