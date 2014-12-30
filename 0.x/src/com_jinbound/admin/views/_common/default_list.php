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
	<?php echo JHtml::_('jinbound.startTabSet', 'jinbound_default_tabs', array('active' => 'content_tab')); ?>
	<?php echo JHtml::_('jinbound.addTab', 'jinbound_default_tabs', 'content_tab', JText::_('JTOOLBAR_EDIT', true)); ?>
	<form action="<?php echo JInboundHelperUrl::view($this->viewName); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
<?php else : ?>
		<div id="j-main-container">
<?php endif;?>
	
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
	</div>
	
	</form>
	<?php echo JHtml::_('jinbound.endTab'); ?>
	<?php if ($this->permissions && JFactory::getUser()->authorise('core.admin', JInbound::COM)) : ?>
		<?php echo JHtml::_('jinbound.addTab', 'jinbound_default_tabs', 'permissions_tab', JText::_('JCONFIG_PERMISSIONS_LABEL', true)); ?>
	<div class="row-fluid">
		<form action="<?php echo JRoute::_('index.php?option=com_jinbound&task=' . $this->viewName . '.permissions'); ?>" method="post">
			<?php foreach ($this->permissions->getFieldsets() as $fieldset) : ?>
			<?php $fields = $this->permissions->getFieldset($fieldset->name); ?>
			<fieldset>
				<legend><?php echo JText::_($fieldset->label); ?></legend>
				<?php foreach ($fields as $field) : ?>
					<?php echo $field->input; ?>
				<?php endforeach; ?>
			</fieldset>
			<?php endforeach; ?>
			<?php echo JHtml::_('form.token'); ?>
			<button type="submit" class="btn btn-primary"><i class="icon-save"></i> <?php echo JText::_('JTOOLBAR_APPLY'); ?> </button>
		</form>
	</div>
		<?php echo JHtml::_('jinbound.endTab'); ?>
	<?php endif; ?>
	<?php echo JHtml::_('jinbound.endTabSet'); ?>
</div>
<?php echo $this->loadTemplate('footer'); ?>
<?php if (JInbound::config("debug", 0)) : ?>
<h3>State</h3>
<pre><?php echo $this->escape(print_r($this->state, 1)); ?></pre>
<h3>Items</h3>
<pre><?php echo $this->escape(print_r($this->items, 1)); ?></pre>
<?php endif; ?>