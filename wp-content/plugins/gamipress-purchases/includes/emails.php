<?php
/**
 * Emails
 *
 * @package     GamiPress\Purchases\Emails
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get an array of email pattern tags used on purchase receipt email
 *
 * @since 1.0.0
 *
 * @return array The registered achievement earned email pattern tags
 */
function gamipress_purchases_get_purchase_receipt_email_pattern_tags() {

    $email_pattern_tags = gamipress_get_email_pattern_tags();

    return apply_filters( 'gamipress_purchases_purchase_receipt_email_pattern_tags', array_merge( $email_pattern_tags, array(

        // User details
        '{user}'            =>  __( 'Customer display name.', 'gamipress-purchases' ),
        '{user_first}'      =>  __( 'Customer first name.', 'gamipress-purchases' ),
        '{user_last}'       =>  __( 'Customer last name.', 'gamipress-purchases' ),
        '{user_email}'      =>  __( 'Customer email.', 'gamipress-purchases' ),
        '{user_address}'    =>  __( 'Customer address.', 'gamipress-purchases' ),
        '{user_city}'       =>  __( 'Customer city.', 'gamipress-purchases' ),
        '{user_postcode}'   =>  __( 'Customer postal code / zip.', 'gamipress-purchases' ),
        '{user_country}'    =>  __( 'Customer country.', 'gamipress-purchases' ),
        '{user_state}'      =>  __( 'Customer state.', 'gamipress-purchases' ),

        // Order details
        '{purchase_number}' =>  __( 'Purchase number.', 'gamipress-purchases' ),
        '{purchase_date}'   =>  __( 'Purchase date.', 'gamipress-purchases' ),
        '{purchase_items}'  =>  __( 'Purchase items list.', 'gamipress-purchases' ),
        '{purchase_subtotal}'  =>  __( 'Purchase subtotal amount.', 'gamipress-purchases' ),
        '{purchase_tax}'  =>  __( 'Purchase tax amount (with tax percent).', 'gamipress-purchases' ),
        '{purchase_total}'  =>  __( 'Purchase total amount.', 'gamipress-purchases' ),
    ) ) );

}

/**
 * Get a string with the desired email pattern tags html markup
 *
 * @since 1.0.0
 *
 * @return string Log pattern tags html markup
 */
function gamipress_purchases_get_purchase_receipt_email_pattern_tags_html() {

    $email_pattern_tags = gamipress_purchases_get_purchase_receipt_email_pattern_tags();

    $output = '<ul class="gamipress-pattern-tags-list">';

    foreach( $email_pattern_tags as $tag => $description ) {

        $attr_id = 'tag-' . str_replace( array( '{', '}', '_' ), array( '', '', '-' ), $tag );

        $output .= "<li id='{$attr_id}'><code>{$tag}</code> - {$description}</li>";
    }

    $output .= '</ul>';

    return $output;

}

/**
 * Parse the purchase receipt/new sale email tags
 *
 * @param array     $replacements
 * @param WP_User   $user
 * @param array     $template_args
 * @return array
 */
function gamipress_purchases_parse_email_tags( $replacements, $user, $template_args ) {

    if(
        ( $template_args['type'] === 'purchase_receipt' || $template_args['type'] === 'new_sale' )
        && isset( $template_args['payment_id'] )
    ) {

        ct_setup_table( 'gamipress_payments' );

        $payment = ct_get_object( $template_args['payment_id'] );

        $countries = gamipress_purchases_get_countries();

        // User details
        $replacements['{user_first}'] = $payment->first_name;
        $replacements['{user_last}'] = $payment->last_name;
        $replacements['{user_email}'] = $payment->email;
        $replacements['{user_address}'] = $payment->address_1 . ( ! empty( $payment->address_2 ) ?  ' ' . $payment->address_2 : '' );
        $replacements['{user_city}'] = $payment->city;
        $replacements['{user_postcode}'] = $payment->postcode;
        $replacements['{user_country}'] = $countries[$payment->country];
        $replacements['{user_state}'] = $payment->state;

        // Order details

        $purchase_items_html = '';

        $purchase_items = gamipress_purchases_get_payment_items( $template_args['payment_id'] );

        if( ! empty( $purchase_items ) ) {

            $purchase_items_html = '<ul>';

            foreach( $purchase_items as $purchase_item ) {
                // {description} (x{quantity}): {total}
                $purchase_items_html .= '<li>'
                        . $purchase_item->description
                        . ( absint( $purchase_item->quantity ) > 1 ? ' (x' . $purchase_item->quantity . ')' : '' )
                        . ': ' . gamipress_purchases_format_price( $purchase_item->total )
                    . '</li>';
            }

            $purchase_items_html .= '</ul>';

        }

        $replacements['{purchase_number}'] = $payment->number;
        $replacements['{purchase_date}'] = date_i18n( get_option( 'date_format' ), strtotime( $payment->date ) );
        $replacements['{purchase_items}'] = $purchase_items_html;
        $replacements['{purchase_subtotal}'] = gamipress_purchases_format_price( $payment->subtotal );
        $replacements['{purchase_tax}'] = gamipress_purchases_format_price( $payment->tax_amount ) . ' (' . gamipress_purchases_format_amount( $payment->tax ) . '%)';
        $replacements['{purchase_total}'] = gamipress_purchases_format_price( $payment->total );

    }

    return $replacements;

}
add_filter( 'gamipress_parse_email_tags', 'gamipress_purchases_parse_email_tags', 10, 3 );

/**
 * Parse the preview purchase receipt/new sale email tags
 *
 * @param array     $replacements
 * @param WP_User   $user
 * @param array     $template_args
 * @return array
 */
function gamipress_purchases_parse_preview_email_tags( $replacements, $user, $template_args ) {

    if( $template_args['type'] === 'purchase_receipt' || $template_args['type'] === 'new_sale' ) {

        $countries = gamipress_purchases_get_countries();

        $user_details = gamipress_purchases_get_user_billing_details( $user->ID );

        // User details
        $replacements['{user_first}'] = $user_details['first_name'];
        $replacements['{user_last}'] = $user_details['last_name'];
        $replacements['{user_email}'] = $user_details['email'];
        $replacements['{user_address}'] = $user_details['address_1'] . ( ! empty( $user_details['address_2'] ) ?  ' ' . $user_details['address_2'] : '' );
        $replacements['{user_city}'] = $user_details['city'];
        $replacements['{user_postcode}'] = $user_details['postcode'];
        $replacements['{user_country}'] = $countries[$user_details['country']];
        $replacements['{user_state}'] = $user_details['state'];

        // Order details
        $purchase_items_html = '<ul>';

        $purchase_items_html .= '<li>'
            . __( 'Sample Item', 'gamipress-purchases' )
            . ': ' . gamipress_purchases_format_price( 19 )
            . '</li>';

        $purchase_items_html .= '<li>'
            . __( 'Sample Multiple Items', 'gamipress-purchases' )
            . ' (x2)'
            . ': ' . gamipress_purchases_format_price( 59 )
            . '</li>';

        $purchase_items_html .= '</ul>';


        $replacements['{purchase_number}'] = '123';
        $replacements['{purchase_date}'] = date_i18n( get_option( 'date_format' ), current_time( 'timestamp' ) );
        $replacements['{purchase_items}'] = $purchase_items_html;
        $replacements['{purchase_subtotal}'] = gamipress_purchases_format_price( 78 );
        $replacements['{purchase_tax}'] = gamipress_purchases_format_price( 78 * 0.20 ) . ' (' . gamipress_purchases_format_amount( 20 ) . '%)';
        $replacements['{purchase_total}'] = gamipress_purchases_format_price( 78 * 1.20 );

    }

    return $replacements;

}
add_filter( 'gamipress_parse_preview_email_tags', 'gamipress_purchases_parse_preview_email_tags', 10, 3 );

/**
 * Preview purchase receipt email action
 *
 * @since 1.0.0
 */
function gamipress_purchases_preview_purchase_receipt_email() {

    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    global $gamipress_email_template_args;

    $gamipress_email_template_args = array(
        'type' => 'purchase_receipt'
    );

    $subject = gamipress_purchases_get_option( 'purchase_receipt_email_subject' );
    $message = gamipress_purchases_get_option( 'purchase_receipt_email_content' );

    gamipress_preview_email( $subject, $message );

    exit;

}
add_action( 'gamipress_action_get_preview_purchase_receipt_email', 'gamipress_purchases_preview_purchase_receipt_email' );

/**
 * Preview new sale email action
 *
 * @since 1.0.0
 */
function gamipress_purchases_preview_new_sale_email() {

    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    global $gamipress_email_template_args;

    $gamipress_email_template_args = array(
        'type' => 'new_sale'
    );

    $subject = gamipress_purchases_get_option( 'new_sale_email_subject' );
    $message = gamipress_purchases_get_option( 'new_sale_email_content' );

    gamipress_preview_email( $subject, $message );

    exit;

}
add_action( 'gamipress_action_get_preview_new_sale_email', 'gamipress_purchases_preview_new_sale_email' );

/**
 * Send a test purchase receipt email
 *
 * @since 1.0.0
 */
function gamipress_purchases_send_test_purchase_receipt_email() {

    global $gamipress_email_template_args;

    $gamipress_email_template_args = array(
        'type' => 'purchase_receipt',
    );

    $subject = gamipress_purchases_get_option( 'purchase_receipt_email_subject' );
    $message = gamipress_purchases_get_option( 'purchase_receipt_email_content' );

    gamipress_send_test_email( $subject, $message );

    exit;

}
add_action( 'gamipress_action_get_send_test_purchase_receipt_email', 'gamipress_purchases_send_test_purchase_receipt_email' );

/**
 * Send a test new sale email
 *
 * @since 1.0.0
 */
function gamipress_purchases_send_test_new_sale_email() {

    global $gamipress_email_template_args;

    $gamipress_email_template_args = array(
        'type' => 'new_sale',
    );

    $subject = gamipress_purchases_get_option( 'new_sale_email_subject' );
    $message = gamipress_purchases_get_option( 'new_sale_email_content' );

    gamipress_send_test_email( $subject, $message );

    exit;

}
add_action( 'gamipress_action_get_send_test_new_sale_email', 'gamipress_purchases_send_test_new_sale_email' );

/**
 * Email the receipt to the email provided at purchase form
 *
 * @since 1.0.0
 *
 * @param string    $new_status
 * @param string    $old_status
 * @param stdClass  $payment
 */
function gamipress_purchases_maybe_send_purchase_receipt_to_user( $new_status, $old_status, $payment ) {

    global $gamipress_email_template_args;

    // Not complete yet
    if( $new_status !== 'complete' ) {
        return;
    }

    // Already sent
    if( $old_status === 'complete' ) {
        return;
    }

    // Just if purchase receipt is active
    if( ! (bool) gamipress_purchases_get_option( 'disable_purchase_receipt_email', false ) ) {

        $gamipress_email_template_args = array(
            'user_id' => $payment->user_id,
            'payment_id' => $payment->payment_id,
            'type' => 'purchase_receipt',
        );

        $subject = gamipress_purchases_get_option( 'purchase_receipt_email_subject' );
        $message = gamipress_purchases_get_option( 'purchase_receipt_email_content' );

        gamipress_send_email( $payment->email, $subject, $message );

    }

    // Just if new sale is active
    if( ! (bool) gamipress_purchases_get_option( 'disable_new_sale_email', false ) ) {

        $gamipress_email_template_args = array(
            'user_id' => $payment->user_id,
            'payment_id' => $payment->payment_id,
            'type' => 'new_sale',
        );

        $subject = gamipress_purchases_get_option( 'new_sale_email_subject' );
        $message = gamipress_purchases_get_option( 'new_sale_email_content' );

        $administrators = get_users( array(
            'role'         => 'administrator',
            'number'       => -1,
            'fields'       => array( 'user_email' ),
        ) );

        $to = array();

        foreach( $administrators as $administrator ) {
            $to[] = $administrator->user_email;
        }

        gamipress_send_email( $to, $subject, $message );

    }

}
add_action( 'gamipress_purchases_transition_payment_status', 'gamipress_purchases_maybe_send_purchase_receipt_to_user', 10, 3 );

/**
 * Send again the purchase receipt to the user
 *
 * @since 1.0.0
 *
 * @param integer $payment_id
 */
function gamipress_purchases_resend_purchase_receipt( $payment_id ) {

    global $gamipress_email_template_args;

    ct_setup_table( 'gamipress_payments' );
    $payment = ct_get_object( $payment_id );

    $gamipress_email_template_args = array(
        'user_id' => $payment->user_id,
        'payment_id' => $payment->payment_id,
        'type' => 'purchase_receipt',
    );

    $subject = gamipress_purchases_get_option( 'purchase_receipt_email_subject' );
    $message = gamipress_purchases_get_option( 'purchase_receipt_email_content' );

    gamipress_send_email( $payment->email, $subject, $message );

    // Add an informative note about this resend
    gamipress_purchases_insert_payment_note( $payment_id,
        __( 'Purchase Receipt Email', 'gamipress-purchases' ),
        sprintf( __( 'Purchase Receipt has been sent again to the email address %s', 'gamipress-purchases' ), $payment->email )
    );

    $redirect = add_query_arg( array( 'message' => 'resend_purchase_receipt' ), ct_get_edit_link( 'gamipress_payments', $payment->payment_id ) );

    // Redirect to the same payment edit screen and with the var message
    wp_redirect( $redirect );
    exit;

}
add_action( 'gamipress_purchases_process_payment_action_resend_purchase_receipt', 'gamipress_purchases_resend_purchase_receipt' );

/**
 * Register resend purchase receipt message
 *
 * @since 1.0.0
 *
 * @param array $messages
 *
 * @return array
 */
function gamipress_purchases_register_resend_purchase_receipt_message( $messages ) {

    $messages['resend_purchase_receipt'] = __( 'Purchase receipt sent successfully.', 'gamipress-purchases' );

    return $messages;
}
add_filter( 'ct_table_updated_messages', 'gamipress_purchases_register_resend_purchase_receipt_message' );