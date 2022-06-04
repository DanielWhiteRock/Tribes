<?php
/**
 * Ajax Functions
 *
 * @package     GamiPress\Transfers\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function to users autocomplete
 *
 * @since   1.0.0
 * @updated 1.0.1 Make the query more flexible and filterable
 */
function gamipress_transfers_ajax_users_autocomplete() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_transfers', 'nonce' );

    global $wpdb;

    // Setup vars
    $results        = array();
    $search         = sanitize_text_field( $_REQUEST['term'] );
    $search_like    = '%' . $wpdb->esc_like( $search ) . '%';
    $user_id        = get_current_user_id();

    // Setup query vars
    $query_vars = array(
        'select'    => array( "u.ID" ),
        'from'      => array( "{$wpdb->users} AS u" ),
        'join'      => array(),
        'where'     => array( "u.ID != {$user_id}", "( u.user_login LIKE '{$search_like}' OR u.user_email LIKE '{$search_like}' OR u.display_name LIKE '{$search_like}' )" ),
        'order_by'  => array(),
        'limit'     => array(),
    );

    // User roles
    $roles = gamipress_transfers_get_option( 'recipient_roles', array() );

    if( is_array( $roles ) && count( $roles ) ) {

        $cap_meta_key = $wpdb->get_blog_prefix() . 'capabilities';

        $query_vars['join'][] = "LEFT JOIN {$wpdb->usermeta} AS urole ON ( urole.user_id = u.ID AND urole.meta_key = '$cap_meta_key' )";

        foreach( $roles as $role ) {
            $query_vars['where'][] = "urole.meta_value LIKE '%\"{$role}\"%'";
        }

    }

    /**
     * Filter the recipient autocomplete query vars (before get processed)
     *
     * @since 1.0.1
     *
     * @param array     $query_vars     An array with all query vars
     * @param integer   $user_id        Current logged in user ID
     * @param string    $search         User search term
     */
    $query_vars = apply_filters( "gamipress_transfers_users_autocomplete_pre_query_vars", $query_vars, $user_id, $search );

    // Process query vars
    $query_vars['select']   = ( ! empty( $query_vars['select'] )    ? implode( ', ', $query_vars['select'] )                                : '' );
    $query_vars['from']     = ( ! empty( $query_vars['from'] )      ? implode( ', ', $query_vars['from'] )                                  : '' );
    $query_vars['join']     = ( ! empty( $query_vars['join'] )      ? implode( ' ',  $query_vars['join'] )                                  : '' );
    $query_vars['where']    = ( ! empty( $query_vars['where'] )     ? 'WHERE ' . implode( ' AND ', $query_vars['where'] )                   : '' );
    $query_vars['order_by'] = ( ! empty( $query_vars['order_by'] )  ? 'ORDER BY ( ' . implode( ' + ', $query_vars['order_by'] ) . ' ) DESC' : '' );
    $query_vars['limit']    = ( absint( $query_vars['limit'] ) > 0  ? "LIMIT 0, {$query_vars['limit']}"                                     : '' );

    /**
     * Filter the recipient autocomplete query vars (after get processed)
     *
     * @since 1.0.1
     *
     * @param array     $query_vars     An array with all query vars
     * @param integer   $user_id        Current logged in user ID
     * @param string    $search         User search term
     */
    $query_vars = apply_filters( "gamipress_transfers_users_autocomplete_query_vars", $query_vars, $user_id, $search );

    // Setup the query
    $query = "SELECT DISTINCT {$query_vars['select']}
        FROM {$query_vars['from']}
        {$query_vars['join']}
        {$query_vars['where']}
        {$query_vars['order_by']}
        {$query_vars['limit']}";

    /**
     * Filter the recipient autocomplete query
     *
     * @since 1.0.1
     *
     * @param string    $query          The recipient autocomplete query
     * @param array     $query_vars     An array with all query vars
     * @param integer   $user_id        Current logged in user ID
     * @param string    $search         User search term
     */
    $query = apply_filters( "gamipress_transfers_users_autocomplete_query", $query, $query_vars, $user_id, $search );

    $users = $wpdb->get_results( $query );

    if ( is_array( $users ) ) {

        // Loop found users to build an array of with label and value as keys
        foreach ( $users as $user ) {

            $user = get_userdata( $user->ID );

            $results[] = array(
                'label' => gamipress_transfers_display_recipient( $user ),
                'value' => $user->ID
            );

        }

    }

    // Send found results
    wp_send_json( $results );

}
add_action( 'wp_ajax_gamipress_transfers_users_autocomplete', 'gamipress_transfers_ajax_users_autocomplete' );

/**
 * Ajax function to achievements autocomplete
 *
 * @since 1.0.0
 */
function gamipress_transfers_ajax_achievements_autocomplete() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_transfers', 'nonce' );

    global $wpdb;

    $results = array();
    $search  = sanitize_text_field( $_REQUEST['term'] );
    $user_id = get_current_user_id();

    $earned_ids = gamipress_get_user_earned_achievement_ids( $user_id, gamipress_get_achievement_types_slugs() );

    $achievements = $wpdb->get_results( $wpdb->prepare(
        "SELECT ID, post_title, post_type
        FROM {$wpdb->posts}
        WHERE ID IN ( "  . implode( ", ", $earned_ids ) ." )
        AND post_status = 'publish'
        AND post_title LIKE %s
        ORDER BY post_type",
        '%' . $search . '%'
    ) );

    if ( ! empty( $earned_ids ) && is_array( $achievements ) ) {

        // Loop found achievements to build an array of with label and value as keys
        foreach ( $achievements as $achievement ) {

            $results[] = array(
                'label' => $achievement->post_title,
                'display_label' => $achievement->post_title . '<small>' . gamipress_get_achievement_type_singular( $achievement->post_type ) . '</small>',
                'value' => $achievement->ID
            );

        }

    }

    // Send found results
    wp_send_json( $results );

}
add_action( 'wp_ajax_gamipress_transfers_achievements_autocomplete', 'gamipress_transfers_ajax_achievements_autocomplete' );

/**
 * Ajax function to render an achievement
 *
 * @since 1.0.0
 */
function gamipress_transfers_ajax_get_achievement_render() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_transfers', 'nonce' );

    $achievement_id = $_REQUEST['achievement_id'];
    $template_args = $_REQUEST;

    // Unset not template arguments
    unset( $template_args['action'] );
    unset( $template_args['achievement_id'] );

    echo gamipress_render_achievement( $achievement_id, $template_args );

    // Send found results
    die;

}
add_action( 'wp_ajax_gamipress_transfers_get_achievement_render', 'gamipress_transfers_ajax_get_achievement_render' );

/**
 * Ajax function to ranks autocomplete
 *
 * @since 1.0.0
 */
function gamipress_transfers_ajax_ranks_autocomplete() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_transfers', 'nonce' );

    $results = array();
    $search  = sanitize_text_field( $_REQUEST['term'] );
    $user_id = get_current_user_id();

    $rank_types = gamipress_get_rank_types();

    foreach( $rank_types as $rank_type => $data ) {

        $user_rank = gamipress_get_user_rank( $user_id, $rank_type );

        if( ! gamipress_is_lowest_priority_rank( $user_rank->ID )
            && ( $search === '' || strpos( strtolower( $user_rank->post_title ), strtolower( $search ) ) !== false ) ) {

            $results[] = array(
                'label' => $user_rank->post_title,
                'display_label' => $user_rank->post_title . '<small>' . $data['singular_name'] . '</small>',
                'value' => $user_rank->ID
            );
        }

    }

    // Send found results
    wp_send_json( $results );

}
add_action( 'wp_ajax_gamipress_transfers_ranks_autocomplete', 'gamipress_transfers_ajax_ranks_autocomplete' );

/**
 * Ajax function to render an rank
 *
 * @since 1.0.0
 */
function gamipress_transfers_ajax_get_rank_render() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_transfers', 'nonce' );

    $rank_id = $_REQUEST['rank_id'];
    $template_args = $_REQUEST;

    // Unset not template arguments
    unset( $template_args['action'] );
    unset( $template_args['rank_id'] );

    echo gamipress_render_rank( $rank_id, $template_args );

    // Send found results
    die;

}
add_action( 'wp_ajax_gamipress_transfers_get_rank_render', 'gamipress_transfers_ajax_get_rank_render' );

/**
 * Ajax function to process the transfer
 *
 * @since 1.0.0
 */
function gamipress_transfers_ajax_process_transfer() {

    global $wpdb;

    $nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

    // Security check
    if ( ! wp_verify_nonce( $nonce, 'gamipress_transfers_transfer_form' ) )
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress-transfers' ) );

    // Check transfer form parameters
    $transfer_key = isset( $_POST['transfer_key'] ) ? $_POST['transfer_key'] : '';
    $form_id = 'gamipress-transfers-transfer-form-' . $transfer_key;

    if( empty( $transfer_key ) )
        wp_send_json_error( __( 'Invalid transaction ID.', 'gamipress-transfers' ) );

    /* ----------------------------
     * Check user and recipient
     ---------------------------- */

    // Check the sender and recipient
    $user_id = get_current_user_id();
    $recipient_id = isset( $_POST['recipient_id'] ) ? absint( $_POST['recipient_id'] ) : 0;

    // If not recipient ID provided, check from the recipient value
    if( $recipient_id === 0 ) {

        $recipient = isset( $_POST[$form_id . '-recipient'] ) ? $_POST[$form_id . '-recipient'] : '';

        $recipient_id = absint( $wpdb->get_var( $wpdb->prepare(
            "SELECT ID
            FROM {$wpdb->users}
            WHERE ID != %d
            AND ( user_login = %s OR user_email = %s )",
            $user_id,
            $recipient,
            $recipient
        ) ) );

    }

    if( $recipient_id === 0 || $recipient_id === $user_id ) {
        wp_send_json_error( __( 'Invalid recipient.', 'gamipress-transfers' ) );
    }

    /* ----------------------------
     * Transfer type (points, achievement or rank)
     ---------------------------- */

    // We need to decide if is an achievement, points or rank transfer form
    $transfer_type = isset( $_POST['transfer_type'] ) ? $_POST['transfer_type'] : '';

    if( ! in_array( $transfer_type, array( 'points', 'achievement', 'rank' ) ) ) {
        wp_send_json_error( __( 'Form not well configured.', 'gamipress-transfers' ) );
    }

    // Setup vars based on transfer type
    if( $transfer_type === 'points' ) {
        // Points transfer

        // Check points type and its conversion
        $points_types = gamipress_get_points_types();
        $points_type = isset( $_POST['points_type'] ) ? $_POST['points_type'] : '';

        if( ! isset( $points_types[$points_type] ) ) {
            wp_send_json_error( __( 'Invalid points type.', 'gamipress-transfers' ) );
        }

        $points_type_object = $points_types[$points_type];

        $amount = isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : 0;

        // Check if amount is correct
        if( $amount <= 0 ) {
            wp_send_json_error( __( 'Invalid amount.', 'gamipress-transfers' ) );
        }

        $user_points = gamipress_get_user_points( $user_id, $points_type );

        // Check if user has the amount he wants to transfer
        if( $amount > $user_points ) {
            wp_send_json_error( sprintf( __( 'Insufficient %s to transfer.', 'gamipress-transfers' ), $points_type_object['plural_name'] ) );
        }

        // Setup the transfer item data
        $transfer_item = array(
            'post_id' => $points_type_object['ID'],
            'post_type' => $points_type,
            'description' => $points_type_object['plural_name'],
            'quantity' => $amount,
        );

    } else if( $transfer_type === 'achievement' ) {
        // Achievement transfer

        // Check the achievement
        $achievement_id = isset( $_POST['achievement_id'] ) ? $_POST['achievement_id'] : '';
        $achievement = get_post( $achievement_id );

        if( ! $achievement ) {
            wp_send_json_error( __( 'Invalid achievement.', 'gamipress-transfers' ) );
        }

        // Check achievement type
        $achievement_types = gamipress_get_achievement_types();
        $achievement_type = $achievement->post_type;

        if( ! isset( $achievement_types[$achievement_type] ) ) {
            wp_send_json_error( __( 'Invalid achievement type.', 'gamipress-transfers' ) );
        }

        // Setup the transfer item data
        $transfer_item = array(
            'post_id' => $achievement_id,
            'post_type' => $achievement_type,
            'description' => $achievement_types[$achievement_type]['singular_name'] . ': ' . $achievement->post_title,
            'quantity' => 1, // TODO: Make amount editable
        );

    } else if( $transfer_type === 'rank' ) {
        // Rank transfer

        // Check the rank
        $rank_id = isset( $_POST['rank_id'] ) ? $_POST['rank_id'] : '';
        $rank = get_post( $rank_id );

        if( ! $rank ) {
            wp_send_json_error( __( 'Invalid rank.', 'gamipress-transfers' ) );
        }

        // Check achievement type
        $rank_types = gamipress_get_rank_types();
        $rank_type = $rank->post_type;

        if( ! isset( $rank_types[$rank_type] ) ) {
            wp_send_json_error( __( 'Invalid rank type.', 'gamipress-transfers' ) );
        }

        // Setup the transfer item data
        $transfer_item = array(
            'post_id' => $rank_id,
            'post_type' => $rank_type,
            'description' => $rank_types[$rank_type]['singular_name'] . ': ' . $rank->post_title,
            'quantity' => 1, // TODO: Make amount editable
        );

    }

    /* ----------------------------
     * Everything done, so process it!
     ---------------------------- */

    // Lets to create the transfer
    $ct_table = ct_setup_table( 'gamipress_transfers' );

    $transfer = array(

        // Transfer details

        'number' => gamipress_transfers_get_transfer_next_transfer_number(),
        'date' => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
        'status' => 'processing',
        'transfer_key' => $transfer_key,

        // User details

        'user_id' => $user_id,
        'user_ip' => gamipress_transfers_get_ip(),

        // Recipient details

        'recipient_id' => $recipient_id,

    );

    $transfer_id = $ct_table->db->insert( $transfer );

    // Store the given transfer id to assign it to the transfer items and for hooks
    $transfer['transfer_id'] = $transfer_id;

    // Lets to create the transfer items (just one, with the amount of points)
    $ct_table = ct_setup_table( 'gamipress_transfer_items' );

    $transfer_item['transfer_id'] = $transfer_id;

    $transfer_item_id = $ct_table->db->insert( $transfer_item );

    $transfer_item['transfer_item_id'] = $transfer_item_id;

    // Setup vars for coming filters
    $transfer_items = array( $transfer_item );
    $transfer_link = gamipress_transfers_get_transfer_details_link( $transfer_id );

    /* ----------------------------
     * Response processing
     ---------------------------- */

    $response = array(
        'success'       => true,
        'message'       => '',
        'redirect'      => $transfer_link ? true : false,
        'redirect_url'  => $transfer_link,
    );

    // Process the transfer based on settings
    if( (bool) gamipress_transfers_get_option( 'pending_transfers', false ) ) {

        // Mark transfer as pending
        gamipress_transfers_update_transfer_status( $transfer_id, 'pending' );

        // Insert an informative note to the transfer
        gamipress_transfers_insert_transfer_note( $transfer_id,
            __( 'Transfer Pending', 'gamipress-transfers' ),
            __( 'Transfer has been marked as pending and is waiting for approval.', 'gamipress-transfers' )
        );

        // Update message
        $response['message'] = __( 'Your transfer has been made successfully and is waiting for approval.', 'gamipress-transfers' );

    } else {

        // Mark transfer as complete
        gamipress_transfers_update_transfer_status( $transfer_id, 'complete' );

        // Insert an informative note to the transfer
        gamipress_transfers_insert_transfer_note( $transfer_id,
            __( 'Transfer Complete', 'gamipress-transfers' ),
            __( 'Transfer has been completed successfully.', 'gamipress-transfers' )
        );

        // Update message
        $response['message'] = __( 'Your transfer has been completed successfully.', 'gamipress-transfers' );
    }

    // Just add the "Redirecting ..." part if transfer link is set
    if( $transfer_link ) {
        $response['message'] .= ' ' . __( 'Redirecting to transfer details ...', 'gamipress-transfers' );
    }

    /**
     * Let other functions process the transfer and get their response
     *
     * @since 1.0.0
     *
     * @param array     $response       Processing response
     * @param array     $transfer       Transfer data array
     * @param array     $transfer_items Transfer items array
     *
     * @return array    $response       Response
     */
    $response = apply_filters( "gamipress_transfers_process_transfer_response", $response, $transfer, $transfer_items );

    if( $response['success'] === true ) {
        wp_send_json_success( $response );
    } else {
        wp_send_json_error( $response );
    }

}
add_action( 'wp_ajax_gamipress_transfers_process_transfer', 'gamipress_transfers_ajax_process_transfer' );

/**
 * Ajax function to add a transfer note at backend
 *
 * @since 1.0.0
 */
function gamipress_transfers_ajax_add_transfer_note() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Security check
    if( ! current_user_can( gamipress_get_manager_capability() ) )
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress-transfers' ) );

    // Setup vars
    $user_id = get_current_user_id();
    $transfer_id = absint( $_REQUEST['transfer_id'] );
    $title = sanitize_text_field( $_REQUEST['title'] );
    $description = sanitize_textarea_field( $_REQUEST['description'] );

    // Check all vars
    if( $transfer_id === 0 )
        wp_send_json_error( __( 'Wrong transfer ID.', 'gamipress-transfers' ) );

    if( empty( $title ) )
        wp_send_json_error( __( 'Please, fill the title.', 'gamipress-transfers' ) );

    if( empty( $description ) )
        wp_send_json_error( __( 'Please, fill the note.', 'gamipress-transfers' ) );

    // Setup the transfer notes table
    $ct_table = ct_setup_table( 'gamipress_transfer_notes' );

    // Insert the new transfer note
    $transfer_note_id = $ct_table->db->insert( array(
        'transfer_id' => $transfer_id,
        'title' => $title,
        'description' => $description,
        'user_id' => $user_id,
        'date' => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) )
    ) );

    // Get the transfer note object
    $transfer_note = ct_get_object( $transfer_note_id );

    // Setup the transfers table
    ct_setup_table( 'gamipress_transfers' );

    // Get the transfer object
    $transfer = ct_get_object( $transfer_id );

    // Get the transfer note html to return as response
    ob_start();

    gamipress_transfers_admin_render_transfer_note( $transfer_note, $transfer );

    $response = ob_get_clean();

    wp_send_json_success( $response );

}
add_action( 'wp_ajax_gamipress_transfers_add_transfer_note', 'gamipress_transfers_ajax_add_transfer_note' );

/**
 * Ajax function to delete a transfer note at backend
 *
 * @since 1.0.0
 */
function gamipress_transfers_ajax_delete_transfer_note() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Security check
    if( ! current_user_can( gamipress_get_manager_capability() ) )
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress-transfers' ) );

    // Setup vars
    $transfer_note_id = absint( $_REQUEST['transfer_note_id'] );

    // Check all vars
    if( $transfer_note_id === 0 )
        wp_send_json_error( __( 'Wrong transfer note ID.', 'gamipress-transfers' ) );

    // Setup the transfer notes table
    $ct_table = ct_setup_table( 'gamipress_transfer_notes' );

    $result = $ct_table->db->delete( $transfer_note_id );

    wp_send_json_success( __( 'Transfer note deleted successfully.', 'gamipress-transfers' ) );

}
add_action( 'wp_ajax_gamipress_transfers_delete_transfer_note', 'gamipress_transfers_ajax_delete_transfer_note' );