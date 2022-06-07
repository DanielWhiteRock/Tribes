(function( $ ) {

    // Click on change user and recipient

    $('#gamipress-transfers-change-user, #gamipress-transfers-change-recipient').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);
        var field = $this.closest('.cmb-td').find('select');
        var actions = $this.parent();
        var field_actions = actions.prev();

        // Stores current value
        field.data('current', field.val() );

        if( ! field.hasClass('.select2-hidden-accessible') ) {

            // User/Recipient Select2

            field.gamipress_select2({
                ajax: {
                    url: ajaxurl,
                    dataType: 'json',
                    delay: 250,
                    type: 'POST',
                    data: function( params ) {
                        return {
                            q: params.term,
                            action: 'gamipress_get_users',
                            nonce: gamipress_transfers_transfers.nonce,
                        };
                    },
                    processResults: gamipress_select2_users_process_results
                },
                escapeMarkup: function ( markup ) { return markup; }, // Let our custom formatter work
                templateResult: gamipress_select2_users_template_result,
                theme: 'default gamipress-select2',
                placeholder: 'Select an User',
                allowClear: true,
                multiple: false
            });

        }

        // Hide actions
        actions.slideUp();

        // Show the field, select2 and his actions
        field.slideDown();
        field.next('.select2').slideDown();
        field_actions.slideDown();

    });

    // Click on change user and recipient select

    $('.save-user-select').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);
        var field = $this.closest('.cmb-td').find('select');
        var field_actions = $this.parent();
        var actions = field_actions.next();

        var user_id = field.val();
        var prev_id = field.data( 'current' );

        // Check if user has truly changed
        if( parseInt( user_id ) !== parseInt( prev_id ) ) {

            // Update display name and email
            var user_data = field.gamipress_select2('data')[0];
            var user_name_element = $this.closest('.cmb-td').find('h2');
            var user_email_element = $this.closest('.cmb-td').find('.user-email');

            if( user_data.user_email !== undefined ) {
                user_name_element.html( user_data.display_name );
                user_email_element.html( user_data.user_email );
            } else{
                // user data without email has not been loaded through ajax, so it means that is the original user option
                user_name_element.html( user_name_element.data('original') );
                user_email_element.html( user_email_element.data('original') );
            }

            // Update view profile link
            var profile_link = actions.find('a');

            profile_link.attr( 'href', profile_link.attr('href').replace( 'user_id=' + prev_id, 'user_id=' + user_id ) );

            // If user link is target to profile.php, then force the replacement
            profile_link.attr( 'href', profile_link.attr('href').replace( 'profile.php', 'user-edit.php?user_id=' + user_id ) );

        }

        // Show actions
        actions.slideDown();

        // Hide the field, select2 and his actions
        field.slideUp();
        field.next('.select2').slideUp();
        field_actions.slideUp();

    });

    // Click on cancel user and recipient select

    $('.cancel-user-select').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);
        var field = $this.closest('.cmb-td').find('select');
        var field_actions = $this.parent();
        var actions = field_actions.next();

        // Restore field value
        field.val( field.data('current' ) );

        // Show actions
        actions.slideDown();

        // Hide the field, select2 and his actions
        field.slideUp();
        field.next('.select2').slideUp();
        field_actions.slideUp();


    });

    // Transfer items post assignments

    function gamipress_transfers_transfer_check_item_assignments( item ) {
        // Setup vars
        var container = item.find('.gamipress-transfers-transfer-items-assignment');
        var container_text = container.find('.gamipress-transfers-transfer-items-assignment-text');
        var container_fields = container.find('.gamipress-transfers-transfer-items-assignment-fields');

        var post_id = parseInt( item.find('input[name$="[post_id]"]').val() );
        var post_type = item.find('input[name$="[post_type]"]').val();

        if( post_id === 0 || isNaN( post_id ) ) {
            // Set the no assignment text
            container_text.html( gamipress_transfers_transfers.strings.no_assignment );
        } else {
            // Build the assignment link title
            var item_title = '';

            if( post_type in gamipress_transfers_transfers.points_types ) {
                item_title = gamipress_transfers_transfers.points_types[post_type].plural_name;
            } else if( post_type in gamipress_transfers_transfers.achievement_types ) {
                item_title = gamipress_transfers_transfers.achievement_types[post_type].singular_name;
            } else if( post_type in gamipress_transfers_transfers.rank_types ) {
                item_title = gamipress_transfers_transfers.rank_types[post_type].singular_name;
            }

            // Built the item link
            var item_url = gamipress_transfers_transfers.admin_url + 'post.php?post=' + post_id + '&action=edit';
            var item_link = '<a href="' + item_url + '" target="_blank">' + item_title + '</a>';

            // Replace {item_link}
            var text = gamipress_transfers_transfers.strings.assignment.replace( '{item_link}', item_link );

            container_text.html( text );

            // Update post type value
            container_fields.find('.gamipress-transfers-transfer-items-assignment-post-type').val(post_type);
        }
    }

    // Initial check

    $('.cmb2-id-transfer-items .cmb-repeatable-grouping').each(function() {
        gamipress_transfers_transfer_check_item_assignments( $(this) );
    });

    // Click on add new group

    $('body').on('click', '.cmb2-id-transfer-items .cmb-add-group-row', function(e) {

        // Add a timeout to get the last one
        setTimeout( function() {
            var item = $('.cmb2-id-transfer-items').find('.cmb-repeatable-grouping').last();

            gamipress_transfers_transfer_check_item_assignments( item );
        }, 10 );
    });

    // Click on assign post to item

    $('body').on('click', '.gamipress-transfers-transfer-items-assignment .gamipress-transfers-assign-post-to-item', function(e) {
        e.preventDefault();

        var container = $(this).closest('.gamipress-transfers-transfer-items-assignment');
        var container_text = container.find('.gamipress-transfers-transfer-items-assignment-text');
        var container_fields = container.find('.gamipress-transfers-transfer-items-assignment-fields');

        // Toggle visibility
        container_text.slideUp();
        container_fields.slideDown();
    });

    // Click on unassign post to item

    $('body').on('click', '.gamipress-transfers-transfer-items-assignment .gamipress-transfers-unassign-post-to-item', function(e) {
        e.preventDefault();

        var item = $(this).closest('.cmb-repeatable-grouping');

        item.find('input[name$="[post_id]"]').val( '0' );
        item.find('input[name$="[post_type]"]').val( '' );

        gamipress_transfers_transfer_check_item_assignments( item );
    });

    // Change assignment post type
    $('body').on('change', '.gamipress-transfers-transfer-items-assignment .gamipress-transfers-transfer-items-assignment-post-type', function(e) {

        // Setup vars
        var post_type = $(this).val();
        var item = $(this).closest('.cmb-repeatable-grouping');
        var container = $(this).closest('.gamipress-transfers-transfer-items-assignment');
        var container_text = container.find('.gamipress-transfers-transfer-items-assignment-text');
        var container_fields = container.find('.gamipress-transfers-transfer-items-assignment-fields');
        var post_id_field = container_fields.find('.gamipress-transfers-transfer-items-assignment-post-id');

        if( post_type in gamipress_transfers_transfers.points_types ) {
            // For points types, we have the IDs at localized vars
            var post_id = gamipress_transfers_transfers.points_types[post_type].ID;
            var plural_name = gamipress_transfers_transfers.points_types[post_type].plural_name;

            // Add a new option to the post ID field and set it as value
            post_id_field.html('<option value="' + post_id + '">' + plural_name + '</option>');
            post_id_field.val(post_id);

            // Hide the post ID field
            post_id_field.hide();
        } else {
            // For achievement and rank types

            var action = '';

            if( post_type in gamipress_transfers_transfers.achievement_types ) {
                action = 'gamipress_get_achievements_options_html';
            } else if( post_type in gamipress_transfers_transfers.rank_types ) {
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
                    nonce: gamipress_transfers_transfers.nonce,
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

    $('body').on('click', '.gamipress-transfers-transfer-items-assignment .save-assignment', function(e) {
        e.preventDefault();

        if( $(this).hasClass('disabled') ) {
            return;
        }

        // Setup vars
        var item = $(this).closest('.cmb-repeatable-grouping');
        var container = $(this).closest('.gamipress-transfers-transfer-items-assignment');
        var container_text = container.find('.gamipress-transfers-transfer-items-assignment-text');
        var container_fields = container.find('.gamipress-transfers-transfer-items-assignment-fields');
        var post_id_field = container_fields.find('.gamipress-transfers-transfer-items-assignment-post-id');

        var post_id = 0;
        var post_type = container_fields.find('.gamipress-transfers-transfer-items-assignment-post-type').val();

        if( post_type in gamipress_transfers_transfers.points_types ) {
            // For points types, we have the IDs at localized vars
            post_id = gamipress_transfers_transfers.points_types[post_type].ID;
        } else {
            post_id = parseInt( post_id_field.val() );
        }

        item.find('input[name$="[post_id]"]').val( post_id );
        item.find('input[name$="[post_type]"]').val( post_type );

        gamipress_transfers_transfer_check_item_assignments( item );

        // Toggle visibility
        container_text.slideDown();
        container_fields.slideUp();
    });

    // Click on cancel assign post to item

    $('body').on('click', '.gamipress-transfers-transfer-items-assignment .cancel-assignment', function(e) {
        e.preventDefault();

        var container = $(this).closest('.gamipress-transfers-transfer-items-assignment');
        var container_text = container.find('.gamipress-transfers-transfer-items-assignment-text');
        var container_fields = container.find('.gamipress-transfers-transfer-items-assignment-fields');

        // Toggle visibility
        container_text.slideDown();
        container_fields.slideUp();
    });

    // Transfer notes

    $('#add-new-transfer-note').on('click', function(e) {
        e.preventDefault();

        // Toggle visibility
        $(this).parent().slideUp();
        $('#new-transfer-note-fieldset').slideDown();
    });

    // Save note

    $('#save-transfer-note').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);

        if( $this.hasClass('disabled') ) {
            return;
        }

        // Disable the button
        $this.addClass('disabled');

        // Save the transfer note
        var title = $('#transfer-note-title').val();
        var description = $('#transfer-note-description').val();
        var notice = $('#new-transfer-note-submit .notice');

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
                action: 'gamipress_transfers_add_transfer_note',
                nonce: gamipress_transfers_transfers.nonce,
                transfer_id: $('#ct_edit_form input#object_id').val(),
                title: title,
                description: description
            },
            success: function( response ) {

                if( response.success ) {
                    // Add transfer note to the list of notes (at the top of the list!)
                    $('.transfer-notes-list tbody').prepend(response.data);

                    // Toggle visibility
                    $this.closest('#new-transfer-note-fieldset').slideUp();
                    $this.closest('#new-transfer-note-fieldset').prev().slideDown();

                    // Clear fields
                    $('#transfer-note-title').val('');
                    $('#transfer-note-description').val('');
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

    $('#cancel-transfer-note').on('click', function(e) {
        e.preventDefault();

        // Toggle visibility
        $(this).closest('#new-transfer-note-fieldset').slideUp();
        $(this).closest('#new-transfer-note-fieldset').prev().slideDown();

        // Clear fields
        $('#transfer-note-title').val('');
        $('#transfer-note-description').val('');
    });

    // Delete note
    $('.transfer-note .row-actions .delete').on('click', function(e) {
        e.preventDefault();

        var confirmed = confirm('Do you want to remove this transfer note?');

        var $this = $(this);

        if ( confirmed ) {

            // Hide note
            $this.closest('.transfer-note').fadeOut();

            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'gamipress_transfers_delete_transfer_note',
                    nonce: gamipress_transfers_transfers.nonce,
                    transfer_note_id: $this.data('transfer-note-id'),
                },
                success: function( response ) {

                    if( response.success ) {

                        // Remove the note
                        $this.closest('.transfer-note').remove();

                    } else {
                        // TODO: Report error

                        // Show note again
                        $this.closest('.transfer-note').fadeIn();
                    }
                }
            });
        }
    });

})( jQuery );