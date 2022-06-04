(function( $ ) {

    // Toggle button
    $('body').on('click', '.gamipress-submissions-toggle-button', function( e ) {
        var $this = $(this);
        var form = $this.next('.gamipress-submissions-form');

        form.slideDown('fast');
        $this.slideUp('fast');
    });

    // Submit button
    $('body').on('click', '.gamipress-submissions-button', function( e ) {
        e.preventDefault();

        var $this = $(this);

        // Bail if button disabled
        if( $this.prop('disabled') ) {
            return false;
        }

        var form = $this.closest('.gamipress-submissions-form');
        var spinner = form.find('.gamipress-spinner');
        var post_id = form.data('id');
        var notes_wrap = form.find('.gamipress-submissions-notes-wrap');

        // Ensure response wrap
        if( form.find('.gamipress-submissions-form-response').length === 0 ) {
            form.append('<div class="gamipress-submissions-form-response gamipress-notice" style="display: none;"></div>');
        }

        var response_wrap = form.find('.gamipress-submissions-form-response');

        // Check if notes is active
        if( notes_wrap.length ) {

            var notes = form.find('.gamipress-submissions-notes').val();

            // If notes enabled, then require to the user to provide any note
            if( notes.length === 0 ) {
                // Add class gamipress-notice-error and show the error message
                response_wrap.addClass( 'gamipress-notice-error' );
                // Update and show response messages
                response_wrap.html( gamipress_submissions.notes_error );
                response_wrap.slideDown('fast');
                return false;
            }

        }

        // Hide response wrap an ensure to remove any error class
        response_wrap.slideUp('fast');
        response_wrap.removeClass( 'gamipress-notice-error' );
        response_wrap.html( '' );

        // Disable button
        $this.prop('disabled', true);

        // Show the spinner
        spinner.show();

        $.ajax( {
            url: gamipress_submissions.ajaxurl,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'gamipress_submissions_process_submission',
                nonce: gamipress_submissions.nonce,
                post_id: post_id,
                notes: ( notes_wrap.length ? notes : '' )
            },
            success: function( response ) {

                // Add class gamipress-notice-success on successful unlock, if not will add the class gamipress-notice-error
                response_wrap.addClass( 'gamipress-notice-' + ( response.success === true ? 'success' : 'error' ) );

                // Update and show response message
                response_wrap.html( response.data.message );
                response_wrap.slideDown();

                // Hide the spinner
                spinner.hide();

                if( response.success === true ) {

                    // Hide the button
                    $this.slideUp('fast');

                    // Hide the notes wrap
                    if( notes_wrap.length ) {
                        notes_wrap.slideUp('fast');
                    }

                } else {

                    // Enable the button
                    $this.prop( 'disabled', false );

                }

            }
        });
    });

})( jQuery );