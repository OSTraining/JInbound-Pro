<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$id = $this->escape($this->input->id);
?>
<script type="text/javascript">
(function($){$(function(){
	$('#<?php echo $id; ?>_sidebar').tabs();
	$("#<?php echo $id; ?>_elements, #<?php echo $id; ?>_fields").sortable({
		connectWith: ".<?php echo $id; ?>_connected",
		revert: false,
		placeholder: "ui-state-highlight"
	});
	$("#<?php echo $id; ?>_elements").on("sortreceive", function(e, ui) {
		$('#<?php echo $id; ?>_' + ui.item.attr('data-id'));
		console.log(ui.item.attr('data-id'));
	});
	$("#<?php echo $id; ?>_fields").on("sortreceive", function(e, ui) {
		console.log(ui.item.attr('data-id'));
	});
	$(document).on('click', "#<?php echo $id; ?>_elements .btn-block", function(e){
		$("#<?php echo $id; ?>_elements .btn-block.btn-primary").removeClass('btn-primary');
		$(this).addClass('btn-primary');
		var tabs = $('#<?php echo $id; ?>_sidebar');
		tabs.tabs('refresh');
		tabs.tabs('option', 'active', -1);
		$('.<?php echo $id; ?>_fieldsettingsform').hide();
		$('#<?php echo $id; ?>_' + $(this).attr('data-id')).show();
	});
});})(jQuery);
</script>

<div id="<?php echo $id; ?>" class="container-fluid">
	<ul id="<?php echo $id; ?>_elements" class="well <?php echo $id; ?>_connected">
<?php foreach ($this->input->getFormValue() as $element) : ?>
		<li class="btn btn-block"><?php echo $element; ?></li>
<?php endforeach; ?>
	</ul>
</div>

<div>
	<input id="<?php echo $id; ?>_value" name="<?php echo $this->escape($this->input->name); ?>" type="<?php echo (defined('JDEBUG') && JDEBUG) ? 'hidden' : 'text'; ?>" value="" />
</div>
<pre><?php print_r($this->input->value); ?></pre>
