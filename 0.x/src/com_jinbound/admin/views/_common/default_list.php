<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JHtml::_('behavior.tooltip');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
?>

<div id="jinbound_component" class="<?php echo $this->viewClass; ?>">
	<form action="<?php echo JInboundHelperUrl::view($this->viewName); ?>" method="post" name="adminForm" id="adminForm">
<?php echo $this->loadTemplate('list_top'); ?>
		<div class="row-fluid">
			<?php
				echo $this->loadTemplate('filters');
				if (empty($this->items)) :
					echo $this->loadTemplate('empty');
				else :
				?>
			<table class="adminlist table table-striped">
				<thead><?php echo $this->loadTemplate('head');?></thead>
				<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
				<tbody><?php echo $this->loadTemplate('body');?></tbody>
			</table>
			<?php endif; ?>
			<div>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
	</form>
</div>
<?php echo $this->loadTemplate('footer'); ?>
<?php if (JInbound::config("debug", 0)) : ?>
<h3>State</h3>
<pre><?php echo $this->escape(print_r($this->state, 1)); ?></pre>
<h3>Items</h3>
<pre><?php echo $this->escape(print_r($this->items, 1)); ?></pre>
<?php endif; ?>