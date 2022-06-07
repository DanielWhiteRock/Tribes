(function($) {

    var prefix = '-gamipress-points-payouts-';
    var _prefix = '_gamipress_points_payouts_';

    // On change enable checkbox, toggle fields visibility

    $('#' + _prefix + 'enable').on('change', function() {

        var target = $( '.cmb2-id-' + prefix + 'conversion, '
            + '.cmb2-id-' + prefix + 'min-amount, '
            + '.cmb2-id-' + prefix + 'max-amount' );

        if( $(this).prop('checked') ) {
            target.slideDown().removeClass('cmb2-tab-ignore');
        } else {
            target.slideUp().addClass('cmb2-tab-ignore');
        }
    });

    if( ! $('#' + _prefix + 'enable').prop('checked') ) {
        $( '.cmb2-id-' + prefix + 'conversion, '
            + '.cmb2-id-' + prefix + 'min-amount, '
            + '.cmb2-id-' + prefix + 'max-amount'
        ).hide().addClass('cmb2-tab-ignore');
    }

})(jQuery);