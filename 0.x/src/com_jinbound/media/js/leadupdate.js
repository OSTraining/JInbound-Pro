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
				var data = {
					task  : 'lead.' + ($(el).hasClass('change_priority') ? 'priority' : 'status')
				,	id    : ($(el).attr('name')).toString().replace(/[^0-9]/g, '')
				,	value : $(el).val()
				,	tmpl  : 'component'
				};
				data[window.jinbound_leadupdate_token] = 1;
				$.ajax('index.php?option=com_jinbound', {
					type    : 'post',
					data    : data,
					success : function(response) {
						//console.log(response);
					}
				});
			});
		});
	});
})(jQuery);
