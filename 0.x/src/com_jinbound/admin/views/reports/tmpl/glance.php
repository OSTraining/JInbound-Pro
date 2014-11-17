<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div id="jinbound-reports-glance" class="row-fluid">
	<!-- visits -->
	<div class="span2 text-center">
		<h3 id="jinbound-reports-glance-views"><?php echo $this->getVisitCount(); ?></h3>
		<span><?php echo JText::_('COM_JINBOUND_LANDING_PAGE_VIEWS'); ?></span>
	</div>
	<!-- arrow -->
	<div class="span1 text-center">
		<h3><img src="<?php echo JInboundHelperUrl::media() . '/images/summary_arrows.png'; ?>" /></h3>
	</div>
	<!-- leads -->
	<div class="span1 text-center">
		<h3 id="jinbound-reports-glance-leads"><?php echo $this->getLeadCount(); ?></h3>
		<span><?php echo JText::_('COM_JINBOUND_LEADS'); ?></span>
	</div>
	<!-- arrow -->
	<div class="span1 text-center">
		<h3><img src="<?php echo JInboundHelperUrl::media() . '/images/summary_arrows.png'; ?>" /></h3>
	</div>
	<!-- views to leads -->
	<div class="span2 text-center">
		<h3 id="jinbound-reports-glance-views-to-leads"><?php echo $this->getViewsToLeads(); ?> %</h3>
		<span><?php echo JText::_('COM_JINBOUND_VIEWS_TO_LEADS'); ?></span>
	</div>
	<!-- arrow -->
	<div class="span1 text-center">
		<h3><img src="<?php echo JInboundHelperUrl::media() . '/images/summary_arrows.png'; ?>" /></h3>
	</div>
	<!-- customers -->
	<div class="span1 text-center">
		<h3 id="jinbound-reports-glance-conversion-count"><?php echo $this->getConversionCount(); ?></h3>
		<span><?php echo JText::_('COM_JINBOUND_CONVERSIONS'); ?></span>
	</div>
	<!-- arrow -->
	<div class="span1 text-center">
		<h3><img src="<?php echo JInboundHelperUrl::media() . '/images/summary_arrows.png'; ?>" /></h3>
	</div>
	<!-- conversions -->
	<div class="span2 text-center">
		<h3 id="jinbound-reports-glance-conversion-rate"><?php echo $this->getConversionRate(); ?> %</h3>
		<span><?php echo JText::_('COM_JINBOUND_VIEWS_TO_CONVERSIONS'); ?></span>
	</div>
</div>