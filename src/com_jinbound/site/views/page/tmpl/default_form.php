<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div class="row-fluid">
	<div class="span12">
		<?php echo $this->loadTemplate('form_open'); ?>
			<div class="row-fluid">
				<div class="span12">
					<h4><?php echo $this->escape($this->item->formname); ?></h4>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<?php
						foreach ($this->form->getFieldset('lead') as $key => $field) :
							$this->_currentField = $field;
							echo $this->loadTemplate('form_field');
						endforeach;
					?>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="btn-toolbar">
						<div class="btn-group row-fluid">
							<?php echo $this->loadTemplate('form_submit'); ?>
						</div>
					</div>
				</div>
			</div>
		<?php echo $this->loadTemplate('form_close'); ?>
	</div>
</div>
