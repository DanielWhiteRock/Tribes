(function( $ ) {

    var $body = $( 'body' );

    // Listen for unlock post with points button click
    $body.on( 'click', '.gamipress-restrict-content-unlock-post-with-points-button, .gamipress-restrict-content-unlock-content-with-points-button', function(e) {

        var button = $(this);
        var submit_wrap;
        var data = {};

        if( button.hasClass('gamipress-restrict-content-unlock-post-with-points-button') ) {

            submit_wrap = button.closest('.gamipress-restrict-content-unlock-post-with-points');

            // Unlock post data
            data = {
                action: 'gamipress_restrict_content_unlock_post_with_points',
                nonce: gamipress_restrict_content.nonce,
                post_id: button.data('id')
            };
        } else {

            submit_wrap = button.closest('.gamipress-restrict-content-unlock-content-with-points');

            // Unlock content data
            data = {
                action: 'gamipress_restrict_content_unlock_content_with_points',
                nonce: gamipress_restrict_content.nonce,
                content_id: button.data('id'),
                post_id: button.data('post-id')
            };
        }

        var spinner = submit_wrap.find('.gamipress-spinner');

        // Disable the button
        button.prop( 'disabled', true );

        // Hide previous notices
        if( submit_wrap.find('.gamipress-restrict-content-response').length ) {
            submit_wrap.find('.gamipress-restrict-content-response').slideUp()
        }

        // Show the spinner
        spinner.show();

        $.ajax( {
            url: gamipress_restrict_content.ajaxurl,
            method: 'POST',
            dataType: 'json',
            data: data,
            success: function( response ) {

                // Ensure response wrap
                if( submit_wrap.find('.gamipress-restrict-content-response').length === 0 ) {
                    submit_wrap.prepend('<div class="gamipress-restrict-content-response gamipress-notice" style="display: none;"></div>')
                }

                var response_wrap = submit_wrap.find('.gamipress-restrict-content-response');

                // Add class gamipress-notice-success on successful unlock, if not will add the class gamipress-notice-error
                response_wrap.addClass( 'gamipress-notice-' + ( response.success === true ? 'success' : 'error' ) );

                // Update and show response messages
                var message = ( response.data.message !== undefined ? response.data.message : response.data );

                response_wrap.html( message );
                response_wrap.slideDown();

                // Hide the spinner
                spinner.hide();

                if( response.success === true ) {
                    // Hide the button
                    button.slideUp();

                    if(  response.data.redirect !== undefined ) {
                        // Redirect to the given url
                        window.location.href = response.data.redirect;
                    } else {
                        // Refresh the page
                        location.reload( true );
                    }

                } else {
                    // Enable the button
                    button.prop( 'disabled', false );
                }
            }
        });
    });

})( jQuery );