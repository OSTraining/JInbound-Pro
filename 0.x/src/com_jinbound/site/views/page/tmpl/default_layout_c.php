<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div class="row-fluid">
	<div class="span12">
		<div class="pull-right">
			<?php echo $this->loadTemplate('social'); ?>
		</div>
		<h2><?php echo $this->escape($this->item->subheading); ?></h2>
	</div>
</div>
<div class="row-fluid">
	<div class="span3 well">
		<?php echo $this->loadTemplate('form'); ?>
	</div>
	<div class="span9">
		<div class="row-fluid">
			<div class="span12">
				<div class="pull-right">
					<?php echo $this->loadTemplate('image'); ?>
				</div>
				<?php echo $this->loadTemplate('body'); ?>
			</div>
		</div>
	</div>
</div>
