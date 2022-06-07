<?php
/**
 * Admin Dashboard Reports Page
 *
 * @package     GamiPress\Reports\Admin\Dashboard_Reports
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register dashboard reports page.
 *
 * @since  1.0.0
 *
 * @return void
 */
function gamipress_reports_register_dashboard_reports_page() {

    gamipress_register_reports_page( 'dashboard', __( 'Dashboard', 'gamipress-reports' ) );

}
add_action( 'cmb2_admin_init', 'gamipress_reports_register_dashboard_reports_page' );

/**
 * Initial report tab load through ajax
 *
 * @since 1.0.0
 */
function gamipress_reports_dashboard_load_tab() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    $tab = $_REQUEST['tab'];

    if( function_exists( "gamipress_reports_dashboard_load_{$tab}_tab" ) )
        call_user_func( "gamipress_reports_dashboard_load_{$tab}_tab" );

}
add_action( 'wp_ajax_gamipress_reports_load_dashboard_tab', 'gamipress_reports_dashboard_load_tab' );

/**
 * Register dashboard reports sections
 *
 * @since  1.0.0
 *
 * @return array
 */
function gamipress_reports_register_dashboard_reports_sections( $reports_sections ) {

    $reports_sections['points'] = array(
        'title' => __( 'Points', 'gamipress-reports' ),
        'icon' => 'dashicons-star-filled'
    );

    $reports_sections['achievements'] = array(
        'title' => __( 'Achievements', 'gamipress-reports' ),
        'icon' => 'dashicons-awards'
    );

    $reports_sections['ranks'] = array(
        'title' => __( 'Ranks', 'gamipress-reports' ),
        'icon' => 'dashicons-rank'
    );

    return $reports_sections;

}
add_action( 'gamipress_reports_dashboard_sections', 'gamipress_reports_register_dashboard_reports_sections' );

/* --------------------------------
 * Points
   -------------------------------- */

/**
 * Points reports meta boxes
 *
 * @since 1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_reports_dashboard_points_meta_boxes( $meta_boxes ) {

    // Widgets
    $meta_boxes['dashboard-points-circulation'] = array(
        'classes' => 'gamipress-reports-col-3',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'In Circulation', 'gamipress-reports' ),
            'icon' => 'dashicons-image-rotate',
            'color' => 'blue'
        ) ),
    );

    $meta_boxes['dashboard-points-awarded'] = array(
        'classes' => 'gamipress-reports-col-3',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Awarded', 'gamipress-reports' ),
            'icon' => 'dashicons-star-filled',
            'color' => 'green'
        ) ),
    );

    $meta_boxes['dashboard-points-deducted'] = array(
        'classes' => 'gamipress-reports-col-3',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Deducted', 'gamipress-reports' ),
            'icon' => 'dashicons-star-empty',
            'color' => 'red'
        ) ),
    );

    $meta_boxes['dashboard-points-expended'] = array(
        'classes' => 'gamipress-reports-col-3',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Expended', 'gamipress-reports' ),
            'icon' => 'dashicons-update',
            'color' => 'purple'
        ) ),
    );

    // List table
    $meta_boxes['dashboard-points-list-table'] = array(
        'title' => __( 'Points Reports', 'gamipress-reports' ),
        'classes' => 'gamipress-reports-col-12',
        'content' => '<div></div>',
    );

    return $meta_boxes;
}
add_filter( "gamipress_reports_dashboard_points_meta_boxes", 'gamipress_reports_dashboard_points_meta_boxes' );

/**
 * Initial points report tab load through ajax
 *
 * @since 1.0.0
 */
function gamipress_reports_dashboard_load_points_tab() {

    $points_types = gamipress_get_points_types();

    // Counters for all points types
    $data = array(
        'dashboard_points_circulation'   => 0,
        'dashboard_points_awarded'       => 0,
        'dashboard_points_deducted'      => 0,
        'dashboard_points_expended'      => 0,
    );

    foreach( $points_types as $points_type => $points_type_data ) {

        $data['dashboard_points_circulation']    += gamipress_get_user_meta_sum( "_gamipress_{$points_type}_points" );
        $data['dashboard_points_awarded']        += gamipress_get_user_meta_sum( "_gamipress_{$points_type}_points_awarded" );
        $data['dashboard_points_deducted']       += gamipress_get_user_meta_sum( "_gamipress_{$points_type}_points_deducted" );
        $data['dashboard_points_expended']       += gamipress_get_user_meta_sum( "_gamipress_{$points_type}_points_expended" );

    }

    $data['dashboard_points_circulation']    = number_format( $data['dashboard_points_circulation'], 0 );
    $data['dashboard_points_awarded']        = number_format( $data['dashboard_points_awarded'], 0 );
    $data['dashboard_points_deducted']       = number_format( $data['dashboard_points_deducted'], 0 );
    $data['dashboard_points_expended']       = number_format( $data['dashboard_points_expended'], 0 );

    // Setup the points list table
    $table = new GamiPress_Report_List_Table( array(
        'id' => 'dashboard_points_list_table',
        'singular' => __( 'Point', 'gamipress-reports' ),
        'plural' => __( 'Points', 'gamipress-reports' ),
    ) );

    $table->prepare_items();

    ob_start();
    $table->display();
    $data['dashboard_points_list_table'] = ob_get_clean();

    wp_send_json_success( $data );

}

/**
 * Custom query for report list table
 *
 * @since 1.0.0
 *
 * @param string $query
 * @param array $args
 *
 * @return string
 */
function gamipress_reports_dashboard_points_list_table_query( $query, $args ) {

    global $wpdb;

    // Initialize query vars
    $select = array( 'p.ID AS post_id' );
    $from = array( "{$wpdb->posts} AS p" );
    $join = array();
    $where = array( '1=1' );
    $order_by = array();
    $query_args = array();

    $where[] = 'p.post_type = %s';
    $query_args[] = 'points-type';

    $points_types = gamipress_get_points_types();

    // Define metrics as fields => query arg (%points_type% will be replaced after)
    $metrics = array(
        'circulation' => "_gamipress_%points_type%_points",
        'awarded' => "_gamipress_%points_type%_points_awarded",
        'deducted' => "_gamipress_%points_type%_points_deducted",
        'expended' => "_gamipress_%points_type%_points_expended",
    );

    // Loop all metrics to generate the query
    foreach( $metrics as $metric => $query_arg ) {

        $meta_keys = array();

        foreach( $points_types as $points_type => $points_type_data ) {
            $meta_keys[] = str_replace( '%points_type%', $points_type, $query_arg );
        }


        $select[] = "IFNULL( ( SELECT SUM( um.meta_value ) FROM $wpdb->usermeta AS um WHERE um.meta_key = REPLACE('{$query_arg}', '%points_type%', p.post_name) ), 0 ) AS `{$metric}`";
    }

    $order_by[] = "post_id";

    // Process query vars
    $select = ( ! empty( $select ) ? implode( ', ', $select ) : '' );
    $from = ( ! empty( $from ) ? implode( ', ', $from ) : '' );
    $join = ( ! empty( $join ) ? implode( ' ', $join ) : '' );
    $where = ( ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '' );
    $order_by = ( ! empty( $order_by ) ? 'ORDER BY ' . implode( ', ', $order_by ) . ' DESC' : '' );

    // Execute our query
    return $wpdb->prepare(
        "SELECT DISTINCT {$select}
         FROM {$from}
         {$join}
         {$where}
         {$order_by}",
        $query_args
    );

}
add_filter( "gamipress_reports_dashboard_points_list_table_query", 'gamipress_reports_dashboard_points_list_table_query', 10, 2 );

/**
 * Custom report list table columns
 *
 * @since 1.0.0
 *
 * @param array $columns
 *
 * @return array
 */
function gamipress_reports_dashboard_points_list_table_columns( $columns ) {

    $columns['post_id'] = __( 'Points Type', 'gamipress-reports' );
    $columns['circulation'] = __( 'Circulation', 'gamipress-reports' );
    $columns['awarded'] = __( 'Awarded', 'gamipress-reports' );
    $columns['deducted'] = __( 'Deducted', 'gamipress-reports' );
    $columns['expended'] = __( 'Expended', 'gamipress-reports' );

    return $columns;
}
add_filter( "gamipress_reports_manage_dashboard_points_list_table_columns", 'gamipress_reports_dashboard_points_list_table_columns' );

/**
 * Custom report list table columns output
 *
 * @since 1.0.0
 *
 * @param string    $column_name
 * @param stdClass  $item
 */
function gamipress_reports_dashboard_points_list_table_custom_column( $column_name, $item ) {

    switch( $column_name ) {
        case 'post_id':

            $points_types = gamipress_get_points_types();

            $points_type = get_post_field( 'post_name', $item->post_id );
            $post_title = get_post_field( 'post_title', $item->post_id );

            $can_edit_posts = current_user_can( 'edit_posts' );

            if( $can_edit_posts ) {
                printf(
                    '<a href="%s" aria-label="%s">%s</a>',
                    get_edit_post_link( $item->post_id ),
                    /* translators: %s: post title */
                    esc_attr( sprintf( __( '&#8220;%s&#8221; (Edit)' ), $post_title ) ),
                    $points_types[$points_type]['plural_name']
                );
            } else {
                echo $points_types[$points_type]['plural_name'];
            }
            break;
        case 'circulation':
        case 'awarded':
        case 'deducted':
        case 'expended':

            if( function_exists('gamipress_format_amount') ) {
                $points_type = get_post_field( 'post_name', $item->post_id );

                echo gamipress_format_amount( $item->$column_name, $points_type );
            }

            break;
    }

}
add_action( "gamipress_reports_manage_dashboard_points_list_table_custom_column", 'gamipress_reports_dashboard_points_list_table_custom_column', 10, 2 );

/**
 * Custom report list table row actions
 *
 * @since 1.0.0
 *
 * @param array     $actions
 * @param stdClass  $item
 *
 * @return array
 */
function gamipress_reports_dashboard_points_list_table_row_actions( $actions, $item ) {

    $points_type = get_post_field( 'post_name', $item->post_id );
    $post_title = get_post_field( 'post_title', $item->post_id );

    $can_edit_posts = current_user_can( 'edit_posts' );

    if( $can_edit_posts ) {
        $actions['edit'] = sprintf(
            '<a href="%s" aria-label="%s">%s</a>',
            get_edit_post_link( $item->post_id ),
            esc_attr( sprintf( __( '&#8220;%s&#8221; (Edit)' ), $post_title ) ),
            __( 'Edit' )
        );
    }

    $actions['view_reports'] = sprintf(
        '<a href="%s" aria-label="%s">%s</a>',
        admin_url( 'admin.php?page=gamipress_reports_points&tab=opt-tab-' . $points_type ),
        esc_attr( __( 'View Reports', 'gamipress-reports' ) ),
        /*'<i class="dashicons dashicons-chart-area"></i> ' .*/ __( 'View Reports', 'gamipress-reports' )
    );

    return $actions;
}
add_action( "dashboard_points_list_table_row_actions", 'gamipress_reports_dashboard_points_list_table_row_actions', 10, 2 );

/* --------------------------------
 * Achievements
   -------------------------------- */

/**
 * Achievements reports meta boxes
 *
 * @since 1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_reports_dashboard_achievements_meta_boxes( $meta_boxes ) {

    // Widgets
    $meta_boxes['dashboard-achievements-active'] = array(
        'classes' => 'gamipress-reports-col-4',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Active', 'gamipress-reports' ),
            'icon' => 'dashicons-thumbs-up',
            'color' => 'blue'
        ) ),
    );

    $meta_boxes['dashboard-achievements-earned'] = array(
        'classes' => 'gamipress-reports-col-4',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Earned', 'gamipress-reports' ),
            'icon' => 'dashicons-flag',
            'color' => 'green'
        ) ),
    );

    $meta_boxes['dashboard-achievements-awarded'] = array(
        'classes' => 'gamipress-reports-col-4',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Awarded', 'gamipress-reports' ),
            'icon' => 'dashicons-awards',
            'color' => 'purple'
        ) ),
    );

    $meta_boxes['dashboard-achievements-list-table'] = array(
        'title' => __( 'Achievements Reports', 'gamipress-reports' ),
        'classes' => 'gamipress-reports-col-12',
        'content' => '<div></div>',
    );

    return $meta_boxes;
}
add_filter( "gamipress_reports_dashboard_achievements_meta_boxes", 'gamipress_reports_dashboard_achievements_meta_boxes' );

/**
 * Initial achievements report tab load through ajax
 *
 * @since 1.0.0
 */
function gamipress_reports_dashboard_load_achievements_tab() {

    global $wpdb;

    $achievements_types = gamipress_get_achievement_types_slugs();
    $achievements_types_where = implode( "', '", $achievements_types );

    $active = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type IN ( '" . $achievements_types_where . "' ) AND post_status = %s",
        'publish'
    ) );

    $ct_table = ct_setup_table( 'gamipress_user_earnings' );

    // Earned
    $earned = $wpdb->get_results( "SELECT post_id FROM {$ct_table->db->table_name} WHERE post_type IN ( '" . $achievements_types_where . "' )");

    // Awarded
    $awarded_ids = array();
    $awarded = 0;

    foreach( $earned as $earning ) {
        $awarded_ids[] = $earning->post_id;
    }

    if( count( $awarded_ids ) ) {

        $awarded = gamipress_reports_get_logs_report( array(
            'select' => array(
                'count' => array(
                    'field' => 'log_id',
                    'function' => 'COUNT'
                )
            ),
            'where' => array(
                'type' => array(
                    'value' => 'achievement_award',
                ),
                '_gamipress_achievement_id' => array(
                    'value' => $awarded_ids,
                    'compare' => 'IN'
                )
            )
        ) );

        $awarded = ( isset( $awarded[0] ) ? absint( $awarded[0]['count'] ) : 0 );

    }

    // Counters for all achievements types
    $data = array(
        'dashboard_achievements_active'   => $active,
        'dashboard_achievements_earned'   => count( $earned ),
        'dashboard_achievements_awarded'  => $awarded,
    );

    $data['dashboard_achievements_active']  = number_format( $data['dashboard_achievements_active'], 0 );
    $data['dashboard_achievements_earned']  = number_format( $data['dashboard_achievements_earned'], 0 );
    $data['dashboard_achievements_awarded'] = number_format( $data['dashboard_achievements_awarded'], 0 );

    // Setup the achievements list table
    $table = new GamiPress_Report_List_Table( array(
        'id' => 'dashboard_achievements_list_table',
        'singular' => __( 'Achievement', 'gamipress-reports' ),
        'plural' => __( 'Achievements', 'gamipress-reports' ),
    ) );

    $table->prepare_items();

    ob_start();
    $table->display();
    $data['dashboard_achievements_list_table'] = ob_get_clean();

    wp_send_json_success( $data );

}

/**
 * Custom query for report list table
 *
 * @since 1.0.0
 *
 * @param string $query
 * @param array $args
 *
 * @return string
 */
function gamipress_reports_dashboard_achievements_list_table_query( $query, $args ) {

    global $wpdb;

    // Initialize query vars
    $select = array( 'p.ID AS post_id' );
    $from = array( "{$wpdb->posts} AS p" );
    $join = array();
    $where = array( '1=1' );
    $order_by = array();
    $query_args = array();

    // Prepare table names
    $ct_table = ct_setup_table( 'gamipress_user_earnings' );

    $earnings_table = $ct_table->db->table_name;

    $ct_table = ct_setup_table( 'gamipress_logs' );

    $logs_table = $ct_table->db->table_name;
    $logs_meta_table = $ct_table->meta->db->table_name;

    // Active column
    $select[] = "IFNULL( ( SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = p.post_name AND post_status = %s ), 0 ) AS `active`";
    $query_args[] = 'publish';

    // Earned column
    $select[] = "IFNULL( ( SELECT COUNT(*) FROM {$earnings_table} AS e WHERE e.post_type = p.post_name ), 0 ) AS `earned`";

    // Awarded column
    $select[] = "IFNULL( (
        SELECT COUNT(*)
        FROM {$logs_table} AS l
        LEFT JOIN {$logs_meta_table} AS lm ON ( lm.log_id = l.log_id )
        WHERE l.type = %s
        AND ( lm.meta_key = %s AND lm.meta_value IN (
            SELECT ue.post_id FROM {$earnings_table} AS ue WHERE ue.post_type = p.post_name
         ) )
    ), 0 ) AS `awarded`";
    $query_args[] = 'achievement_award';
    $query_args[] = '_gamipress_achievement_id';

    // Where to limit just to achievement type posts
    $where[] = 'p.post_type = %s';
    $query_args[] = 'achievement-type';

    $order_by[] = "post_id";

    // Process query vars
    $select = ( ! empty( $select ) ? implode( ', ', $select ) : '' );
    $from = ( ! empty( $from ) ? implode( ', ', $from ) : '' );
    $join = ( ! empty( $join ) ? implode( ' ', $join ) : '' );
    $where = ( ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '' );
    $order_by = ( ! empty( $order_by ) ? 'ORDER BY ' . implode( ', ', $order_by ) . ' DESC' : '' );

    // Execute our query
    return $wpdb->prepare(
        "SELECT DISTINCT {$select}
         FROM {$from}
         {$join}
         {$where}
         {$order_by}",
        $query_args
    );

}
add_filter( "gamipress_reports_dashboard_achievements_list_table_query", 'gamipress_reports_dashboard_achievements_list_table_query', 10, 2 );

/**
 * Custom report list table columns
 *
 * @since 1.0.0
 *
 * @param array $columns
 *
 * @return array
 */
function gamipress_reports_dashboard_achievements_list_table_columns( $columns ) {

    $columns['post_id'] = __( 'Achievement Type', 'gamipress-reports' );
    $columns['active'] = __( 'Active', 'gamipress-reports' );
    $columns['earned'] = __( 'Earned', 'gamipress-reports' );
    $columns['awarded'] = __( 'Awarded', 'gamipress-reports' );

    return $columns;
}
add_filter( "gamipress_reports_manage_dashboard_achievements_list_table_columns", 'gamipress_reports_dashboard_achievements_list_table_columns' );

/**
 * Custom report list table columns output
 *
 * @since 1.0.0
 *
 * @param string    $column_name
 * @param stdClass  $item
 */
function gamipress_reports_dashboard_achievements_list_table_custom_column( $column_name, $item ) {

    switch( $column_name ) {
        case 'post_id':

            $achievement_types = gamipress_get_achievement_types();

            $achievement_type = get_post_field( 'post_name', $item->post_id );
            $post_title = get_post_field( 'post_title', $item->post_id );

            $can_edit_posts = current_user_can( 'edit_posts' );

            if( $can_edit_posts ) {
                printf(
                    '<a href="%s" aria-label="%s">%s</a>',
                    get_edit_post_link( $item->post_id ),
                    /* translators: %s: post title */
                    esc_attr( sprintf( __( '&#8220;%s&#8221; (Edit)' ), $post_title ) ),
                    $achievement_types[$achievement_type]['plural_name']
                );
            } else {
                echo $achievement_types[$achievement_type]['plural_name'];
            }
            break;
    }

}
add_action( "gamipress_reports_manage_dashboard_achievements_list_table_custom_column", 'gamipress_reports_dashboard_achievements_list_table_custom_column', 10, 2 );

/**
 * Custom report list table row actions
 *
 * @since 1.0.0
 *
 * @param array     $actions
 * @param stdClass  $item
 *
 * @return array
 */
function gamipress_reports_dashboard_achievements_list_table_row_actions( $actions, $item ) {

    $achievement_type = get_post_field( 'post_name', $item->post_id );
    $post_title = get_post_field( 'post_title', $item->post_id );

    $can_edit_posts = current_user_can( 'edit_posts' );

    if( $can_edit_posts ) {
        $actions['edit'] = sprintf(
            '<a href="%s" aria-label="%s">%s</a>',
            get_edit_post_link( $item->post_id ),
            esc_attr( sprintf( __( '&#8220;%s&#8221; (Edit)' ), $post_title ) ),
            __( 'Edit' )
        );
    }

    $actions['view_reports'] = sprintf(
        '<a href="%s" aria-label="%s">%s</a>',
        admin_url( 'admin.php?page=gamipress_reports_achievements&tab=opt-tab-' . $achievement_type ),
        esc_attr( __( 'View Reports', 'gamipress-reports' ) ),
        /*'<i class="dashicons dashicons-chart-area"></i> ' .*/ __( 'View Reports', 'gamipress-reports' )
    );

    return $actions;
}
add_action( "dashboard_achievements_list_table_row_actions", 'gamipress_reports_dashboard_achievements_list_table_row_actions', 10, 2 );

/* --------------------------------
 * Ranks
   -------------------------------- */

/**
 * Ranks reports meta boxes
 *
 * @since 1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_reports_dashboard_ranks_meta_boxes( $meta_boxes ) {

    // Widgets
    $meta_boxes['dashboard-ranks-active'] = array(
        'classes' => 'gamipress-reports-col-4',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Active', 'gamipress-reports' ),
            'icon' => 'dashicons-rank',
            'color' => 'blue'
        ) ),
    );

    $meta_boxes['dashboard-ranks-highest'] = array(
        'classes' => 'gamipress-reports-col-4',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Users on highest rank', 'gamipress-reports' ),
            'icon' => 'dashicons-arrow-up-alt',
            'color' => 'green'
        ) ),
    );

    $meta_boxes['dashboard-ranks-lowest'] = array(
        'classes' => 'gamipress-reports-col-4',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Users on lowest rank', 'gamipress-reports' ),
            'icon' => 'dashicons-arrow-down-alt',
            'color' => 'red'
        ) ),
    );

    $meta_boxes['dashboard-ranks-list-table'] = array(
        'title' => __( 'Ranks Reports', 'gamipress-reports' ),
        'classes' => 'gamipress-reports-col-12',
        'content' => '<div></div>',
    );

    return $meta_boxes;
}
add_filter( "gamipress_reports_dashboard_ranks_meta_boxes", 'gamipress_reports_dashboard_ranks_meta_boxes' );

/**
 * Initial ranks report tab load through ajax
 *
 * @since 1.0.0
 */
function gamipress_reports_dashboard_load_ranks_tab() {

    global $wpdb;

    $rank_types = gamipress_get_rank_types_slugs();
    $rank_types_where = implode( "', '", $rank_types );

    $active = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type IN ( '" . $rank_types_where . "' ) AND post_status = %s",
        'publish'
    ) );

    // Counters for all ranks types
    $data = array(
        'dashboard_ranks_active'    => $active,
        'dashboard_ranks_highest'   => 0,
        'dashboard_ranks_lowest'    => 0,
    );

    $data['dashboard_ranks_active']     = number_format( $data['dashboard_ranks_active'], 0 );
    $data['dashboard_ranks_highest']    = number_format( $data['dashboard_ranks_highest'], 0 );
    $data['dashboard_ranks_lowest']     = number_format( $data['dashboard_ranks_lowest'], 0 );

    foreach( $rank_types as $rank_type ) {

        // Get rank type highest priority rank ID
        $highest_rank_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s ORDER BY menu_order DESC LIMIT 1",
            $rank_type,
            'publish'
        ) );

        // Get rank type lowest priority rank ID
        $lowest_rank_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s ORDER BY menu_order ASC LIMIT 1",
            $rank_type,
            'publish'
        ) );

        // Select users with lowest rank id or without this meta key
        $lowest =  $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT( DISTINCT user_id )
         FROM {$wpdb->usermeta}
         WHERE meta_key = %s
          AND meta_value = %s
          OR user_id NOT IN (
            SELECT DISTINCT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s
          )",
            "_gamipress_{$rank_type}_rank",
            $lowest_rank_id,
            "_gamipress_{$rank_type}_rank"
        ) );

        $data['dashboard_ranks_highest']    += gamipress_reports_get_user_meta_count( "_gamipress_{$rank_type}_rank", $highest_rank_id );
        $data['dashboard_ranks_lowest']     += absint( $lowest );
    }

    // Setup the ranks list table
    $table = new GamiPress_Report_List_Table( array(
        'id' => 'dashboard_ranks_list_table',
        'singular' => __( 'Rank', 'gamipress-reports' ),
        'plural' => __( 'Ranks', 'gamipress-reports' ),
    ) );

    $table->prepare_items();

    ob_start();
    $table->display();
    $data['dashboard_ranks_list_table'] = ob_get_clean();

    wp_send_json_success( $data );

}

/**
 * Custom query for report list table
 *
 * @since 1.0.0
 *
 * @param string $query
 * @param array $args
 *
 * @return string
 */
function gamipress_reports_dashboard_ranks_list_table_query( $query, $args ) {

    global $wpdb;

    // Initialize query vars
    $select = array( 'p.ID AS post_id' );
    $from = array( "{$wpdb->posts} AS p" );
    $join = array();
    $where = array( '1=1' );
    $order_by = array();
    $query_args = array();

    // Prepare table names
    $ct_table = ct_setup_table( 'gamipress_user_earnings' );

    $earnings_table = $ct_table->db->table_name;

    $ct_table = ct_setup_table( 'gamipress_logs' );

    $logs_table = $ct_table->db->table_name;
    $logs_meta_table = $ct_table->meta->db->table_name;

    // Active column
    $select[] = "IFNULL( ( SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = p.post_name AND post_status = %s ), 0 ) AS `active`";
    $query_args[] = 'publish';

    // Highest column
    $select[] = "IFNULL( (
            SELECT COUNT(*)
            FROM $wpdb->usermeta AS um
            WHERE um.meta_key = REPLACE('_gamipress_%rank_type%_rank', '%rank_type%', p.post_name)
            AND um.meta_value = (
                SELECT ID FROM {$wpdb->posts} WHERE post_type = p.post_name AND post_status = 'publish' ORDER BY menu_order DESC LIMIT 1
            )
        ), 0 ) AS `highest`";

    // Lowest column
    $select[] = "IFNULL( (
            SELECT COUNT( DISTINCT um.user_id )
            FROM $wpdb->usermeta AS um
            WHERE um.meta_key = REPLACE('_gamipress_%rank_type%_rank', '%rank_type%', p.post_name)
            AND um.meta_value = (
                SELECT ID FROM {$wpdb->posts} WHERE post_type = p.post_name AND post_status = 'publish' ORDER BY menu_order ASC LIMIT 1
            )
            OR um.user_id NOT IN (
            SELECT DISTINCT sum.user_id FROM {$wpdb->usermeta} AS sum WHERE sum.meta_key = REPLACE('_gamipress_%rank_type%_rank', '%rank_type%', p.post_name)
          )
        ), 0 ) AS `lowest`";

    // Where to limit just to rank type posts
    $where[] = 'p.post_type = %s';
    $query_args[] = 'rank-type';

    $order_by[] = "post_id";

    // Process query vars
    $select = ( ! empty( $select ) ? implode( ', ', $select ) : '' );
    $from = ( ! empty( $from ) ? implode( ', ', $from ) : '' );
    $join = ( ! empty( $join ) ? implode( ' ', $join ) : '' );
    $where = ( ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '' );
    $order_by = ( ! empty( $order_by ) ? 'ORDER BY ' . implode( ', ', $order_by ) . ' DESC' : '' );

    // Execute our query
    return $wpdb->prepare(
        "SELECT DISTINCT {$select}
         FROM {$from}
         {$join}
         {$where}
         {$order_by}",
        $query_args
    );

}
add_filter( "gamipress_reports_dashboard_ranks_list_table_query", 'gamipress_reports_dashboard_ranks_list_table_query', 10, 2 );

/**
 * Custom report list table columns
 *
 * @since 1.0.0
 *
 * @param array $columns
 *
 * @return array
 */
function gamipress_reports_dashboard_ranks_list_table_columns( $columns ) {

    $columns['post_id'] = __( 'Rank Type', 'gamipress-reports' );
    $columns['active'] = __( 'Active', 'gamipress-reports' );
    $columns['highest'] = __( 'In highest rank', 'gamipress-reports' );
    $columns['lowest'] = __( 'In lowest rank', 'gamipress-reports' );

    return $columns;
}
add_filter( "gamipress_reports_manage_dashboard_ranks_list_table_columns", 'gamipress_reports_dashboard_ranks_list_table_columns' );

/**
 * Custom report list table columns output
 *
 * @since 1.0.0
 *
 * @param string    $column_name
 * @param stdClass  $item
 */
function gamipress_reports_dashboard_ranks_list_table_custom_column( $column_name, $item ) {

    switch( $column_name ) {
        case 'post_id':

            $rank_types = gamipress_get_rank_types();

            $rank_type = get_post_field( 'post_name', $item->post_id );
            $post_title = get_post_field( 'post_title', $item->post_id );

            $can_edit_posts = current_user_can( 'edit_posts' );

            if( $can_edit_posts ) {
                printf(
                    '<a href="%s" aria-label="%s">%s</a>',
                    get_edit_post_link( $item->post_id ),
                    /* translators: %s: post title */
                    esc_attr( sprintf( __( '&#8220;%s&#8221; (Edit)' ), $post_title ) ),
                    $rank_types[$rank_type]['plural_name']
                );
            } else {
                echo $rank_types[$rank_type]['plural_name'];
            }
            break;
    }

}
add_action( "gamipress_reports_manage_dashboard_ranks_list_table_custom_column", 'gamipress_reports_dashboard_ranks_list_table_custom_column', 10, 2 );

/**
 * Custom report list table row actions
 *
 * @since 1.0.0
 *
 * @param array     $actions
 * @param stdClass  $item
 *
 * @return array
 */
function gamipress_reports_dashboard_ranks_list_table_row_actions( $actions, $item ) {

    $rank_type = get_post_field( 'post_name', $item->post_id );
    $post_title = get_post_field( 'post_title', $item->post_id );

    $can_edit_posts = current_user_can( 'edit_posts' );

    if( $can_edit_posts ) {
        $actions['edit'] = sprintf(
            '<a href="%s" aria-label="%s">%s</a>',
            get_edit_post_link( $item->post_id ),
            esc_attr( sprintf( __( '&#8220;%s&#8221; (Edit)' ), $post_title ) ),
            __( 'Edit' )
        );
    }

    $actions['view_reports'] = sprintf(
        '<a href="%s" aria-label="%s">%s</a>',
        admin_url( 'admin.php?page=gamipress_reports_ranks&tab=opt-tab-' . $rank_type ),
        esc_attr( __( 'View Reports', 'gamipress-reports' ) ),
        /*'<i class="dashicons dashicons-chart-area"></i> ' .*/ __( 'View Reports', 'gamipress-reports' )
    );

    return $actions;
}
add_action( "dashboard_ranks_list_table_row_actions", 'gamipress_reports_dashboard_ranks_list_table_row_actions', 10, 2 );