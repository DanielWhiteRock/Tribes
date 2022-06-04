(function( $ ) {

    // Toggle visibility of exchange rates
    $('body').on('change', '#_gamipress_points_exchanges_exchanges_enabled', function() {

        var target = $('.cmb2-id--gamipress-points-exchanges-rates');

        if( $(this).prop('checked') ) {
            target.slideDown();
        } else {
            target.slideUp();
        }

    });

    // Initial check of exchange rates visibility
    if( ! $('#_gamipress_points_exchanges_exchanges_enabled').prop('checked') ) {
        $('.cmb2-id--gamipress-points-exchanges-rates').hide();
    }

    // Add an event prevent default to void url changes
    $('body').on( 'click', '#gamipress-points-exchanges-rates-table-link', function(e) {
        e.preventDefault();

        setTimeout( function() {
            // Add a custom class to the thickbox
            $('#TB_window').addClass('gamipress-points-exchanges-thickbox');
        }, 0 );
    });

})( jQuery );