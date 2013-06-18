<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$id     = $this->escape($this->input->id) . '_' . $this->_currentField->id;
$name   = $this->escape($this->input->name . '[' . $this->_currentField->id . '][options]');
$values = $this->input->value;
if (is_object($values) && method_exists($values, 'toArray')) {
	$values = $values->toArray();
}
if (!is_array($values)) {
	$values = array();
}

?>
<div class="row-fluid">
	<label for="<?php echo $id; ?>_options"><?php echo JText::_('COM_JINBOUND_FIELD_OPTIONS'); ?></label>
</div>
<div class="row-fluid formbuilder-field-options">
	<div class="span12">
		
		<div class="formbuilder-option formbuilder-default-option">
			<?php
				$this->_optname  = $name;
				$this->_optvalue = '';
				echo $this->loadTemplate('sidebar_field_option');
			?>
		</div>
		
		
		<div class="formbuilder-field-options-stage">
			<div class="formbuilder-option">
				<?php
					foreach ($values['name'] as $k => $v) {
						$this->_optname  = $v;
						$this->_optvalue = $values['value'][$k];
						echo $this->loadTemplate('sidebar_field_option');
					}
					$this->_optname  = '';
					$this->_optvalue = '';
					echo $this->loadTemplate('sidebar_field_option');
				?>
			</div>
		</div>
	</div>
</div>
