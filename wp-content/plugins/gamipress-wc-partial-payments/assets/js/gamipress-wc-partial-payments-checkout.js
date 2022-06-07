(function($) {

    var prefix = 'gamipress-wc-partial-payments-';

    // Form toggle
    $('body').on('click', '.' + prefix + 'form-toggle a', function(e) {
        e.preventDefault();

        $('.' + prefix + 'form').slideToggle();
    });

    // Range preview
    $('body').on('input', '.' + prefix + 'points', function(e) {
        var $this = $(this);

        $('#' + $this.attr('id') + '-preview').text( $this.val() );
    });

    // Points change
    $('body').on('change input', '.' + prefix + 'points', function(e) {
        gamipress_wc_partial_payments_update_preview();
    });

    // Points type change
    $('body').on('change', '#' + prefix + 'points-type', function(e) {
        var $this = $(this);
        var points_type = $this.val();

        // Hide all points types fields
        $('.' + prefix + 'points-label').hide();
        $('.' + prefix + 'points-preview').hide();
        $('.' + prefix + 'points').hide();
        $('.' + prefix + 'points-balance').hide();

        // Show current selected points type fields
        $('label[for="' + prefix + 'points-' + points_type + '"]').show();
        $('#' + prefix + 'points-' + points_type + '-preview').show();
        $('#' + prefix + 'points-' + points_type).show();
        $('#' + prefix + 'points-' + points_type + '-balance').show();

        gamipress_wc_partial_payments_update_preview();
    });

    // Submit partial payments form
    $('body').on('click', '#gamipress-wc-partial-payments button[name="apply_partial_payment"]', function(e) {
        e.preventDefault();

        var $this = $(this);
        var $form = $this.closest( '.' + prefix + 'form' );
        var notices_wrapper = $('.gamipress-wc-partial-payments-notices');

        // Block the form
        gamipress_wc_partial_payments_block( $form );

        $.ajax({
            url: gamipress_wc_partial_payments.ajaxurl,
            method: 'POST',
            data: $form.serialize()
                + '&action=gamipress_wc_partial_payments_apply_partial_payment'
                + '&nonce=' + gamipress_wc_partial_payments.nonce,
            success: function( response ) {
                // Clean up other notices
                notices_wrapper.find('.woocommerce-error, .woocommerce-message, .woocommerce-info').remove();

                if( response.success === false ) {
                    // Display an error notice
                    gamipress_wc_partial_payments_show_notice( '<div class="woocommerce-error" role="alert">' + response.data + '</div>', notices_wrapper );
                } else {
                    // Display a success notice
                    gamipress_wc_partial_payments_show_notice( '<div class="woocommerce-message" role="alert">' + response.data + '</div>', notices_wrapper );
                    // Update the cart
                    gamipress_wc_partial_payments_update_cart( true );
                }
            },
            error: function( response ) {
                // Clean up other notices
                notices_wrapper.find('.woocommerce-error, .woocommerce-message, .woocommerce-info').remove();
                // Display an error notice
                gamipress_wc_partial_payments_show_notice( '<div class="woocommerce-error" role="alert">' + response.data + '</div>', notices_wrapper );
            },
            complete: function() {
                // Unblock the form
                gamipress_wc_partial_payments_unblock( $form );
            }
        });

    });

    // Click remove partial payment
    $('body').on('click', '.gamipress-wc-partial-payments-remove', function(e) {
        e.preventDefault();

        var $this = $(this);
        var $wrapper = $this.closest( '.cart_totals' );
        var notices_wrapper = $('.gamipress-wc-partial-payments-notices');

        if( ! $wrapper.length )
            $wrapper = $this.closest( '.shop_table' );

        // Block the totals
        gamipress_wc_partial_payments_block( $wrapper );

        $.ajax({
            url: gamipress_wc_partial_payments.ajaxurl,
            method: 'POST',
            data: {
                action: 'gamipress_wc_partial_payments_remove_partial_payment',
                nonce: gamipress_wc_partial_payments.nonce,
                points_type: $this.data('points-type')
            },
            success: function( response ) {
                // Clean up other notices
                notices_wrapper.find('.woocommerce-error, .woocommerce-message, .woocommerce-info').remove();

                if( response.success === false ) {
                    // Display an error notice
                    gamipress_wc_partial_payments_show_notice( '<div class="woocommerce-error" role="alert">' + response.data + '</div>', notices_wrapper );
                } else {
                    // Slide the partial payments tr element for a faster visual update
                    $this.closest('tr').slideUp('fast');
                    // Display a success notice
                    gamipress_wc_partial_payments_show_notice( '<div class="woocommerce-message" role="alert">' + response.data + '</div>', notices_wrapper );
                    // Update the cart
                    gamipress_wc_partial_payments_update_cart( true );
                }
            },
            error: function( response ) {
                // Clean up other notices
                notices_wrapper.find('.woocommerce-error, .woocommerce-message, .woocommerce-info').remove();
                // Display an error notice
                gamipress_wc_partial_payments_show_notice( '<div class="woocommerce-error" role="alert">' + response.data + '</div>', notices_wrapper );
            },
            complete: function() {
                // Unblock the cart totals
                gamipress_wc_partial_payments_unblock( $wrapper );
            }
        });
    });

    // -----------------------------------------
    // WC Partial Payments functions
    // -----------------------------------------

    /**
     * Update preview function
     *
     * @since 1.0.0
     */
    function gamipress_wc_partial_payments_update_preview() {
        var $form = $( '.' + prefix + 'form' );
        var points_type = $form.find('*[name="points_type"]').val();
        var points_type_label = gamipress_wc_partial_payments.points_types[points_type].plural_name;
        var points =  $form.find('*[name="' + points_type + '_points"]').val();

        // Update points preview amount
        $('.' + prefix + 'preview-points').text( points );
        $('.' + prefix + 'preview-points-type').text( points_type_label );

        // Update money amount
        var money = gamipress_wc_partial_payments_convert_to_money( points, points_type );

        var decimals           = gamipress_wc_partial_payments.decimals;
        var decimal_separator  = gamipress_wc_partial_payments.decimal_separator;
        var thousand_separator = gamipress_wc_partial_payments.thousand_separator;

        money = gamipress_wc_partial_payments_number_format( money, decimals, decimal_separator, thousand_separator );

        $('.' + prefix + 'preview-money').text( money );
    }

    /**
     * Javascript version of the gamipress_wc_partial_payments_get_conversion() PHP function
     *
     * @since 1.0.0
     *
     * @param points_type
     *
     * @returns {boolean|*}
     */
    function gamipress_wc_partial_payments_get_conversion( points_type ) {
        if( gamipress_wc_partial_payments.points_types[points_type] === undefined )
            return false;

        points_type = gamipress_wc_partial_payments.points_types[points_type];

        return points_type['conversion'];
    }

    /**
     * Javascript version of the gamipress_wc_partial_payments_convert_to_money() PHP function
     *
     * @since 1.0.0
     *
     * @param amount
     * @param points_type
     *
     * @returns {number}
     */
    function gamipress_wc_partial_payments_convert_to_money( amount, points_type = '' ) {
        var conversion = gamipress_wc_partial_payments_get_conversion( points_type );

        if( ! conversion ) return 0;

        var conversion_rate  = conversion['money'] / conversion['points'];

        return amount * conversion_rate;
    }

    /**
     * Javascript version of the number_format() PHP function
     *
     * @since 1.0.0
     *
     * @param number
     * @param decimals
     * @param decimal_separator
     * @param thousand_separator
     *
     * @returns {string}
     */
    function gamipress_wc_partial_payments_number_format( number, decimals, decimal_separator, thousand_separator ) {
        decimals = Math.abs(decimals);
        decimals = isNaN(decimals) ? 2 : decimals;

        var sign = number < 0 ? "-" : "";

        number = Math.abs(Number(number) || 0);

        var string_number = parseInt( number.toFixed(decimals) ).toString();
        var thousands = ( string_number.length > 3 ) ? string_number.length % 3 : 0;

        return sign
            + (thousands ? string_number.substr(0, thousands) + thousand_separator : '') + string_number.substr(thousands).replace(/(\d{3})(?=\d)/g, "$1" + thousand_separator)
            + (decimals ? decimal_separator + Math.abs(number - string_number).toFixed(decimals).slice(2) : "");
    }

    // -----------------------------------------
    //  WooCommerce functions
    // -----------------------------------------

    // Block form
    function gamipress_wc_partial_payments_block( $form ) {
        // Block form through WooCommerce functions
        $form.addClass( 'processing' ).block( {
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        } );
    }

    // Unblock form
    function gamipress_wc_partial_payments_unblock( $form ) {
        // Unlock form through WooCommerce functions
        $form.removeClass( 'processing' ).unblock();
    }

    // Update cart
    function gamipress_wc_partial_payments_update_cart( preserve_notices ) {
        var $form = $( '.woocommerce-cart-form' );

        gamipress_wc_partial_payments_block( $form );
        gamipress_wc_partial_payments_block( $( 'div.cart_totals' ) );

        // Make call to actual form post URL.
        $.ajax( {
            type:     $form.attr( 'method' ),
            url:      $form.attr( 'action' ),
            data:     $form.serialize(),
            dataType: 'html',
            success:  function( response ) {
                gamipress_wc_partial_payments_update_wc_div( response, preserve_notices );
            },
            complete: function() {
                gamipress_wc_partial_payments_unblock( $form );
                gamipress_wc_partial_payments_unblock( $( 'div.cart_totals' ) );
                $.scroll_to_notices( $( '[role="alert"]' ) );
            }
        } );
    }

    // Update div
    function gamipress_wc_partial_payments_update_wc_div( html_str, preserve_notices ) {
        var $html       = $.parseHTML( html_str );
        var $new_form   = $( '.woocommerce-cart-form', $html );
        var $new_totals = $( '.cart_totals', $html );
        var $notices    = $( '.woocommerce-error, .woocommerce-message, .woocommerce-info', $html );

        // No form, cannot do this.
        if ( $( '.woocommerce-cart-form' ).length === 0 ) {
            window.location.reload();
            return;
        }

        // Remove errors
        if ( ! preserve_notices ) {
            $( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
        }

        if ( $new_form.length === 0 ) {
            // If the checkout is also displayed on this page, trigger reload instead.
            if ( $( '.woocommerce-checkout' ).length ) {
                window.location.reload();
                return;
            }

            // No items to display now! Replace all cart content.
            var $cart_html = $( '.cart-empty', $html ).closest( '.woocommerce' );
            $( '.woocommerce-cart-form__contents' ).closest( '.woocommerce' ).replaceWith( $cart_html );

            // Display errors
            if ( $notices.length > 0 ) {
                gamipress_wc_partial_payments_show_notice( $notices );
            }

            // Notify plugins that the cart was emptied.
            $( document.body ).trigger( 'wc_cart_emptied' );
        } else {
            // If the checkout is also displayed on this page, trigger update event.
            if ( $( '.woocommerce-checkout' ).length ) {
                $( document.body ).trigger( 'update_checkout' );
            }

            $( '.woocommerce-cart-form' ).replaceWith( $new_form );
            $( '.woocommerce-cart-form' ).find( ':input[name="update_cart"]' ).prop( 'disabled', true );

            if ( $notices.length > 0 ) {
                gamipress_wc_partial_payments_show_notice( $notices );
            }

            gamipress_wc_partial_payments_update_cart_totals_div( $new_totals );
        }

        $( document.body ).trigger( 'updated_wc_div' );
    }

    // Update cart totals
    function gamipress_wc_partial_payments_update_cart_totals_div( html_str ) {
        $( '.cart_totals' ).replaceWith( html_str );
        $( document.body ).trigger( 'updated_cart_totals' );
    }

    // Show notice
    function gamipress_wc_partial_payments_show_notice( html_element, $target ) {
        if ( ! $target ) {
            $target = $( '.woocommerce-notices-wrapper:first' ) || $( '.cart-empty' ).closest( '.woocommerce' ) || $( '.woocommerce-cart-form' );
        }
        $target.prepend( html_element );
    }

})(jQuery);