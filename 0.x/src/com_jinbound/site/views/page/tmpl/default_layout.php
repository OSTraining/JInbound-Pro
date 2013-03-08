<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

if (!property_exists($this->item, 'layout') || !in_array($this->item->layout, array('a','b','c','d'))) {
	$this->item->layout = 'a';
}

?>
<div class="row-fluid">
	<div class="span12">
		<h1><?php echo $this->escape($this->item->heading); ?></h1>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
<?php echo $this->loadTemplate('layout_' . strtolower($this->item->layout)); ?>
	</div>
</div>