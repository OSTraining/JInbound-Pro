<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div class="row-fluid">
	<!-- visits -->
	<div class="span3 text-center">
		<h3><?php echo $this->getVisitCount(); ?></h3>
		<span><?php echo JText::_('COM_JINBOUND_WEBSITE_VISITS'); ?></span>
	</div>
	<!-- arrow -->
	<div class="span1 text-center">
		<h3>&rarr;</h3>
	</div>
	<!-- leads -->
	<div class="span3 text-center">
		<h3><?php echo $this->getLeadCount(); ?></h3>
		<span><?php echo JText::_('COM_JINBOUND_LEADS'); ?></span>
	</div>
	<!-- arrow -->
	<div class="span1 text-center">
		<h3>&rarr;</h3>
	</div>
	<!-- conversions -->
	<div class="span3 text-center">
		<h3><?php echo $this->getConversionRate(); ?> %</h3>
		<span><?php echo JText::_('COM_JINBOUND_CONVERSION_RATES'); ?></span>
	</div>
</div>