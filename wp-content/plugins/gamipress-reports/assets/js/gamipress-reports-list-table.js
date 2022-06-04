(function( $ ) {

    // Helper to retrieve an URL parameter
    function gamipress_reports_list_table_get_parameter( sURL, sParam ) {

        var sPageURL = decodeURIComponent(sURL.split('?')[1]),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }

    }

    function gamipress_reports_list_table_add_loader( table ) {
        table.find('#the-list').append('<div class="gamipress-reports-loader"><span class="spinner is-active gamipress-reports-spinner"></span></div>')
    }

    function gamipress_reports_list_table_remove_loader( table ) {
        table.find('#the-list .gamipress-reports-loader').remove();
    }

    // Ajax pagination
    function gamipress_reports_list_table_paginate_table( table, paged ) {

        // Setup vars
        var args = table.data('args');

        // Turn query args into an object
        args = $.parseJSON( args.split("'").join('"') );

        // Add the table loader
        gamipress_reports_list_table_add_loader( table );

        $.ajax({
            url: ajaxurl,
            cache: false,
            data: {
                action: 'gamipress_reports_list_table_request',
                nonce: gamipress_reports_list_table.nonce,
                args: args,
                paged: paged
            },
            success: function( response ) {

                if( response.data.length ) {
                    var parsed_response = $(response.data);

                    // Update top and bottom pagination
                    table.find('.tablenav.top').html(parsed_response.find('.tablenav.top').html());
                    table.find('.tablenav.bottom').html(parsed_response.find('.tablenav.bottom').html());

                    // Update table content
                    table.find('.wp-list-table').html(parsed_response.find('.wp-list-table').html());
                }

            }
        });

    }

    // Initialize table listeners
    $('body').on('click', '.gamipress-reports-list-table .pagination-links a', function(e) {
        e.preventDefault();

        var url = $(this).attr('href');
        var paged = gamipress_reports_list_table_get_parameter( url, 'paged' );

        gamipress_reports_list_table_paginate_table( $(this).closest('.gamipress-reports-list-table'), paged );
    });

    $('body').on( 'change', '.gamipress-reports-points .paging-input .current-page', function(e) {
        var paged = $(this).val();

        var total_pages = parseInt( $(this).closest('.gamipress-reports-list-table').find('.tablenav.top .paging-input .total-pages').text() );

        if( paged > total_pages ) {
            paged = total_pages;
            $(this).val(total_pages)
        }

        gamipress_reports_list_table_paginate_table( $(this).closest('.gamipress-reports-list-table'), paged );
    });

})( jQuery );