<?php
/**
 * Bank Transfer Gateway
 *
 * @package     GamiPress\Purchases\Gateways\Bank_Transfer
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
function gamipress_purchases_register_bank_transfer_gateway( $gateways = array() ) {

    $gateways['bank_transfer'] = __( 'Bank Transfer', 'gamipress-purchases' );

    return $gateways;

}
add_filter( 'gamipress_purchases_get_gateways', 'gamipress_purchases_register_bank_transfer_gateway' );

/**
 * Render this gateway settings fields
 *
 * @since 1.0.0
 *
 * @param array     $fields   Fields container
 *
 * @return array
 */
function gamipress_purchases_bank_transfer_settings_fields( $fields ) {

    $prefix = 'gamipress_purchases_bank_transfer_';

    $fields = array_merge( $fields, array(

        $prefix . 'text' => array(
            'name' => __( 'Text', 'gamipress-purchases' ),
            'desc' => __( 'Text to show to customers when bank transfer was select as payment method.', 'gamipress-purchases' ),
            'type' => 'wysiwyg',
            'default' => __( 'You can choose one of the following bank accounts to make the transfer:', 'gamipress-purchases' ),
        ),
        $prefix . 'accounts' => array(
            'name' => __( 'Accounts', 'gamipress-purchases' ),
            'desc' => __( 'Business bank accounts to show to customers when bank transfer was select as payment method.', 'gamipress-purchases' ),
            'type' => 'text',
            'repeatable' => true,
            'text' => array(
                'add_row_text' => __( 'Add Account Number', 'gamipress-purchases' ),
            ),
        ),

    ) );

    return $fields;

}
add_filter( 'gamipress_purchases_settings_fields', 'gamipress_purchases_bank_transfer_settings_fields' );

/**
 * Render this gateway settings tabs
 *
 * @since 1.0.0
 *
 * @param array     $tabs   Fields container
 *
 * @return array
 */
function gamipress_purchases_bank_transfer_settings_tabs( $tabs ) {

    $prefix = 'gamipress_purchases_bank_transfer_';

    $tabs = array_merge( $tabs, array(

        'bank_transfer' => array(
            'icon' => 'dashicons-bank-transfer',
            'title' => __( 'Bank Transfer', 'gamipress-purchases' ),
            'fields' => array(
                $prefix . 'text',
                $prefix . 'accounts',
            ),
        ),

    ) );

    return $tabs;

}
add_filter( 'gamipress_purchases_settings_tabs', 'gamipress_purchases_bank_transfer_settings_tabs' );

/**
 * Render this gateway form
 *
 * @since 1.0.0
 *
 * @param integer   $user_id        User ID
 * @param array     $user_details   User billing details
 * @param array     $template_args  Template received arguments
 */
function gamipress_purchases_bank_transfer_form( $user_id, $user_details, $template_args ) {

    $prefix = 'bank_transfer_';

    $text = gamipress_purchases_get_option( $prefix . 'text', '' );
    $accounts = gamipress_purchases_get_option( $prefix . 'accounts', array() ); ?>

    <?php if( ! empty( $text ) ) : ?>

        <div class="gamipress-purchases-gateway-bank-transfer-text">
            <?php echo $text; ?>
        </div>

    <?php endif; ?>

    <?php if( ! empty( $accounts ) ) : ?>

        <div class="gamipress-purchases-gateway-bank-transfer-accounts">

            <?php foreach( $accounts as $account ) : ?>

                <div class="gamipress-purchases-gateway-bank-transfer-account">
                    <?php echo $account; ?>
                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <?php
}
add_action( 'gamipress_purchases_bank_transfer_form', 'gamipress_purchases_bank_transfer_form', 10, 3 );

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
function gamipress_purchases_bank_transfer_process_payment( $gateway_response, $payment, $payment_items ) {

    $nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

    // Security check
    if ( ! wp_verify_nonce( $nonce, 'gamipress_purchases_purchase_form' ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress-purchases' ) );
    }

    // Insert an informative note to the payment
    gamipress_purchases_insert_payment_note( $payment['payment_id'],
        __( 'Payment Pending', 'gamipress-purchases' ),
        __( 'Bank transfer payments need to completed manually when customer performs the transaction successfully.', 'gamipress-purchases' )
    );

    // For bank transfer, just update payment status from processing to pending and wait to manually set it to complete
    gamipress_purchases_update_payment_status( $payment['payment_id'], 'pending' );

    $gateway_response['success'] = true;
    $gateway_response['message'] = __( 'Your purchase has been completed successfully.', 'gamipress-purchases' );

    return $gateway_response;

}
add_filter( 'gamipress_purchases_bank_transfer_process_payment', 'gamipress_purchases_bank_transfer_process_payment', 10, 4 );

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
function gamipress_purchases_bank_transfer_payment_actions( $payment_actions, $payment ) {

    // Bail if payment has not set this gateway
    if( $payment->gateway !== 'bank_transfer' ) {
        return $payment_actions;
    }

    if( $payment->status === 'pending' ) {

        $payment_actions['bank_transfer_paid'] = array(
            'label' => __( 'Mark as paid, user did the transfer' ),
            'icon' => 'dashicons-bank-transfer'
        );

    }

    return $payment_actions;
}
add_filter( 'gamipress_purchases_payment_actions', 'gamipress_purchases_bank_transfer_payment_actions', 10, 2 );

/**
 * Process gateway actions
 *
 * @since 1.0.0
 *
 * @param integer $payment_id
 */
function gamipress_purchases_bank_transfer_paid( $payment_id ) {

    ct_setup_table( 'gamipress_payments' );
    $payment = ct_get_object( $payment_id );

    if( ! $payment ) {
        return;
    }

    // Bail if payment has not set this gateway
    if( $payment->gateway !== 'bank_transfer' ) {
        return;
    }

    // Insert an informative note to the payment
    gamipress_purchases_insert_payment_note( $payment_id,
        __( 'Payment Complete', 'gamipress-purchases' ),
        __( 'User did the bank transfer successfully.', 'gamipress-purchases' )
    );

    // For bank transfer, just update payment status from processing to pending and wait to manually set it to complete
    gamipress_purchases_update_payment_status( $payment_id, 'complete' );

    $redirect = add_query_arg( array( 'message' => 'bank_transfer_paid' ), ct_get_edit_link( 'gamipress_payments', $payment->payment_id ) );

    do_action( 'gamipress_purchases_bank_transfer_complete', $payment, $payment_id );

    // Redirect to the same payment edit screen and with the var message
    wp_redirect( $redirect );
    exit;

}
add_action( 'gamipress_purchases_process_payment_action_bank_transfer_paid', 'gamipress_purchases_bank_transfer_paid' );

/**
 * Add gateway custom messages
 *
 * @since 1.0.0
 *
 * @param array $messages
 *
 * @return array
 */
function gamipress_purchases_bank_transfer_updated_messages( $messages ) {

    $messages['bank_transfer_paid'] = __( 'Purchase marked as paid successfully.', 'gamipress-purchases' );

    return $messages;
}
add_filter( 'ct_table_updated_messages', 'gamipress_purchases_bank_transfer_updated_messages' );