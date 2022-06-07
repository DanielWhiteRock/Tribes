(function( $ ) {

    // Listen for changes to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and custom selectors
        var trigger_type = $(this).val();
        var points_type = $(this).siblings('.select-purchases-points-type');
        var points_amount = $(this).siblings('.input-purchases-points-amount');
        var points_amount_text = $(this).siblings('.purchases-points-amount-text');
        var achievement_type = $(this).siblings('.select-purchases-achievement-type');
        var rank_type = $(this).siblings('.select-purchases-rank-type');

        // Hide all
        points_type.hide();
        points_amount.hide();
        points_amount_text.hide();
        achievement_type.hide();
        rank_type.hide();

        // Points type fields
        if( trigger_type === 'gamipress_purchases_new_points_purchase' ) {
            points_type.show();
            points_amount.show();
            points_amount_text.show();
        }

        // Achievement type fields
        if( trigger_type === 'gamipress_purchases_new_achievement_purchase' ) {
            achievement_type.show();

            // Trigger the change event
            achievement_type.trigger('change');
        }

        // Rank type fields
        if( trigger_type === 'gamipress_purchases_new_rank_purchase' ) {
            rank_type.show();

            // Trigger the change event
            rank_type.trigger('change');
        }

    });

    // Loop requirement list items to show/hide category select on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type and custom selectors
        var trigger_type = $(this).find('.select-trigger-type').val();
        var points_type = $(this).find('.select-purchases-points-type');
        var points_amount = $(this).find('.input-purchases-points-amount');
        var points_amount_text = $(this).find('.purchases-points-amount-text');
        var achievement_type = $(this).find('.select-purchases-achievement-type');
        var rank_type = $(this).find('.select-purchases-rank-type');

        // Hide all
        points_type.hide();
        points_amount.hide();
        points_amount_text.hide();
        achievement_type.hide();
        rank_type.hide();

        // Points type fields
        if( trigger_type === 'gamipress_purchases_new_points_purchase' ) {
            points_type.show();
            points_amount.show();
            points_amount_text.show();
        }

        // Achievement type fields
        if( trigger_type === 'gamipress_purchases_new_achievement_purchase' ) {
            achievement_type.show();

            // Trigger the change event
            achievement_type.trigger('change');
        }

        // Rank type fields
        if( trigger_type === 'gamipress_purchases_new_rank_purchase' ) {
            rank_type.show();

            // Trigger the change event
            rank_type.trigger('change');
        }

    });

    // Listen for changes to our achievement type selectors
    $('.requirements-list').on( 'change', '.select-purchases-achievement-type', function() {

        // Grab our selected trigger type and custom selectors
        var $this                   = $(this);
        var trigger_type            = $(this).find('.select-trigger-type').val();
        var achievement_type        = $(this).siblings('.select-purchases-achievement-type').val();
        var requirement_id          = $(this).parent('li').attr('data-requirement-id');
        var requirement_type        = $(this).siblings('input[name="requirement_type"]').val();
        var achievement_id_select   = $(this).siblings('.select-purchases-achievement-id');

        // Just has some effect if is a specific trigger type
        if( trigger_type === 'gamipress_purchases_new_specific_achievement_purchase' ) {

            // Show a spinner
            $('<span class="spinner is-active" style="float: none;"></span>').insertAfter( $this );

            $.post(
                ajaxurl,
                {
                    action: 'gamipress_get_achievements_options_html',
                    nonce: gamipress_purchases_requirements_ui.nonce,
                    requirement_id: requirement_id,
                    requirement_type: requirement_type,
                    achievement_type: achievement_type
                },
                function( response ) {

                    // Remove the spinner
                    $this.next('.spinner').remove();

                    achievement_id_select.html( response );
                    achievement_id_select.show();
                }
            );

        } else {
            achievement_id_select.hide();
        }

    });

    // Listen for changes to our rank type selectors
    $('.requirements-list').on( 'change', '.select-purchases-rank-type', function() {

        // Grab our selected trigger type and custom selectors
        var $this                   = $(this);
        var trigger_type            = $(this).find('.select-trigger-type').val();
        var rank_type               = $(this).siblings('.select-purchases-rank-type').val();
        var requirement_id          = $(this).parent('li').attr('data-requirement-id');
        var rank_id_select          = $(this).siblings('.select-purchases-rank-id');

        // Just has some effect if is a specific trigger type
        if( trigger_type === 'gamipress_purchases_new_specific_rank_purchase' ) {

            // Show a spinner
            $('<span class="spinner is-active" style="float: none;"></span>').insertAfter( $this );

            $.post(
                ajaxurl,
                {
                    action: 'gamipress_get_ranks_options_html',
                    nonce: gamipress_purchases_requirements_ui.nonce,
                    requirement_id: requirement_id,
                    post_type: rank_type
                },
                function( response ) {

                    // Remove the spinner
                    $this.next('.spinner').remove();

                    rank_id_select.html( response );
                    rank_id_select.show();
                }
            );

        } else {
            rank_id_select.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function( e, requirement_details, requirement ) {

        // Points type fields
        if( requirement_details.trigger_type === 'gamipress_purchases_new_points_purchase' ) {
            requirement_details.purchases_points_type = requirement.find( '.select-purchases-points-type' ).val();
            requirement_details.purchases_points_amount = requirement.find( '.input-purchases-points-amount' ).val();
        }

        // Achievement type fields
        if( requirement_details.trigger_type === 'gamipress_purchases_new_achievement_purchase' ) {
            requirement_details.purchases_achievement_type = requirement.find( '.select-purchases-achievement-type' ).val();
        }

        // Rank type fields
        if( requirement_details.trigger_type === 'gamipress_purchases_new_rank_purchase' ) {
            requirement_details.purchases_rank_type = requirement.find( '.select-purchases-rank-type' ).val();
        }

    });

})( jQuery );