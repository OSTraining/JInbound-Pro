<?php
/**
 * @version		$Id$
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
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>
	<th width="1%" class="nowrap hidden-phone">
		<?php echo JText::_('COM_JINBOUND_ID'); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_LANDINGPAGE_NAME', 'Page.name', $listDirn, $listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_PUBLISHED', 'Page.status', $listDirn, $listOrder); ?>
	</th>
			<th>
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_CATEGORY', 'Page.category', $listDirn, $listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_VIEWS', 'Page.views', $listDirn, $listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_SUBMISSIONS', 'Page.submissions', $listDirn, $listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_CONVERSIONS', 'Page.conversions', $listDirn, $listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'COM_JINBOUND_REPORT', 'Page.report', $listDirn, $listOrder); ?>
	</th>
</tr>