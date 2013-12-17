<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

echo $this->loadTemplate('edit');

?>
<script type="text/javascript">
(function($){$(function(){
<?php if ($this->item->id) : ?>
	$.ajax('../index.php?option=com_jinbound&task=landingpageurl&id=<?php echo (int) $this->item->id; ?>', {
		dataType: 'json',
		success: function(response) {
			$('#jform_alias').closest('.row-fluid').after($('<div class="row-fluid"><div class="span12"><a href="' + response.sef + '" target="_blank">' + response.sef + '</a></div></div>'));
		}
	});
<?php endif; ?>
	var hideSidebar = function() {
		var row = $('#jform_sidebartext').closest('.row-fluid'), d = [4];
		switch($('#jform_layout').val()) {
			case '0':
				d = [];
			case 'A':
				row.show();
				break;
			default:
				row.hide();
				break;
		}
		try {
			$('#jinbound_default_tabs').tabs("option", "disabled", d);
		}
		catch (err) {
			if (d.length) {
				$('#jinbound_default_tabs').find('dt.tabs')[d[0]].hide();
			}
			else {
				$('#jinbound_default_tabs').find('dt.tabs').show();
			}
		}
	};
	hideSidebar();
	$('#jform_layout').change(hideSidebar);
});})(jQuery);
</script>
