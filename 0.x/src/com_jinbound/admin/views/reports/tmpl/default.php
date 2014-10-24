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
	
	</div>
</div>
<?php echo $this->loadTemplate('footer'); ?>
<?php echo $this->loadTemplate('script'); ?>
