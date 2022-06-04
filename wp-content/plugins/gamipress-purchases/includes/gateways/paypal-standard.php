<?php
/**
 * PayPal Standard Gateway
 *
 * @package     GamiPress\Purchases\Gateways\PayPal_Standard
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register this gateway
 *
 * @since 1.0.0
 *
 * @param array $gateways
 *
 * @return array
 */
function gamipress_purchases_register_paypal_standard_gateway( $gateways = array() ) {

    $gateways['paypal_standard'] = is_admin() ? __( 'PayPal Standard', 'gamipress-purchases' ) : __( 'PayPal', 'gamipress-purchases' );

    return $gateways;

}
add_filter( 'gamipress_purchases_get_gateways', 'gamipress_purchases_register_paypal_standard_gateway' );

/**
 * Render this gateway settings fields
 *
 * @since 1.0.0
 *
 * @param array     $fields   Fields container
 *
 * @return array
 */
function gamipress_purchases_paypal_standard_settings_fields( $fields ) {

    $prefix = 'gamipress_purchases_paypal_standard_';

    $fields = array_merge( $fields, array(

        $prefix . 'email' => array(
            'name' => __( 'PayPal Email', 'gamipress-purchases' ),
            'desc' => __( 'Enter your PayPal account\'s email.', 'gamipress-purchases' ),
            'type' => 'text',
        ),
        $prefix . 'image' => array(
            'name' => __( 'PayPal Image', 'gamipress-purchases' ),
            'desc' => __( 'Upload, choose or paste the URL of the image URL.', 'gamipress-purchases' ),
            'type' => 'file',
        ),
        $prefix . 'sandbox' => array(
            'name' => __( 'Sandbox Mode', 'gamipress-purchases' ),
            'desc' => __( 'Enable PayPal sandbox mode.', 'gamipress-purchases' ),
            'type' => 'checkbox',
            'classes' => 'gamipress-switch'
        ),
        $prefix . 'disable_ipn_verification' => array(
            'name' => __( 'Disable IPN Verification', 'gamipress-purchases' ),
            'desc' => __( 'If payments are not getting marked as complete, then check this box. This forces the site to use a slightly less secure method of verifying purchases.', 'gamipress-purchases' ),
            'type' => 'checkbox',
            'classes' => 'gamipress-switch'
        ),
        $prefix . 'text' => array(
            'name' => __( 'Text', 'gamipress-purchases' ),
            'desc' => __( 'Text to show to customers when PayPal was select as payment method.', 'gamipress-purchases' ),
            'type' => 'wysiwyg',
            'default' => __( 'Pay via PayPal. You can pay with your credit card if you don\'t have a PayPal account.', 'gamipress-purchases' ),
        ),
        $prefix . 'api_title' => array(
            'name' => __( 'API Credentials', 'gamipress-purchases' ),
            'desc' => sprintf(
                __( 'Enter your PayPal API credentials to process refunds via PayPal. Learn how to access your %s.', 'gamipress-purchases' ),
                '<a href="https://developer.paypal.com/webapps/developer/docs/classic/api/apiCredentials/#creating-an-api-signature" target="_blank">'
                    . __( 'PayPal API Credentials', 'gamipress-purchases' )
                . '</a>'
            ),
            'type' => 'title',
        ),
        $prefix . 'api_username' => array(
            'name' => __( 'API Username', 'gamipress-purchases' ),
            'desc' => __( 'Your PayPal API username.', 'gamipress-purchases' ),
            'type' => 'text',
        ),
        $prefix . 'api_password' => array(
            'name' => __( 'API Password', 'gamipress-purchases' ),
            'desc' => __( 'Your PayPal API password.', 'gamipress-purchases' ),
            'type' => 'text',
        ),
        $prefix . 'api_signature' => array(
            'name' => __( 'API Signature', 'gamipress-purchases' ),
            'desc' => __( 'Your PayPal API signature.', 'gamipress-purchases' ),
            'type' => 'text',
        ),

    ) );

    return $fields;

}
add_filter( 'gamipress_purchases_settings_fields', 'gamipress_purchases_paypal_standard_settings_fields' );

/**
 * Render this gateway settings tabs
 *
 * @since 1.0.0
 *
 * @param array     $tabs   Fields container
 *
 * @return array
 */
function gamipress_purchases_paypal_standard_settings_tabs( $tabs ) {

    $prefix = 'gamipress_purchases_paypal_standard_';

    $tabs = array_merge( $tabs, array(

        'paypal_standard' => array(
            'icon' => 'dashicons-paypal',
            'title' => __( 'PayPal Standard', 'gamipress-purchases' ),
            'fields' => array(
                $prefix . 'email',
                $prefix . 'image',
                $prefix . 'sandbox',
                $prefix . 'disable_ipn_verification',
                $prefix . 'text',
                $prefix . 'api_title',
                $prefix . 'api_username',
                $prefix . 'api_password',
                $prefix . 'api_signature',
            ),
        ),

    ) );

    return $tabs;

}
add_filter( 'gamipress_purchases_settings_tabs', 'gamipress_purchases_paypal_standard_settings_tabs' );

/**
 * Render this gateway form
 *
 * @since 1.0.0
 *
 * @param integer   $user_id        User ID
 * @param array     $user_details   User billing details
 * @param array     $template_args  Template received arguments
 */
function gamipress_purchases_paypal_standard_form( $user_id, $user_details, $template_args ) {

    $prefix = 'paypal_standard_';

    $text = gamipress_purchases_get_option( $prefix . 'text', '' ); ?>

    <?php if( ! empty( $text ) ) : ?>

        <div class="gamipress-purchases-gateway-paypal-standard-text">
            <?php echo $text; ?>
        </div>

    <?php endif; ?>

    <?php
}
add_action( 'gamipress_purchases_paypal_standard_form', 'gamipress_purchases_paypal_standard_form', 10, 3 );

/**
 * Process payment
 *
 * @since 1.0.0
 *
 * @param   array     $gateway_response   Waiting gateway response
 * @param   array     $payment            Payment data array
 * @param   array     $payment_items      Payment items array
 *
 * @return  array    $gateway_response    Gateway response
 */
function gamipress_purchases_paypal_standard_process_payment( $gateway_response, $payment, $payment_items ) {

    $nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

    // Security check
    if ( ! wp_verify_nonce( $nonce, 'gamipress_purchases_purchase_form' ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress-purchases' ) );
    }

    // Referrer was check on ajax function
    $referrer = $_POST['referrer'];

    // Setup the payments table
    $ct_table = ct_setup_table( 'gamipress_payments' );

    // Update the payment status from processing to pending and wait to PayPal response
    $ct_table->db->update(
        array( 'status' => 'pending' ),
        array( 'payment_id' => $payment['payment_id'] )
    );

    // Mark payment to meet that has been made with sandbox mode enabled
    $sandbox = (bool) gamipress_purchases_get_option( 'paypal_standard_sandbox', false );

    ct_update_object_meta( $payment['payment_id'], '_gamipress_purchases_paypal_standard_sandbox', ( $sandbox ? '1' : '0' ) );

    // Only send to PayPal if the pending payment is created successfully
    $listener_url = add_query_arg( 'gamipress-listener', 'IPN', home_url( 'index.php' ) );

    // Get the success url
    $return_url = add_query_arg( array(
        'payment-confirmation' => 'paypal_standard',
        'payment-id' => $payment['payment_id']
    ), $referrer );

    // Get the cancel url
    $cancel_url = add_query_arg( array(
        'payment-cancelled' => 'paypal_standard',
        'payment-id' => $payment['payment_id']
    ), $referrer );

    // Setup PayPal arguments
    $paypal_args = array(
        'business'      => gamipress_purchases_get_option( 'paypal_standard_email', false ),
        'image_url'     => gamipress_purchases_get_option( 'paypal_standard_image', '' ),
        'invoice'       => $payment['purchase_key'],
        'no_shipping'   => '1',
        'shipping'      => '0',
        'no_note'       => '1',
        'currency_code' => gamipress_purchases_get_currency(),
        'charset'       => get_bloginfo( 'charset' ),
        'custom'        => $payment['payment_id'],
        'rm'            => '2',
        'return'        => $return_url,
        'cancel_return' => $cancel_url,
        'notify_url'    => $listener_url,
        'cbt'           => get_bloginfo( 'name' ),
        'cmd'           => '_cart',
        'upload'        => '1',
        'first_name'    => $payment['first_name'],
        'last_name'     => $payment['last_name'],
        'email'         => $payment['email'],
        'address1'      => $payment['address_1'],
        'address2'      => $payment['address_2'],
        'city'          => $payment['city'],
        'zip'           => $payment['postcode'],
        'country'       => $payment['country'],
        'state'         => $payment['state'],
    );

    // Add the payment items
    $i = 1;

    if( is_array( $payment_items ) && ! empty( $payment_items ) ) {

        foreach ( $payment_items as $item ) {

            // Setting low conversion on a points type makes points price less than 0.01 that is the minimum supported by PayPal
            // So we need to change the item price with the total amount purchased
            if( floatval( $item['price'] ) > 0.01 ) {
                $paypal_args['item_name_' . $i ] = stripslashes_deep( html_entity_decode( $item['description'], ENT_COMPAT, 'UTF-8' ) );
                $paypal_args['quantity_' . $i ]  = $item['quantity'];
                $paypal_args['amount_' . $i ]    = $item['price'];
            } else {
                $paypal_args['item_name_' . $i ] = stripslashes_deep( html_entity_decode( $item['description'] . ' x' .  $item['quantity'], ENT_COMPAT, 'UTF-8' ) );
                $paypal_args['quantity_' . $i ]  = 1;
                $paypal_args['amount_' . $i ]    = $item['total'];
            }

            if ( $item['post_id'] ) {
                $paypal_args['item_number_' . $i ] = $item['post_id'];
            }

            $i++;

        }

    }

    // Add taxes to the payment
    if ( (bool) gamipress_purchases_get_option( 'enable_taxes', false ) ) {

        $paypal_args['tax_cart'] = gamipress_purchases_format_amount( $payment['tax_amount'] );

    }

    /**
     * Filter to override PayPal Standard args
     *
     * @since 1.0.0
     *
     * @param array $paypal_args
     * @param array $payment
     * @param array $payment_items
     *
     * @return array $paypal_args
     */
    $paypal_args = apply_filters( 'gamipress_purchases_paypal_standard_args', $paypal_args, $payment, $payment_items );

    // Get the PayPal redirect uri
    $paypal_redirect = trailingslashit( gamipress_purchases_get_paypal_standard_redirect() ) . '?';

    // Build query
    $paypal_redirect .= http_build_query( $paypal_args );

    // Fix for some sites that encode the entities
    $paypal_redirect = str_replace( '&amp;', '&', $paypal_redirect );

    // Redirect to PayPal
    $gateway_response['success'] = true;
    $gateway_response['message'] = __( 'Your purchase has been processed successfully. Redirecting to PayPal...', 'gamipress-purchases' );
    $gateway_response['redirect'] = true;
    $gateway_response['redirect_url'] = $paypal_redirect;

    return $gateway_response;

}
add_filter( 'gamipress_purchases_paypal_standard_process_payment', 'gamipress_purchases_paypal_standard_process_payment', 10, 4 );

/**
 * Listener for a PayPal payment cancellation
 *
 * @since 1.0.0
 *
 * @return void
 */
function gamipress_purchases_listen_for_paypal_standard_cancelled() {

    if ( isset( $_GET['payment-cancelled'] ) && $_GET['payment-cancelled'] == 'paypal_standard' ) {

        $payment_id = isset( $_GET['payment-id'] ) ? absint( $_GET['payment-id'] ) : 0;

        // Check the payment ID
        if( $payment_id !== 0 ) {

            ct_setup_table( 'gamipress_payments' );

            $payment = ct_get_object( $payment_id );

            // just continue if payment exists and is pending
            if( $payment && $payment->status === 'pending' ) {
                // Update the payment status
                gamipress_purchases_update_payment_status( $payment_id, 'cancelled' );
            }

            ct_reset_setup_table();
        }

        // Remove query args
        wp_redirect( remove_query_arg( array( 'payment-cancelled', 'payment-id' ) ) );
        exit;
    }

}
add_action( 'template_redirect', 'gamipress_purchases_listen_for_paypal_standard_cancelled' );

/**
 * Listener for a PayPal IPN requests and then sends to the processing function
 *
 * @since 1.0.0
 *
 * @return void
 */
function gamipress_purchases_listen_for_paypal_standard_ipn() {
    // Regular PayPal IPN
    if ( isset( $_GET['gamipress-listener'] ) && $_GET['gamipress-listener'] == 'IPN' ) {
        do_action( 'gamipress_purchases_verify_paypal_standard_ipn' );
    }
}
add_action( 'init', 'gamipress_purchases_listen_for_paypal_standard_ipn' );

/**
 * Process PayPal IPN
 *
 * @since 1.0
 */
function gamipress_purchases_process_paypal_standard_ipn() {

    // Check the request method is POST
    if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] != 'POST' ) {
        return;
    }

    // Set initial post data to empty string
    $post_data = '';

    // Fallback just in case post_max_size is lower than needed
    if ( ini_get( 'allow_url_fopen' ) ) {
        $post_data = file_get_contents( 'php://input' );
    } else {
        // If allow_url_fopen is not enabled, then make sure that post_max_size is large enough
        ini_set( 'post_max_size', '12M' );
    }
    // Start the encoded data collection with notification command
    $encoded_data = 'cmd=_notify-validate';

    // Get current arg separator
    $arg_separator = ini_get( 'arg_separator.output' );

    // Verify there is a post_data
    if ( $post_data || strlen( $post_data ) > 0 ) {
        // Append the data
        $encoded_data .= $arg_separator . $post_data;
    } else {
        // Check if POST is empty
        if ( empty( $_POST ) ) {
            // Nothing to do
            return;
        } else {
            // Loop through each POST
            foreach ( $_POST as $key => $value ) {
                // Encode the value and append the data
                $encoded_data .= $arg_separator . "$key=" . urlencode( $value );
            }
        }
    }

    // Convert collected post data to an array
    parse_str( $encoded_data, $encoded_data_array );

    foreach ( $encoded_data_array as $key => $value ) {

        if ( false !== strpos( $key, 'amp;' ) ) {
            $new_key = str_replace( '&amp;', '&', $key );
            $new_key = str_replace( 'amp;', '&', $new_key );

            unset( $encoded_data_array[ $key ] );
            $encoded_data_array[ $new_key ] = $value;
        }

    }

    if ( ! (bool) gamipress_purchases_get_option( 'paypal_standard_disable_ipn_verification', false ) ) {

        // Validate the IPN

        $remote_post_vars = array(
            'method'      => 'POST',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking'    => true,
            'headers'     => array(
                'host'         => 'www.paypal.com',
                'connection'   => 'close',
                'content-type' => 'application/x-www-form-urlencoded',
                'post'         => '/cgi-bin/webscr HTTP/1.1',
                'user-agent'   => 'GamiPress IPN Verification/' . GAMIPRESS_VER . '; ' . get_bloginfo( 'url' )

            ),
            'sslverify'   => false,
            'body'        => $encoded_data_array
        );

        // Get response
        $api_response = wp_remote_post(
            gamipress_purchases_get_paypal_standard_redirect( true, true ),
            $remote_post_vars
        );

        $has_errors = false;

        if ( is_wp_error( $api_response ) ) {

            // Something went wrong
            $has_errors = true;
        }

        if ( ! $has_errors && wp_remote_retrieve_body( $api_response ) !== 'VERIFIED' ) {

            // Response not okay
            $has_errors = true;
        }

        // If there is any error, log it
        if( $has_errors ) {

            gamipress_purchases_log_gateway_error(
                __( 'PayPal Standard: IPN Error', 'gamipress-purchases' ),
                sprintf( __( 'Invalid IPN verification response. Response: %s', 'gamipress-purchases' ), json_encode( $api_response ) )
            );

        }
    }

    // Check if $post_data_array has been populated
    if ( ! is_array( $encoded_data_array ) && ! empty( $encoded_data_array ) ) {
        return;
    }

    $defaults = array(
        'txn_type'       => '',
        'payment_status' => ''
    );

    $encoded_data_array = wp_parse_args( $encoded_data_array, $defaults );

    $payment_id = 0;

    // If transaction ID given, then search payment ID by it
    if ( ! empty( $encoded_data_array[ 'parent_txn_id' ] ) ) {
        $payment_id = gamipress_purchases_get_payment_id_by( 'transaction_id', $encoded_data_array[ 'parent_txn_id' ] );
    } elseif ( ! empty( $encoded_data_array[ 'txn_id' ] ) ) {
        $payment_id = gamipress_purchases_get_payment_id_by( 'transaction_id', $encoded_data_array[ 'txn_id' ] );
    }

    // Payment ID fallback to the custom parameter
    if ( empty( $payment_id ) ) {
        $payment_id = ! empty( $encoded_data_array[ 'custom' ] ) ? absint( $encoded_data_array[ 'custom' ] ) : 0;
    }

    if ( has_action( 'gamipress_purchases_paypal_standard_' . $encoded_data_array['txn_type'] ) ) {
        // Allow PayPal IPN types to be processed separately
        do_action( 'gamipress_purchases_paypal_standard_' . $encoded_data_array['txn_type'], $encoded_data_array, $payment_id );
    } else {
        // Fallback to web accept just in case the txn_type isn't present
        do_action( 'gamipress_purchases_paypal_standard_web_accept', $encoded_data_array, $payment_id );
    }
    exit;
}
add_action( 'gamipress_purchases_verify_paypal_standard_ipn', 'gamipress_purchases_process_paypal_standard_ipn' );

/**
 * Process web accept (one time) payment IPNs
 *
 * @since 1.0.0
 *
 * @param array   $data         IPN Data
 * @param integer $payment_id   The payment ID
 */
function gamipress_purchases_process_paypal_standard_web_accept_and_cart( $data, $payment_id ) {

    if ( $data['txn_type'] != 'web_accept' && $data['txn_type'] != 'cart' && $data['payment_status'] != 'Refunded' ) {
        return;
    }

    if( empty( $payment_id ) ) {
        return;
    }

    // Setup the payments table
    $ct_table = ct_setup_table( 'gamipress_payments' );

    $payment = ct_get_object( $payment_id );

    // Bail if isn't a PayPal Standard IPN
    if ( $payment->gateway != 'paypal_standard' ) {
        return;
    }

    // Collect payment details
    $purchase_key   = isset( $data['invoice'] ) ? $data['invoice'] : $data['item_number'];
    $paypal_amount  = $data['mc_gross'];
    $payment_status = strtolower( $data['payment_status'] );
    $currency_code  = strtolower( $data['mc_currency'] );
    $business_email = isset( $data['business'] ) && is_email( $data['business'] ) ? trim( $data['business'] ) : trim( $data['receiver_email'] );

    // Verify payment recipient
    if ( strcasecmp( $business_email, trim( gamipress_purchases_get_option( 'paypal_standard_email', false ) ) ) != 0 ) {

        // Log the error
        gamipress_purchases_log_gateway_error(
            __( 'PayPal Standard: IPN Error', 'gamipress-purchases' ),
            sprintf( __( 'Invalid business email in IPN response. Response: %s', 'gamipress-purchases' ), json_encode( $data ) )
        );

        // Update the payment status
        gamipress_purchases_update_payment_status( $payment_id, 'failed' );

        // Insert an informative note to the payment
        gamipress_purchases_insert_payment_note( $payment_id,
            __( 'Payment Failed', 'gamipress-purchases' ),
            __( 'Payment failed due to invalid PayPal business email.', 'gamipress-purchases' )
        );

        return;
    }

    // Verify payment currency
    if ( $currency_code != strtolower( gamipress_purchases_get_currency() ) ) {

        // Log the error
        gamipress_purchases_log_gateway_error(
            __( 'PayPal Standard: IPN Error', 'gamipress-purchases' ),
            sprintf( __( 'Invalid currency in IPN response. Response: %s', 'gamipress-purchases' ), json_encode( $data ) )
        );

        // Update the payment status
        gamipress_purchases_update_payment_status( $payment_id, 'failed' );

        // Insert an informative note to the payment
        gamipress_purchases_insert_payment_note( $payment_id,
            __( 'Payment Failed', 'gamipress-purchases' ),
            __( 'Payment failed due to invalid currency in PayPal IPN.', 'gamipress-purchases' )
        );

        return;
    }

    if ( $payment_status == 'refunded' || $payment_status == 'reversed' ) {

        // Process a refund
        gamipress_purchases_process_paypal_standard_refund( $data, $payment_id );

    } else {

        // Only complete payments once
        if ( $payment->status === 'complete' ) {
            return;
        }

        // Retrieve the total purchase amount (before PayPal)
        $payment_amount = $payment->total;

        // Verify prices
        if ( number_format( (float) $paypal_amount, 2 ) < number_format( (float) $payment_amount, 2 ) ) {

            // Log the error
            gamipress_purchases_log_gateway_error(
                __( 'PayPal Standard: IPN Error', 'gamipress-purchases' ),
                sprintf( __( 'Invalid payment amount in IPN response. Response: %s', 'gamipress-purchases' ), json_encode( $data ) )
            );

            // Update the payment status
            gamipress_purchases_update_payment_status( $payment_id, 'failed' );

            // Insert an informative note to the payment
            gamipress_purchases_insert_payment_note( $payment_id,
                __( 'Payment Failed', 'gamipress-purchases' ),
                __( 'Payment failed due to invalid amount in PayPal IPN.', 'gamipress-purchases' )
            );

            return;
        }

        // Verify purchase keys
        if ( $purchase_key != $payment->purchase_key ) {

            // Log the error
            gamipress_purchases_log_gateway_error(
                __( 'PayPal Standard: IPN Error', 'gamipress-purchases' ),
                sprintf( __( 'Invalid purchase key in IPN response. Response: %s', 'gamipress-purchases' ), json_encode( $data ) )
            );

            // Update the payment status
            gamipress_purchases_update_payment_status( $payment_id, 'failed' );

            // Insert an informative note to the payment
            gamipress_purchases_insert_payment_note( $payment_id,
                __( 'Payment Failed', 'gamipress-purchases' ),
                __( 'Payment failed due to invalid purchase key in PayPal IPN.', 'gamipress-purchases' )
            );

            return;
        }

        if ( 'completed' == $payment_status ) {

            if( $payment->transaction_id !== $data['txn_id'] ) {

                // Insert an informative note to the payment
                gamipress_purchases_insert_payment_note( $payment_id,
                    __( 'PayPal Transaction ID', 'gamipress-purchases' ),
                    $data['txn_id']
                );

                // CT Table to payments
                $ct_table->db->update(
                    array( 'transaction_id', $data['txn_id'] ),
                    array( 'payment_id', $payment_id )
                );

            }

            gamipress_purchases_update_payment_status( $payment_id, 'complete' );

        } else if ( 'pending' == $payment_status && isset( $data['pending_reason'] ) ) {

            // Look for possible pending reasons, such as an echeck

            $note = '';

            switch( strtolower( $data['pending_reason'] ) ) {

                case 'echeck' :
                    gamipress_purchases_update_payment_status( $payment_id, 'processing' );
                    $note = __( 'Payment made via eCheck and will clear automatically in 5-8 days.', 'gamipress-purchases' );
                    break;
                case 'address' :
                    $note = __( 'Payment requires a confirmed customer address and must be accepted manually through PayPal.', 'gamipress-purchases' );
                    break;
                case 'intl' :
                    $note = __( 'Payment must be accepted manually through PayPal due to international account regulations.', 'gamipress-purchases' );
                    break;
                case 'multi-currency' :
                    $note = __( 'Payment received in non-shop currency and must be accepted manually through PayPal.', 'gamipress-purchases' );
                    break;
                case 'paymentreview' :
                case 'regulatory_review' :
                    $note = __( 'Payment is being reviewed by PayPal staff as high-risk or in possible violation of government regulations.', 'gamipress-purchases' );
                    break;
                case 'unilateral' :
                    $note = __( 'Payment was sent to non-confirmed or non-registered email address.', 'gamipress-purchases' );
                    break;
                case 'upgrade' :
                    $note = __( 'PayPal account must be upgraded before this payment can be accepted.', 'gamipress-purchases' );
                    break;
                case 'verify' :
                    $note = __( 'PayPal account is not verified. Verify account in order to accept this payment.', 'gamipress-purchases' );
                    break;
                case 'other' :
                    $note = __( 'Payment is pending for unknown reasons. Contact PayPal support for assistance.', 'gamipress-purchases' );
                    break;

            }

            if( ! empty( $note ) ) {

                // Insert an informative note to the payment
                gamipress_purchases_insert_payment_note( $payment_id,
                    __( 'Payment Pending', 'gamipress-purchases' ),
                    $note
                );
            }

        }
    }
}
add_action( 'gamipress_purchases_paypal_standard_web_accept', 'gamipress_purchases_process_paypal_standard_web_accept_and_cart', 10, 2 );

/**
 * Process PayPal IPN Refunds
 *
 * @since 1.0.0
 *
 * @param array   $data IPN Data
 */
function gamipress_purchases_process_paypal_standard_refund( $data, $payment_id = 0 ) {

    // Collect payment details

    if( empty( $payment_id ) ) {
        return;
    }

    // Setup the payments table
    ct_setup_table( 'gamipress_payments' );

    $payment = ct_get_object( $payment_id );

    if ( $payment->status === 'refunded' ) {
        return; // Only refund payments once
    }

    $payment_amount = $payment->total;
    $refund_amount  = $data['mc_gross'] * -1;

    if ( number_format( (float) $refund_amount, 2 ) < number_format( (float) $payment_amount, 2 ) ) {
        // This is a partial refund

        // Insert an informative note to the payment
        gamipress_purchases_insert_payment_note( $payment_id,
            __( 'Partial Payment Refund', 'gamipress-purchases' ),
            sprintf( __( 'Partial PayPal refund processed: %s', 'gamipress-purchases' ), $data['parent_txn_id'] )
        );

        return;

    }

    // Insert an informative note to the payment
    gamipress_purchases_insert_payment_note( $payment_id,
        __( 'Payment Refunded', 'gamipress-purchases' ),
        sprintf( __( 'PayPal Payment #%s Refunded for reason: %s', 'gamipress-purchases' ), $data['parent_txn_id'], $data['reason_code'] )
    );

    // Insert the transaction ID as an informative note to the payment
    gamipress_purchases_insert_payment_note( $payment_id,
        __( 'PayPal Refund Transaction ID', 'gamipress-purchases' ),
        $data['txn_id']
    );

    gamipress_purchases_update_payment_status( $payment_id, 'refunded' );
}

/**
 * Get PayPal Redirect
 *
 * @since 1.0.0
 *
 * @param bool    $ssl_check Is SSL?
 * @param bool    $ipn       Is this an IPN verification check?
 *
 * @return string
 */
function gamipress_purchases_get_paypal_standard_redirect( $ssl_check = false, $ipn = false ) {

    // Decide the protocol to use
    $protocol = 'http://';

    if ( is_ssl() || ! $ssl_check ) {
        $protocol = 'https://';
    }

    // Check is sandbox mode is enabled
    if ( (bool) gamipress_purchases_get_option( 'paypal_standard_sandbox', false ) ) {

        // Test mode

        if( $ipn ) {

            $paypal_url = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';

        } else {

            $paypal_url = $protocol . 'www.sandbox.paypal.com/cgi-bin/webscr';

        }

    } else {

        // Live mode

        if( $ipn ) {

            $paypal_url = 'https://ipnpb.paypal.com/cgi-bin/webscr';

        } else {

            $paypal_url = $protocol . 'www.paypal.com/cgi-bin/webscr';

        }

    }

    return apply_filters( 'gamipress_purchases_paypal_standard_url', $paypal_url, $ssl_check, $ipn );
}

/**
 * Gateway actions on payment edit screen
 *
 * @since 1.0.0
 *
 * @param array     $payment_actions
 * @param stdClass  $payment
 *
 * @return array
 */
function gamipress_purchases_paypal_standard_payment_actions( $payment_actions, $payment ) {

    // Bail if payment has not set this gateway
    if( $payment->gateway !== 'paypal_standard' ) {
        return $payment_actions;
    }

    $prefix = '_gamipress_purchases_paypal_standard_';

    // Setup CT Table
    ct_setup_table( 'gamipress_payments' );

    // Check if payment has been done when PayPal Standard sandbox mode was enabled
    $sandbox = (bool) ct_get_object_meta( $payment->payment_id, $prefix . 'sandbox', true );

    // Build the PayPal transaction details url
    $url_prefix = $sandbox ? 'sandbox.' : '';
    $transaction_url = 'https://www.' . $url_prefix . 'paypal.com/webscr?cmd=_history-details-from-hub&id=' . $payment->transaction_id ;

    $payment_actions['paypal_standard_details'] = array(
        'label' => __( 'See Details in PayPal' ),
        'icon' => 'dashicons-paypal',
        'url' => esc_url( $transaction_url ),
        'target' => '_blank'
    );

    // Actions if payment is completed
    if( $payment->status === 'complete' ) {

        $payment_actions['paypal_standard_refund'] = array(
            'label' => __( 'Refund Payment in PayPal' ),
            'icon' => 'dashicons-paypal'
        );

    }

    return $payment_actions;
}
add_filter( 'gamipress_purchases_payment_actions', 'gamipress_purchases_paypal_standard_payment_actions', 10, 2 );

/**
 * Process gateway actions
 *
 * @since 1.0.0
 *
 * @param integer $payment_id
 */
function gamipress_purchases_paypal_standard_refund( $payment_id ) {

    $prefix = '_gamipress_purchases_paypal_standard_';

    ct_setup_table( 'gamipress_payments' );
    $payment = ct_get_object( $payment_id );

    if( ! $payment ) {
        return;
    }

    // Bail if payment has not set this gateway
    if( $payment->gateway !== 'paypal_standard' ) {
        return;
    }

    $refunded = ct_get_object_meta( $payment_id, $prefix . 'refunded', true );

    // Bail if the payment has already been refunded in the past
    if ( $refunded ) {
        return;
    }

    // Process the refund in PayPal.
    gamipress_purchases_refund_paypal_standard_purchase( $payment );

    // Update payment status
    gamipress_purchases_update_payment_status( $payment_id, 'refunded' );

    // Insert an informative note to the payment
    gamipress_purchases_insert_payment_note( $payment_id,
        __( 'Payment Refunded', 'gamipress-purchases' ),
        __( 'Payment has been successfully refunded.', 'gamipress-purchases' )
    );

    $redirect = add_query_arg( array( 'message' => 'paypal_standard_refund' ), ct_get_edit_link( 'gamipress_payments', $payment->payment_id ) );

    // Redirect to the same payment edit screen and with the var message
    wp_redirect( $redirect );
    exit;

}
add_action( 'gamipress_purchases_process_payment_action_paypal_standard_refund', 'gamipress_purchases_paypal_standard_refund' );


/**
 * Add gateway custom messages
 *
 * @since 1.0.0
 *
 * @param array $messages
 *
 * @return array
 */
function gamipress_purchases_paypal_standard_updated_messages( $messages ) {

    $messages['paypal_standard_refund'] = __( 'Payment refunded in PayPal successfully.', 'gamipress-purchases' );

    return $messages;
}
add_filter( 'ct_table_updated_messages', 'gamipress_purchases_paypal_standard_updated_messages' );

/**
 * Process gateway refund
 *
 * @since 1.0.0
 *
 * @param object $payment
 *
 * @return array
 */
function gamipress_purchases_refund_paypal_standard_purchase( $payment ) {

    $prefix = '_gamipress_purchases_paypal_standard_';

    // Setup CT Table
    ct_setup_table( 'gamipress_payments' );

    // Check if payment has been done when PayPal Standard sandbox mode was enabled
    $sandbox = (bool) ct_get_object_meta( $payment->payment_id, $prefix . 'sandbox', true );

    // Set PayPal API key credentials.
    $credentials = array(
        'api_endpoint'  => $sandbox ? 'https://api-3t.sandbox.paypal.com/nvp' : 'https://api-3t.paypal.com/nvp',
        'api_username'  => gamipress_purchases_get_option( 'paypal_standard_api_username' ),
        'api_password'  => gamipress_purchases_get_option( 'paypal_standard_api_password' ),
        'api_signature' => gamipress_purchases_get_option( 'paypal_standard_api_signature' )
    );

    $credentials = apply_filters( 'gamipress_purchases_paypal_standard_refund_api_credentials', $credentials, $payment );

    $body = array(
        'USER' 			=> $credentials['api_username'],
        'PWD'  			=> $credentials['api_password'],
        'SIGNATURE' 	=> $credentials['api_signature'],
        'VERSION'       => '124',
        'METHOD'        => 'RefundTransaction',
        'TRANSACTIONID' => $payment->transaction_id,
        'REFUNDTYPE'    => 'Full'
    );

    $body = apply_filters( 'gamipress_purchases_paypal_standard_refund_body_args', $body, $payment );

    // Prepare the headers of the refund request.
    $headers = array(
        'Content-Type'  => 'application/x-www-form-urlencoded',
        'Cache-Control' => 'no-cache'
    );

    $headers = apply_filters( 'gamipress_purchases_paypal_standard_refund_header_args', $headers, $payment );

    // Prepare args of the refund request.
    $args = array(
        'body' 	      => $body,
        'headers'     => $headers,
        'httpversion' => '1.1'
    );

    $args = apply_filters( 'gamipress_purchases_paypal_standard_refund_request_args', $args, $payment );

    $error_msg = '';
    $request   = wp_remote_post( $credentials['api_endpoint'], $args );

    if ( is_wp_error( $request ) ) {

        $success   = false;
        $error_msg = $request->get_error_message();

    } else {

        $body    = wp_remote_retrieve_body( $request );
        $code    = wp_remote_retrieve_response_code( $request );
        $message = wp_remote_retrieve_response_message( $request );
        if( is_string( $body ) ) {
            wp_parse_str( $body, $body );
        }

        if( empty( $code ) || 200 !== (int) $code ) {
            $success = false;
        }

        if( empty( $message ) || 'OK' !== $message ) {
            $success = false;
        }

        if( isset( $body['ACK'] ) && 'success' === strtolower( $body['ACK'] ) ) {
            $success = true;
        } else {
            $success = false;
            if( isset( $body['L_LONGMESSAGE0'] ) ) {
                $error_msg = $body['L_LONGMESSAGE0'];
            } else {
                $error_msg = __( 'PayPal refund failed for unknown reason.', 'gamipress-purchases' );
            }
        }

    }

    if( $success ) {

        // Prevents the PayPal Standard from trying to process the refund more times
        ct_update_object_meta( $payment->payment_id, $prefix . 'refunded', '1' );

        // Insert an informative note to the payment with the transaction ID
        gamipress_purchases_insert_payment_note( $payment->payment_id,
            __( 'PayPal Refund Transaction ID', 'gamipress-purchases' ),
            $body['REFUNDTRANSACTIONID']
        );
    } else {

        // Insert an informative note to the payment with the error
        gamipress_purchases_insert_payment_note( $payment->payment_id,
            __( 'PayPal Refund Failed', 'gamipress-purchases' ),
            $error_msg
        );

    }

    // Hook to letting know the payment has been refunded successfully through PayPal
    do_action( 'gamipress_purchases_paypal_standard_refund_purchase', $payment );

}