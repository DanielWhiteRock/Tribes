(function( $ ) {

    // Display automatically

    if( $('.gamipress_settings').length ) {

        if( ! $('#gamipress_social_share_display_automatically').prop('checked') ) {
            $(
                '.cmb2-id-gamipress-social-share-placement, '
                + '.cmb2-id-gamipress-social-share-alignment, '
                + '.cmb2-id-gamipress-social-share-post-types, '
                + '.cmb2-id-gamipress-social-share-title, '
                + '.cmb2-id-gamipress-social-share-url'
            ).css({display:'none'}).addClass( 'cmb2-tab-ignore' );
        }

        $('#gamipress_social_share_display_automatically').on('change', function() {
            var target = $(
                '.cmb2-id-gamipress-social-share-placement, '
                + '.cmb2-id-gamipress-social-share-alignment, '
                + '.cmb2-id-gamipress-social-share-post-types, '
                + '.cmb2-id-gamipress-social-share-title, '
                + '.cmb2-id-gamipress-social-share-url'
            );

            if( $(this).prop('checked') ) {
                target.slideDown( 'fast' ).removeClass( 'cmb2-tab-ignore' );
            } else {
                target.slideUp( 'fast' ).addClass( 'cmb2-tab-ignore' );
            }
        });

    }

    // Twitter

    if( ! $('#gamipress_social_share_twitter').prop('checked') ) {
        $(
            '.cmb2-id-gamipress-social-share-twitter-pattern, '
            + '.cmb2-id-gamipress-social-share-twitter-username, '
            + '.cmb2-id-gamipress-social-share-twitter-count-box, '
            + '.cmb2-id-gamipress-social-share-twitter-button-size'
        ).css({display:'none'}).addClass( 'cmb2-tab-ignore' );
    }

    $('#gamipress_social_share_twitter').on('change', function() {
        var target = $(
            '.cmb2-id-gamipress-social-share-twitter-pattern, '
            + '.cmb2-id-gamipress-social-share-twitter-username, '
            + '.cmb2-id-gamipress-social-share-twitter-count-box, '
            + '.cmb2-id-gamipress-social-share-twitter-button-size'
        );

        var active_tab = $(this).closest('.cmb-tabs-wrap').find('.cmb-tabs .cmb-tab.active').attr('id');

        if( active_tab.indexOf('twitter') === -1 ) {
            return;
        }

        if( $(this).prop('checked') ) {
            target.slideDown( 'fast' ).removeClass( 'cmb2-tab-ignore' );
        } else {
            target.slideUp( 'fast' ).addClass( 'cmb2-tab-ignore' );
        }
    });

    // Facebook

    if( ! $('#gamipress_social_share_facebook').prop('checked') ) {
        $(
            '.cmb2-id-gamipress-social-share-facebook-app-id, '
            + '.cmb2-id-gamipress-social-share-facebook-action, '
            + '.cmb2-id-gamipress-social-share-facebook-button-layout, '
            + '.cmb2-id-gamipress-social-share-facebook-button-size, '
            + '.cmb2-id-gamipress-social-share-facebook-share'
        ).css({display:'none'}).addClass( 'cmb2-tab-ignore' );
    }

    $('#gamipress_social_share_facebook').on('change', function() {
        var target = $(
            '.cmb2-id-gamipress-social-share-facebook-app-id, '
            + '.cmb2-id-gamipress-social-share-facebook-action, '
            + '.cmb2-id-gamipress-social-share-facebook-button-layout, '
            + '.cmb2-id-gamipress-social-share-facebook-button-size, '
            + '.cmb2-id-gamipress-social-share-facebook-share'
        );

        var active_tab = $(this).closest('.cmb-tabs-wrap').find('.cmb-tabs .cmb-tab.active').attr('id');

        if( active_tab.indexOf('facebook') === -1 ) {
            return;
        }

        if( $(this).prop('checked') ) {
            target.slideDown( 'fast' ).removeClass( 'cmb2-tab-ignore' );
        } else {
            target.slideUp( 'fast' ).addClass( 'cmb2-tab-ignore' );
        }
    });

    // LinkedIn

    if( ! $('#gamipress_social_share_linkedin').prop('checked') ) {
        $(
            '.cmb2-id-gamipress-social-share-linkedin-counter'
        ).css({display:'none'}).addClass( 'cmb2-tab-ignore' );
    }

    $('#gamipress_social_share_linkedin').on('change', function() {
        var target = $(
            '.cmb2-id-gamipress-social-share-linkedin-counter'
        );

        var active_tab = $(this).closest('.cmb-tabs-wrap').find('.cmb-tabs .cmb-tab.active').attr('id');

        if( active_tab.indexOf('linkedin') === -1 ) {
            return;
        }

        if( $(this).prop('checked') ) {
            target.slideDown( 'fast' ).removeClass( 'cmb2-tab-ignore' );
        } else {
            target.slideUp( 'fast' ).addClass( 'cmb2-tab-ignore' );
        }
    });

    // Pinterest

    if( ! $('#gamipress_social_share_pinterest').prop('checked') ) {
        $(
            '.cmb2-id-gamipress-social-share-pinterest-thumbnail, '
            + '.cmb2-id-gamipress-social-share-pinterest-round, '
            + '.cmb2-id-gamipress-social-share-pinterest-count, '
            + '.cmb2-id-gamipress-social-share-pinterest-description, '
            + '.cmb2-id-gamipress-social-share-pinterest-tall'
        ).css({display:'none'}).addClass( 'cmb2-tab-ignore' );
    }

    $('#gamipress_social_share_pinterest').on('change', function() {
        var target = $(
            '.cmb2-id-gamipress-social-share-pinterest-thumbnail, '
            + '.cmb2-id-gamipress-social-share-pinterest-round, '
            + '.cmb2-id-gamipress-social-share-pinterest-count, '
            + '.cmb2-id-gamipress-social-share-pinterest-description, '
            + '.cmb2-id-gamipress-social-share-pinterest-tall'
        );

        var active_tab = $(this).closest('.cmb-tabs-wrap').find('.cmb-tabs .cmb-tab.active').attr('id');

        if( active_tab.indexOf('pinterest') === -1 ) {
            return;
        }

        if( $(this).prop('checked') ) {
            target.slideDown( 'fast' ).removeClass( 'cmb2-tab-ignore' );
        } else {
            target.slideUp( 'fast' ).addClass( 'cmb2-tab-ignore' );
        }
    });
})( jQuery );