<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$saveOrder = ($listOrder == 'Campaign.id');
?>
<tr>
	<th width="1%" class="nowrap hidden-phone">
		<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
	</th>
	<th width="1%" class="nowrap hidden-phone">
		<?php echo JText::_('COM_JINBOUND_ID'); ?>
	</th>
	<th>
		<?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_CAMPAIGN_NAME', 'Campaign.name', $listDirn, $listOrder); ?>
	</th>
	<th width="5%">
		<?php echo JHtml::_($this->sortFunction, 'JPUBLISHED', 'Campaign.published', $listDirn, $listOrder); ?>
	</th>
	<th width="5%">
		<?php echo JHtml::_($this->sortFunction, 'JGLOBAL_CREATED', 'Campaign.created', $listDirn, $listOrder); ?>
	</th>
</tr>