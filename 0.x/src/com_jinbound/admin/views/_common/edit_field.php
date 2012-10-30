<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

if ($this->_currentField->hidden) :
	echo $this->_currentField->input;
else:

?>
<li>
<?php
	
	echo $field->label;
	echo $field->input;
	
?>
	<div class="clr"></div>
</li>
<?php

endif;
