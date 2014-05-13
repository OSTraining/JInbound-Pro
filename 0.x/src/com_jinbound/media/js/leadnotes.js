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
						lead_id  : fieldset.find('input[name=lead_id]').val()
					,	text     : fieldset.find('textarea.leadnotes-new-text').val()
					,	asset_id : 0
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
						var container = $this.closest('.leadnotes');
						var notes     = container.find('.leadnotes-notes');
						var count     = container.find('.leadnotes-count');
						var single    = $('#jinbound_leadnotes_table');
						notes.empty();
						if (single && single.length) {
							single.empty();
						}
						count.text(parseInt(response.notes.length, 10));
						for (var i = 0, n = response.notes.length; i < n; i++) {
							var row = $('<div class="leadnote alert-message"><span class="label"></span><div class="leadnote-text"></div></div>');
							row.find('.label').text(response.notes[i].created);
							row.find('.leadnote-text').text(response.notes[i].text);
							notes.append(row);
							if (single && single.length) {
								var trow = $('<tr><td><span class="label"></span></td><td class="note"></td></tr>');
								trow.find('.label').text(response.notes[i].created);
								trow.find('.note').text(response.notes[i].text);
								single.find('tbody').append(trow);
							}
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
		var deleteNote = function(e) {
			var $this  = $(this);
			var note   = $this.closest('.leadnote');
			var data   = {
				task    : 'note.delete'
			,	format  : 'json'
			,	id      : [parseInt($this.attr('data-noteid'), 10)]
			,	leadid  : parseInt($this.attr('data-leadid'), 10)
			}
			data[window.jinbound_leadnotes_token] = 1;
			$.ajax('index.php?option=com_jinbound', {
				data     : data
			,	dataType : 'json'
			,	type     : 'post'
			,	success  : function(response) {
					var container = $this.closest('.leadnotes');
					var notes     = container.find('.leadnotes-notes');
					var count     = container.find('.leadnotes-count');
					var single    = $('#jinbound_leadnotes_table');
					notes.empty();
					if (single && single.length) {
						single.empty();
					}
					count.text(parseInt(response.notes.length, 10));
					for (var i = 0, n = response.notes.length; i < n; i++) {
						var row = $('<div class="leadnote alert" data-stopPropagation="true"><a class="close" data-dismiss="alert" data-noteid="' + response.notes[i].id + '" data-leadid="' + response.notes[i].lead_id + '" href="#" onclick="(function(){return confirm(Joomla.JText._(\'COM_JINBOUND_CONFIRM_DELETE\'));})();">&times;</a><span class="label" data-stopPropagation="true">' + response.notes[i].created + '</span> ' + response.notes[i].author + '<div class="leadnote-text" data-stopPropagation="true">' + response.notes[i].text + '</div></div>');
						notes.append(row);
						if (single && single.length) {
							var trow = $('<tr><td><span class="label"></span></td><td class="note"></td></tr>');
							trow.find('.label').text(response.notes[i].created);
							trow.find('.note').text(response.notes[i].text);
							single.find('tbody').append(trow);
						}
					}
					container.on('click', '.close', deleteNote);
					container.find('textarea').val('');
				}
			});
		};
		$('.leadnotes-notes .leadnote').on('click', '.close', deleteNote);
	});
})(jQuery);
