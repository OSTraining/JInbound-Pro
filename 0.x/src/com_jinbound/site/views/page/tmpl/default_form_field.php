<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$field = JInboundHelperForm::getField($this->_currentField->name, $this->item->formid);

if ($field) :
?>
<?php if ($field->reg->get('show_label', 1)) : ?>
<div class="row-fluid">
	<?php echo $this->_currentField->label; ?>
</div>
<?php endif; ?>
<div class="row-fluid">
	<?php echo $this->_currentField->input; ?>
</div>
<?php endif;
