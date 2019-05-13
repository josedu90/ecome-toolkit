(function ($) {
    "use strict";

    window.addEventListener('load', function () {
        setTimeout(function () {
            $('.submit-newsletter').on('click', function (e) {
                var thisWrap = $(this).closest('.newsletter-form-wrap'),
                    email    = thisWrap.find('input[name="email"]').val(),
                    data     = {
                        action: 'submit_mailchimp_via_ajax',
                        email: email
                    }
                if ( thisWrap.hasClass('processing') ) {
                    e.preventDefault();
                }
                thisWrap.addClass('processing');
                thisWrap.parent().find('.return-message').remove();
                $.post(toolkit_mailchimp.ajaxurl, data, function (response) {
                    if ( $.trim(response[ 'success' ]) == 'yes' ) {
                        thisWrap.parent().append('<div class="return-message bg-success">' + response[ 'message' ] + '</div>');
                        thisWrap.find('input[name="email"]').val('');
                    }
                    else {
                        thisWrap.parent().append('<div class="return-message bg-danger">' + response[ 'message' ] + '</div>');
                    }
                    thisWrap.removeClass('processing');
                });
                e.preventDefault();
            })
        }, 400);
    }, false);

})(jQuery);