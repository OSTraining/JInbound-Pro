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
$saveOrder = ($listOrder == 'Email.id');
$trashed   = (-2 == $this->state->get('filter.published'));
$colors    = array('success', 'warning', 'info');

if (JInbound::version()->isCompatible('3.0')) JHtml::_('dropdown.init');



if (!empty($this->items)) :
	$lastCampaign = false;
	$lastColor    = current($colors);
	foreach ($this->items as $i => $item) :
		$this->_itemNum = $i;

		$canEdit    = $user->authorise('core.edit', JInbound::COM.'.email.'.$item->id);
		$canEditOwn = $user->authorise('core.edit.own', JInbound::COM.'.email.'.$item->id) && $item->created_by == $userId;
		$canChange  = $user->authorise('core.edit.state', JInbound::COM.'.email.'.$item->id);
		
		if ($lastCampaign !== $item->campaign_name) {
			$name = $this->escape($item->campaign_name);
			if (false === next($colors)) {
				reset($colors);
			}
		}
		else {
			$name = '&nbsp;';
		}
		$lastCampaign = $item->campaign_name;
		$lastColor    = current($colors);
		
		
		
	?>
	<tr class="row<?php echo $i % 2; ?> <?php echo $lastColor; ?>">
		<td class="hidden-phone">
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td class="">
			<?php echo $name; ?>
		</td>
		<td class="nowrap has-context">
			<div class="pull-left">
				<?php if ($item->checked_out) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->author_name, $item->checked_out_time, 'emails.', $canCheckin); ?>
				<?php endif; ?>
				<?php if ($canEdit || $canEditOwn) : ?>
					<a href="<?php echo JInboundHelperUrl::edit('email', $item->id); ?>">
						<?php echo $this->escape($item->name); ?>
					</a>
				<?php else : ?>
					<?php echo $this->escape($item->name); ?>
				<?php endif; ?>
			</div>
			<?php $this->currentItem = $item; echo $this->loadTemplate('list_dropdown'); ?>
		</td>
		<td class="hidden-phone">
			<?php echo JHtml::_('jgrid.published', $item->published, $i, 'emails.', $canChange, 'cb'); ?>
		</td>
		<td class="hidden-phone">
			<?php echo JText::sprintf('COM_JINBOUND_EMAIL_SCHEDULE', (int) $item->sendafter); ?>
		</td>
		<td class="hidden-phone">
			<?php echo $item->id;  ?>
		</td>
	</tr>
	<?php endforeach;
endif;
