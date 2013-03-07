<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$saveOrder = ($listOrder == 'Lead.id');
$trashed   = (-2 == $this->state->get('filter.published'));

if (JInbound::version()->isCompatible('3.0')) JHtml::_('dropdown.init');



if (!empty($this->items)) :
	foreach($this->items as $i => $item):

		$canEdit    = $user->authorise('core.edit', JInbound::COM.'.lead.'.$item->id);
		$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
		$canEditOwn = $user->authorise('core.edit.own', JInbound::COM.'.lead.'.$item->id) && $item->created_by == $userId;
		$canChange  = $user->authorise('core.edit.state', JInbound::COM.'.lead.'.$item->id) && $canCheckin;
	?>
	<tr class="row<?php echo $i % 2; ?>">
		<td class="hidden-phone">
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td class="hidden-phone">
			<?php echo $item->id;  ?>
		</td>
		<td class="nowrap has-context">
			<div class="pull-left">
				<?php if ($item->checked_out) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->author_name, $item->checked_out_time, 'leads.', $canCheckin); ?>
				<?php endif; ?>
				<?php if ($canEdit || $canEditOwn) : ?>
					<a href="<?php echo JInboundHelperUrl::edit('lead', $item->id); ?>">
						<?php echo $this->escape($item->name); ?>
					</a>
				<?php else : ?>
					<?php echo $this->escape($item->name); ?>
				<?php endif; ?>
			</div>
			<?php if (JInbound::version()->isCompatible('3.0')) : ?>
			<div class="pull-left"><?php

				JHtml::_('dropdown.edit', $item->id, 'lead.');
				JHtml::_('dropdown.divider');
				JHtml::_('dropdown.' . ($item->published ? 'un' : '') . 'publish', 'cb' . $i, 'statuses.');
				if ($item->checked_out) :
					JHtml::_('dropdown.checkin', 'cb' . $i, 'leads.');
				endif;
				JHtml::_('dropdown.' . ($trashed ? 'un' : '') . 'trash', 'cb' . $i, 'leads.');

				echo JHtml::_('dropdown.render');

			?></div>
			<?php endif; ?>
		</td>
		<td class="hidden-phone">
			<?php echo JHtml::_('jgrid.published', $item->published, $i, 'leads.', $canChange, 'cb'); ?>
		</td>
		<td class="hidden-phone hidden-tablet">
			<?php echo $this->escape($item->date); ?>
		</td>
		<td class="hidden-phone hidden-tablet">
			<?php echo $this->escape($item->converted); ?>
		</td>
		<td class="hidden-phone hidden-tablet">
			<?php echo $this->escape($item->priority_id); ?>
		</td>
		<td class="hidden-phone hidden-tablet">
			<?php echo $this->escape($item->status_id); ?>
		</td>
		<td class="hidden-phone hidden-tablet">
			<?php echo $this->escape($item->note); ?>
		</td>
	</tr>
	<?php endforeach;
endif;
