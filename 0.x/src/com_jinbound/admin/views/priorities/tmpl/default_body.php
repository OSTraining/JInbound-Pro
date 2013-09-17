<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$saveOrder = ($listOrder == 'Priority.ordering');
$trashed   = (-2 == $this->state->get('filter.published'));

if (JInbound::version()->isCompatible('3.0')) JHtml::_('dropdown.init');



if (!empty($this->items)) :
	foreach ($this->items as $i => $item) :
		$this->_itemNum = $i;

		$canEdit    = $user->authorise('core.edit', JInbound::COM.'.priority.'.$item->id);
		$canEditOwn = $user->authorise('core.edit.own', JInbound::COM.'.priority.'.$item->id) && $item->created_by == $userId;
		$canChange  = $user->authorise('core.edit.state', JInbound::COM.'.priority.'.$item->id);
	?>
	<tr class="row<?php echo $i % 2; ?>">
		<td class="hidden-phone">
			<?php echo $item->id;  ?>
		</td>
		<td class="hidden-phone">
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td class="nowrap has-context">
			<div class="pull-left">
				<?php if ($item->checked_out) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->author_name, $item->checked_out_time, 'priorities.', $canCheckin); ?>
				<?php endif; ?>
				<?php if ($canEdit || $canEditOwn) : ?>
					<a href="<?php echo JInboundHelperUrl::edit('priority', $item->id); ?>">
						<?php echo $this->escape($item->name); ?>
					</a>
				<?php else : ?>
					<?php echo $this->escape($item->name); ?>
				<?php endif; ?>
			</div>
			<?php $this->currentItem = $item; echo $this->loadTemplate('list_dropdown'); ?>
		</td>
		<td class="hidden-phone">
			<?php echo JHtml::_('jgrid.published', $item->published, $i, 'priorities.', $canChange, 'cb'); ?>
		</td>
		<td class="order">
			<?php if ($canChange) : ?>
				<?php if ($saveOrder) : ?>
					<span><?php echo $this->pagination->orderUpIcon($i, 0 == $i, 'priorities.orderup', 'JLIB_HTML_MOVE_UP', $item->ordering); ?></span>
					<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, false, 'priorities.orderdown', 'JLIB_HTML_MOVE_DOWN', $item->ordering); ?></span>
				<?php endif; ?>
				<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
				<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
			<?php else : ?>
				<?php echo $item->ordering; ?>
			<?php endif; ?>
		</td>
		<td class="hidden-phone">
			<?php echo $item->description; ?>
		</td>
	</tr>
	<?php endforeach;
endif;
