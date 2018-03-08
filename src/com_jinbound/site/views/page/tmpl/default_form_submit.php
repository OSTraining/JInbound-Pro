<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$go = trim((string) $this->item->submit_text);
if (empty($go)) {
	$go = JText::_('JSUBMIT');
}

?>
<button type="submit" class="btn btn-primary"><?php echo $this->escape($go); ?></button>