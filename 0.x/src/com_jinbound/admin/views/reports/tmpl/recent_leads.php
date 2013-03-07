<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<!-- Row 5: Recent Leads -->
<div class="row-fluid">
	<h4><?php echo JText::_('COM_JINBOUND_RECENT_LEADS'); ?></h4>
	<table class="table table-striped">
		<thead>
			<tr>
				<th><?php echo JText::_('COM_JINBOUND_NAME'); ?></th>
				<th><?php echo JText::_('COM_JINBOUND_DATE'); ?></th>
				<th><?php echo JText::_('COM_JINBOUND_FORM_CONVERTED_ON'); ?></th>
				<th><?php echo JText::_('COM_JINBOUND_WEBSITE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Name</td>
				<td>Date</td>
				<td>Form Converted On</td>
				<td>Website</td>
			</tr>
		</tbody>
	</table>
</div>