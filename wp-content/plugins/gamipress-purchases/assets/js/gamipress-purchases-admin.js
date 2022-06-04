(function( $ ) {

    $('#_gamipress_purchases_allow_purchase').on('change', function() {
        var target = $('.cmb2-id--gamipress-purchases-price');

        if( $(this).prop('checked') ) {
            target.slideDown();
        } else {
            target.slideUp();
        }
    });

    if( ! $('#_gamipress_purchases_allow_purchase').prop('checked') ) {
        $('.cmb2-id--gamipress-purchases-price').hide();
    }

})( jQuery );