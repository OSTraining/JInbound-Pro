<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$this->default_tab = 'profile';

echo $this->loadTemplate('edit');
//echo "<pre>" . $this->escape(print_r($this->item, 1)) . "</pre>";