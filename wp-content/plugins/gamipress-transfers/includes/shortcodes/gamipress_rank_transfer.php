<?php
/**
 * GamiPress Rank Transfer Shortcode
 *
 * @package     GamiPress\Transfers\Shortcodes\Shortcode\GamiPress_Rank_Transfer
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_rank_transfer] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_transfers_register_rank_transfer_shortcode() {

    // Setup the rank fields
    $rank_fields = GamiPress()->shortcodes['gamipress_rank']->fields;

    unset( $rank_fields['id'] );

    gamipress_register_shortcode( 'gamipress_rank_transfer', array(
        'name'              => __( 'Rank Transfer', 'gamipress-transfers' ),
        'description'       => __( 'Render a rank transfer form.', 'gamipress-transfers' ),
        'output_callback'   => 'gamipress_transfers_rank_transfer_shortcode',
        'icon'              => 'transfer',
        'group'             => 'transfers',
        'tabs'              => array(
            'form' => array(
                'icon' => 'dashicons-feedback',
                'title' => __( 'Form', 'gamipress-transfers' ),
                'fields' => array(
                    'id',
                    'select_rank',
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
            'rank' => array(
                'icon' => 'dashicons-rank',
                'title' => __( 'Rank', 'gamipress-transfers' ),
                'fields' => array_keys( $rank_fields ),
            ),
        ),

        'fields'            => array_merge( array(
            'id' => array(
                'name'          => __( 'Rank', 'gamipress-transfers' ),
                'description'   => __( 'The rank to transfer.', 'gamipress-transfers' ),
                'type'          => 'select',
                'classes' 	        => 'gamipress-post-selector',
                'attributes' 	    => array(
                    'data-post-type' => implode( ',',  gamipress_get_rank_types_slugs() ),
                    'data-placeholder' => __( 'Select a rank', 'gamipress-transfers' ),
                ),
                'default'       => '',
                'options_cb'    => 'gamipress_options_cb_posts'
            ),
            'select_rank' => array(
                'name'          => __( 'Allow Select Rank', 'gamipress-transfers' ),
                'description'   => __( 'Allow user to select a specific rank to transfer. If rank is set it will be used as initial rank selected.', 'gamipress-transfers' ),
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

        ), $rank_fields ),
    ) );

}
add_action( 'init', 'gamipress_transfers_register_rank_transfer_shortcode' );

/**
 * Transfer Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_transfers_rank_transfer_shortcode( $atts = array() ) {

    global $gamipress_transfers_template_args;

    // Unset id attr from rank shortcode defaults since it initializes id with current post ID
    $rank_defaults = gamipress_rank_shortcode_defaults();

    unset( $rank_defaults['id'] );

    $shortcode_defaults = array_merge( array(
        'id'                        => '0',
        'select_rank'               => 'yes',
        'button_text' 		        => __( 'Transfer', 'gamipress-transfers' ),
        // Recipient
        'recipient_id'                  => '0',
        'select_recipient'              => 'no',
        'recipient_placeholder'         => __( 'Enter username or email', 'gamipress-transfers' ),
        'recipient_autocomplete'        => 'no',
    ), $rank_defaults );

    /**
     * Filters shortcode defaults
     *
     * @since 1.0.0
     *
     * @param array $shortcode_defaults
     *
     * @return array
     */
    $shortcode_defaults = apply_filters( 'gamipress_rank_transfer_shortcode_defaults', $shortcode_defaults );

    // Get the shortcode attributes
    $atts = shortcode_atts( $shortcode_defaults, $atts, 'gamipress_rank_transfer' );

    // Ensure values as int
    $atts['id'] = absint( $atts['id'] );
    $atts['recipient_id'] = absint( $atts['recipient_id'] );
    $atts['rank_type'] = ''; // Initialize this var to be passed to gamipress_get_template() function

    // Setup user id
    $user_id = get_current_user_id();

    if( $user_id === 0 ) {
        return sprintf( __( 'You need to <a href="%s">log in</a> to make a transfer.', 'gamipress-transfers' ), wp_login_url( get_permalink() ) );
    }

    // Check user ranks
    $rank_types = gamipress_get_rank_types();

    $user_ranks = array();

    foreach( $rank_types as $rank_type => $data ) {

        $user_rank_id = gamipress_get_user_rank_id( $user_id, $rank_type );

        if( ! gamipress_is_lowest_priority_rank( $user_rank_id ) ) {
            $user_ranks[] = $user_rank_id;
        }

    }

    // if user has not earned anything can't transfer anything
    if( empty( $user_ranks ) ) {
        return '';
    }

    // Need to ensure rank ID is select rank is not set
    if( $atts['select_rank'] === 'no' ) {

        // Return if rank id not specified
        if ( $atts['id'] === 0 )
            return '';

        // Setup the rank
        $rank = get_post( $atts['id'] );

        if( ! $rank ) {
            return gamipress_transfers_notify_form_error( __( 'Rank not exists.', 'gamipress-transfers' ) );
        }

        if( ! gamipress_is_lowest_priority_rank( $atts['id'] ) ) {
            return gamipress_transfers_notify_form_error( __( 'Rank is the lowest priority rank and they can\'t be transferred.', 'gamipress-transfers' ) );
        }

        // User just can transfer a rank if has earned it
        if( ! in_array( $atts['id'], $user_ranks ) ) {
            return '';
        }

        $atts['rank_type'] = $rank->post_type;

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

    // Setup rank template args
    $gamipress_transfers_template_args['template_args'] = array();

    $rank_fields = array_keys( GamiPress()->shortcodes['gamipress_rank']->fields );

    foreach( $rank_fields as $rank_field ) {

        if( ! isset( $atts[$rank_field] ) )
            continue;

        $gamipress_transfers_template_args['template_args'][$rank_field] = $atts[$rank_field];
    }

    // If not select rank, check if rank is already pending to transfer
    if( $atts['select_rank'] === 'no' ) {

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

    // Also pass the array of user ranks IDs (that are not default ranks)
    $gamipress_transfers_template_args['user_ranks'] = $user_ranks;

    // Enqueue assets
    gamipress_transfers_enqueue_scripts();

    ob_start();
    gamipress_get_template_part( 'rank-transfer-form', $atts['rank_type'] );
    $output = ob_get_clean();

    // Return our rendered rank transfer form
    return $output;
}
