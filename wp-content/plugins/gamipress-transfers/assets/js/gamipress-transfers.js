(function( $ ) {

    /* -------------------------------
     * Common transfer forms
       ------------------------------- */

    // Prevent the transfer form submission from pressing the enter key

    $('body').on( 'submit', '.gamipress-transfers-form', function(e) {
        e.preventDefault();

        return false;
    });

    $('body').on( 'keypress', '.gamipress-transfers-form', function(e) {
        return e.keyCode != 13;
    });

    // Handle the transfer form submission through clicking the transfer button

    $('body').on( 'click', '.gamipress-transfers-form .gamipress-transfers-form-submit-button', function(e) {
        e.preventDefault();

        var $this               = $(this);
        var form                = $this.closest('.gamipress-transfers-form');
        var submit_wrap         = form.find('.gamipress-transfers-form-submit');
        var recipient_input     = form.find('.gamipress-transfers-recipient-form-recipient-input');
        var recipient_id        = parseInt( form.find('input[name="recipient_id"]').val() );
        var user_points         = parseInt( form.find('input[name="user_points"]').val() );

        // Ensure response wrap
        if( submit_wrap.find('.gamipress-transfers-form-response').length === 0 )
            submit_wrap.prepend('<div class="gamipress-transfers-form-response" style="display: none;"></div>')

        var response_wrap = submit_wrap.find('.gamipress-transfers-form-response');

        // If recipient input exists and is not an autocomplete input, need to override recipient ID
        if( recipient_input.length && ! recipient_input.hasClass('.gamipress-transfers-recipient-form-recipient-autocomplete') )
            recipient_id = -1;

        // Check the recipient ID
        if( isNaN( recipient_id ) || recipient_id === 0 ) {
            response_wrap.addClass( 'gamipress-transfers-error' );
            response_wrap.html( gamipress_transfers.no_recipient_error );
            response_wrap.slideDown();
            return;
        }

        // Check the user balance (just for points transfer form)
        if( form.hasClass('gamipress-transfers-points-transfer-form') ) {

            var amount = parseInt( form.find('input[name="amount"]').val() );

            if( isNaN( amount ) || amount > user_points ) {
                response_wrap.addClass( 'gamipress-transfers-error' );
                response_wrap.html( gamipress_transfers.insufficient_amount_error );
                response_wrap.slideDown();
                return;
            }

        }

        // Disable the submit button
        $this.prop( 'disabled', true );

        // Hide previous notices
        if( response_wrap.length )
            response_wrap.slideUp();

        // Show the loading spinner
        submit_wrap.find( '.gamipress-spinner' ).show();

        /**
         * Event before perform a transfer request
         * Example:  $('body').on( 'gamipress_transfers_before_transfer_request', '.gamipress-transfers-form', function(e) {});
         *
         * @since 1.0.3
         *
         * @selector    .gamipress-transfers-form
         * @event       gamipress_transfers_before_transfer_request
         */
        form.trigger( 'gamipress_transfers_before_transfer_request' );

        $.ajax({
            url: gamipress_transfers.ajaxurl,
            method: 'POST',
            data: form.serialize() + '&action=gamipress_transfers_process_transfer',
            success: function( response ) {

                // Add class gamipress-transfers-success on successful transfer, if not will add the class gamipress-transfers-error
                response_wrap.addClass( 'gamipress-transfers-' + ( response.success === true ? 'success' : 'error' ) );

                // Update and show response messages
                response_wrap.html( ( response.data.message !== undefined ? response.data.message : response.data ) );
                response_wrap.slideDown();

                // Restore transfer button on not success
                if( response.success !== true ) {
                    $this.prop( 'disabled', false );
                }

                // Hide the loading spinner
                submit_wrap.find( '.gamipress-spinner' ).hide();

                /**
                 * Triggers 'gamipress_transfers_transfer_success' on success and 'gamipress_transfers_transfer_error' on error
                 *
                 * @since 1.0.3
                 *
                 * @selector    .gamipress-transfers-form
                 * @event       gamipress_transfers_transfer_success|gamipress_transfers_transfer_error
                 */
                form.trigger( 'gamipress_transfers_transfer_' + ( response.success === true ? 'success' : 'error' ) );

                /**
                 * Event after perform a transfer request
                 *
                 * @since 1.0.3
                 *
                 * @selector    .gamipress-transfers-form
                 * @event       gamipress_transfers_after_transfer_request
                 */
                form.trigger( 'gamipress_transfers_after_transfer_request' );

                // Apply response redirect
                if( response.data.redirect === true
                    && response.data.redirect_url !== undefined
                    && response.data.redirect_url.length ) {

                    window.location.href = response.data.redirect_url;

                }

            },
            error: function( response ) {

                /**
                 * Triggers transfer error
                 *
                 * @since 1.0.3
                 *
                 * @selector    .gamipress-transfers-form
                 * @event       gamipress_transfers_transfer_error
                 */
                form.trigger( 'gamipress_transfers_transfer_error' );

                /**
                 * Event after perform a transfer request
                 *
                 * @since 1.0.3
                 *
                 * @selector    .gamipress-transfers-form
                 * @event       gamipress_transfers_after_transfer_request
                 */
                form.trigger( 'gamipress_transfers_after_transfer_request' );

            }
        });
    });

    /* -------------------------------
     * Points Type
     ------------------------------- */

    $('body').on( 'change', '.gamipress-transfers-form .gamipress-transfers-points-transfer-form-points-type', function(e) {

        var $this = $(this);
        var form = $this.closest('.gamipress-transfers-form');
        var points_type = $this.val();
        var option = $this.find('option[value="' + points_type + '"]');

        // Update points type labels
        form.find('.gamipress-transfers-points-type-label').text( option.data('plural') );

        // Update user points
        form.find('.gamipress-transfers-points-transfer-form-current-balance-amount').text( option.data('balance') );

        // Update new balance
        gamipress_transfers_update_form_balance( form, form.find('input[name="amount"]').val() );

    });

    /* -------------------------------
     * Recipient
     ------------------------------- */

    // Recipient autocomplete

    var gamipress_transfers_users_cache  = {};

    $('input.gamipress-transfers-recipient-form-recipient-autocomplete').each(function() {

        var $this = $(this);
        var form = $this.closest('.gamipress-transfers-form');
        var parent_id = $this.parent().attr('id');

        $(this).autocomplete({
            minLength : 2,
            source    : function( request, response ) {

                var term = request.term;

                // Check if term has been already cached
                if ( term in gamipress_transfers_users_cache ) {
                    response( gamipress_transfers_users_cache[ term ] );
                    return;
                }

                // Set our ajax action
                request.action = 'gamipress_transfers_users_autocomplete';
                request.nonce = gamipress_transfers.nonce;

                $.getJSON( gamipress_transfers.ajaxurl, request, function( data, status, xhr ) {

                    // Update the cache
                    gamipress_transfers_users_cache[ term ] = data;

                    // Parse the data to the response callback
                    response( data );
                });

            },
            messages: {
                noResults : '',
                results   : function() {}
            },
            select: function( event, ui ) {

                // Set to this field the label as value
                $this.val( ui.item.label );

                // Store the recipient ID on a hidden input
                form.find('input[name="recipient_id"]').val( ui.item.value );

                return false;

            },
            // Position removed because in some themes it causes position issues so let's jQuery UI decide
            //position: { my : "right top", at: "right bottom" },
            appendTo: '#' + parent_id
        });
    });

    /* -------------------------------
     * Points transfer form
     ------------------------------- */

    function gamipress_transfers_update_form_balance( form, amount ) {

        if( isNaN( amount ) )
            amount = 0;

        form.find('.gamipress-transfers-points-transfer-form-total-amount').text( amount );
        form.find('input[name="amount"]').val( amount );

        var current_balance = parseInt( form.find('.gamipress-transfers-points-transfer-form-current-balance-amount').text() );
        var new_balance_wrap = form.find('.gamipress-transfers-points-transfer-form-new-balance-amount');
        var new_balance = current_balance - amount;

        new_balance_wrap.text( new_balance );

        // Toggle amount classes
        if( new_balance > 1 ) {
            new_balance_wrap.removeClass('gamipress-transfers-negative').addClass('gamipress-transfers-positive');
        } else {
            new_balance_wrap.removeClass('gamipress-transfers-positive').addClass('gamipress-transfers-negative');
        }

    }

    // Update balance on the custom input form

    $('body').on('change keyup', '.gamipress-transfers-points-transfer-form-custom-amount', function() {

        var form = $(this).closest('.gamipress-transfers-form');
        var amount = parseInt( $(this).val() );

        gamipress_transfers_update_form_balance( form, amount );

    });

    // Toggle visibility of the custom transfer form option

    $('body').on('change', '.gamipress-transfers-points-transfer-form-option input', function() {
        var target = $(this).closest('.gamipress-transfers-points-transfer-form-options').find('.gamipress-transfers-points-transfer-form-options-custom-amount');

        if( $(this).val() === 'custom' ) {

            target.find('input').trigger('change');
            target.slideDown();

        } else {

            target.slideUp();

            var form = $(this).closest('.gamipress-transfers-form');
            var amount = parseInt( $(this).val() );

            gamipress_transfers_update_form_balance( form, amount );

        }
    });

    $('.gamipress-transfers-points-transfer-form-option input:checked').trigger('change');

    // Update balance on the options custom input form

    $('body').on('change keyup', '.gamipress-transfers-points-transfer-form-options-custom-amount-input', function() {

        var form = $(this).closest('.gamipress-transfers-form');
        var amount = parseInt( $(this).val() );

        gamipress_transfers_update_form_balance( form, amount );

    });

    /* -------------------------------
     * Achievement transfer form
     ------------------------------- */


    // Achievement autocomplete

    var gamipress_transfers_achievements_cache  = {};
    var gamipress_transfers_achievements_render_cache  = {};

    $('input.gamipress-transfers-achievement-transfer-form-achievement-input').each(function() {

        var $this = $(this);
        var form = $this.closest('.gamipress-transfers-form');
        var parent_id = $this.parent().attr('id');

        $(this).autocomplete({
            minLength : 0,
            source    : function( request, response ) {

                var term = request.term;

                // Check if term has been already cached
                if ( term in gamipress_transfers_achievements_cache ) {
                    response( gamipress_transfers_achievements_cache[ term ] );
                    return;
                }

                // Set our ajax action
                request.action = 'gamipress_transfers_achievements_autocomplete';
                request.nonce = gamipress_transfers.nonce;

                $.getJSON( gamipress_transfers.ajaxurl, request, function( data, status, xhr ) {

                    // Update the cache
                    gamipress_transfers_achievements_cache[ term ] = data;

                    // Parse the data to the response callback
                    response( data );
                });

            },
            messages: {
                noResults : '',
                results   : function() {}
            },
            select: function( event, ui ) {

                var achievement_id = ui.item.value;

                // Set to this field the label as value
                $this.val( ui.item.label );

                // Store the achievement ID on a hidden input
                form.find('input[name="achievement_id"]').val( achievement_id );

                var achievement_wrap = form.find('.gamipress-transfers-form-achievement-wrapper');
                var achievement_render = achievement_wrap.find('.gamipress-transfers-form-achievement-render');

                // If achievement has been already rendered, then show from cache
                if ( achievement_id in gamipress_transfers_achievements_render_cache ) {
                    achievement_render.html( gamipress_transfers_achievements_render_cache[ achievement_id ] );
                    return;
                }

                // Show spinner on achievement preview
                achievement_render.html('<span class="gamipress-spinner"></span>');

                // Setup request
                var request = {
                    action: 'gamipress_transfers_get_achievement_render',
                    nonce: gamipress_transfers.nonce,
                    achievement_id: achievement_id
                };

                // Loop all hidden inputs to get the template arguments
                form.find('.gamipress-transfers-form-achievement-wrapper input').each(function() {
                    request[$(this).data('name')] = $(this).val();
                });

                $.get( gamipress_transfers.ajaxurl, request, function( data, status, xhr  ) {

                    // Update achievements render cache
                    gamipress_transfers_achievements_render_cache[ achievement_id ] = data;

                    // Update the achievement preview
                    achievement_render.html(data);

                } );

                return false;

            },
            // Position removed because in some themes it causes position issues so let's jQuery UI decide
            //position: { my : "right top", at: "right bottom" },
            appendTo: '#' + parent_id
        })
        // Trigger the search on focus
        .bind('focus', function(){ $(this).autocomplete("search"); } )
        // Custom render item callback
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( item.display_label )
                .appendTo( ul );
        };
    });

    /* -------------------------------
     * Rank transfer form
     ------------------------------- */

    // Rank autocomplete

    var gamipress_transfers_ranks_cache  = {};
    var gamipress_transfers_ranks_render_cache  = {};

    $('input.gamipress-transfers-rank-transfer-form-rank-input').each(function() {

        var $this = $(this);
        var form = $this.closest('.gamipress-transfers-form');
        var parent_id = $this.parent().attr('id');

        $(this).autocomplete({
            minLength : 0,
            source    : function( request, response ) {

                var term = request.term;

                // Check if term has been already cached
                if ( term in gamipress_transfers_ranks_cache ) {
                    response( gamipress_transfers_ranks_cache[ term ] );
                    return;
                }

                // Set our ajax action
                request.action = 'gamipress_transfers_ranks_autocomplete';
                request.nonce = gamipress_transfers.nonce;

                $.getJSON( gamipress_transfers.ajaxurl, request, function( data, status, xhr ) {

                    // Update the cache
                    gamipress_transfers_ranks_cache[ term ] = data;

                    // Parse the data to the response callback
                    response( data );
                });

            },
            messages: {
                noResults : '',
                results   : function() {}
            },
            select: function( event, ui ) {

                var rank_id = ui.item.value;

                // Set to this field the label as value
                $this.val( ui.item.label );

                // Store the rank ID on a hidden input
                form.find('input[name="rank_id"]').val( rank_id );

                var rank_wrap = form.find('.gamipress-transfers-form-rank-wrapper');
                var rank_render = rank_wrap.find('.gamipress-transfers-form-rank-render');

                // If rank has been already rendered, then show from cache
                if ( rank_id in gamipress_transfers_ranks_render_cache ) {
                    rank_render.html( gamipress_transfers_ranks_render_cache[ rank_id ] );
                    return;
                }

                // Show spinner on rank preview
                rank_render.html('<span class="gamipress-spinner"></span>');

                // Setup request
                var request = {
                    action: 'gamipress_transfers_get_rank_render',
                    nonce: gamipress_transfers.nonce,
                    rank_id: rank_id
                };

                // Loop all hidden inputs to get the template arguments
                form.find('.gamipress-transfers-form-rank-wrapper input').each(function() {
                    request[$(this).data('name')] = $(this).val();
                });

                $.get( gamipress_transfers.ajaxurl, request, function( data, status, xhr  ) {

                    // Update ranks render cache
                    gamipress_transfers_ranks_render_cache[ rank_id ] = data;

                    // Update the rank preview
                    rank_render.html(data);

                } );

                return false;

            },
            // Position removed because in some themes it causes position issues so let's jQuery UI decide
            //position: { my : "right top", at: "right bottom" },
            appendTo: '#' + parent_id
        })
        // Trigger the search on focus
        .bind('focus', function(){ $(this).autocomplete("search"); } )
        // Custom render item callback
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" )
                .append( item.display_label )
                .appendTo( ul );
        };
    });

})( jQuery );