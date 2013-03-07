<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<!-- Main Component container -->
<div class="container-fluid" id="jinbound_component">
	<!-- Main Dashboard columns -->
  <div class="row-fluid">
  	<!-- Left panel -->
    <div class="span8">
      <!-- Row 1 - Welcome Message-->
      <div class="row-fluid" id="welcome_message">
      	<div class="span12">
      		<p class="lead"><?php echo JText::_('COM_JINBOUND_WELCOME_TO_JINBOUND'); ?></p>
      	</div>
      </div>
      <!-- Row 2 - Buttons -->
      <div class="row-fluid">
      	<a href="<?php echo JInboundHelperUrl::view('pages'); ?>" class="span3 btn text-center">
      		<span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/icon-48-jinbound.png'; ?>" />
	      	</span>
      		<span><?php echo JText::_('COM_JINBOUND_LANDING_PAGES'); ?></span>
      	</a>
      	<a href="<?php echo JInboundHelperUrl::view('campaigns'); ?>" class="span3 btn text-center">
      		<span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/icon-48-jinbound.png'; ?>" />
	      	</span>
      		<span><?php echo JText::_('COM_JINBOUND_LEAD_NURTURING_MANAGER'); ?></span>
      	</a>
      	<a href="<?php echo JInboundHelperUrl::view('leads'); ?>" class="span3 btn text-center">
      		<span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/icon-48-jinbound.png'; ?>" />
	      	</span>
      		<span><?php echo JText::_('COM_JINBOUND_LEAD_MANAGER'); ?></span>
      	</a>
      	<a href="<?php echo JInboundHelperUrl::view('reports'); ?>" class="span3 btn text-center">
      		<span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/icon-48-jinbound.png'; ?>" />
	      	</span>
      		<span><?php echo JText::_('COM_JINBOUND_REPORTS'); ?></span>
      	</a>
      </div>
      
			<!-- Row 3 - Monthly Report -->
			<div class="row-fluid">
				<!-- start the container -->
				<div class="well">
					<!-- Report Heading -->
					<div class="row-fluid">
						<div class="span12">
							<h3 class="text-center"><?php echo JText::_('COM_JINBOUND_MONTHLY_REPORTING_SNAPSHOT'); ?></h3>
						</div>
					</div>
					<?php echo $this->reports->glance; ?>
				</div>
			</div>
      <?php
      	echo $this->reports->top_pages;
      	echo $this->reports->recent_leads;
      ?>
			
		</div>
		<!-- Sidebar -->
		<div class="span4">
			<!-- Row 1 - links -->
			<div class="well">
				<h4><?php echo JText::_('COM_JINBOUND_CREATE_A_NEW'); ?></h4>
				<ul>
					<li><?php echo JHtml::link(JInboundHelperUrl::task('page.add'), JText::_('COM_JINBOUND_LANDING_PAGE')); ?></li>
					<li><?php echo JHtml::link(JInboundHelperUrl::task('campaign.add'), JText::_('COM_JINBOUND_LEAD_NURTURING_CAMPAIGN')); ?></li>
					<li><?php echo JHtml::link(JInboundHelperUrl::task('lead.add'), JText::_('COM_JINBOUND_LEAD')); ?></li>
				</ul>
				<h4><?php echo JText::_('COM_JINBOUND_VIEW_REPORTS'); ?></h4>
				<ul>
					<li><?php echo JHtml::link(JInboundHelperUrl::view('reports'), JText::_('COM_JINBOUND_CONVERSIONS')); ?></li>
					<li><?php echo JHtml::link(JInboundHelperUrl::view('reports'), JText::_('COM_JINBOUND_LANDING_PAGE_REPORT')); ?></li>
					<li><?php echo JHtml::link(JInboundHelperUrl::view('reports'), JText::_('COM_JINBOUND_LEAD_NURTURING_CAMPAIGN')); ?></li>
				</ul>
			</div>
			<!-- Row 2: Updates -->
			<div class="well">
				<h3><?php echo JText::_('COM_JINBOUND_ALERTS_UPDATES'); ?></h3>
				<div class="well">
					something here
				</div>
				<a href="javascript:alert('need a jed url');" class="btn btn-block"><?php echo JText::_('COM_JINBOUND_PLEASE_RATE_US_ON_JED'); ?></a>
				<h4><?php echo JText::_('COM_JINBOUND_CONNECT_FOR_GREAT_INBOUND_CONTENT'); ?></h4>
				<span class="3">twi</span>
				<span class="3">rss</span>
				<span class="3">fbk</span>
				<span class="3">ytb</span>
			</div>
			<!-- Row 3 - banner -->
			<div class="well">
				AD BANNER
			</div>
			<!-- Row 4 - other links -->
			<div class="well">
				<h4><?php echo JText::_('COM_JINBOUND_EDUCATION'); ?></h4>
				<ul>
					<li><?php echo JHtml::link('#', 'Something from RSS - TODO'); ?></li>
				</ul>
				<h4><?php echo JText::_('COM_JINBOUND_DOWNLOADS'); ?></h4>
				<ul>
					<li><?php echo JHtml::link('#', 'Download Links - TODO'); ?></li>
				</ul>
			</div>
		</div>

  </div>
</div>