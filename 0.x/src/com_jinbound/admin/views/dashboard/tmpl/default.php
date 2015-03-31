<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JText::script('COM_JINBOUND_RESET_CONFIRM');

$user = JFactory::getUser();
foreach (array('campaign', 'email', 'page', 'contact', 'report') as $type)
{
	foreach (array('manage', 'create') as $var)
	{
		${"can".str_replace(' ', '', ucwords(str_replace('.', ' ', $var))).ucwords($type)} = $user->authorise("core.$var", JInbound::COM . ".$type");
	}
}
$jserror = "javascript:alert('" . JText::_('JERROR_ALERTNOAUTHOR') . "');";

?>
<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if ('reset' === task && confirm(Joomla.JText._('COM_JINBOUND_RESET_CONFIRM'))) {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
};
<?php if (!empty($this->updates)) : ?>
(function($,d){
	var urls = [<?php
	$updates = array();
	foreach ($this->updates as $update)
	{
		$updates[] = "'" . JInboundHelperFilter::escape_js($update) . "'";
	}
	echo implode(',', $updates);
	?>];
	$(d).ready(function(){
		$.each(urls, function(i, url){
			$.ajax(url, {success: function(response){
				if (!response.length) {
					return;
				}
				$('<div class="alert alert-info">' + response + '</div>').insertBefore($('#adminForm'));
			}});
		});
	});
})(jQuery,document);
<?php endif; ?>
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
				<?php
					$class = 'span3 btn text-center';
					if ($canManageCampaign)
					{
						$href = JInboundHelperUrl::view('campaigns');
					}
					else
					{
						$href = $jserror;
						$class .= ' disabled';
					}
				?>
      	<a href="<?php echo $href; ?>" class="<?php echo $class; ?>">
      		<span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/lead_manager.png'; ?>" />
	      	</span>
      		<span class="btn-text"><?php echo JText::_('COM_JINBOUND_STEP_1_CREATE_A_CAMPAIGN'); ?></span>
      	</a>
				<?php
					$class = 'span3 btn text-center';
					if ($canManageEmail)
					{
						$href = JInboundHelperUrl::view('emails');
					}
					else
					{
						$href = $jserror;
						$class .= ' disabled';
					}
				?>
      	<a href="<?php echo $href; ?>" class="<?php echo $class; ?>">
      		<span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/leads_nurturing.png'; ?>" />
	      	</span>
      		<span class="btn-text"><?php echo JText::_('COM_JINBOUND_STEP_2_WRITE_EMAILS_FOR_YOUR_CAMPAIGN'); ?></span>
      	</a>
				<?php
					$class = 'span3 btn text-center';
					if ($canManagePage)
					{
						$href = JInboundHelperUrl::view('pages');
					}
					else
					{
						$href = $jserror;
						$class .= ' disabled';
					}
				?>
      	<a href="<?php echo $href; ?>" class="<?php echo $class; ?>">
      		<span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/landing_pages.png'; ?>" />
	      	</span>
      		<span class="btn-text"><?php echo JText::_('COM_JINBOUND_STEP_3_CREATE_LANDING_PAGES_TO_GET_PEOPLE_INTO_YOUR_CAMPAIGN'); ?></span>
      	</a>
				<?php
					$class = 'span3 btn text-center';
					if ($canManageContact)
					{
						$href = JInboundHelperUrl::view('contacts');
					}
					else
					{
						$href = $jserror;
						$class .= ' disabled';
					}
				?>
      	<a href="<?php echo $href; ?>" class="<?php echo $class; ?>">
      		<span class="row text-center">
	      		<img class="img-rounded" src="<?php echo JInboundHelperUrl::media() . '/images/reports.png'; ?>" />
	      	</span>
      		<span class="btn-text"><?php echo JText::_('COM_JINBOUND_STEP_4_GET_REPORTS_ON_PEOPLE_WHO_SIGNED_UP'); ?></span>
      	</a>
      </div>
      
			<?php if ($canManageReport) : ?>
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
				endif;
				if ($canManageContact) :
					echo $this->reports->recent_leads;
				endif;
      ?>
			
		</div>
		<!-- Sidebar -->
		<div class="span4">
			<div class="well">
				<img alt="<?php echo JText::_('COM_JINBOUND_CREATE_A_NEW'); ?>" src="<?php echo JInboundHelperUrl::media() . '/images/start_by_creating.png'; ?>" />
				<ul>
					<li><?php echo JHtml::link($canCreateCampaign ? JInboundHelperUrl::task('campaign.add') : $jserror, JText::_('COM_JINBOUND_LEAD_NURTURING_CAMPAIGN')); ?></li>
					<li><?php echo JHtml::link($canCreateEmail ? JInboundHelperUrl::task('email.add') : $jserror, JText::_('COM_JINBOUND_EMAIL')); ?></li>
					<li><?php echo JHtml::link($canCreatePage ? JInboundHelperUrl::task('page.add') : $jserror, JText::_('COM_JINBOUND_LANDING_PAGE')); ?></li>
				</ul>
				<?php if ($canManageReport) : ?>
				<h3><?php echo JHtml::link(JInboundHelperUrl::view('reports'), '<img alt="' . JText::_('COM_JINBOUND_VIEW_REPORTS') . '" src="' . JInboundHelperUrl::media() . '/images/view_reports.png" /> <span>' . JText::_('COM_JINBOUND_VIEW_REPORTS') . '</span>'); ?></h3>
				<?php endif; ?>
			</div>
			<div class="well"><?php echo $this->news; ?></div>
			<div class="well"><?php echo $this->feed; ?></div>
		</div>

  </div>
	
	</div>
</div>
<?php

echo $this->loadTemplate('footer');

echo $this->reports->script;

