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
	<!-- start the container -->
	<div class="well span8 offset2">
		<div class="row-fluid">
			<div class="span12">
				<div id="jinbound-reports-graph" style="width:100%;height:300px"></div>
			</div>
		</div>
	</div>
</div>
<?php
echo $this->loadTemplate('leads', 'recent');
echo $this->loadTemplate('pages', 'top');
