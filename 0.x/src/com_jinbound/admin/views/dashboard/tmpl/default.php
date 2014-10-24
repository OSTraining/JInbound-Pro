<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JText::script('COM_JINBOUND_RESET_CONFIRM');

?>
<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if ('reset' === task && confirm(Joomla.JText._('COM_JINBOUND_RESET_CONFIRM'))) {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
};
</script>
<form action="<?php echo JInboundHelperUrl::_(); ?>" method="post" id="adminForm" name="adminForm" class="form-validate" enctype="multipart/form-data">
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<!-- Main Component container -->
<div class="container-fluid" id="jinbound_component">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		
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
      	<a href="<?php echo JInboundHelperUrl::view('campaigns'); ?>" class="span3 btn text-center">
      		<span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/lead_manager.png'; ?>" />
	      	</span>
      		<span class="btn-text"><?php echo JText::_('COM_JINBOUND_STEP_1_CREATE_A_CAMPAIGN'); ?></span>
      	</a>
      	<a href="<?php echo JInboundHelperUrl::view('emails'); ?>" class="span3 btn text-center">
      		<span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/leads_nurturing.png'; ?>" />
	      	</span>
      		<span class="btn-text"><?php echo JText::_('COM_JINBOUND_STEP_2_WRITE_EMAILS_FOR_YOUR_CAMPAIGN'); ?></span>
      	</a>
      	<a href="<?php echo JInboundHelperUrl::view('pages'); ?>" class="span3 btn text-center">
      		<span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/landing_pages.png'; ?>" />
	      	</span>
      		<span class="btn-text"><?php echo JText::_('COM_JINBOUND_STEP_3_CREATE_LANDING_PAGES_TO_GET_PEOPLE_INTO_YOUR_CAMPAIGN'); ?></span>
      	</a>
      	<a href="<?php echo JInboundHelperUrl::view('reports'); ?>" class="span3 btn text-center">
      		<span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/reports.png'; ?>" />
	      	</span>
      		<span class="btn-text"><?php echo JText::_('COM_JINBOUND_STEP_4_GET_REPORTS_ON_PEOPLE_WHO_SIGNED_UP'); ?></span>
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
					<?php
						$filter_start = new DateTime();
						$filter_end = clone $filter_start;
						$filter_start->modify('-1 month');
						$filter_end->modify('+1 day');
						
					?>
					<input id="filter_start" type="hidden" value="<?php echo $filter_start->format('Y-m-d'); ?>" />
					<input id="filter_end" type="hidden" value="<?php echo $filter_end->format('Y-m-d'); ?>" />
					<select id="filter_campaign" style="display:none"><option value=""></option></select>
					<select id="filter_page" style="display:none"><option value=""></option></select>
				</div>
			</div>
      <?php
      	echo $this->reports->top_pages;
      	echo $this->reports->recent_leads;
      ?>
			
		</div>
		<!-- Sidebar -->
		<div class="span4">
			<div class="well">
				<img alt="<?php echo JText::_('COM_JINBOUND_CREATE_A_NEW'); ?>" src="<?php echo JInboundHelperUrl::media() . '/images/start_by_creating.png'; ?>" />
				<ul>
					<li><?php echo JHtml::link(JInboundHelperUrl::task('campaign.add'), JText::_('COM_JINBOUND_LEAD_NURTURING_CAMPAIGN')); ?></li>
					<li><?php echo JHtml::link(JInboundHelperUrl::task('email.add'), JText::_('COM_JINBOUND_EMAIL')); ?></li>
					<li><?php echo JHtml::link(JInboundHelperUrl::task('page.add'), JText::_('COM_JINBOUND_LANDING_PAGE')); ?></li>
				</ul>
				<h3><?php echo JHtml::link(JInboundHelperUrl::view('reports'), '<img alt="' . JText::_('COM_JINBOUND_VIEW_REPORTS') . '" src="' . JInboundHelperUrl::media() . '/images/view_reports.png" /> <span>' . JText::_('COM_JINBOUND_VIEW_REPORTS') . '</span>'); ?></h3>
			</div>
			<div class="well"><?php echo $this->feed; ?></div>
		</div>

  </div>
	
	</div>
</div>
<?php

echo $this->loadTemplate('footer');

echo $this->reports->script;

