<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div class="row-fluid">
	<!-- start the container -->
	<div class="well span8 offset2">
		<!-- Report Heading -->
		<div class="row-fluid">
			<div class="span12">
				<h3 class="text-center"><?php echo JText::_('COM_JINBOUND_AT_A_GLANCE'); ?></h3>
			</div>
		</div>
		<?php echo $this->loadTemplate(null, 'glance'); ?>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<h4><img alt="<?php echo JText::_('COM_JINBOUND_RECENT_LEADS'); ?>" src="<?php echo JInboundHelperUrl::media() . '/images/recent_leads.png'; ?>" /> <span><?php echo JText::_('COM_JINBOUND_RECENT_LEADS'); ?></span></h4>
		<div id="reports_recent_leads"></div>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<h4><img alt="<?php echo JText::_('COM_JINBOUND_TOP_PERFORMING_LANDING_PAGES'); ?>" src="<?php echo JInboundHelperUrl::media() . '/images/top_performing_landing_pages.png'; ?>" /> <span><?php echo JText::_('COM_JINBOUND_TOP_PERFORMING_LANDING_PAGES'); ?></span></h4>
		<div id="reports_top_pages"></div>
	</div>
</div>
<?php

//echo $this->loadTemplate('leads', 'recent');
//echo $this->loadTemplate('pages', 'top');
