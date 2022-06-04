(function($) {

    // Prevent the points payout form submission from pressing the enter key
    $('body').on( 'submit', '.gamipress-points-payout-form', function(e) {
        e.preventDefault();

        return false;
    });

    $('body').on( 'keypress', '.gamipress-points-payout-form', function(e) {
        return e.keyCode != 13;
    });

    // Handle the form submission through clicking the submit button

    $('body').on( 'click', '.gamipress-points-payout-form .gamipress-points-payout-form-submit-button', function(e) {
        e.preventDefault();

        var $this               = $(this);
        var form                = $this.closest('.gamipress-points-payout-form');
        var points_type         = form.find('[name="points_type"]').val();
        var points              = parseInt( form.find('input[name="amount"]').val() );
        var submit_wrap         = form.find('.gamipress-points-payout-form-submit');
        var payment_method      = '';

        // Ensure response wrap
        if( submit_wrap.find('.gamipress-points-payout-form-response').length === 0 ) {
            submit_wrap.prepend('<div class="gamipress-points-payout-form-response" style="display: none;"></div>');
        }

        var response_wrap = submit_wrap.find('.gamipress-points-payout-form-response');

        // Check the points amount
        if( isNaN( points ) || points === 0 ) {
            response_wrap.addClass( 'gamipress-points-payout-error' );
            response_wrap.html( gamipress_points_payouts.points_error );
            response_wrap.slideDown();
            return;
        }

        // Check the payment method
        if( form.find('.gamipress-points-payout-form-payment-method').length ) {

            payment_method =  form.find('.gamipress-points-payout-form-payment-method').val();

            if( payment_method.length === 0 ) {
                response_wrap.addClass( 'gamipress-points-payout-error' );
                response_wrap.html( gamipress_points_payouts.payment_method_error );
                response_wrap.slideDown();
                return;
            }
        }

        // Disable the submit button
        $this.prop( 'disabled', true );

        // Hide previous notices
        if( response_wrap.length ) {
            response_wrap.slideUp();
        }

        // Show the loading spinner
        submit_wrap.find( '.gamipress-spinner' ).show();

        $.ajax({
            url: gamipress_points_payouts.ajaxurl,
            method: 'POST',
            data: form.serialize() + '&action=gamipress_points_payouts_process_points_payout',
            success: function( response ) {

                // Add class gamipress-points-payouts-success on successful, if not will add the class gamipress-points-payouts-error
                response_wrap.addClass( 'gamipress-points-payouts-' + ( response.success === true ? 'success' : 'error' ) );

                // Update and show response messages
                response_wrap.html( ( response.data.message !== undefined ? response.data.message : response.data ) );
                response_wrap.slideDown();

                // Restore withdrawal button on not success
                if( response.success !== true ) {
                    $this.prop( 'disabled', false );
                }

                // Hide the loading spinner
                submit_wrap.find( '.gamipress-spinner' ).hide();

                // Apply response redirect
                if( response.data.redirect === true
                    && response.data.redirect_url !== undefined
                    && response.data.redirect_url.length ) {

                    window.location.href = response.data.redirect_url;

                }

            },
            error: function( response ) {

            }
        });
    });

    // On change points type
    $('body').on('change', '.gamipress-points-payout-form-points-type', function() {

        var points_type = $(this).val();
        var option = $(this).find('option[value="' + points_type + '"]');
        var form = $(this).closest('.gamipress-points-payout-form');
        var amount = form.find('.gamipress-points-payout-form-points-input-' + points_type + ' input').val();

        // Hide active points input
        form.find('.gamipress-points-payout-form-points-input-active').hide().removeClass('gamipress-points-payout-form-points-input-active');

        // Show points type points input
        form.find('.gamipress-points-payout-form-points-input-' + points_type).show().addClass('gamipress-points-payout-form-points-input-active');

        // Update points type labels
        form.find('.gamipress-points-payout-points-type-label').text( option.data('plural') );

        // Update user points
        form.find('.gamipress-points-payout-form-current-balance-amount').text( option.data('balance') );

        gamipress_points_payouts_update_form_totals( form, amount );

    });

    // Update balance on the custom input form

    $('body').on('change keyup', '.gamipress-points-payout-form-points-amount', function() {

        var form = $(this).closest('.gamipress-points-payout-form');
        var amount = parseInt( $(this).val() );

        // Prevent to exceed the maximum allowed
        if( $(this).attr('max') !== undefined &&  parseInt( $(this).attr('max') ) > 0 ) {
            var max = parseInt( $(this).attr('max') );

            if( amount > max ) {
                $(this).val( max );
                amount = max;
            }
        }

        gamipress_points_payouts_update_form_totals( form, amount );

    });

    function gamipress_points_payouts_update_form_totals( form, amount ) {

        if( isNaN( amount ) ) {
            amount = 0;
        }

        var points_type = form.find('[name="points_type"]').val();

        // Update amount used
        form.find('.gamipress-points-payout-form-total-amount').text( amount );
        form.find('input[name="amount"]').val( amount );

        // Update money
        // Update money amount
        var money = gamipress_points_payouts_convert_to_money( amount, points_type );

        var decimals           = gamipress_points_payouts.decimals;
        var decimal_separator  = gamipress_points_payouts.decimal_separator;
        var thousand_separator = gamipress_points_payouts.thousand_separator;

        money = gamipress_points_payouts_number_format( money, decimals, decimal_separator, thousand_separator );

        form.find('.gamipress-points-payout-form-total-money').text( money );

        // Update current balance
        var current_balance = parseInt( form.find('.gamipress-points-payout-form-current-balance-amount').text() );
        var new_balance_wrap = form.find('.gamipress-points-payout-form-new-balance-amount');
        var new_balance = current_balance - amount;

        new_balance_wrap.text( new_balance );

        // Toggle amount classes
        if( new_balance > 1 ) {
            new_balance_wrap.removeClass('gamipress-points-payouts-negative').addClass('gamipress-points-payouts-positive');
        } else {
            new_balance_wrap.removeClass('gamipress-points-payouts-positive').addClass('gamipress-points-payouts-negative');
        }

        // Update label position
        var label_position = 'after';

        if( gamipress_points_payouts.points_types[points_type] !== undefined ) {
            label_position = gamipress_points_payouts.points_types[points_type]['label_position'];
        }

        if( label_position === 'after' ) {
            // Total
            form.find('.gamipress-points-payout-form-total-amount').insertBefore( form.find('.gamipress-points-payout-form-total-points-label') );
            form.find('.gamipress-points-payout-form-total-amount').after( ' ' );

            // Current balance
            form.find('.gamipress-points-payout-form-current-balance-amount').insertBefore( form.find('.gamipress-points-payout-form-current-balance-points-label') );
            form.find('.gamipress-points-payout-form-current-balance-amount').after( ' ' );

            // New balance
            form.find('.gamipress-points-payout-form-new-balance-amount').insertBefore( form.find('.gamipress-points-payout-form-new-balance-points-label') );
            form.find('.gamipress-points-payout-form-new-balance-amount').after( ' ' );
        } else if( label_position === 'before' ) {
            // Total
            form.find('.gamipress-points-payout-form-total-amount').insertAfter( form.find('.gamipress-points-payout-form-total-points-label') );
            form.find('.gamipress-points-payout-form-total-amount').before( ' ' );

            // Current balance
            form.find('.gamipress-points-payout-form-current-balance-amount').insertAfter( form.find('.gamipress-points-payout-form-current-balance-points-label') );
            form.find('.gamipress-points-payout-form-current-balance-amount').before( ' ' );

            // New balance
            form.find('.gamipress-points-payout-form-new-balance-amount').insertAfter( form.find('.gamipress-points-payout-form-new-balance-points-label') );
            form.find('.gamipress-points-payout-form-new-balance-amount').before( ' ' );


        }

    }

    /**
     * Javascript version of the gamipress_points_payouts_get_conversion() PHP function
     *
     * @since 1.0.0
     *
     * @param points_type
     *
     * @returns {boolean|*}
     */
    function gamipress_points_payouts_get_conversion( points_type ) {
        if( gamipress_points_payouts.points_types[points_type] === undefined )
            return false;

        points_type = gamipress_points_payouts.points_types[points_type];

        return points_type['conversion'];
    }

    /**
     * Javascript version of the gamipress_points_payouts_convert_to_money() PHP function
     *
     * @since 1.0.0
     *
     * @param amount
     * @param points_type
     *
     * @returns {number}
     */
    function gamipress_points_payouts_convert_to_money( amount, points_type = '' ) {
        var conversion = gamipress_points_payouts_get_conversion( points_type );

        if( ! conversion ) return 0;


        var conversion_rate  = conversion['money'] / conversion['points'];

        return amount * conversion_rate;
    }

    /**
     * Javascript version of the number_format() PHP function
     *
     * @since 1.0.0
     *
     * @param {float} number
     * @param {integer} decimals
     * @param {string} decimal_separator
     * @param {string} thousand_separator
     *
     * @returns {string}
     */
    function gamipress_points_payouts_number_format( number, decimals, decimal_separator, thousand_separator ) {
        var decimals = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals,
            decimal_separator = decimal_separator == undefined ? "." : decimal_separator,
            thousand_separator = thousand_separator == undefined ? "," : thousand_separator,
            sign = number < 0 ? "-" : "",
            i = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(decimals))),
            j = (j = i.length) > 3 ? j % 3 : 0;
        return sign + (j ? i.substr(0, j) + thousand_separator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand_separator) + (decimals ? decimal_separator + Math.abs(number - i).toFixed(decimals).slice(2) : "");
    }

})(jQuery);