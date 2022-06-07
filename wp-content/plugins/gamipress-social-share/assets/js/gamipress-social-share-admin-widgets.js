(function($) {

    function gamipress_social_share_widget_field_selector( field_name ) {
        return 'input[id^="widget-gamipress_social_share_widget"][id$="[' + field_name + ']"]';
    }

    function gamipress_social_share_widget_row_selector( field_name ) {
        return '.cmb-row[class*="' + field_name + '"]';
    }

    // Twitter

    $(gamipress_social_share_widget_field_selector('twitter')).on('change', function() {
        var target = $(this).closest('.cmb2-metabox').find(
            gamipress_social_share_widget_row_selector('twitter-pattern') + ', '
            + gamipress_social_share_widget_row_selector('twitter-username') + ', '
            + gamipress_social_share_widget_row_selector('twitter-count-box') + ', '
            + gamipress_social_share_widget_row_selector('twitter-button-size')
        );

        if( $(this).prop('checked') ) {
            target.slideDown( 'fast' );
        } else {
            target.slideUp( 'fast' );
        }
    });


    // Facebook

    $(gamipress_social_share_widget_field_selector('facebook')).on('change', function() {
        var target = $(this).closest('.cmb2-metabox').find(
            gamipress_social_share_widget_row_selector('facebook-action') + ', '
            + gamipress_social_share_widget_row_selector('facebook-button-layout') + ', '
            + gamipress_social_share_widget_row_selector('facebook-button-size') + ', '
            + gamipress_social_share_widget_row_selector('facebook-share')
        );

        if( $(this).prop('checked') ) {
            target.slideDown( 'fast' );
        } else {
            target.slideUp( 'fast' );
        }
    });

    // LinkedIn

    $(gamipress_social_share_widget_field_selector('linkedin')).on('change', function() {
        var target = $(this).closest('.cmb2-metabox').find(
            gamipress_social_share_widget_row_selector('linkedin-counter')
        );

        if( $(this).prop('checked') ) {
            target.slideDown( 'fast' );
        } else {
            target.slideUp( 'fast' );
        }
    });

    // Pinterest

    $(gamipress_social_share_widget_field_selector('pinterest')).on('change', function() {
        var target = $(this).closest('.cmb2-metabox').find(
            gamipress_social_share_widget_row_selector('pinterest-thumbnail') + ', '
            + gamipress_social_share_widget_row_selector('pinterest-round') + ', '
            + gamipress_social_share_widget_row_selector('pinterest-tall') + ', '
            + gamipress_social_share_widget_row_selector('pinterest-count') + ', '
            + gamipress_social_share_widget_row_selector('pinterest-description')
        );

        if( $(this).prop('checked') ) {
            target.slideDown( 'fast' );
        } else {
            target.slideUp( 'fast' );
        }
    });

    $(
        gamipress_social_share_widget_field_selector('twitter') + ', '
        + gamipress_social_share_widget_field_selector('facebook') + ', '
        + gamipress_social_share_widget_field_selector('linkedin') + ', '
        + gamipress_social_share_widget_field_selector('pinterest')
    ).trigger('change');
})(jQuery);