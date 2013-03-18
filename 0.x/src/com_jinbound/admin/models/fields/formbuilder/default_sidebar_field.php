<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$id = $this->escape($this->input->id) . '_' . $this->_currentField->id;
$name = $this->escape($this->input->name . '[' . $this->_currentField->id . ']');
$values = $this->input->value;
if (is_object($values) && method_exists($values, 'toArray')) {
	$values = $values->toArray();
}
if (!is_array($values)) {
	$values = array();
}

if (array_key_exists($this->_currentField->id, $values)) {
	$value = $values[$this->_currentField->id];
}
else {
	$value = array('title' => $this->_currentField->name, 'required' => 0, 'enabled' => 0);
}

?>
<div id="<?php echo $id; ?>" class="container-fluid">
	<div class="row-fluid">
		<label for="<?php echo $id; ?>_title"><?php echo JText::_('COM_JINBOUND_FIELD_TITLE'); ?></label>
	</div>
	<div class="row-fluid">
		<input id="<?php echo $id; ?>_title" class="input-medium" type="text" name="<?php echo $name; ?>[title]" value="<?php echo $this->escape($value['title']); ?>" />
	</div>
	<div class="row-fluid">
		<label for="<?php echo $id; ?>_required"><?php echo JText::_('COM_JINBOUND_FIELD_REQUIRED'); ?></label>
	</div>
	<div class="row-fluid">
		<?php echo JHtml::_('select.genericlist', array(JHtml::_('select.option', '1', JText::_('JYES')), JHtml::_('select.option', '0', JText::_('JNO'))), $name . '[required]', 'class="input-medium"', 'value', 'text', $value['required'], $id . '_required'); ?>
	</div>
	<div>
		<input id="<?php echo $id; ?>_enabled" type="<?php echo (defined('JDEBUG') && JDEBUG ? 'text' : 'hidden'); ?>" name="<?php echo $name; ?>[enabled]" value="<?php echo (int) $value['enabled']; ?>" />
	</div>
	<?php echo $this->loadTemplate('sidebar_field_' . $this->_currentField->type); ?>
	<div class="row-fluid">
		<h4>Value:</h4>
		<pre><?php print_r($value); ?></pre>
	</div>
</div>