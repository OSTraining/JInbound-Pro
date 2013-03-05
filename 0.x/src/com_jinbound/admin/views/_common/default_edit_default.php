<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

foreach ($this->form->getFieldset('default') as $field) :
	$label = trim($field->label . '');
	if (empty($label)) :
		echo $field->input;
	else :
	?>
<div class="row-fluid">
	<div class="span1"><?php echo $label; ?></div>
<div class="span10 offset1"><?php echo $field->input; ?></div>
</div>
		<?php
	endif;
endforeach;
