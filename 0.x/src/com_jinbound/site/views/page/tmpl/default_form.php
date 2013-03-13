<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

foreach ($this->item->formbuilder as $key => $element) :
	$this->_currentFieldName = $key;
	$this->_currentField     = $element;
	echo $this->loadTemplate('form_field_' . $key);
endforeach;