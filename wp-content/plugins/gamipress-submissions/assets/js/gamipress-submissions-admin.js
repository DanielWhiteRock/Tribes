(function( $ ) {

    // On change enable submissions
    $('#_gamipress_submissions_enable').on('change', function() {
        var target = $(this).closest('.cmb-row').siblings('.cmb-row');

        if( $(this).prop('checked') ) {
            target.slideDown();
        } else {
            target.slideUp();
        }
    });

    if( ! $('#_gamipress_submissions_enable').prop('checked') ) {
        $('#_gamipress_submissions_enable').closest('.cmb-row').siblings('.cmb-row').hide();
    }

})( jQuery );