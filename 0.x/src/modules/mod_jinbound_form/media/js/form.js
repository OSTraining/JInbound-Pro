/**
 * form.js
 * 
 * This script should be embedded in 3rd party sites using the following markup:
 * <script src="{path}/form.js" data-j-ref="jinbound" data-j-form="{id}"></script>
 */

// based on http://dustindiaz.com/smallest-domready-ever
function JInboundRemoteReady(f){/in/.test(document.readyState)?setTimeout('JInboundRemoteReady('+f+')',9):f()}

/**
 * JInbound object
 * @returns {JInbound}
 */
var JInboundRemote = function()
{
	this.__constructor();
};

/**
 * JInbound object constructor
 * @returns {undefined}
 */
JInboundRemote.prototype.__constructor = function()
{
	// get all the tags
	var tags = document.getElementsByTagName('script'), i = 0, n = tags.length;
	// find the script tags that have correct data-ref attr
	for (; i < n; i++)
	{
		// undefined or incorrect data-ref means it isn't jinbound
		if (!tags[i].getAttribute('data-j-form') && !tags[i].getAttribute('data-j-module'))
		{
			continue;
		}
		this.initForm(tags[i]);
	}
};

JInboundRemote.prototype.ajaxError = function(responseText, errorCode)
{
	alert('Error ' + errorCode);
};

JInboundRemote.prototype.initForm = function(tag)
{
	var url = this.getUrlFromTag(tag), $this = this;
	this.ajax({
		url: url,
		success: function(responseText) {
			try
			{
				var json = JSON.parse(responseText);
			}
			catch (err)
			{
				$this.ajaxError(0, err);
				return;
			}
			if (json && json.data)
			{
				tag.outerHTML = json.data.form;
				if (json.data.style)
				{
					var link = document.createElement('link');
					link.type = 'text/css';
					link.rel  = 'stylesheet';
					link.href = json.data.style;
					document.head.appendChild(link);
				}
			}
		}
	});
};

JInboundRemote.prototype.getUrlFromTag = function(tag)
{
	var f = tag.getAttribute('data-j-form') || tag.getAttribute('data-j-module')
	,   o = tag.getAttribute('data-j-option')
	,   r = tag.getAttribute('data-j-return');
	return tag.getAttribute('src').replace(/media\/mod_jinbound_form\/js\/form\.js$/, 'index.php?option=com_' + (o ? o + '&task=' : '') + 'ajax&format=json&module=jinbound_form&method=getForm&id=' + f + (r ? '&return_url=' + r : ''));
}

// http://stackoverflow.com/questions/8567114/how-to-make-an-ajax-call-without-jquery
JInboundRemote.prototype.ajax = function(options)
{
	var $this = this, xmlhttp;
	
	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {
		// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState !== XMLHttpRequest.DONE) {
			return;
		}
		if (xmlhttp.status == 200) {
			if ('function' == typeof options.success) options.success(xmlhttp.responseText);
		}
		else {
			if ('function' == typeof options.error) options.error(xmlhttp.status, xmlhttp.responseText);
			else $this.ajaxError(xmlhttp.status, xmlhttp.responseText);
		}
	}
	
	xmlhttp.open(options.type || "GET", options.url, true);
	xmlhttp.send();
};

// init
JInboundRemoteReady(function(){new JInboundRemote()});