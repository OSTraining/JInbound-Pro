<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div class="row-fluid">
	<div class="span12">
		<ul class="unstyled">
			<li><a href="<?php echo JInboundHelperUrl::_(array('option' => 'com_categories', 'extension' => JInbound::COM)); ?>"><?php echo JText::_('COM_CATEGORIES'); ?></a></li>
			<li><a href="<?php echo JInboundHelperUrl::view('campaigns', false); ?>"><?php echo JText::_('COM_JINBOUND_CAMPAIGNS'); ?></a></li>
			<li><a href="<?php echo JInboundHelperUrl::view('statuses', false); ?>"><?php echo JText::_('COM_JINBOUND_STATUSES'); ?></a></li>
			<li><a href="<?php echo JInboundHelperUrl::view('priorities', false); ?>"><?php echo JText::_('COM_JINBOUND_PRIORITIES'); ?></a></li>
		</ul>
	</div>
</div>