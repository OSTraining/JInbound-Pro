<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('_JEXEC') or die;

?>
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
