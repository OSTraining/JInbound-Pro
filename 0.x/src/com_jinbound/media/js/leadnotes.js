/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

window.jinbound_leadnotes_token = false;

(function($) {
	$(function() {
		$('input').each(function(idx, el) {
			if (window.jinbound_leadnotes_token) {
				return;
			}
			if (1 == $(el).val() && 32 == ($(el).attr('name')).toString().length) {
				window.jinbound_leadnotes_token = $(el).attr('name');
			}
		});
		
		$('.leadnotes .leadnotes-submit').each(function(idx, el) {
			$(el).click(function(e) {
				var $this    = $(this);
				var fieldset = $this.closest('fieldset');
				var data = {
					jform   : {
						lead_id : fieldset.find('input[name=lead_id]').val()
					,	text    : fieldset.find('textarea.leadnotes-new-text').val()
					}
				,	task    : 'note.save'
				,	format  : 'json'
				,	id      : 0
				}
				if (!data.jform.text) {
					return false;
				}
				data[window.jinbound_leadnotes_token] = 1;
				$.ajax('index.php?option=com_jinbound', {
					data     : data
				,	dataType : 'json'
				,	type     : 'post'
				,	success  : function(response) {
						var container = $this.closest('.leadnotes')
						var notes     = container.find('.leadnotes-notes');
						notes.empty();
						for (var i = 0, n = response.notes.length; i < n; i++) {
							var row = $('<div class="leadnote"><span class="label"></span><div class="leadnote-text"></div></div>');
							row.find('.label').text(response.notes[i].created);
							row.find('.leadnote-text').text(response.notes[i].text);
							notes.append(row);
						}
						container.find('textarea').val('');
					}
				});
			});
		});
		$('.leadnotes .dropdown-menu').on('contextmenu', '[data-stopPropagation]', function(e) {
			console.log('contextmenu');
			e.stopPropagation();
		});
		$('.leadnotes .dropdown-menu').on('click', '[data-stopPropagation]', function(e) {
			console.log('click');
			e.stopPropagation();
		});
		$('.leadnotes .dropdown-menu').on('dblclick', '[data-stopPropagation]', function(e) {
			console.log('dblclick');
			e.stopPropagation();
		});
	});
})(jQuery);
