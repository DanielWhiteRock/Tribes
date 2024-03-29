(function( $ ) {

    function gamipress_purchases_update_payment_totals() {

        var subtotal = 0;

        $('#gamipress-payment-items-data .cmb-repeatable-grouping').each(function() {

            var item_total = 0;

            // If this item total has focus do not change it
            if( $(this).find('input[name$="[total]"]').is(':focus') || $(this).find('input[name$="[total]"]').data('manual') === 'true' ) {
                if( ! isNaN( $(this).find('input[name$="[total]"]').val() ) ) {
                    item_total = parseFloat( $(this).find('input[name$="[total]"]').val() );
                }
            } else {
                // Calculate item total based on quantity and price
                var quantity = $(this).find('input[name$="[quantity]"]').val();
                var price = $(this).find('input[name$="[price]"]').val();

                quantity = parseFloat( quantity );
                price = parseFloat( price );

                if( ! isNaN( quantity ) && ! isNaN( price ) ) {
                    item_total = quantity * price;
                }
            }

            // Last check to ensure a numeric value
            if( isNaN( item_total ) ) {
                item_total = 0;
            }

            if( item_total !== 0 ) {
                $(this).find('input[name$="[total]"]').val( item_total );
            }

            subtotal += item_total;

        });

        // Update the invoice total
        $('#gamipress-payment-items-data .gamipress-purchases-payment-subtotal-amount').html( gamipress_purchases_format_price( subtotal ) );
        $('#subtotal').val( gamipress_purchases_format_amount( subtotal ) );

        // Get the tax rate to apply to the invoice total
        var tax = parseFloat( $('#tax').val() );

        if( isNaN( tax ) ) {
            tax = 0;
        }

        // Calculate the tax rate
        var tax_rate = tax / 100;

        var tax_amount = subtotal * tax_rate;

        // Update the tax amount
        $('#gamipress-payment-items-data .gamipress-purchases-payment-tax-amount').html( gamipress_purchases_format_price( tax_amount ) );
        $('#tax_amount').val( tax_amount );

        var total = tax_amount + subtotal;

        // Update the invoice total
        $('#gamipress-payment-items-data .gamipress-purchases-payment-total-amount').html( gamipress_purchases_format_price( total ) );
        $('#total').val( gamipress_purchases_format_amount( total ) );

    }

    // Payment User Select2

    $('#cmb2-metabox-gamipress-payment-data .cmb2-id-user-id select').gamipress_select2({
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            data: function( params ) {
                return {
                    q: params.term,
                    action: 'gamipress_get_users',
                    nonce: gamipress_purchases_payments.nonce,
                };
            },
            processResults: function( results, page ) {
                if( results === null ) {
                    return { results: [] };
                }

                var formatted_results = [];

                results.data.forEach(function(item) {
                    formatted_results.push({
                        id: item.ID,
                        text: item.user_login,
                    });
                });

                return { results: formatted_results };
            }
        },
        theme: 'default gamipress-select2',
        placeholder: 'Select an User',
        allowClear: true,
        multiple: false
    });

    // Load user billing details

    $('#gamipress-purchases-load-user-billing-details').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);

        if( $this.data('loading') === 'true' ) {
            return;
        }

        var user_id = $this.siblings('select#user_id').val();

        if( isNaN(user_id) ) {
            // TODO: add notice
            return false;
        }

        $this.data('loading', 'true');

        var spinner_inline_css = ''
        + 'float: none; '
        + 'margin: 0; '
        + 'width: 15px; '
        + 'height: 15px; '
        + 'background-size: 15px 15px; ';

        $this.parent().append('<span class="spinner is-active" style="' + spinner_inline_css + '"></span>');

        $.ajax({
            url: ajaxurl,
            data: {
                action: 'gamipress_purchases_get_user_billing_details',
                nonce: gamipress_purchases_payments.nonce,
                user_id: user_id
            },
            success: function( response ) {
                $this.data('loading', 'false');
                $this.parent().find('.spinner').remove();

                if( response.success === true ) {

                    var user_details = response.data;

                    $('#first_name').val( user_details.first_name );
                    $('#last_name').val( user_details.last_name );
                    $('#email').val( user_details.email );
                    $('#address_1').val( user_details.address_1 );
                    $('#address_2').val( user_details.address_2 );
                    $('#city').val( user_details.city );
                    $('#postcode').val( user_details.postcode );
                    $('#country').val( user_details.country );
                    $('#state').val( user_details.state );

                }
            }

        });
    });

    // Trigger update total on change payment total

    $('body').on('change keyup', '#gamipress-payment-items-data .cmb-row[class*="-quantity"] input, #gamipress-payment-items-data .cmb-row[class*="-price"] input', function() {
        gamipress_purchases_update_payment_totals();
    });

    $('body').on('change keyup blur', '#gamipress-payment-items-data .cmb-row[class*="-total"] input', function() {

        // Leave user desired value if not empty
        if( $(this).data('manual') === 'true' && $(this).val() === '' ) {
            $(this).data('manual', 'false');
        } else {
            $(this).data('manual', 'true');
        }

        gamipress_purchases_update_payment_totals();
    });

    // Recalculate the tax rate on change rate

    $('body').on('change keyup', '#tax', function() {
        gamipress_purchases_update_payment_totals();
    });

    // Payment items post assignments

    function gamipress_purchases_payment_check_item_assignments( item ) {
        // Setup vars
        var container = item.find('.gamipress-purchases-payment-items-assignment');
        var container_text = container.find('.gamipress-purchases-payment-items-assignment-text');
        var container_fields = container.find('.gamipress-purchases-payment-items-assignment-fields');

        var post_id = parseInt( item.find('input[name$="[post_id]"]').val() );
        var post_type = item.find('input[name$="[post_type]"]').val();

        if( post_id === 0 || isNaN( post_id ) ) {
            // Set the no assignment text
            container_text.html( gamipress_purchases_payments.strings.no_assignment );
        } else {
            // Build the assignment link title
            var item_title = '';

            if( post_type in gamipress_purchases_payments.points_types ) {
                item_title = gamipress_purchases_payments.points_types[post_type].plural_name;
            } else if( post_type in gamipress_purchases_payments.achievement_types ) {
                item_title = gamipress_purchases_payments.achievement_types[post_type].singular_name;
            } else if( post_type in gamipress_purchases_payments.rank_types ) {
                item_title = gamipress_purchases_payments.rank_types[post_type].singular_name;
            }

            // Built the item link
            var item_url = gamipress_purchases_payments.admin_url + 'post.php?post=' + post_id + '&action=edit';
            var item_link = '<a href="' + item_url + '" target="_blank">' + item_title + '</a>';

            // Replace {item_link}
            var text = gamipress_purchases_payments.strings.assignment.replace( '{item_link}', item_link );

            container_text.html( text );

            // Update post type value
            container_fields.find('.gamipress-purchases-payment-items-assignment-post-type').val(post_type);
        }
    }

    // Initial check

    $('.cmb2-id-payment-items .cmb-repeatable-grouping').each(function() {
        gamipress_purchases_payment_check_item_assignments( $(this) );
    });

    // Click on add new group

    $('body').on('click', '.cmb2-id-payment-items .cmb-add-group-row', function(e) {

        // Add a timeout to get the last one
        setTimeout( function() {
            var item = $('.cmb2-id-payment-items').find('.cmb-repeatable-grouping').last();

            gamipress_purchases_payment_check_item_assignments( item );
        }, 10 );
    });

    // Click on assign post to item

    $('body').on('click', '.gamipress-purchases-payment-items-assignment .gamipress-purchases-assign-post-to-item', function(e) {
        e.preventDefault();

        var container = $(this).closest('.gamipress-purchases-payment-items-assignment');
        var container_text = container.find('.gamipress-purchases-payment-items-assignment-text');
        var container_fields = container.find('.gamipress-purchases-payment-items-assignment-fields');

        // Toggle visibility
        container_text.slideUp();
        container_fields.slideDown();
    });

    // Click on unassign post to item

    $('body').on('click', '.gamipress-purchases-payment-items-assignment .gamipress-purchases-unassign-post-to-item', function(e) {
        e.preventDefault();

        var item = $(this).closest('.cmb-repeatable-grouping');

        item.find('input[name$="[post_id]"]').val( '0' );
        item.find('input[name$="[post_type]"]').val( '' );

        gamipress_purchases_payment_check_item_assignments( item );
    });

    // Change assignment post type
    $('body').on('change', '.gamipress-purchases-payment-items-assignment .gamipress-purchases-payment-items-assignment-post-type', function(e) {

        // Setup vars
        var post_type = $(this).val();
        var item = $(this).closest('.cmb-repeatable-grouping');
        var container = $(this).closest('.gamipress-purchases-payment-items-assignment');
        var container_text = container.find('.gamipress-purchases-payment-items-assignment-text');
        var container_fields = container.find('.gamipress-purchases-payment-items-assignment-fields');
        var post_id_field = container_fields.find('.gamipress-purchases-payment-items-assignment-post-id');

        if( post_type in gamipress_purchases_payments.points_types ) {
            // For points types, we have the IDs at localized vars
            var post_id = gamipress_purchases_payments.points_types[post_type].ID;
            var plural_name = gamipress_purchases_payments.points_types[post_type].plural_name;

            post_id_field.html('<option value="' + post_id + '">' + plural_name + '</option>');
            post_id_field.val(post_id);

            // Hide the post ID field
            post_id_field.hide();
        } else {
            // For achievement and rank types

            var action = '';

            if( post_type in gamipress_purchases_payments.achievement_types ) {
                action = 'gamipress_get_achievements_options_html';
            } else if( post_type in gamipress_purchases_payments.rank_types ) {
                action = 'gamipress_get_ranks_options_html';
            }

            // Setup vars
            var spinner = container_fields.find('.spinner');
            var save_button = container_fields.find('.save-assignment');

            // Hide the post id field
            post_id_field.slideUp();

            // Show the spinner
            spinner.addClass('is-active');

            // Disable the save button
            save_button.addClass('disabled');

            $.ajax({
                url: ajaxurl,
                data: {
                    action: action,
                    nonce: gamipress_purchases_payments.nonce,
                    post_type: post_type,
                    achievement_type: post_type, // Needle for gamipress_get_achievements_options_html action
                    selected: post_id_field.val()
                }, success: function( response ) {

                    // Hide the spinner
                    spinner.removeClass('is-active');

                    // Enable the save button
                    save_button.removeClass('disabled');

                    // Add the response and show the post id field
                    post_id_field.html( response );
                    post_id_field.slideDown();

                }
            });
        }
    });

    // Click on save assign post to item

    $('body').on('click', '.gamipress-purchases-payment-items-assignment .save-assignment', function(e) {
        e.preventDefault();

        if( $(this).hasClass('disabled') ) {
            return;
        }

        // Setup vars
        var item = $(this).closest('.cmb-repeatable-grouping');
        var container = $(this).closest('.gamipress-purchases-payment-items-assignment');
        var container_text = container.find('.gamipress-purchases-payment-items-assignment-text');
        var container_fields = container.find('.gamipress-purchases-payment-items-assignment-fields');

        var post_id = 0;
        var post_type = container_fields.find('.gamipress-purchases-payment-items-assignment-post-type').val();

        if( post_type in gamipress_purchases_payments.points_types ) {
            // For points types, we have the IDs at localized vars
            post_id = gamipress_purchases_payments.points_types[post_type].ID;
        } else {
            post_id = parseInt( container_fields.find('.gamipress-purchases-payment-items-assignment-post-id').val() );
        }

        item.find('input[name$="[post_id]"]').val( post_id );
        item.find('input[name$="[post_type]"]').val( post_type );

        gamipress_purchases_payment_check_item_assignments( item );

        // Toggle visibility
        container_text.slideDown();
        container_fields.slideUp();
    });

    // Click on cancel assign post to item

    $('body').on('click', '.gamipress-purchases-payment-items-assignment .cancel-assignment', function(e) {
        e.preventDefault();

        var container = $(this).closest('.gamipress-purchases-payment-items-assignment');
        var container_text = container.find('.gamipress-purchases-payment-items-assignment-text');
        var container_fields = container.find('.gamipress-purchases-payment-items-assignment-fields');

        // Toggle visibility
        container_text.slideDown();
        container_fields.slideUp();
    });

    // Payment notes

    $('#add-new-payment-note').on('click', function(e) {
        e.preventDefault();

        // Toggle visibility
        $(this).parent().slideUp();
        $('#new-payment-note-fieldset').slideDown();
    });

    // Save note

    $('#save-payment-note').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);

        if( $this.hasClass('disabled') ) {
            return;
        }

        // Disable the button
        $this.addClass('disabled');

        // Save the payment note
        var title = $('#payment-note-title').val();
        var description = $('#payment-note-description').val();
        var notice = $('#new-payment-note-submit .notice');

        if( title.length === 0 || description.length === 0 ) {
            notice.find('.error').html('Please, fill the form correctly');
            notice.removeClass('hidden');
            return;
        }

        if( ! notice.hasClass('hidden') ) {
            notice.addClass('hidden');
        }

        $.ajax({
            url: ajaxurl,
            data: {
                action: 'gamipress_purchases_add_payment_note',
                nonce: gamipress_purchases_payments.nonce,
                payment_id: $('#ct_edit_form input#object_id').val(),
                title: title,
                description: description
            },
            success: function( response ) {

                if( response.success ) {
                    // Add payment note to the list of notes (at the top of the list!)
                    $('.payment-notes-list tbody').prepend(response.data);

                    // Toggle visibility
                    $this.closest('#new-payment-note-fieldset').slideUp();
                    $this.closest('#new-payment-note-fieldset').prev().slideDown();

                    // Clear fields
                    $('#payment-note-title').val('');
                    $('#payment-note-description').val('');
                } else {
                    // Show error reported
                    notice.find('.error').html(response.data);
                    notice.removeClass('hidden');
                }

                // Restore the button
                $this.removeClass('disabled');
            }
        });
    });

    // Cancel add note

    $('#cancel-payment-note').on('click', function(e) {
        e.preventDefault();

        // Toggle visibility
        $(this).closest('#new-payment-note-fieldset').slideUp();
        $(this).closest('#new-payment-note-fieldset').prev().slideDown();

        // Clear fields
        $('#payment-note-title').val('');
        $('#payment-note-description').val('');
    });

    // Delete note
    $('.payment-note .row-actions .delete').on('click', function(e) {
        e.preventDefault();

        var confirmed = confirm('Do you want to remove this payment note?');

        var $this = $(this);

        if ( confirmed ) {

            // Hide note
            $this.closest('.payment-note').fadeOut();

            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'gamipress_purchases_delete_payment_note',
                    nonce: gamipress_purchases_payments.nonce,
                    payment_note_id: $this.data('payment-note-id'),
                },
                success: function( response ) {

                    if( response.success ) {

                        // Remove the note
                        $this.closest('.payment-note').remove();

                    } else {
                        // TODO: Report error

                        // Show note again
                        $this.closest('.payment-note').fadeIn();
                    }
                }
            });
        }
    });

})( jQuery );