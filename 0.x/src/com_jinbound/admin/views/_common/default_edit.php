<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div id="jinbound_component" class="row-fluid <?php echo $this->viewClass; ?>">
	<form action="<?php echo JInboundHelperUrl::_(); ?>" method="post" id="adminForm" name="adminForm" class="form-validate" enctype="multipart/form-data">
		<fieldset>
			<?php echo $this->loadTemplate('edit_default'); ?>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="id" value="<?php echo (int) @$this->item->id; ?>" />
			<input type="hidden" name="function" value="<?php echo JRequest::getCmd('function'); ?>" />
			<?php if ('component' == JRequest::getCmd('tmpl')) : ?>
			<input type="hidden" name="tmpl" value="component" />
			<input type="hidden" name="layout" value="modal" />
			<?php endif; ?>
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
		<?php echo $this->loadTemplate('edit_tabs'); ?>
	</form>
</div>
<?php echo $this->loadTemplate('footer'); ?>
<?php if (JInbound::config("debug", 0)) : ?>
<div class="row-fluid">
	<h3>Item:</h3>
	<pre><?php htmlspecialchars(print_r($this->item)); ?></pre>
</div>
<?php endif; ?>
