<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$this->cols = 11;

echo $this->loadTemplate('list');
echo "<pre>" . $this->escape(print_r($this->items, 1)) . "</pre>";