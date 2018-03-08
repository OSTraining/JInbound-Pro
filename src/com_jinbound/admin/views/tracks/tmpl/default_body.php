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

if (!empty($this->items)) :
	foreach ($this->items as $i => $item) :
		$this->_itemNum = $i;
	?>
	<tr class="row<?php echo $i % 2; ?>">
		<td class="nowrap">
			<?php echo $this->escape($item->cookie); ?>
		</td>
		<td class="hidden-phone hidden-tablet">
			<?php echo $this->escape($item->detected_user_id); ?>
		</td>
		<td class="hidden-phone hidden-tablet">
			<?php echo $this->escape($item->current_user_id); ?>
		</td>
		<td class="hidden-phone hidden-tablet">
			<?php echo $this->escape($item->user_agent); ?>
		</td>
		<td class="hidden-phone hidden-tablet">
			<?php echo $this->escape($item->created); ?>
		</td>
		<td class="nowrap">
			<?php echo $this->escape($item->ip); ?>
		</td>
		<td class="hidden-phone">
			<?php echo $this->escape($item->session_id); ?>
		</td>
		<td class="hidden-phone">
			<?php echo $this->escape($item->type); ?>
		</td>
		<td class="nowrap hidden-phone">
			<?php echo $this->escape($item->url); ?>
		</td>
		<td class="hidden-phone">
			<?php echo $this->escape($item->id);  ?>
		</td>
	</tr>
	<?php endforeach;
endif;
