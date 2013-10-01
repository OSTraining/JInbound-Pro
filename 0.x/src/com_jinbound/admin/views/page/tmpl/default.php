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
				$('#jinbound_default_tabsTabs').find('li')[d[0]].hide();
			}
			else {
				$('#jinbound_default_tabsTabs').find('li').show();
			}
		}
	};
	hideSidebar();
	$('#jform_layout').change(hideSidebar);
});})(jQuery);
</script>