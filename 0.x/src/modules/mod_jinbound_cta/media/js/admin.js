(function($){
	$(document).ready(function(){
		var modes = ['', 'alt_'];

		var getModeElementId = function(type, alt) {
			return 'jform_params_cta_' + alt + 'mode_' + type;
		};

		var isUndefined = function(what) {
			return 'undefined' === typeof what;
		};

		$.each(modes, function (i, mode) {
			var select = $('#jform_params_cta_' + mode + 'mode');
			if (isUndefined(select)) return;
			select.change(function(e){
				var enabled = getModeElementId($(this).find(':selected').val(), mode);
				if (isUndefined(enabled)) return;
				$.each($(this).find('option'), function(idx, el) {
					var current = getModeElementId($(el).val(), mode);
					var elem = $('#' + current);
					if (isUndefined(elem)) return;
					if (current === enabled) {
						elem.removeAttr('disabled').trigger('liszt:updated');
						return;
					}
					elem.attr('disabled', 'disabled').trigger('change').trigger('liszt:updated');
				});
			}).trigger('change').trigger('liszt:updated');
		});
	});
})(jQuery);