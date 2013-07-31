<?php
/**
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
      <div class="row-fluid" id="welcome_buttons">
      	<a href="<?php echo JInboundHelperUrl::view('pages'); ?>" class="span3 btn text-center">
      		<span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/landing_pages.png'; ?>" />
	      	</span>
      		<span class="btn-text"><?php echo JText::_('COM_JINBOUND_LANDING_PAGES'); ?></span>
      	</a>
      	<a href="<?php echo JInboundHelperUrl::view('campaigns'); ?>" class="span3 btn text-center">
      		<span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/leads_nurturing.png'; ?>" />
	      	</span>
      		<span class="btn-text"><?php echo JText::_('COM_JINBOUND_LEAD_NURTURING_MANAGER'); ?></span>
      	</a>
      	<a href="<?php echo JInboundHelperUrl::view('leads'); ?>" class="span3 btn text-center">
      		<span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/lead_manager.png'; ?>" />
	      	</span>
      		<span class="btn-text"><?php echo JText::_('COM_JINBOUND_LEAD_MANAGER'); ?></span>
      	</a>
      	<a href="<?php echo JInboundHelperUrl::view('reports'); ?>" class="span3 btn text-center">
      		<span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/reports.png'; ?>" />
	      	</span>
      		<span class="btn-text"><?php echo JText::_('COM_JINBOUND_REPORTS'); ?></span>
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
				<img alt="<?php echo JText::_('COM_JINBOUND_CREATE_A_NEW'); ?>" src="<?php echo JInboundHelperUrl::media() . '/images/start_by_creating.png'; ?>" />
				<ul>
					<li><?php echo JHtml::link(JInboundHelperUrl::task('page.add'), JText::_('COM_JINBOUND_LANDING_PAGE')); ?></li>
					<li><?php echo JHtml::link(JInboundHelperUrl::task('campaign.add'), JText::_('COM_JINBOUND_LEAD_NURTURING_CAMPAIGN')); ?></li>
					<li><?php echo JHtml::link(JInboundHelperUrl::task('lead.add'), JText::_('COM_JINBOUND_LEAD')); ?></li>
				</ul>
				<h3><img alt="<?php echo JText::_('COM_JINBOUND_VIEW_REPORTS'); ?>" src="<?php echo JInboundHelperUrl::media() . '/images/view_reports.png'; ?>" /> <span><?php echo JText::_('COM_JINBOUND_VIEW_REPORTS'); ?></span></h3>
				<ul>
					<li><?php echo JHtml::link(JInboundHelperUrl::view('reports'), JText::_('COM_JINBOUND_CONVERSIONS')); ?></li>
					<li><?php echo JHtml::link(JInboundHelperUrl::view('reports'), JText::_('COM_JINBOUND_LANDING_PAGE_REPORT')); ?></li>
					<li><?php echo JHtml::link(JInboundHelperUrl::view('reports'), JText::_('COM_JINBOUND_LEAD_NURTURING_CAMPAIGN')); ?></li>
				</ul>
			</div>
			<?php /*
			<!-- Row 2: Updates -->
			<div class="well">
				<h4><img alt="<?php echo JText::_('COM_JINBOUND_ALERTS_UPDATES'); ?>" src="<?php echo JInboundHelperUrl::media() . '/images/alerts.png'; ?>" /> <span><?php echo JText::_('COM_JINBOUND_ALERTS_UPDATES'); ?></span></h4>
				<div class="well">
					something here
				</div>
				<a href="javascript:alert('need a jed url');" class="btn btn-block"><?php echo JText::_('COM_JINBOUND_PLEASE_RATE_US_ON_JED'); ?></a>
				<h4><?php echo JText::_('COM_JINBOUND_CONNECT_FOR_GREAT_INBOUND_CONTENT'); ?></h4>
				<div class="row-fluid">
					<div class="span3 text-center">
						<a href="#"><img src="<?php echo $this->escape(JInboundHelperUrl::media() . '/images/twitter.jpg'); ?>" /></a>
					</div>
					<div class="span3 text-center">
						<a href="#"><img src="<?php echo $this->escape(JInboundHelperUrl::media() . '/images/rss.jpg'); ?>" /></a>
					</div>
					<div class="span3 text-center">
						<a href="#"><img src="<?php echo $this->escape(JInboundHelperUrl::media() . '/images/facebook.jpg'); ?>" /></a>
					</div>
					<div class="span3 text-center">
						<a href="#"><img src="<?php echo $this->escape(JInboundHelperUrl::media() . '/images/youtube.jpg'); ?>" /></a>
					</div>
				</div>
			</div>
			<!-- Row 3 - banner -->
			<div class="well">
				AD BANNER
			</div>
			*/ ?>
			<!-- Row 4 - other links -->
			<div class="well"><?php echo $this->feed; ?></div>
		</div>

  </div>
</div>