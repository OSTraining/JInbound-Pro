(function(){
	var statusUrl = 'index.php?option=com_jinbound&task=install.status&format=json';
	var status = document.getElementById('jinbound_postinstall_status');
	var success = function(response) {
		if (status) {
			if (response && response.html) {
				status.innerHTML = response.html;
			}
			else if (response && response.error) {
				status.innerHTML = response.error;
			}
			else {
				console.log(response);
			}
		}
	};
	if ('undefined' != typeof jQuery) {
		(function($){
			$.ajax(statusUrl, {
				dataType: 'json',
				success: success
			});
		})(jQuery);
	}
	else {
		new Request.JSON({
			url: statusUrl,
			onComplete: success
		}).send();
	}
	if (status) {
		var css = document.createElement("link");
		css.setAttribute("rel", "stylesheet");
		css.setAttribute("type", "text/css");
		css.setAttribute("href", "../media/jinbound/css/install.css");
		document.getElementsByTagName("head")[0].appendChild(css);
	}
})();
