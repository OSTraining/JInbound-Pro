/** 
 * popup.legacy.js
 */

var JInboundPopupIntervals = {};

(function()
{	
	function isScrolledIntoView(elem)
	{
		var docViewTop = window.getScroll().y;
		var docViewBottom = docViewTop + window.getSize().y;

		var elemTop = elem.getPosition().y;
		var elemBottom = elemTop + elem.getSize().y;

		return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
	}
	
	function createCookie(name, value, days)
	{
		var expires;

		if (days)
		{
			var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			expires = "; expires=" + date.toGMTString();
		}
		else
		{
				expires = "";
		}
		document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
	}
	
	function readCookie(name)
	{
		var nameEQ = encodeURIComponent(name) + "=";
		var ca = document.cookie.split(';');
		for (var i = 0; i < ca.length; i++)
		{
			var c = ca[i];
			while (c.charAt(0) === ' ') c = c.substring(1, c.length);
			if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
		}
		return null;
	}
	
	window.addEvent('load', function()
	{
		Array.each($$('.mod_jinbound_popup_container'), function(el)
		{
			el.getChildren('.hide').set('styles', {display: 'none'});
			var key = 'mod_' + el.getAttribute('data-moduleid');
			if (readCookie(key))
			{
				return;
			}
			JInboundPopupIntervals[key] = setInterval(function()
			{
				if (isScrolledIntoView(el))
				{
					var form = el.getElement('.mod_jinbound_popup_form');
					if (form)
					{
						form.set('styles', {display: 'block'});
						SqueezeBox.setContent('adopt', form);
						createCookie(key, 1, 9999);
					}
					clearInterval(JInboundPopupIntervals[key]);
				}
			}, 500);
		});
	});
})();
