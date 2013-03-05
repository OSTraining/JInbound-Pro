<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<h2><?php echo JText::_('COM_JINBOUND_CREATE_A_NEW_LANDING_PAGE'); ?></h2>
<div class="btn-toolbar">
	<div class="btn-group row-fluid">
<?php foreach (array('A', 'B', 'C', 'D') as $template) : ?>
		<div class="btn span2<?php echo ('A' == $template ? ' offset1' : ''); ?>">
			<a href="<?php echo JInboundHelperUrl::edit('page', 0, false, array('template' => $template)); ?>"><?php echo JText::_('COM_JINBOUND_TEMPLATE_' . $template); ?></a>
		</div>
<?php endforeach; ?>
		<div class="btn span2">
			<a href="<?php echo JInboundHelperUrl::edit('page', 0, false, array('template' => 'custom')); ?>"><?php echo JText::_('COM_JINBOUND_TEMPLATE_CUSTOM'); ?></a>
		</div>
	</div>
</div>