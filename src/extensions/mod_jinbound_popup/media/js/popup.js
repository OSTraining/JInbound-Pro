var JInboundPopupIntervals = {};

(function($) {
    function isScrolledIntoView(elem) {
        var $elem = $(elem);
        var $window = $(window);

        var docViewTop = $window.scrollTop();
        var docViewBottom = docViewTop + $window.height();

        var elemTop = $elem.offset().top;
        var elemBottom = elemTop + $elem.height();

        return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
    }

    function createCookie(name, value, days) {
        var expires;

        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
        }
        else {
            expires = "";
        }
        document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
    }

    function readCookie(name) {
        var nameEQ = encodeURIComponent(name) + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
        }
        return null;
    }

    $(document).ready(function() {
        $.each($('.mod_jinbound_popup_container'), function(idx, el) {
            var key = 'mod_' + $(el).attr('data-moduleid');
            if (readCookie(key)) {
                return;
            }
            JInboundPopupIntervals[key] = setInterval(function() {
                if (isScrolledIntoView(el)) {
                    var form = $(el).children().children().first();
                    if (form.length) {
                        SqueezeBox.setContent('adopt', form[0]);
                        createCookie(key, 1, 9999);
                    }
                    clearInterval(JInboundPopupIntervals[key]);
                }
            }, 500);
        });
    });
})(jQuery);
