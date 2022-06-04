(function( $ ) {

    var $body = $( 'body' );

    // Listen for unlock post with points button click
    $body.on( 'click', '.gamipress-restrict-unlock-access-with-points-button', function(e) {

        var button = $(this);
        var submit_wrap;

        submit_wrap = button.closest('.gamipress-restrict-unlock-access-with-points');

        var spinner = submit_wrap.find('.gamipress-spinner');

        // Disable the button
        button.prop( 'disabled', true );

        // Hide previous notices
        if( submit_wrap.find('.gamipress-restrict-unlock-response').length )
            submit_wrap.find('.gamipress-restrict-unlock-response').slideUp();

        // Show the spinner
        spinner.show();

        $.ajax( {
            url: gamipress_restrict_unlock.ajaxurl,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'gamipress_restrict_unlock_access_with_points',
                nonce: gamipress_restrict_unlock.nonce,
                post_id: button.data('id')
            },
            success: function( response ) {

                // Ensure response wrap
                if( submit_wrap.find('.gamipress-restrict-unlock-response').length === 0 )
                    submit_wrap.prepend('<div class="gamipress-restrict-unlock-response gamipress-notice" style="display: none;"></div>')

                var response_wrap = submit_wrap.find('.gamipress-restrict-unlock-response');

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

                    // Redirect to the given url, if not, refresh the page
                    if( response.data.redirect !== undefined )
                        window.location.href = response.data.redirect;
                    else
                        location.reload( true ); // Refresh the page

                } else {
                    // Enable the button
                    button.prop( 'disabled', false );
                }
            }
        });
    });

})( jQuery );