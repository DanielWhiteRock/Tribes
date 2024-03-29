(function( $ ) {

    // Helper function to build a widget field selector
    function gamipress_purchases_widget_field_selector( shortcode, field ) {
        return '[id^="widget-gamipress_' + shortcode + '"][id$="[' + field + ']"]';
    }

    // On change points form type
    $('body').on('change', 'select[id^="widget-gamipress_points_purchase_form"][id$="[form_type]"]', function() {
        var type = $(this).val();

        var amount = $(this).closest('.cmb2-metabox').find( gamipress_purchases_widget_field_selector('points_purchase_form', 'amount') ).closest('.cmb-row');
        var options = $(this).closest('.cmb2-metabox').find( '.cmb-row[class*="gamipress-points-purchase-form"][class*="options"]' );
        var allow_user_input = $(this).closest('.cmb2-metabox').find( gamipress_purchases_widget_field_selector('points_purchase_form', 'allow_user_input') ).closest('.cmb-row');
        var initial_amount = $(this).closest('.cmb2-metabox').find( gamipress_purchases_widget_field_selector('points_purchase_form', 'initial_amount') ).closest('.cmb-row');

        amount.hide();

        options.hide();
        allow_user_input.hide();

        initial_amount.hide();

        if( type === 'fixed' ) {
            amount.show();
        } else if( type === 'custom' ) {
            initial_amount.show();
        } else if( type === 'options' ) {
            options.show();
            allow_user_input.show();

            allow_user_input.find('input').trigger('change');
        }
    });

    $( 'select[id^="widget-gamipress_points_purchase_form"][id$="[form_type]"]' ).trigger('change');

    // On change points allow user input
    $('body').on('change', 'input[id^="widget-gamipress_points_purchase_form"][id$="[allow_user_input]"]', function() {
        var target = $(this).closest('.cmb2-metabox').find( gamipress_purchases_widget_field_selector('points_purchase_form', 'initial_amount') ).closest('.cmb-row');
        var type = $(this).closest('.cmb2-metabox').find( gamipress_purchases_widget_field_selector('points_purchase_form', 'form_type') ).val();

        if( $(this).prop('checked') && type === 'options' ) {
            target.show();
        } else {
            target.hide();
        }

    });

    $( 'input[id^="widget-gamipress_points_purchase_form"][id$="[allow_user_input]"]' ).trigger('change');

    // Acceptance visibility
    $('body').on('change', 'input[id^="widget-gamipress_points_purchase_form"][id$="[acceptance]"], input[id^="widget-gamipress_acheivement_purchase_form"][id$="[acceptance]"], input[id^="widget-gamipress_rank_purchase_form"][id$="[acceptance]"]', function() {

        // Acceptance text is the next field
        var target = $(this).closest('.cmb-row').next();

        if( $(this).prop('checked') ) {
            target.slideDown().removeClass('cmb2-tab-ignore');
        } else {
            target.slideUp().addClass('cmb2-tab-ignore');
        }

    });

    $( 'input[id^="widget-gamipress_points_purchase_form"][id$="[acceptance]"], input[id^="widget-gamipress_acheivement_purchase_form"][id$="[acceptance]"], input[id^="widget-gamipress_rank_purchase_form"][id$="[acceptance]"]' ).trigger('change');

    // Initialize on widgets area
    $(document).on('widget-updated widget-added', function(e, widget) {

        widget.find( 'select[id^="widget-gamipress_points_purchase_form"][id$="[form_type]"]' ).trigger('change');

        widget.find( 'input[id^="widget-gamipress_points_purchase_form"][id$="[allow_user_input]"]' ).trigger('change');

        widget.find( 'input[id^="widget-gamipress_points_purchase_form"][id$="[acceptance]"], input[id^="widget-gamipress_acheivement_purchase_form"][id$="[acceptance]"], input[id^="widget-gamipress_rank_purchase_form"][id$="[acceptance]"]' ).trigger('change');

    });

})( jQuery );