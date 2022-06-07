(function( $ ) {

    // Prevent the purchase form submission from pressing the enter key

    $('body').on( 'submit', '.gamipress-purchases-form', function(e) {
        e.preventDefault();

        return false;
    });

    $('body').on( 'keypress', '.gamipress-purchases-form', function(e) {
        return e.keyCode != 13;
    });

    // Handle the purchase form submission through clicking the purchase button

    $('body').on( 'click', '.gamipress-purchases-form .gamipress-purchases-form-submit-button', function(e) {
        e.preventDefault();

        var $this               = $(this);
        var form                = $(this).closest('.gamipress-purchases-form');
        var acceptance_input    = form.find('.gamipress-purchases-form-acceptance-input');
        var submit_wrap         = form.find('.gamipress-purchases-form-submit');

        // Ensure response wrap
        if( submit_wrap.find('.gamipress-purchases-form-response').length === 0 ) {
            submit_wrap.prepend('<div class="gamipress-purchases-form-response" style="display: none;"></div>')
        }

        var response_wrap = submit_wrap.find('.gamipress-purchases-form-response');

        // Acceptance check
        if( acceptance_input.length && ! acceptance_input.prop('checked') ) {
            response_wrap.addClass( 'gamipress-purchases-error' );
            response_wrap.html( gamipress_purchases.acceptance_error );
            response_wrap.slideDown();
            return;
        }

        // Disable the submit button
        $this.prop( 'disabled', true );

        // Hide previous notices
        if( response_wrap.length ) {
            response_wrap.slideUp()
        }

        // Show the loading spinner
        submit_wrap.find( '.gamipress-spinner' ).show();

        /**
         * Event before perform a purchase request
         * Example:  $('body').on( 'gamipress_purchases_before_purchase_request', '.gamipress-purchases-form', function(e) {});
         *
         * @since 1.0.5
         *
         * @selector    .gamipress-purchases-form
         * @event       gamipress_purchases_before_purchase_request
         */
        form.trigger( 'gamipress_purchases_before_purchase_request' );

        $.ajax({
            url: gamipress_purchases.ajaxurl,
            method: 'POST',
            data: form.serialize() + '&action=gamipress_purchases_process_purchase',
            success: function( response ) {

                // Add class gamipress-purchases-success on successful purchase, if not will add the class gamipress-purchases-error
                response_wrap.addClass( 'gamipress-purchases-' + ( response.success === true ? 'success' : 'error' ) );

                // Update and show response messages
                response_wrap.html( ( response.data.message !== undefined ? response.data.message : response.data ) );
                response_wrap.slideDown();

                // Restore purchase button on not success
                if( response.success !== true ) {
                    $this.prop( 'disabled', false );
                }

                // Hide the loading spinner
                submit_wrap.find( '.gamipress-spinner' ).hide();

                /**
                 * Triggers 'gamipress_purchases_purchase_success' on success and 'gamipress_purchases_purchase_error' on error
                 *
                 * @since 1.0.5
                 *
                 * @selector    .gamipress-purchases-form
                 * @event       gamipress_purchases_purchase_success|gamipress_purchases_purchase_error
                 */
                form.trigger( 'gamipress_purchases_purchase_' + ( response.success === true ? 'success' : 'error' ) );

                /**
                 * Event after perform a purchase request
                 *
                 * @since 1.0.5
                 *
                 * @selector    .gamipress-purchases-form
                 * @event       gamipress_purchases_after_purchase_request
                 */
                form.trigger( 'gamipress_purchases_after_purchase_request' );

                // Apply response redirect (normally provoked by the payment gateway)
                if( response.data.redirect === true
                    && response.data.redirect_url !== undefined
                    && response.data.redirect_url.length ) {

                    window.location.href = response.data.redirect_url;

                }

            },
            error: function( response ) {

                /**
                 * Triggers purchase error
                 *
                 * @since 1.0.3
                 *
                 * @selector    .gamipress-purchases-form
                 * @event       gamipress_purchases_purchase_error
                 */
                form.trigger( 'gamipress_purchases_purchase_error' );

                /**
                 * Event after perform a purchase request
                 *
                 * @since 1.0.3
                 *
                 * @selector    .gamipress-purchases-form
                 * @event       gamipress_purchases_after_purchase_request
                 */
                form.trigger( 'gamipress_purchases_after_purchase_request' );

            }
        });
    });

    // Update the preview of the custom input form

    $('body').on('change keyup', '.gamipress-purchases-form-custom-amount', function() {

        var form = $(this).closest('.gamipress-purchases-form');
        var type = form.find('input[name="amount_type"]').val();
        var value_str = $(this).val();
        var value = parseFloat( $(this).val() );
        var points_type = form.find('input[name="points_type"]').val();
        var preview = $(this).siblings('.gamipress-purchases-form-custom-preview');
        var points_total = 0;
        var subtotal = 0;
        var converted_value = 0;

        if( value_str === '' || isNaN( value_str ) ) {
            value = 0;
        }

        if( type === 'points' ) {

            // User has input a desired amount of points, so turn it into money value
            converted_value = gamipress_purchases_convert_to_money( value, points_type );

            points_total = value;
            subtotal = converted_value;

            // Update the hidden amount
            form.find('input[name="amount"]').val( points_total );

            // Update the live preview
            preview.html(gamipress_purchases_format_price( subtotal ));
        } else {

            // User has input a desired amount of money to spend, so turn it into points value
            converted_value = gamipress_purchases_convert_to_points( value, points_type );

            points_total = converted_value;
            subtotal = value;

            // Update the hidden amount
            form.find('input[name="amount"]').val( subtotal );

            // Update the live preview
            preview.html( points_total );
        }

        // Update preview amount and subtotal amount
        form.find('.gamipress-purchases-form-subtotal-amount').html( gamipress_purchases_format_price( subtotal ) );
        form.find('.gamipress-purchases-form-points-total-amount').html( points_total );

        // Update hidden subtotal
        form.find('input[name="subtotal"]').val( subtotal );

        // Trigger country change to recalculate taxes
        form.find('.gamipress-purchases-form-country-select').trigger('change');

    });

    // Toggle visibility of the custom purchase form option

    $('body').on('change', '.gamipress-purchases-form-option input', function() {
        var target = $(this).closest('.gamipress-purchases-form-options').find('.gamipress-purchases-form-options-custom-amount');

        if( $(this).val() === 'custom' ) {
            target.find('input').trigger('change');
            target.slideDown();
        } else {
            target.slideUp();

            var form = $(this).closest('.gamipress-purchases-form');
            var type = form.find('input[name="amount_type"]').val();
            var value_str = $(this).val();
            var value = parseFloat( $(this).val() );
            var points_type = form.find('input[name="points_type"]').val();
            var converted_value = 0;
            var points_total = 0;
            var subtotal = 0;

            if( value_str === '' || isNaN( value_str ) ) {
                value = 0;
            }

            if( type === 'points' ) {

                // Options are an amount of points, so turn it into money value
                converted_value = gamipress_purchases_convert_to_money( value, points_type );

                points_total = value;
                subtotal = converted_value;

                // Update the hidden amount
                form.find('input[name="amount"]').val( points_total );

            } else {

                // Options are an amount of money to spend
                converted_value = gamipress_purchases_convert_to_points( value, points_type );

                points_total = converted_value;
                subtotal = value;

                // Update the hidden amount
                form.find('input[name="amount"]').val( subtotal );

            }

            // Update preview amount and subtotal amount
            form.find('.gamipress-purchases-form-subtotal-amount').html( gamipress_purchases_format_price( subtotal ) );
            form.find('.gamipress-purchases-form-points-total-amount').html( points_total );

            // Update hidden subtotal
            form.find('input[name="subtotal"]').val( subtotal );

            // Trigger country change to recalculate taxes
            form.find('.gamipress-purchases-form-country-select').trigger('change');
        }
    });

    $('.gamipress-purchases-form-option input:checked').trigger('change');

    // Update the preview of the options custom input form

    $('body').on('change keyup', '.gamipress-purchases-form-options-custom-amount-input', function() {
        var form = $(this).closest('.gamipress-purchases-form');
        var type = form.find('input[name="amount_type"]').val();
        var value_str = $(this).val();
        var value = parseFloat( $(this).val() );
        var points_type = form.find('input[name="points_type"]').val();
        var converted_value = 0;
        var points_total = 0;
        var subtotal = 0;

        if( value_str === '' || isNaN( value_str ) ) {
            value = 0;
        }

        if( type === 'points' ) {

            // User has input a desired amount of points, so turn it into money value
            converted_value = gamipress_purchases_convert_to_money( value, points_type );

            points_total = value;
            subtotal = converted_value;

            // Update the hidden amount
            form.find('input[name="amount"]').val( points_total );

        } else {

            // User has input a desired amount of money to spend
            converted_value = gamipress_purchases_convert_to_points( value, points_type );

            points_total = converted_value;
            subtotal = value;

            // Update the hidden amount
            form.find('input[name="amount"]').val( subtotal );
        }

        // Update preview amount and subtotal amount
        form.find('.gamipress-purchases-form-subtotal-amount').html( gamipress_purchases_format_price( subtotal ) );
        form.find('.gamipress-purchases-form-points-total-amount').html( points_total );

        // Update hidden subtotal
        form.find('input[name="subtotal"]').val( subtotal );

        // Trigger country change to recalculate taxes
        form.find('.gamipress-purchases-form-country-select').trigger('change');
    });

    // On change country, state or postcode, update taxes

    $('body').on( 'change', '.gamipress-purchases-form-country-select, .gamipress-purchases-form-state-input, .gamipress-purchases-form-postcode-input', function() {

        var form = $(this).closest('.gamipress-purchases-form');
        var country = form.find('.gamipress-purchases-form-country-select').val();
        var state = form.find('.gamipress-purchases-form-state-input').val();
        var postcode = form.find('.gamipress-purchases-form-postcode-input').val();
        var subtotal = parseFloat( form.find('input[name="subtotal"]').val() );

        // Recalculate taxes
        var tax_rate = gamipress_purchases_get_tax_rate( country, state, postcode );
        var tax = tax_rate * 100;
        var tax_amount = subtotal * tax_rate;
        var total = tax_amount + subtotal;

        // Update taxes and total
        form.find('.gamipress-purchases-form-tax-amount').html( gamipress_purchases_format_price( tax_amount ) );
        form.find('.gamipress-purchases-form-tax-percent').html( '(' + tax + '%)' );
        form.find('.gamipress-purchases-form-total-amount').html( gamipress_purchases_format_price( total ) );

        // Update hidden taxes and total
        form.find('input[name="tax_amount"]').val( tax_amount );
        form.find('input[name="tax"]').val( tax );
        form.find('input[name="total"]').val( total );
    });

    // Toggle visibility of the payment gateway forms

    $('body').on('change', '.gamipress-purchases-form-gateway-option input', function() {
        var form = $(this).closest('.gamipress-purchases-form');

        // Hide all gateways forms
        form.find('.gamipress-purchases-form-gateway-form').hide();

        // Show the selected gateway form
        form.find('.gamipress-purchases-form-gateway-form.gamipress-purchases-form-gateway-' + $(this).val() + '-form').show();
    });

    $('.gamipress-purchases-form-gateway-option input:checked').trigger('change');

})( jQuery );