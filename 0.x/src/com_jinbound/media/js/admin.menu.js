window.addEvent('load', function(){
	var select = document.getElementById('jform_template_style_id'), template_id = 0;
	if (!select) {
		return;
	}
	for (var i = 0, n = select.childNodes.length, y; i < n; i++) {
		y = select.childNodes[i];
		if (!y.tagName) {
			continue;
		}
		if ('optgroup' != y.tagName.toLowerCase()) {
			continue;
		}
		if ('jinbound' != y.getAttribute('label').toString().toLowerCase()) {
			continue;
		}
		for (var j = 0, o = y.childNodes.length, z; j < o; j++) {
			z = y.childNodes[j];
			if (!z.tagName) {
				continue;
			}
			if ('option' != z.tagName.toLowerCase()) {
				continue;
			}
			template_id = parseInt(z.getAttribute('value').toString(), 10);
			break;
		}
		if (template_id) {
			break;
		}
	}
	if (template_id != select.options[select.selectedIndex].value && document.getElementById('jform_link').getAttribute('value').toString().match(/^index.php\?option=com_jinbound/)) {
		var id_input  = document.getElementById('jform_id');
		var form      = document.getElementById('item-form');
		var id_action = false;
		if (form) {
			var action_vars = form.getAttribute('action').split('?')[1].split('&')
			for (var i = 0; i < action_vars.length; i++) {
				var pair = action_vars[i].split("=");
				if ('id' == pair[0]) {
					if (0 < parseInt(pair[1],10)) {
						id_action = parseInt(pair[1],10);
					}
					break;
				}
			}
		}
		if ((id_input && 0 < id_input.value) || (id_action)) {
			alert(Joomla.JText._('COM_JINBOUND_MENU_NOT_SET_TO_USE_JINBOUND_TEMPLATE'));
		}
		else {
			console.log(select);
			console.log(select.value);
			select.value = template_id;
			try {
				jQuery('#jform_template_style_id').trigger('liszt:updated');
			}
			catch (err) {
				// do nothing, probably 2.5
			}
			console.log(select);
			console.log(select.value);
		}
	}
});
