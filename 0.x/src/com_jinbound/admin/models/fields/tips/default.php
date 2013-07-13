<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$id = $this->escape($this->input->id);
?>

<div id="<?php echo $id; ?>" class="container-fluid">
	<?php echo JText::_('COM_JINBOUND_TIPS_' . strtoupper($id)); ?>
</div>
