(function( $ ) {

    // Meta Box

    $('body').on( 'change', '.gamipress-expirations-expiration select', function() {

        var value = $(this).val();
        var box = $(this).closest('.gamipress-expirations-expiration').parent();

        var show = ( box.hasClass('cmb2-metabox') ? 'slideDown' : 'show' );
        var hide = ( box.hasClass('cmb2-metabox') ? 'slideUp' : 'hide' );

        if( value === '' ) {
            box.find('.gamipress-expirations-amount')[hide]();
            box.find('.gamipress-expirations-date')[hide]();
            box.find('.gamipress-expirations-recalculate')[hide]();
        } else if( value === 'date' ) {
            box.find('.gamipress-expirations-amount')[hide]();
            box.find('.gamipress-expirations-date')[show]();
            box.find('.gamipress-expirations-recalculate')[hide]();
        } else {
            // Update the amount label
            box.find('.gamipress-expirations-amount .cmb2-metabox-description span').html( gamipress_expirations_admin.labels[value] );

            box.find('.gamipress-expirations-amount')[show]();
            box.find('.gamipress-expirations-date')[hide]();
            box.find('.gamipress-expirations-recalculate')[show]();
        }

    });

    $('.gamipress-expirations-expiration select').trigger('change');

    // Requirements UI

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Force a change event on the expiration select
        $(this).siblings('.gamipress-expirations-requirement-expiration').find('.gamipress-expirations-expiration select').trigger('change');

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        // Add expiration fields
        requirement_details.expirations_expiration = requirement.find( '.gamipress-expirations-expiration select' ).val();
        requirement_details.expirations_amount = requirement.find( '.gamipress-expirations-amount input' ).val();
        requirement_details.expirations_date = requirement.find( '.gamipress-expirations-date input' ).val();

    });

})( jQuery );