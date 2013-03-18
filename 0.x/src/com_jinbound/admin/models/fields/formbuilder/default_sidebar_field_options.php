<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$id     = $this->escape($this->input->id);
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
<div class="row-fluid">
	TODO: options
</div>