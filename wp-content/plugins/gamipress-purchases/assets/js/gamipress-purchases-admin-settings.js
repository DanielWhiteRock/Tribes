(function( $ ) {

    // Currency Settings

    $('#gamipress_purchases_currency').gamipress_select2({ theme: 'default gamipress-select2' });

    // Gateways Settings

    $('.cmb2-id-gamipress-purchases-gateways input').on('change', function() {

        var $this = $(this);
        var gateway = $this.val();
        var tab = $this.closest('.cmb-tabs-wrap').find('.cmb-tabs .cmb-tab#gamipress-purchases-settings-tab-' + gateway);

        if( $this.prop('checked') ) {
            tab.slideDown();
        } else {
            tab.slideUp();
        }

    });

    $('.cmb2-id-gamipress-purchases-gateways input:checked').trigger('change');

    // Taxes Settings

    $('.cmb2-id-gamipress-purchases-enable-taxes input').on('change', function() {

        var target = $('.cmb2-id-gamipress-purchases-taxes, .cmb2-id-gamipress-purchases-default-tax');

        if( $(this).prop('checked') ) {
            target.slideDown().removeClass('cmb2-tab-ignore');

            $('.cmb2-id-gamipress-purchases-taxes').find('.cmb-row').addClass('cmb-tab-active-item');
        } else {
            target.slideUp().addClass('cmb2-tab-ignore');
        }

    });

    if( ! $('.cmb2-id-gamipress-purchases-enable-taxes input').prop('checked') ) {
        $('.cmb2-id-gamipress-purchases-taxes, .cmb2-id-gamipress-purchases-default-tax').hide().addClass('cmb2-tab-ignore');
    }

    // Taxes country select2

    $('.cmb2-id-gamipress-purchases-taxes select[name^="gamipress_purchases_taxes"][name$="[country]"]').gamipress_select2({ theme: 'default gamipress-select2' });

    $('.cmb2-id-gamipress-purchases-taxes .cmb-add-group-row').on('click', function() {

        // Add a timeout to get the last one
        setTimeout( function() {
            var last_row = $('.cmb2-id-gamipress-purchases-taxes').find('.cmb-repeatable-grouping').last();
            var new_select = last_row.find('select[name^="gamipress_purchases_taxes"][name$="[country]"]');

            // Remove Select2 container
            last_row.find('.select2').remove();

            // Apply the select2 to the new country select
            new_select.gamipress_select2({ theme: 'default gamipress-select2' });
        }, 10 );
    });

    // Send test email click

    $('#purchase-receipt-email-send, #new-sale-email-send').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);

        $this.prop( 'disabled', true );

        $this.parent().append('<span class="spinner is-active" style="float:none;"></span>');

        $.ajax({
            url: $this.attr('href'),
            method: 'get',
            success: function( response ) {
                $this.prop( 'disabled', false );
                $this.parent().find('.spinner').remove();
                $this.parent().append('<span class="send-response" ' + ( ! response.success ? 'style="color: #a00;' : '' ) + '">' + response.data + '</span>');

                setTimeout(function() {
                    $this.parent().find('.send-response').remove();
                }, 3000);
            },
            error: function( response ) {
                $this.prop( 'disabled', false );
                $this.parent().find('.spinner').remove();
                $this.parent().append('<span class="send-response" style="color: #a00;">' + response.data + '</span>');

                setTimeout(function() {
                    $this.parent().find('.send-response').remove();
                }, 3000);
            }

        });
    });

    // Disable purchase receipt email

    $('#gamipress_purchases_disable_purchase_receipt_email').on('change', function() {
        var target = $('.cmb2-id-gamipress-purchases-purchase-receipt-email-subject, .cmb2-id-gamipress-purchases-purchase-receipt-email-content');

        if( $(this).prop('checked') ) {
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            target.slideDown().removeClass('cmb2-tab-ignore');
        }
    } );

    if( $('#gamipress_purchases_disable_purchase_receipt_email').prop('checked') ) {
        $('.cmb2-id-gamipress-purchases-purchase-receipt-email-subject, .cmb2-id-gamipress-purchases-purchase-receipt-email-content').hide().addClass('cmb2-tab-ignore');
    }

    // Disable new sale email

    $('#gamipress_purchases_disable_new_sale_email').on('change', function() {
        var target = $('.cmb2-id-gamipress-purchases-new-sale-email-subject, .cmb2-id-gamipress-purchases-new-sale-email-content');

        if( $(this).prop('checked') ) {
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            target.slideDown().removeClass('cmb2-tab-ignore');
        }
    } );

    if( $('#gamipress_purchases_disable_new_sale_email').prop('checked') ) {
        $('.cmb2-id-gamipress-purchases-new-sale-email-subject, .cmb2-id-gamipress-purchases-new-sale-email-content').hide().addClass('cmb2-tab-ignore');
    }
})( jQuery );