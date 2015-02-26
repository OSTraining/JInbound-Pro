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
window.jinboundlayouttags = {};
<?php foreach ($this->layouttags as $tag => $value) : ?>
window.jinboundlayouttags.form_<?php echo $tag; ?> = '<?php echo JInboundHelperFilter::escape_js($value); ?>';
<?php endforeach; ?>
(function($){$(document).ready(function(){
<?php if ($this->item->id) : ?>
	$.ajax('../index.php?option=com_jinbound&task=landingpageurl&id=<?php echo (int) $this->item->id; ?>', {
		dataType: 'json',
		success: function(response) {
			if (response) {
				var link = '';
				if (response.error) {
					link = response.error;
				}
				else if (response.sef) {
					link = '<a href="' + response.sef + '" target="_blank">' + response.sef + '</a>';
				}
				$('#jform_alias').closest('.row-fluid').after($('<div class="row-fluid"><div class="span12">' + link + '</div></div>'));
			}
		}
	});
<?php endif; ?>
	var hideSidebar = function() {
		var row = $('#jform_sidebartext').closest('.row-fluid'), d = [3], hide = true, tabs, tab;
		switch($('#jform_layout').find(':checked').val()) {
			case '0':
				d = [];
			case 'A':
				row.show();
				break;
			default:
				row.hide();
				break;
		}
		// check for tabs
		tabs = $('#jinbound_default_tabsTabs');
		tab  = 'li';
		if (!tabs.length)
		{
			tabs = $('#jinbound_default_tabs');
			tab  = 'dt.tabs';
		}
		if ('function' == typeof $().tab)
		{
			$('.nav-tabs a').click(function(e){
				e.preventDefault();
				$(this).tab('show');
			});
		}
		else if ('function' == typeof $().tabs)
		{
			$('#jinbound_default_tabsTabs').tabs("option", "disabled", d);
			hide = false;
		}
		if (hide)
		{
			try
			{
				if (d.length)
				{
					tabs.find(tab)[d[0]].hide();
				}
				else
				{
					tabs.find(tab).show();
				}
			}
			catch (err)
			{
				try {
					console.log(err);
				}
				catch (err2) {}
			}
		}
		
		$('.jinbound_legacy #jform_layout').find('.active').removeClass('active');
		$('.jinbound_legacy #jform_layout').find('.btn-success').removeClass('btn-success');
		$('.jinbound_legacy #jform_layout').find(':checked').next().addClass('active').addClass('btn-success')
	};
	
	hideSidebar();
	$('#jform_layout').find('input').change(hideSidebar);
	$('.jinbound_bootstrap #jform_layout .btn').click(hideSidebar);
	
	$('#jform_formid').change(function(){
		var v = $(this).find(':selected').val(), k = 'form_' + v, h = '';
		if ('undefined' !== typeof window.jinboundlayouttags[k]) {
			h += window.jinboundlayouttags[k];
		}
		$('#jform_template_tags').empty().html(h);
	});
	$('#jform_formid').trigger('change');
	
});})(jQuery);
</script>
