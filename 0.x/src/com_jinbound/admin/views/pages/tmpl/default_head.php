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
	<th width="1%" class="nowrap hidden-phone">
		<?php echo JText::_('COM_JINBOUND_ID'); ?>
	</th>
	<th style="min-width:24px;">
		&nbsp;
	</th>
	<th>
		<?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_LANDINGPAGE_NAME', 'Page.name', $listDirn, $listOrder); ?>
	</th>
	<th class="hidden-phone">
		<?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_PUBLISHED', 'Page.published', $listDirn, $listOrder); ?>
	</th>
	<th class="hidden-phone">
		<?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_CAMPAIGN', 'campaign_name', $listDirn, $listOrder); ?>
	</th>
	<th class="hidden-phone">
		<?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_CATEGORY', 'Page.category', $listDirn, $listOrder); ?>
	</th>
	<th class="hidden-phone">
		<?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_LAYOUT', 'Page.layout', $listDirn, $listOrder); ?>
	</th>
	<th class="hidden-phone">
		<?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_VIEWS', 'Page.hits', $listDirn, $listOrder); ?>
	</th>
	<th class="hidden-phone">
		<?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_SUBMISSIONS', 'submissions', $listDirn, $listOrder); ?>
	</th>
	<th class="hidden-phone">
		<?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_LEADS', 'contact_submissions', $listDirn, $listOrder); ?>
	</th>
	<th class="hidden-phone">
		<?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_CONVERSIONS', 'conversions', $listDirn, $listOrder); ?>
	</th>
</tr>