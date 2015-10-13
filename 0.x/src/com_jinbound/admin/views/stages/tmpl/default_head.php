<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$listOrder	= ''; //$this->state->get('list.ordering');
$listDirn	= ''; //$this->state->get('list.direction');
$saveOrder = ''; //($listOrder == 'Page.id');
?>
<tr>
	<th width="1%" class="nowrap hidden-phone">
		<?php echo JText::_('COM_JINBOUND_ID'); ?>
	</th>
	<th width="1%" class="nowrap hidden-phone">
		<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
	</th>
	<th>
		<?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_CATEGORY_NAME', 'Status.name', $listDirn, $listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_PUBLISHED', 'Status.status', $listDirn, $listOrder); ?>
	</th>
			<th>
		<?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_CATEGORY_DESCRIPTION', 'Status.description', $listDirn, $listOrder); ?>
	</th>
</tr>