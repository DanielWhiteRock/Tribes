(function( $ ) {

    // Listen for changes to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and custom selectors
        var trigger_type = $(this).val();
        var points_type = $(this).siblings('.select-points-exchanges-points-type');
        var points_amount = $(this).siblings('.input-points-exchanges-points-amount');
        var points_amount_text = $(this).siblings('.points-exchanges-points-amount-text');

        // Hide all
        points_type.hide();
        points_amount.hide();
        points_amount_text.hide();

        // Points type fields
        if( trigger_type === 'gamipress_points_exchanges_new_points_exchange' ) {
            points_type.show();
            points_amount.show();
            points_amount_text.show();
        }

    });

    // Loop requirement list items to show/hide category select on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type and custom selectors
        var trigger_type = $(this).find('.select-trigger-type').val();
        var points_type = $(this).find('.select-points-exchanges-points-type');
        var points_amount = $(this).find('.input-points-exchanges-points-amount');
        var points_amount_text = $(this).find('.points-exchanges-points-amount-text');

        // Hide all
        points_type.hide();
        points_amount.hide();
        points_amount_text.hide();

        // Points type fields
        if( trigger_type === 'gamipress_points_exchanges_new_points_exchange' ) {
            points_type.show();
            points_amount.show();
            points_amount_text.show();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function( e, requirement_details, requirement ) {

        // Points type fields
        if( requirement_details.trigger_type === 'gamipress_points_exchanges_new_points_exchange' ) {
            requirement_details.points_exchanges_points_type = requirement.find( '.select-points-exchanges-points-type' ).val();
            requirement_details.points_exchanges_points_amount = requirement.find( '.input-points-exchanges-points-amount' ).val();
        }

    });

})( jQuery );