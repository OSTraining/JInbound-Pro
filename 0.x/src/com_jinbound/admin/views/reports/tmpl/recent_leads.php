<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$leads = $this->getRecentLeads();

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
<?php if (!empty($leads)) foreach ($leads as $lead) : ?>
			<tr>
				<td><?php echo $this->escape($lead->name); ?></td>
				<td><?php echo $this->escape($lead->date); ?></td>
				<td>TODO</td>
				<td><a href="<?php echo $this->escape($lead->website); ?>" target="_blank"><?php echo $this->escape($lead->website); ?></a></td>
			</tr>
<?php endforeach; ?>
		</tbody>
	</table>
</div>