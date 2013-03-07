<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div class="row-fluid">
	<div class="span3">
		<div class="jinbound-image row-fluid">
			<?php echo $this->loadTemplate('image'); ?>
		</div>
		<div class="jinbound-sidebar row-fluid">
			<?php echo $this->loadTemplate('sidebar'); ?>
		</div>
	</div>
	<div class="span9">
		<div class="row-fluid">
			<div class="span12">
				<h2>TODO subheadline here</h2>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span8">
				<div class="row-fluid">
					<?php echo $this->loadTemplate('body'); ?>
				</div>
				<div class="row-fluid">
					<?php echo $this->loadTemplate('social'); ?>
				</div>
			</div>
			<div class="span4">
				<?php echo $this->loadTemplate('form'); ?>
			</div>
		</div>
	</div>
</div>