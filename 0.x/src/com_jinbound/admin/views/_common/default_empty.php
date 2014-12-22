<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

if (JFactory::getUser()->authorise('core.create', JInbound::COM . '.' . JInboundInflector::singularize($this->viewName))) :
?>
<div class="jinbound-empty">
	<div class="row">
		<div class="span4 offset4">
			<a class="btn btn-large btn-block" href="<?php echo JInboundHelperUrl::task(JInboundInflector::singularize($this->viewName) . '.add'); ?>">
				<i class="icon-plus-sign"></i>
				<span><?php echo JText::_('COM_JINBOUND_' . strtoupper(JInboundInflector::singularize($this->viewName)) . '_ADD_NEW'); ?></span>
			</a>
		</div>
	</div>
</div>
<?php

endif;
