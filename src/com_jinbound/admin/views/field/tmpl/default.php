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
(function($,d){$(d).ready(function(){
	$(d).on('change', '#jform_type', function(e){
		var o = $('div[data-id="jform_params_opts"]');
		if (o.length) {
			switch ($(e.target).find(':selected').val()) {
				case 'checkbox':
				case 'checkboxes':
				case 'list':
				case 'radio':
				case 'groupedlist':
					o.show();
					break;
				default:
					o.hide();
					break;
			}
		}
	});
	$('#jform_type').trigger('click');
});})(jQuery, document);
</script>
