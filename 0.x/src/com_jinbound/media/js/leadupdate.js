/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

window.jinbound_leadupdate_token = false;

(function($) {
	$(function() {
		$('input').each(function(idx, el) {
			if (window.jinbound_leadupdate_token) {
				return;
			}
			if (1 == $(el).val() && 32 == ($(el).attr('name')).toString().length) {
				window.jinbound_leadupdate_token = $(el).attr('name');
			}
		});
		$('.change_priority, .change_status').each(function(idx, el) {
			$(el).change(function(e) {
				var isPriority = $(el).hasClass('change_priority'), data = {
					task        : 'contact.' + (isPriority ? 'priority' : 'status')
				,	id          : $(el).attr('data-id')
				,	campaign_id : $(el).attr('data-campaign')
				,	value       : $(el).val()
				,	format      : 'json'
				};
				data[window.jinbound_leadupdate_token] = 1;
				$.ajax('index.php?option=com_jinbound', {
					type     : 'post',
					data     : data,
					dataType : 'json',
					success  : function(response) {
						$(document.body).trigger('jinboundleadupdate', [response]);
					}
				});
			});
		});
	});
})(jQuery);
