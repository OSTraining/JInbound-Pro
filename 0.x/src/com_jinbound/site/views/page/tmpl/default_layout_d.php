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
	<div class="span12">
		<h2 class="text-center">TODO subheadline here</h2>
	</div>
</div>
<div class="row-fluid">
	<div class="span6">
		<div class="row-fluid">
			<?php echo $this->loadTemplate('body'); ?>
		</div>
		<div class="row-fluid">
			<?php echo $this->loadTemplate('social'); ?>
		</div>
	</div>
	<div class="span6">
		<div class="row-fluid">
			<?php echo $this->loadTemplate('image'); ?>
		</div>
		<div class="row-fluid">
			<?php echo $this->loadTemplate('form'); ?>
		</div>
	</div>
</div>
