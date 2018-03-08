/**
 * form.js
 *
 * This script should be embedded in 3rd party sites using the following markup:
 * <script src="{path}/form.js" data-j-ref="jinbound" data-j-module="{id}"></script>
 */

// based on http://dustindiaz.com/smallest-domready-ever
function JInboundRemoteReady(f) {
    /in/.test(document.readyState) ? setTimeout('JInboundRemoteReady(' + f + ')', 9) : f()
}

/**
 * JInbound object
 * @returns {JInboundRemote}
 */
var JInboundRemote = function() {
    this.__constructor();
};

/**
 * JInboundRemote object constructor
 * @returns {undefined}
 */
JInboundRemote.prototype.__constructor = function() {
    // get all the tags
    var tags = document.getElementsByTagName('script'), i = 0, n = tags.length;
    // find the script tags that have correct data-ref attr
    for (; i < n; i++) {
        // undefined or incorrect data-ref means it isn't jinbound
        if (!tags[i].getAttribute('data-j-form') && !tags[i].getAttribute('data-j-module')) {
            continue;
        }
        this.initForm(tags[i]);
    }
};

JInboundRemote.prototype.ajaxError = function(responseText, errorCode) {
    alert('Error ' + errorCode);
};

JInboundRemote.prototype.initForm = function(tag) {
    var url = this.getUrlFromTag(tag), $this = this;
    this.ajax({
        url    : url,
        success: function(responseText) {
            try {
                var json = JSON.parse(responseText);
            }
            catch (err) {
                $this.ajaxError(0, err);
                return;
            }
            if (json) {
                if (json.data) {
                    var nostyle = tag.getAttribute('data-j-nostyle');
                    var noscript = tag.getAttribute('data-j-noscript');
                    tag.outerHTML = json.data.form;
                    if (!nostyle && json.data.style) {
                        var link = document.createElement('link');
                        link.type = 'text/css';
                        link.rel = 'stylesheet';
                        link.href = json.data.style;
                        document.head.appendChild(link);
                    }
                    var moo = false, j = false;
                    if (!noscript && json.data.scripts) {
                        for (var i = 0, n = json.data.scripts.length; i < n; i++) {
                            moo = moo ? moo : json.data.scripts[i].src.match(/mootools/i);
                            j = j ? j : json.data.scripts[i].src.match(/jquery/i);
                            var script = document.createElement('script');
                            script.type = json.data.scripts[i].mime;
                            script.src = $this.remoteRelativeUrlToAbsolute(tag, json.data.scripts[i].src);
                            if ('undefined' !== typeof json.data.scripts[i].defer) {
                                script.defer = json.data.scripts[i].defer;
                            }
                            if ('undefined' !== typeof json.data.scripts[i].async) {
                                script.async = json.data.scripts[i].async;
                            }
                            document.head.appendChild(script);
                        }
                    }
                    if (!noscript && json.data.script) {
                        try {
                            setTimeout(function() {
                                if (moo && 'function' === typeof $) {
                                    $.prototype.load = function(f) {
                                        console.log('mootools.load');
                                        f();
                                    };
                                }
                                if (j && 'function' === typeof jQuery) {
                                    jQuery.prototype.ready = function(f) {
                                        console.log('jquery.ready');
                                        f(jQuery);
                                    }
                                    jQuery.prototype.load = function(f) {
                                        console.log('jquery.load');
                                        f(jQuery);
                                    }
                                }
                                eval(json.data.script);
                            }, 1500);
                        }
                        catch (err) {
                            $this.ajaxError(0, err);
                        }
                    }
                }
                else if (false === json.success && !tag.getAttribute('data-j-quiet')) {
                    var msg = json.message + '';
                    if (json.messages && json.messages.length) {
                        msg = '<ul><li>' + json.messages.join('</li><li>') + '</li></ul>';
                    }
                    tag.outerHTML = '<div class="alert alert-error">' + msg + '</div>';
                }
            }
        }
    });
};

JInboundRemote.prototype.getUrlFromTag = function(tag) {
    var f   = tag.getAttribute('data-j-form') || tag.getAttribute('data-j-module')
        , o = tag.getAttribute('data-j-option')
        , r = tag.getAttribute('data-j-return');
    return tag.getAttribute('src').replace(/media\/mod_jinbound_form\/js\/form\.js$/, 'index.php?option=com_' + (o ? o.replace(/^com_/, '') + '&task=' : '') + 'ajax&format=json&module=jinbound_form&method=getForm&id=' + f + (r ? '&return_url=' + r : ''));
};

JInboundRemote.prototype.remoteRelativeUrlToAbsolute = function(tag, relative) {
    if (relative.match(/^(https?\:)?\/\//)) {
        return relative;
    }
    return tag.getAttribute('src').replace(/\/media\/mod_jinbound_form\/js\/form\.js$/, relative);
};

// http://stackoverflow.com/questions/8567114/how-to-make-an-ajax-call-without-jquery
JInboundRemote.prototype.ajax = function(options) {
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

    try {
        xmlhttp.open(options.type || "GET", options.url, true);
        xmlhttp.withCredentials = true;
        xmlhttp.send();
    }
    catch (err) {
        // cannot load - security policy in effect?
        if ('undefined' !== typeof console && 'function' === typeof console.log) {
            console.log(err);
        }
    }
};

// init
JInboundRemoteReady(function() {
    new JInboundRemote()
});
