(function( $ ) {

    // Prevent the exchange form submission from pressing the enter key

    $('body').on( 'submit', '.gamipress-points-exchanges-form', function(e) {
        e.preventDefault();

        return false;
    });

    $('body').on( 'keypress', '.gamipress-points-exchanges-form', function(e) {
        return e.keyCode != 13;
    });

    // Handle the exchange form submission through clicking the exchange button

    $('body').on( 'click', '.gamipress-points-exchanges-form .gamipress-points-exchanges-form-submit-button', function(e) {
        e.preventDefault();

        var $this               = $(this);
        var form                = $(this).closest('.gamipress-points-exchanges-form');
        var submit_wrap         = form.find('.gamipress-points-exchanges-form-submit');

        // Ensure response wrap
        if( submit_wrap.find('.gamipress-points-exchanges-form-response').length === 0 )
            submit_wrap.prepend('<div class="gamipress-points-exchanges-form-response" style="display: none;"></div>')

        var response_wrap = submit_wrap.find('.gamipress-points-exchanges-form-response');

        // Disable the submit button
        $this.prop( 'disabled', true );

        // Hide previous notices
        if( response_wrap.length ) {
            response_wrap.slideUp()
        }

        // Show the loading spinner
        submit_wrap.find( '.gamipress-spinner' ).show();

        $.ajax({
            url: gamipress_points_exchanges.ajaxurl,
            method: 'POST',
            // Note: Form already includes a nonce
            data: form.serialize() + '&action=gamipress_points_exchanges_process_exchange',
            success: function( response ) {

                // Add class gamipress-points-exchanges-success on successful exchange, if not will add the class gamipress-points-exchanges-error
                response_wrap.addClass( 'gamipress-points-exchanges-' + ( response.success === true ? 'success' : 'error' ) );

                // Update and show response messages
                response_wrap.html( ( response.data.message !== undefined ? response.data.message : response.data ) );
                response_wrap.slideDown();

                // Restore exchange button on not success
                if( response.success !== true ) {
                    $this.prop( 'disabled', false );
                }

                // Hide the loading spinner
                submit_wrap.find( '.gamipress-spinner' ).hide();

                /**
                 * Event for a points exchange success
                 * Example: $('body').on( 'gamipress_points_exchange_success', '.gamipress-points-exchanges-form', function(e) {});
                 *
                 * @since 1.0.0
                 *
                 * @selector    .gamipress-points-exchanges-form
                 * @event       gamipress_points_exchange_success
                 */
                form.trigger( 'gamipress_points_exchange_success' );

            },
            error: function( response ) {
                // Add class gamipress-points-exchanges-success on successful exchange, if not will add the class gamipress-points-exchanges-error
                response_wrap.addClass( 'gamipress-points-exchanges-error' );

                // Update and show response messages
                response_wrap.html( 'The server has returned an internal error.' );
                response_wrap.slideDown();

                // Restore exchange button on not success
                $this.prop( 'disabled', false );

                // Hide the loading spinner
                submit_wrap.find( '.gamipress-spinner' ).hide();

                /**
                 * Event for a points exchange error
                 * Example: $('body').on( 'gamipress_points_exchange_error', '.gamipress-points-exchanges-form', function(e) {});
                 *
                 * @since 1.0.0
                 *
                 * @selector    .gamipress-points-exchanges-form
                 * @event       gamipress_points_exchange_error
                 */
                form.trigger( 'gamipress_points_exchange_error' );
            }
        });
    });

    function gamipress_points_exchanges_update_points_balances( form ) {

        // Setup vars
        var amount = form.find('input[name="amount"]').val();
        var from = form.find('input[name="from"]').val();
        var to = form.find('input[name="to"]').val();

        // From
        if( from !== '' ) {

            var from_balance = gamipress_points_exchanges_get_user_points_balance( from );
            var from_new_balance = parseInt( from_balance - amount );
            var from_label = gamipress_points_exchanges_get_points_type_label( from );

            form.find('.gamipress-points-exchanges-form-from-details-label').html( from_label );

            // Current from balance
            form.find('.gamipress-points-exchanges-form-from-current-balance-amount').html( from_balance );
            form.find('.gamipress-points-exchanges-form-from-current-balance-points-label').html( from_label );

            // New from balance
            form.find('.gamipress-points-exchanges-form-from-new-balance-amount').html( from_new_balance );
            form.find('.gamipress-points-exchanges-form-from-new-balance-points-label').html( from_label );

            // From amount
            form.find('.gamipress-points-exchanges-form-from-amount-amount').html( amount );
            form.find('.gamipress-points-exchanges-form-from-amount-points-label').html( from_label );

            // Toggle from new balance classes
            if( from_new_balance > 1 ) {
                form.find('.gamipress-points-exchanges-form-from-new-balance-amount').removeClass('gamipress-points-exchanges-negative').addClass('gamipress-points-exchanges-positive');
            } else {
                form.find('.gamipress-points-exchanges-form-from-new-balance-amount').removeClass('gamipress-points-exchanges-positive').addClass('gamipress-points-exchanges-negative');
            }

            // Show from details
            form.find('.gamipress-points-exchanges-form-from-details').show();

        } else {
            // Hide from details
            form.find('.gamipress-points-exchanges-form-from-details').hide();
        }

        // To

        if( to !== '' ) {

            var to_balance = gamipress_points_exchanges_get_user_points_balance( to );
            var to_amount = gamipress_points_exchanges_apply_rate( amount, from, to );
            var to_new_balance = to_balance + to_amount;
            var to_label = gamipress_points_exchanges_get_points_type_label( to );

            form.find('.gamipress-points-exchanges-form-to-details-label').html( to_label );

            // Current to balance
            form.find('.gamipress-points-exchanges-form-to-current-balance-amount').html( to_balance );
            form.find('.gamipress-points-exchanges-form-to-current-balance-points-label').html( to_label );

            // New to balance
            form.find('.gamipress-points-exchanges-form-to-new-balance-amount').html( to_new_balance );
            form.find('.gamipress-points-exchanges-form-to-new-balance-points-label').html( to_label );

            // To amount
            form.find('.gamipress-points-exchanges-form-to-amount-amount').html( to_amount );
            form.find('.gamipress-points-exchanges-form-to-amount-points-label').html( to_label );

            // Show to details
            form.find('.gamipress-points-exchanges-form-to-details').show();

        } else {
            // Hide to details
            form.find('.gamipress-points-exchanges-form-to-details').hide();
        }

        // Rate
        if( from !== '' && to !== '' ) {

            var rate = gamipress_points_exchanges_get_rate( from, to );

            form.find('.gamipress-points-exchanges-form-exchange-rate-from-label').html( from_label );
            form.find('.gamipress-points-exchanges-form-exchange-rate-to-amount').html( rate );
            form.find('.gamipress-points-exchanges-form-exchange-rate-to-label').html( to_label );

            // Show exchange rate and hide the error message
            form.find('.gamipress-points-exchanges-form-exchange-rate').show();
            form.find('.gamipress-points-exchanges-form-exchange-rate-error').hide();

        } else {
            // Hide exchange rate and show an error message
            form.find('.gamipress-points-exchanges-form-exchange-rate').hide();
            form.find('.gamipress-points-exchanges-form-exchange-rate-error').show();
        }

    }

    // On change the amount, update user points balance
    $('body').on('change keyup', '.gamipress-points-exchanges-form-amount-input', function() {

        var form = $(this).closest('.gamipress-points-exchanges-form');
        var amount = parseInt( $(this).val() );

        if( isNaN( amount ) ) {
            amount = 0;
        }

        form.find('input[name="amount"]').val( amount );

        gamipress_points_exchanges_update_points_balances( form );

    })

    // On change from, update user points balance
    $('body').on('change', '.gamipress-points-exchanges-form-from-select', function() {

        var form = $(this).closest('.gamipress-points-exchanges-form');
        var from = $(this).val();
        var to_input = form.find('.gamipress-points-exchanges-form-to-select');
        var to = to_input.val();
        var to_default = to_input.find('option[value=""]').text();
        var options_html = '<option value="">' + to_default + '</option>';

        form.find('input[name="from"]').val( from );

        // On change from update TO options based on available rates
        var rates = gamipress_points_exchanges_get_points_type_exchange_rates( from );

        $.each( rates, function( points_type, rate ) {

            // If there is any rate, then add the points type as option
            if( parseFloat( rate ) !== 0 ) {
                options_html += '<option value="' + points_type + '">' + gamipress_points_exchanges_get_points_type_label( points_type ) + '</option>'
            }

        } );

        // Update TO points type options html
        to_input.html(options_html);

        // Update TO points type value
        if( to_input.find('option[value="' + to + '"]').length ) {
            to_input.val(to);
        } else {
            to_input.val('').trigger('change');
        }

        gamipress_points_exchanges_update_points_balances( form );


    });

    // On change to, update user points balance
    $('body').on('change', '.gamipress-points-exchanges-form-to-select', function() {

        var form = $(this).closest('.gamipress-points-exchanges-form');
        var to = $(this).val();

        form.find('input[name="to"]').val( to );

        gamipress_points_exchanges_update_points_balances( form );


    });

})( jQuery );