<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JHtml::_('behavior.calendar');

?>
<script type="text/javascript">
Joomla.submitbutton = function(task)
{
	console.log('submitbutton');
	console.log(task);
	Joomla.submitform(task, document.getElementById('adminForm'));
};
</script>
<div class="container-fluid" id="jinbound_component">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
	
	<?php echo JHtml::_('jinbound.startTabSet', 'jinbound_default_tabs', array('active' => 'content_tab')); ?>
	<?php echo JHtml::_('jinbound.addTab', 'jinbound_default_tabs', 'content_tab', JText::_('COM_JINBOUND_REPORTS', true)); ?>
		
	<h2><?php echo JText::_('COM_JINBOUND_REPORTS'); ?></h2>
	<form action="<?php echo JInboundHelperUrl::view('reports'); ?>" method="post" id="adminForm" name="adminForm" class="form-validate" enctype="multipart/form-data">
	<div class="row-fluid">
		<div class="span12 text-center">
			<div class="reports_search">
				<?php echo $this->campaign_filter; ?>
				<?php echo $this->page_filter; ?>
				<?php echo JHtml::_('calendar', $this->state->get('filter_start'), 'filter_start', 'filter_start', '%Y-%m-%d', array(
					'size'        => 10
				,	'placeholder' => JText::_('COM_JINBOUND_FROM')
				,	'onchange'    => $this->filter_change_code
				)); ?>
				<?php echo JHtml::_('calendar', $this->state->get('filter_end'), 'filter_end', 'filter_end', '%Y-%m-%d', array(
					'size'        => 10
				,	'placeholder' => JText::_('COM_JINBOUND_TO')
				,	'onchange'    => $this->filter_change_code
				)); ?>
			</div>
		</div>
	</div>
	<?php echo $this->loadTemplate('dashboard'); ?>
	<div>
		<input name="task" value="" type="hidden" />
	</div>
	</form>
	
	<?php echo JHtml::_('jinbound.endTab'); ?>
	<?php if ($this->permissions && JFactory::getUser()->authorise('core.admin', JInbound::COM)) : ?>
		<?php echo JHtml::_('jinbound.addTab', 'jinbound_default_tabs', 'permissions_tab', JText::_('JCONFIG_PERMISSIONS_LABEL', true)); ?>
	<div class="row-fluid">
		<form action="<?php echo JRoute::_('index.php?option=com_jinbound&task=reports.permissions'); ?>" method="post">
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
</div>
<?php echo $this->loadTemplate('footer'); ?>
<?php echo $this->loadTemplate('script'); ?>
