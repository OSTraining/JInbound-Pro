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
		alert(Joomla.JText._('COM_JINBOUND_MENU_NOT_SET_TO_USE_JINBOUND_TEMPLATE'));
	}
});
