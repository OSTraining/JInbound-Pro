<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div class="row-fluid">
	<div class="span9">
		<div class="pull-right">
			<?php echo $this->loadTemplate('image'); ?>
		</div>
<?php if (!empty($this->item->subheading)) : ?>
		<h2><?php echo $this->escape($this->item->subheading); ?></h2>
<?php endif; ?>
		<?php echo $this->loadTemplate('body'); ?>
	</div>
	<div class="span3 well">
		<?php echo $this->loadTemplate('form'); ?>
	</div>
</div>
<div class="row-fluid">
	<?php echo $this->loadTemplate('social'); ?>
</div>
