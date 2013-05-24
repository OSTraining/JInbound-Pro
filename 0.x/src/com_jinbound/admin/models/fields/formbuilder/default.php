<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$id = $this->escape($this->input->id);
$values = $this->input->value;
if (is_object($values) && method_exists($values, 'toArray')) {
	$values = $values->toArray();
}
if (!is_array($values)) {
	$values = array();
}

?>
<script type="text/javascript">
(function($){$(function(){
	var clone, before, parent;
	$('#<?php echo $id; ?>_sidebar').tabs();
	$("#<?php echo $id; ?>_elements, #<?php echo $id; ?>_fields").sortable({
		connectWith: ".<?php echo $id; ?>_connected",
		revert: false,
		placeholder: "ui-state-highlight",
		cancel: ".ui-state-disabled",
		helper: 'clone',
		start: function(event, ui) {
			console.log('Starting sort');
			
			var orig = $(ui.item);
			console.log(orig);
			console.log(orig.attr('data-multi'));
			
			parent = orig.parent();
			console.log(parent);
			console.log(parent.attr('id'));
			
			clone = before = false;
			
			if (orig.attr('data-multi') && !parent.attr('id').match(/_elements$/)) {
				orig.show();
				clone = orig.clone();
				before = orig.prev();
			}
			
			console.log(clone);
			console.log(before);
			console.log('Started sort');
		},
		stop: function(event, ui){
			console.log('Stopping sort');
			
			var orig = $(ui.item);
			console.log(orig);
			console.log(orig.parent());
			
			if (orig.parent().attr('id') == parent.attr('id')) {
				console.log('Stopped sort');
				return;
			}
			
			if (orig && orig.attr('data-multi')) {
				var id = orig.attr('data-id'), reg = /_[0-9]*$/, del = false;
				console.log(id);
				if (id.match(reg)) {
					console.log('Found increment');
					id = id.replace(reg, '');
					del = (0 != orig.parent().find('[data-id=' + id + ']').length);
				}
				else {
					console.log('No increment');
					var formid = id, form, num;
					
					num = parseInt(orig.parent().find('[data-id^=' + id + ']').length, 10)
					console.log(num);
					
					id = id + '_' + num;
					console.log(id);
					
					form = $('#<?php echo $id; ?>_' + id);
					console.log(form);
					
					if (0 == form.length) {
						console.log('No form!');
						
						var oldform = $('#<?php echo $id; ?>_' + formid);
						console.log(oldform);
						
						form = oldform.clone();
						console.log(form);
						
						form.attr('id', '<?php echo $id; ?>_' + id);
						form.find('label').each(function(idx, el) {
							console.log($(el));
							$(el).attr('for', $(el).attr('for') + '_' + num);
						});
						form.find('input').each(function(idx, el) {
							console.log($(el));
							$(el).attr('id', $(el).attr('id') + '_' + num);
							$(el).attr('name', $(el).attr('name').replace('[' + formid + ']', '[' + formid + '_' + num + ']'));
						});
						oldform.after(form);
						oldform.find('input[name*=enabled]').val(0);
					}
				}

				if (del) {
					orig.remove();
				}
				else {
					orig.attr('data-id', id);
				}
			}
			
			if (before && before.length) {
				before.after(clone);
			}
			else if (clone) {
				parent.prepend(clone);
			}
			
			console.log('Stopped sort');
		}
	});
	$("#<?php echo $id; ?>_elements").on("sortreceive", function(e, ui) {
		console.log('Receiving sort - elements');
		$('#<?php echo $id; ?>_' + ui.item.attr('data-id') + '_enabled').val(1);
	});
	$("#<?php echo $id; ?>_fields").on("sortreceive", function(e, ui) {
		console.log('Receiving sort - fields');
		$(ui.item).removeClass('btn-primary');
		$('#<?php echo $id; ?>_' + ui.item.attr('data-id') + '_enabled').val(0);
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
	$(document).on('change', ".<?php echo $id; ?>_fieldsettingsform input[name*=title]", function(e){
		var title = $(this).val(), data = $(this).attr('name').replace(/^.*?\[([^\]]*?)\]\[title\]$/, '$1');
		if (data) {
			var btn = $('#<?php echo $id; ?>_elements [data-id=' + data + ']');
			console.log(btn);
			btn.text(title);
		}
	});
});})(jQuery);
</script>

<div id="<?php echo $id; ?>" class="container-fluid">
	<ul id="<?php echo $id; ?>_elements" class="well <?php echo $id; ?>_connected">
<?php
if (!empty($values)) :
	foreach ($values as $ename => $element) :
		if (0 == $element['enabled']) :
			continue;
		endif;
		switch ($ename) :
			case 'first_name':
			case 'last_name':
			case 'email':
				$class = ' ui-state-disabled';
				break;
			default:
				$class = '';
		endswitch;
?>
		<li class="btn btn-block<?php echo $class; ?>" data-id="<?php echo $this->escape($ename); ?>"><?php echo $this->escape($element['title']); ?></li>
<?php
	
	endforeach;
endif;

?>
	</ul>
</div>

<div>
	<input id="<?php echo $id; ?>_value" name="<?php echo $this->escape($this->input->name); ?>" type="<?php echo (defined('JDEBUG') && JDEBUG) ? 'hidden' : 'text'; ?>" value="" />
</div>
<?php if (defined('JDEBUG') && JDEBUG) : ?>
<h4>Values:</h4>
<pre><?php print_r($values); ?></pre>
<?php endif; ?>