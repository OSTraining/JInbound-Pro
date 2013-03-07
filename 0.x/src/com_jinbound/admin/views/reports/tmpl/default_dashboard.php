<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
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
			<?php echo $this->loadTemplate(null, 'glance'); ?>
		</div>
	</div>
	<!-- Row 4: see more link -->
	<div class="row-fluid">
		<div class="pull-right">
			<?php echo JHtml::link('index.php?option=com_jinbound&view=reports', 'See More'); ?>
		</div>
	</div>
	<?php echo $this->loadTemplate('leads', 'recent'); ?>
	<?php echo $this->loadTemplate('pages', 'top'); ?>