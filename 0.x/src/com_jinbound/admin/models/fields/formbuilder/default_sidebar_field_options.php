<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$id    = $this->escape($this->input->id) . '_' . $this->_currentField->id;
$name  = $this->escape($this->input->name . '[' . $this->_currentField->id . '][options]');
$value = $this->value[$this->_currentField->id];

$this->_optname = $name;

?>
<div class="row-fluid">
	<label for="<?php echo $id; ?>_options"><?php echo JText::_('COM_JINBOUND_FIELD_OPTIONS'); ?></label>
</div>
<div class="row-fluid formbuilder-field-options">
	<div class="span12">
		
		<div class="formbuilder-option formbuilder-default-option">
			<?php
				$this->_optnamevalue  = '';
				$this->_optvaluevalue = '';
				echo $this->loadTemplate('sidebar_field_option');
			?>
		</div>
		
		
		<div class="formbuilder-field-options-stage">
			<?php
					foreach ($value['options']['name'] as $k => $v) :
						if (empty($v)) {
							continue;
						}
						$this->_optnamevalue  = $v;
						$this->_optvaluevalue = $value['options']['value'][$k];
						?>
			<div class="formbuilder-option">
				<?php echo $this->loadTemplate('sidebar_field_option'); ?>
			</div>
			<?php endforeach; ?>
			<div class="formbuilder-option">
				<?php
					$this->_optnamevalue  = '';
					$this->_optvaluevalue = '';
					echo $this->loadTemplate('sidebar_field_option');
				?>
			</div>
		</div>
	</div>
</div>
