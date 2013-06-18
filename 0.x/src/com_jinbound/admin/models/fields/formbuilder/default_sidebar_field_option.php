<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div class="row-fluid">
	<input class="input-block-level" name="<?php echo $this->_optname; ?>[name][]" value="" placeholder="<?php echo JText::_('COM_JINBOUND_FORMFIELD_OPTION_NAME_PLACEHOLDER'); ?>" />
</div>
<div class="row-fluid">
	<input class="input-block-level" name="<?php echo $this->_optname; ?>[value][]" value="" placeholder="<?php echo JText::_('COM_JINBOUND_FORMFIELD_OPTION_VALUE_PLACEHOLDER'); ?>" />
</div>
<div class="row-fluid btn-group">
	<span class="btn formbuilder-option-add">
		<i class="icon-plus"></i>
	</span>
	<span class="btn formbuilder-option-del">
		<i class="icon-minus"></i>
	</span>
	<span class="btn formbuilder-option-up">
		<i class="icon-arrow-up"></i>
	</span>
	<span class="btn formbuilder-option-down">
		<i class="icon-arrow-down"></i>
	</span>
</div>