<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

if (!empty($this->item->image)) :
	?><img src="<?php echo $this->escape($this->item->image); ?>" alt="<?php echo $this->escape($this->item->imagealttext); ?>" /><?php
endif;
