(function($) {
    $(document).ready(function() {
        try {
            $(function() {
                if (!document.getElementById('jinbound_component')) {
                    $('.jinbound_component').attr('id', 'jinbound_component');
                }
            });
        }
        catch (err) {
            if (console && console.log) {
                console.log(err);
            }
        }
        $('#jinbound_landing_page_form .required').each(function(idx, el) {
            $(el).attr('required', 'required');
            $(el).attr('aria-required', 'true');
        });
        $('#jinbound_landing_page_form').submit(function(e) {
            if (!document.formvalidator.isValid(document.getElementById('jinbound_landing_page_form'))) {
                $('html, body').animate({
                    scrollTop: $("#jinbound_landing_page_form .invalid").first().offset().top
                }, 1000);
                e.stopPropagation();
                e.preventDefault();
                return false;
            }
        });
    });
})(jQuery);
