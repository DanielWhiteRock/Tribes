<?php
/**
 * GamiPress Achievement Transfer Shortcode
 *
 * @package     GamiPress\Transfers\Shortcodes\Shortcode\GamiPress_Achievement_Transfer
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_achievement_transfer] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_transfers_register_achievement_transfer_shortcode() {

    // Setup the achievement fields
    $achievement_fields = GamiPress()->shortcodes['gamipress_achievement']->fields;

    unset( $achievement_fields['id'] );

    gamipress_register_shortcode( 'gamipress_achievement_transfer', array(
        'name'              => __( 'Achievement Transfer', 'gamipress-transfers' ),
        'description'       => __( 'Render an achievement transfer form.', 'gamipress-transfers' ),
        'output_callback'   => 'gamipress_transfers_achievement_transfer_shortcode',
        'icon'              => 'transfer',
        'group'             => 'transfers',
        'tabs'              => array(
            'form' => array(
                'icon' => 'dashicons-feedback',
                'title' => __( 'Form', 'gamipress-transfers' ),
                'fields' => array(
                    'id',
                    'select_achievement',
                    'button_text',
                ),
            ),
            'recipient' => array(
                'icon' => 'dashicons-admin-users',
                'title' => __( 'Recipient', 'gamipress-transfers' ),
                'fields' => array(
                    'recipient_id',
                    'select_recipient',
                    'recipient_placeholder',
                    'recipient_autocomplete',
                ),
            ),
            'achievement' => array(
                'icon' => 'dashicons-awards',
                'title' => __( 'Achievement', 'gamipress-transfers' ),
                'fields' => array_keys( $achievement_fields ),
            ),
        ),

        'fields'            => array_merge( array(
            'id' => array(
                'name'          => __( 'Achievement', 'gamipress-transfers' ),
                'description'   => __( 'The achievement to transfer.', 'gamipress-transfers' ),
                'type'          => 'select',
                'classes' 	        => 'gamipress-post-selector',
                'attributes' 	    => array(
                    'data-post-type' => implode( ',',  gamipress_get_achievement_types_slugs() ),
                    'data-placeholder' => __( 'Select an achievement', 'gamipress-transfers' ),
                ),
                'default'       => '',
                'options_cb'    => 'gamipress_options_cb_posts'
            ),
            'select_achievement' => array(
                'name'          => __( 'Allow Select Achievement', 'gamipress-transfers' ),
                'description'   => __( 'Allow user to select a specific achievement to transfer. If achievement is set it will be used as initial achievement selected.', 'gamipress-transfers' ),
                'type' 	        => 'checkbox',
                'classes'       => 'gamipress-switch',
                'default'       => 'yes'
            ),

            // Transfer button text

            'button_text' => array(
                'name'        => __( 'Button Text', 'gamipress-transfers' ),
                'description' => __( 'Transfer button text.', 'gamipress-transfers' ),
                'type' 	=> 'text',
                'default' => __( 'Transfer', 'gamipress-transfers' )
            ),

            // Recipient

            'recipient_id' => array(
                'name'        => __( 'Recipient', 'gamipress-transfers' ),
                'description' => __( 'User that will receive the transfer.', 'gamipress-transfers' ),
                'type'        => 'select',
                'classes' 	  => 'gamipress-user-selector',
                'default'     => '',
                'options_cb'  => 'gamipress_options_cb_users'
            ),
            'select_recipient' => array(
                'name'        => __( 'Allow Select Recipient', 'gamipress-transfers' ),
                'description' => __( 'Allow user to select a specific transfer recipient. If recipient is set it will be used as initial recipient selected.', 'gamipress-transfers' ),
                'type' 	      => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            'recipient_placeholder' => array(
                'name'        => __( 'Recipient placeholder text', 'gamipress-transfers' ),
                'description' => __( 'Recipient input placeholder text.', 'gamipress-transfers' ),
                'type'        => 'text',
                'default'     => __( 'Enter username or email', 'gamipress-transfers' ),
            ),
            'recipient_autocomplete' => array(
                'name'        => __( 'Enable Select Recipient Auto-complete', 'gamipress-transfers' ),
                'description' => __( 'Enabling this functionality will add user suggestions to the recipient field.', 'gamipress-transfers' )
                . __( 'If this option is not enabled, user will be required to provide exactly the recipient user name or email.', 'gamipress-transfers' ),
                'type' 	      => 'checkbox',
                'classes' => 'gamipress-switch',
            ),

        ), $achievement_fields ),
    ) );

}
add_action( 'init', 'gamipress_transfers_register_achievement_transfer_shortcode' );

/**
 * Transfer Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_transfers_achievement_transfer_shortcode( $atts = array() ) {

    global $gamipress_transfers_template_args;

    // Unset id attr from achievement shortcode defaults since it initializes id with current post ID
    $achievement_defaults = gamipress_achievement_shortcode_defaults();

    unset( $achievement_defaults['id'] );

    $shortcode_defaults = array_merge( array(
        'id'                        => '0',
        'select_achievement'        => 'yes',
        'button_text' 		        => __( 'Transfer', 'gamipress-transfers' ),
        // Recipient
        'recipient_id'                  => '0',
        'select_recipient'              => 'no',
        'recipient_placeholder'         => __( 'Enter username or email', 'gamipress-transfers' ),
        'recipient_autocomplete'        => 'no',
    ), $achievement_defaults );

    /**
     * Filters shortcode defaults
     *
     * @since 1.0.0
     *
     * @param array $shortcode_defaults
     *
     * @return array
     */
    $shortcode_defaults = apply_filters( 'gamipress_achievement_transfer_shortcode_defaults', $shortcode_defaults );

    // Get the shortcode attributes
    $atts = shortcode_atts( $shortcode_defaults, $atts, 'gamipress_achievement_transfer' );

    // Ensure values as int
    $atts['id'] = absint( $atts['id'] );
    $atts['recipient_id'] = absint( $atts['recipient_id'] );
    $atts['achievement_type'] = ''; // Initialize this var to be passed to gamipress_get_template() function

    // Setup user id
    $user_id = get_current_user_id();

    if( $user_id === 0 ) {
        return sprintf( __( 'You need to <a href="%s">log in</a> to make a transfer.', 'gamipress-transfers' ), wp_login_url( get_permalink() ) );
    }

    // Check user earned achievements, if user has not earned anything can't transfer anything
    $earned_ids = gamipress_get_user_earned_achievement_ids( $user_id );

    if( empty( $earned_ids ) ) {
        return '';
    }

    // Need to ensure achievement ID is select achievement is not set
    if( $atts['select_achievement'] === 'no' ) {

        // Return if achievement id not specified
        if ( $atts['id'] === 0 )
            return '';

        // Setup the achievement
        $achievement = get_post( $atts['id'] );

        if( ! $achievement ) {
            return gamipress_transfers_notify_form_error( __( 'Achievement not exists.', 'gamipress-transfers' ) );
        }

        // User just can transfer an achievement if has earned it
        if( ! in_array( $atts['id'], $earned_ids ) ) {
            return '';
        }

        $atts['achievement_type'] = $achievement->post_type;

    }

    // If user is not able to select a recipient, need to check if recipient ID is correctly
    if( $atts['select_recipient'] === 'no' ) {

        $recipient = get_userdata( $atts['recipient_id'] );

        if( ! $recipient ) {
            return gamipress_transfers_notify_form_error( __( 'Invalid recipient ID.', 'gamipress-transfers' ) );
        } else if( $atts['recipient_id'] === $user_id ) {
            // user can't transfer to himself
            return '';
        }

    }

    $gamipress_transfers_template_args = $atts;

    // Setup achievement template args
    $gamipress_transfers_template_args['template_args'] = array();

    $achievement_fields = array_keys( GamiPress()->shortcodes['gamipress_achievement']->fields );

    foreach( $achievement_fields as $achievement_field ) {

        if( ! isset( $atts[$achievement_field] ) )
            continue;

        $gamipress_transfers_template_args['template_args'][$achievement_field] = $atts[$achievement_field];
    }

    // If not select achievement, check if achievement is already pending to transfer
    if( $atts['select_achievement'] === 'no' ) {

        // Check if user has a pending transfer with this item
        $pending_transfer = gamipress_transfers_user_get_item_pending( $user_id, $atts['id'] );

        if( $pending_transfer !== false ) {

            // let know to the template that user has a pending transfer
            $gamipress_transfers_template_args['pending_transfer'] = true;

            $gamipress_transfers_template_args['transfer_details_link'] =  gamipress_transfers_get_transfer_details_link( $pending_transfer );
        }

    }

    // Setup the form vars
    $gamipress_transfers_template_args['transfer_key'] = gamipress_transfers_generate_transfer_key();
    $gamipress_transfers_template_args['form_id'] = 'gamipress-transfers-transfer-form-' . esc_attr( $gamipress_transfers_template_args['transfer_key'] );

    // Enqueue assets
    gamipress_transfers_enqueue_scripts();

    ob_start();
    gamipress_get_template_part( 'achievement-transfer-form', $atts['achievement_type'] );
    $output = ob_get_clean();

    // Return our rendered achievement transfer form
    return $output;
}
