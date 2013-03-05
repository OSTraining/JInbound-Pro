<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div class="jinbound-empty">
	<div class="row">
		<div class="span4 offset4">
			<a class="btn btn-large btn-block" href="<?php echo JInboundHelperUrl::task($this->viewItemName . '.add'); ?>">
				<i class="icon-plus-sign"></i>
				<span><?php echo JText::_('COM_JINBOUND_' . strtoupper($this->viewItemName) . '_ADD_NEW'); ?></span>
			</a>
		</div>
	</div>
</div>
