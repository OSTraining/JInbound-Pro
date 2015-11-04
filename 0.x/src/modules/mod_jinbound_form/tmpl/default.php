<?php
/**
 * @package		JInbound
 * @subpackage	mod_jinbound_form
@ant_copyright_header@
 */

defined('_JEXEC') or die;

?>
<!-- <?php echo $form->getName(); ?> -->
<div class="row-fluid">
	<div class="span12">
		<form action="<?php echo $form_url; ?>" method="post" enctype="multipart/form-data">
			<?php foreach ($form->getFieldsets() as $fieldset) : ?>
			<fieldset class="control-list">
				<?php foreach ($form->getFieldset($fieldset->name) as $field) : ?>
				<?php if ($field->hidden) : ?>
				<?php echo $field->input; ?>
				<?php else : ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $field->label; ?>
					</div>
					<div class="controls">
						<?php echo $field->input; ?>
					</div>
				</div>
				<?php endif; ?>
				<?php endforeach; ?>
			</fieldset>
			<?php endforeach; ?>
			<div class="btn-group">
				<button type="submit" class="btn btn-primary"><?php echo JText::_('JSUBMIT'); ?></button>
			</div>
		</form>
	</div>
</div>
