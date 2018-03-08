<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<fieldset class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<?php if (empty($this->item->tracks)) : ?>
				<div class="alert alert-warning"><?php echo JText::_('COM_JINBOUND_NO_TRACKS_FOUND'); ?></div>
			<?php else: ?>
				<table class="table table-striped">
					<thead>
						<tr>
							<th><?php echo JText::_('COM_JINBOUND_URL'); ?></th>
							<th><?php echo JText::_('COM_JINBOUND_VISIT_DATE'); ?></th>
							<th><?php echo JText::_('COM_JINBOUND_USER'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($this->item->tracks as $i => $track) : if (20 < $i) break; ?>
						<tr>
							<td><?php echo $this->escape($track->url); ?></td>
							<td><?php echo JInbound::userDate($track->created); ?></td>
							<td><i class="hasTip hasTooltip icon-<?php echo ($track->current_user_id ? 'user' : 'warning'); ?>" title="<?php echo JText::_('COM_JINBOUND_' . ($track->current_user_id ? 'USER' : 'AUTHOR_GUEST')); ?>"> </i></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>
</fieldset>