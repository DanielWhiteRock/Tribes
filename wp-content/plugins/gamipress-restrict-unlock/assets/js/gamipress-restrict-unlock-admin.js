(function( $ ) {

    var prefix = '-gamipress-restrict-unlock-';
    var _prefix = '_gamipress_restrict_unlock_';

    // On change restrict checkbox (achievements and ranks), toggle fields visibility

    var restrict_common_fields = '.cmb2-id-' + prefix + 'unlock-by, '
        + '.cmb2-id-' + prefix + 'restrict-steps, '
        + '.cmb2-id-' + prefix + 'restrict-rank-requirements, '
        + '.cmb2-id-' + prefix + 'access-with-points, '
        + '.cmb2-id-' + prefix + 'points-to-access, '
        + '.cmb2-id-' + prefix + 'requirements, '
        + '.cmb2-id-' + prefix + 'informational-text, '
        + '#gamipress-restrict-unlock-tab-users';

    $('#' + _prefix + 'restrict').on('change', function() {
        // All fields selector
        var selector = restrict_common_fields;

        if( $(this).prop('checked') ) {

            // Selector when enabled
            selector = '.cmb2-id-' + prefix + 'unlock-by, '
                + '.cmb2-id-' + prefix + 'restrict-steps, '
                + '.cmb2-id-' + prefix + 'restrict-rank-requirements, '
                + '.cmb2-id-' + prefix + 'informational-text, '
                + '#gamipress-restrict-unlock-tab-users';

            // Trigger change on unlock by select
            $('#' + _prefix + 'unlock_by').trigger('change');

            $(selector).slideDown();
        } else {
            $(selector).slideUp();
        }
    });

    $('#' + _prefix + 'restrict').trigger('change');

    // On change restrict points awards or deducts checkbox (points types), toggle fields visibility

    $( '#' + _prefix + 'restrict_points_awards, '
        + '#' + _prefix + 'restrict_points_deducts' ).on('change', function() {
        // All fields selector
        var selector = restrict_common_fields;

        if( $('#' + _prefix + 'restrict_points_awards').prop('checked')
            || $('#' + _prefix + 'restrict_points_deducts').prop('checked') ) {

            // Selector when enabled
            selector = '.cmb2-id-' + prefix + 'unlock-by, '
                + '.cmb2-id-' + prefix + 'informational-text, '
                + '#gamipress-restrict-unlock-tab-users';

            // Trigger change on unlock by select
            $('#' + _prefix + 'unlock_by').trigger('change');

            $(selector).slideDown();
        } else {
            $(selector).slideUp();
        }
    });

    if( $('#' + _prefix + 'restrict_points_awards').length
        && ! ( $('#' + _prefix + 'restrict_points_awards').prop('checked') || $('#' + _prefix + 'restrict_points_deducts').prop('checked') ) )
        $( restrict_common_fields ).hide();

    if( $('#' + _prefix + 'restrict_points_deducts').length
        && ! ( $('#' + _prefix + 'restrict_points_awards').prop('checked') || $('#' + _prefix + 'restrict_points_deducts').prop('checked') ) )
        $( restrict_common_fields ).hide();

    // On change unlock by select, toggle fields visibility

    $('#' + _prefix + 'unlock_by').on('change', function() {

        if( $('#' + _prefix + 'restrict').length
            && ! $('#' + _prefix + 'restrict').prop('checked') ) {
            return;
        } else if( $('#' + _prefix + 'restrict_points_awards').length
            && ! ( $('#' + _prefix + 'restrict_points_awards').prop('checked')
                || $('#' + _prefix + 'restrict_points_deducts').prop('checked') ) ) {
            return;
        }

        var unlock_by = $(this).val();

        if( unlock_by === 'complete-requirements' ) {
            $('.cmb2-id-' + prefix + 'requirements, .cmb2-id-' + prefix + 'access-with-points').slideDown().removeClass('cmb2-tab-ignore');

            // Check if access with points is checked
            if( $('#' + _prefix + 'access_with_points').prop('checked') ) {
                $('.cmb2-id-' + prefix + 'points-to-access').slideDown().removeClass('cmb2-tab-ignore');
            } else {
                $('.cmb2-id-' + prefix + 'points-to-access').slideUp().addClass('cmb2-tab-ignore');
            }

        } else if( unlock_by === 'expend-points' ) {
            $('.cmb2-id-' + prefix + 'requirements, .cmb2-id-' + prefix + 'access-with-points').slideUp().addClass('cmb2-tab-ignore');
            $('.cmb2-id-' + prefix + 'points-to-access').slideDown().removeClass('cmb2-tab-ignore');
        }
    });

    $('#' + _prefix + 'unlock_by').trigger('change');

    // On change access with points checkbox, toggle fields visibility

    $('#' + _prefix + 'access_with_points').on('change', function() {

        var target = $('.cmb2-id-' + prefix + 'points-to-access');

        if( $('#' + _prefix + 'restrict').length
            && ! $('#' + _prefix + 'restrict').prop('checked') ) {
            target.slideUp().addClass('cmb2-tab-ignore');
            return;
        } else if( $('#' + _prefix + 'restrict_points_awards').length
            && ! ( $('#' + _prefix + 'restrict_points_awards').prop('checked')
                || $('#' + _prefix + 'restrict_points_deducts').prop('checked') ) ) {
            target.slideUp().addClass('cmb2-tab-ignore');
            return;
        }

        // Prevent to hide points input if unlock by is no set to complete requirements
        if( $('#' + _prefix + 'unlock_by').val() !== 'complete-requirements' ) {
            return;
        }

        if( $(this).prop('checked') ) {
            target.slideDown().removeClass('cmb2-tab-ignore');
        } else {
            target.slideUp().addClass('cmb2-tab-ignore');
        }
    });

    if( ! $('#' + _prefix + 'access_with_points').prop('checked') && $('#' + _prefix + 'unlock_by').val() === 'complete-requirements' ) {
        $('.cmb2-id-' + prefix + 'points-to-access').hide().addClass('cmb2-tab-ignore');
    }

    // On change requirement type, change fields visibility

    $('body').on('change', 'select[name$="[' + _prefix + 'type]"]', function() {

        var row = $(this).closest('.cmb-row');
        var type = $(this).val();

        // Points fields
        var points = row.siblings('.cmb-row[class*="-' + prefix + 'points "]'); // Add an extra space to don't match other fields
        var points_type = row.siblings('.cmb-row[class*="-' + prefix + 'points-type"]');

        // Rank fields
        var rank = row.siblings('.cmb-row[class*="-' + prefix + 'rank "]'); // Add an extra space to don't match other fields

        // Achievement fields
        var achievement = row.siblings('.cmb-row[class*="-' + prefix + 'achievement "]'); // Add an extra space to don't match other fields
        var achievement_type = row.siblings('.cmb-row[class*="-' + prefix + 'achievement-type"]');

        // The rest of fields
        var count = row.siblings('.cmb-row[class*="-' + prefix + 'count"]');

        // Hide all
        points.hide();
        points_type.hide();
        rank.hide();
        achievement.hide();
        achievement_type.hide();
        count.hide();

        if( type === 'points-balance' ) {
            points.show();
            points_type.show();
        } else if( type === 'earn-rank' ) {
            rank.show();
        } else if( type === 'specific-achievement' ) {
            achievement.show();

            if( count.find('input').val() === '' )
                count.find('input').val('1');

            count.show();
        } else if(  type === 'any-achievement' ) {

            achievement_type.show();

            if( count.find('input').val() === '' )
                count.find('input').val('1');

            count.show();
        } else if( type === 'all-achievements' ) {
            achievement_type.show();
        }

        gamipress_restrict_unlock_generate_label( row.parent() );

    });

    // Before trigger change initialize already defined labels to avoid auto generation

    $('input[name$="[' + _prefix + 'label]"]').each(function() {
        if( $(this).val() !== '' ) {
            $(this).attr( 'data-changed', 'true' );
        }
    });

    // Initial trigger of type change to start the important javascript functions
    $('select[name$="[' + _prefix + 'type]"]').trigger('change');

    // Trigger label generation on change any input
    $('body').on('change', 'input[name$="[' + _prefix + 'points]"], '
        + 'select[name$="[' + _prefix + 'points_type]"], '
        + 'select[name$="[' + _prefix + 'achievement]"], '
        + 'select[name$="[' + _prefix + 'achievement_type]"], '
        + 'select[name$="[' + _prefix + 'rank]"], '
        + 'input[name$="[' + _prefix + 'count]"]', function() {
        gamipress_restrict_unlock_generate_label( $(this).closest('.postbox') );
    });

    // If user manually changes the label, then respect user one
    $('body').on('change', 'input[name$="[' + _prefix + 'label]"]', function() {

        if( $(this).val() !== '' ) {
            $(this).attr( 'data-changed', 'true' );
        } else {
            $(this).attr( 'data-changed', 'false' );
            gamipress_restrict_unlock_generate_label( $(this).closest('.postbox') );
        }

    });

    // Adding a new group element
    $('body').on('click', '.cmb2-id-' + prefix + 'requirements .cmb-add-group-row', function() {

        var last = $(this).closest('.cmb-repeatable-group').find('.cmb-repeatable-grouping').last();

        var rank = last.find('select[name$="[' + _prefix + 'rank]"]');
        var achievement = last.find('select[name$="[' + _prefix + 'achievement]"]');

        // Remove select2 element
        rank.next('.select2').remove();
        achievement.next('.select2').remove();

        // Remove select options (since is a post selector)
        rank.find('option').remove();
        achievement.find('option').remove();

        // Re-init select2
        gamipress_post_selector( rank );
        gamipress_post_selector( achievement );

        // Reset change attr
        last.find('input[name$="[' + _prefix + 'label]"]').attr( 'data-changed', 'false' );

        // Trigger change on new group type select
        last.find('select[name$="[' + _prefix + 'type]"]').trigger('change');

    });

    function gamipress_restrict_unlock_generate_label( box ) {

        // Force regenerate on empty labels
        if(  box.find('input[name$="[' + _prefix + 'label]"]').val() === '' ) {
            box.find('input[name$="[' + _prefix + 'label]"]').attr( 'data-changed', 'false' );
        }

        if( box.find('input[name$="[' + _prefix + 'label]"]').attr( 'data-changed' ) === 'true' ) {
            return;
        }

        var type = box.find('select[name$="[' + _prefix + 'type]"]').val();

        var pattern = gamipress_restrict_unlock.labels[type];

        if( pattern === undefined ) {
            return;
        }

        var parsed_pattern = '';

        if( type === 'points-balance' ) {

            var points_type = box.find('select[name$="[' + _prefix + 'points_type]"]').val();
            var points_type_label = 'Points';

            if( points_type !== '' )
                points_type_label = box.find('select[name$="[' + _prefix + 'points_type]"] option[value="' + points_type + '"]').text();

            parsed_pattern = pattern
                .replace( '{points}', box.find('input[name$="[' + _prefix + 'points]"]').val() )
                .replace( '{points_type}', points_type_label );

        } else if( type === 'earn-rank' ) {

            var rank_id = box.find('select[name$="[' + _prefix + 'rank]"]').val();

            if( rank_id === '' ) {
                box.find('input[name$="[' + _prefix + 'label]"]').val( '' );
                return;
            }

            var rank_label = box.find('select[name$="[' + _prefix + 'rank]"] option[value="' + rank_id + '"]').text();
            // Remove the "(#{ID})" part
            rank_label = rank_label.split('(#')[0];

            parsed_pattern = pattern
                .replace( '{rank}', rank_label );

        } else if( type === 'specific-achievement' ) {

            var achievement_id = box.find('select[name$="[' + _prefix + 'achievement]"]').val();

            if( achievement_id === '' ) {
                box.find('input[name$="[' + _prefix + 'label]"]').val( '' );
                return;
            }

            var achievement_label = box.find('select[name$="[' + _prefix + 'achievement]"] option[value="' + achievement_id + '"]').text();
            // Remove the "(#{ID})" part
            achievement_label = achievement_label.split('(#')[0];

            var count = parseInt( box.find('input[name$="[' + _prefix + 'count]"]').val() );

            parsed_pattern = pattern
                .replace( '{achievement}', achievement_label )
                .replace( '{count}', count + ' ' + ( count === 1 ? 'time' : 'times' ) ); // TODO: add time/times localization

        } else if(  type === 'any-achievement' ) {
            var achievement_type = box.find('select[name$="[' + _prefix + 'achievement_type]"]').val();
            var achievement_type_label = box.find('select[name$="[' + _prefix + 'achievement_type]"] option[value="' + achievement_type + '"]').text();

            if( achievement_type === '' ) {
                box.find('input[name$="[' + _prefix + 'label]"]').val( '' );
                return;
            }

            var count = parseInt( box.find('input[name$="[' + _prefix + 'count]"]').val() );

            parsed_pattern = pattern
                .replace( '{achievement_type}', achievement_type_label )
                .replace( '{count}', count + ' ' + ( count === 1 ? 'time' : 'times' ) ); // TODO: add time/times localization
        } else if( type === 'all-achievements' ) {
            var achievement_type = box.find('select[name$="[' + _prefix + 'achievement_type]"]').val();
            var achievement_type_label = box.find('select[name$="[' + _prefix + 'achievement_type]"] option[value="' + achievement_type + '"]').text();

            if( achievement_type === '' ) {
                box.find('input[name$="[' + _prefix + 'label]"]').val( '' );
                return;
            }

            parsed_pattern = pattern
                .replace( '{achievement_type}', achievement_type_label );
        }

        box.find('input[name$="[' + _prefix + 'label]"]').val( parsed_pattern );

    }

})( jQuery );