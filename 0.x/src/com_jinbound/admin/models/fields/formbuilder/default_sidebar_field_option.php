<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div class="row-fluid">
	<input class="input-block-level" name="<?php echo $this->_optname; ?>[name][]" value="<?php echo $this->escape($this->_optnamevalue); ?>" placeholder="<?php echo JText::_('COM_JINBOUND_FORMFIELD_OPTION_NAME_PLACEHOLDER'); ?>" />
</div>
<div class="row-fluid">
	<input class="input-block-level" name="<?php echo $this->_optname; ?>[value][]" value="<?php echo $this->escape($this->_optvaluevalue); ?>" placeholder="<?php echo JText::_('COM_JINBOUND_FORMFIELD_OPTION_VALUE_PLACEHOLDER'); ?>" />
</div>
<div class="row-fluid btn-group">
	<span class="btn formbuilder-option-add">
		<i class="icon-plus"></i>
	</span>
	<span class="btn formbuilder-option-del">
		<i class="icon-minus"></i>
	</span>
<?php if ('attributes' != $this->optionsInputName) : ?>
	<span class="btn formbuilder-option-move formbuilder-option-up">
		<i class="icon-arrow-up"></i>
	</span>
	<span class="btn formbuilder-option-move formbuilder-option-down">
		<i class="icon-arrow-down"></i>
	</span>
<?php endif; ?>
</div>