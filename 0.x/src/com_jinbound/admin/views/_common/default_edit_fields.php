<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<!-- default_edit_fields -->
<?php

foreach ($this->_currentFieldset as $field) :
	$label = trim($field->label . '');
	if (empty($label)) :
		echo $field->input;
	else :
		$this->_currentField = $field;
		echo $this->loadTemplate('edit_field');
	endif;
endforeach;
