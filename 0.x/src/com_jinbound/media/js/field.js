/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_xml_copyright@
 */

(function($){
	var fixButtons = function(parent) {
		var els = $(parent).find('.jinboundfields_ordering .btn'), i = 0;
		$.each(els, function (idx, el) {
			$(el).removeAttr('disabled');
			if (0 == i || (els.length - 1) == i) {
				$(el).attr('disabled', 'disabled');
			}
			i++;
		});
	};
	var moveF = function(ev, dir) {
		try {
			var p = $(ev.target.parentNode.parentNode.parentNode), t, m;
			switch (dir) {
				case 'before':
					t = $(p).prev('tr');
					m = 'insertBefore';
					break;
				case 'after':
					t = $(p).next('tr');
					m = 'insertAfter';
					break;
				default:
					throw Joomla.JText._('COM_JINBOUND_JINBOUNDFIELD_ERROR_BAD_DIRECTION');
					return false;
			}
			if (t) {
				$(p)[m](t);
				return true;
			}
			throw Joomla.JText._('COM_JINBOUND_JINBOUNDFIELD_ERROR_CANNOT_FIND_AVAILABLE_ITEM');
		}
		catch (err) {
			return false;
		}
	};
	var moveUp = function(ev) {
		return moveF(ev, 'before');
	};
	var moveDown = function(ev) {
		return moveF(ev, 'after');
	};
	$(document).ready(function() {
		// we want this to run for each instance
		var fields = $('.jinboundfields');
		if (!fields.length) {
			alert(Joomla.JText._('COM_JINBOUND_JINBOUNDFORMFIELD_ERROR'));
			return;
		}
		// loop over each of our fields (there should only be one, but hey! who knows?)
		for (var i=0; i<fields.length; i++) {
			var field = fields[i];
			fixButtons(field);
			$.each($(field).find('.jinboundfields_ordering_up'), function(idx, el) {
				$(el).on('click', function (ev) {
					moveUp(ev);
					fixButtons(field);
				});
			});
			$.each($(field).find('.jinboundfields_ordering_down'), function(idx, el) {
				$(el).on('click', function (ev) {
					moveDown(ev);
					fixButtons(field);
				});
			});
			var sortable = function(el, options) {
				if ('undefined' == typeof $(el).sortable) {
					return;
				}
				try {
					if (options.onComplete) {
						options.stop = options.create = options.onComplete;
					}
					$(el).sortable(options);
					$(el).disableSelection();
				}
				catch (err) {}
				return;
			};
			sortable($(field).find('tbody'), {
				clone: false
			,	revert: {duration: 500}
			,	opacity: 0.7
			,	constrain: false
			,	onComplete: function(e) {
					fixButtons(field);
				}
			});
		}
	});
})(jQuery);
