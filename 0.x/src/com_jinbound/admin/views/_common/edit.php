<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
$fieldsets = $this->form->getFieldsets();
$mainfieldset = $this->form->getFieldset('main');
?>
<div class="row-fluid">
	<form action="<?php echo JInbound::_(array('layout'=>'edit', 'id'=>(int) $this->item->id)); ?>" method="post" name="adminForm" id="adminForm" class="<?php echo $this->viewName; ?>-edit-form form-validate">
		<div class="span12">
			<ul class="adminformlist">
			<?php
				foreach ($mainfieldset as $field) :
					$this->_currentField = $field;
					echo $this->loadTemplate('field');
				endforeach;
			?>
			</ul>
		</div>
		
		<div class="span12">
			<?php
			foreach ($fieldsets as $fieldset) :
				if ('main' == $fieldset->name) continue;
			?>
			<ul class="adminformlist">
			<?php
				foreach ($fieldset as $field) :
					$this->_currentField = $field;
					echo $this->loadTemplate('field');
				endforeach;
			?>
			</ul>
			<?php endforeach; ?>
		</div>
		
		<div>
			<input type="hidden" name="task" value="page.edit" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>