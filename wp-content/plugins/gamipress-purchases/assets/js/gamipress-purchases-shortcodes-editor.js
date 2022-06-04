(function( $ ) {

    // Current user field
    $('#gamipress_purchase_history_current_user').on('change', function() {
        var target = $(this).closest('.cmb-row').next(); // User ID field

        if( $(this).prop('checked') ) {
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            if( target.closest('.cmb-tabs-wrap').length ) {
                // Just show if item tab is active
                if( target.hasClass('cmb-tab-active-item') ) {
                    target.slideDown();
                }
            } else {
                target.slideDown();
            }

            target.removeClass('cmb2-tab-ignore');
        }
    });

    // On change points form type
    $( '#gamipress_points_purchase_form_type').on('change', function() {
        var type = $(this).val();

        $('.cmb2-id-gamipress-points-purchase-form-amount').hide();


        $('.cmb2-id-gamipress-points-purchase-form-options').hide();
        $('.cmb2-id-gamipress-points-purchase-form-allow-user-input').hide();

        $('.cmb2-id-gamipress-points-purchase-form-initial-amount').hide();

        if( type === 'fixed' ) {
            $('.cmb2-id-gamipress-points-purchase-form-amount').show();
        } else if( type === 'custom' ) {
            $('.cmb2-id-gamipress-points-purchase-form-initial-amount').show();
        } else if( type === 'options' ) {
            $('.cmb2-id-gamipress-points-purchase-form-options').show();
            $('.cmb2-id-gamipress-points-purchase-form-allow-user-input').show();

            $('#gamipress_points_purchase_allow_user_input').trigger('change');
        }
    });

    $( '#gamipress_points_purchase_form_type').trigger('change');

    // On change points allow user input
    $( '#gamipress_points_purchase_allow_user_input').on('change', function() {
        var target = $('.cmb2-id-gamipress-points-purchase-form-initial-amount');
        var type = $( '#gamipress_points_purchase_form_type').val();

        if( $(this).prop('checked') && type === 'options' ) {
            target.show();
        } else {
            target.hide();
        }

    });

    $( '#gamipress_points_purchase_allow_user_input').trigger('change');

    // Acceptance visibility
    $( '#gamipress_points_purchase_acceptance, #gamipress_achievement_purchase_acceptance, #gamipress_rank_purchase_acceptance').on('change', function() {
        // Acceptance text is the next field
        var target = $(this).closest('.cmb-row').next();

        if( $(this).prop('checked') ) {
            target.slideDown().removeClass('cmb2-tab-ignore');
        } else {
            target.slideUp().addClass('cmb2-tab-ignore');
        }

    });

    $( '#gamipress_points_purchase_acceptance, #gamipress_achievement_purchase_acceptance, #gamipress_rank_purchase_acceptance').trigger('change');

    // Parse [gamipress_points_purchase] atts
    $('body').on( 'gamipress_get_shortcode_attributes', '#gamipress_points_purchase_wrapper', function( e, attrs, inputs ) {

        if( attrs.form_type === 'fixed' ) {
            delete attrs.initial_amount;
            delete attrs.options;
            delete attrs.allow_user_input;
        } else if( attrs.form_type === 'custom' ) {
            delete attrs.amount;
            delete attrs.options;
            delete attrs.allow_user_input;
        } else if( attrs.form_type === 'options' ) {

            delete attrs.amount;

            if( attrs.allow_user_input === 'no' ) {
                delete attrs.initial_amount;
            }
        }
    } );

})( jQuery );