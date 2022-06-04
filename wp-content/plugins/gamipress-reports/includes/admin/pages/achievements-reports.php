<?php
/**
 * Admin Achievements Reports Page
 *
 * @package     GamiPress\Reports\Admin\Achievements_Reports
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register achievements reports page.
 *
 * @since  1.0.0
 *
 * @return void
 */
function gamipress_reports_register_achievements_reports_page() {

    gamipress_register_reports_page( 'achievements', __( 'Achievements Reports', 'gamipress-reports' ) );

}
add_action( 'cmb2_admin_init', 'gamipress_reports_register_achievements_reports_page' );

/**
 * Register achievements reports sections
 *
 * @since  1.0.0
 *
 * @return array
 */
function gamipress_reports_register_achievements_reports_sections( $reports_sections ) {

    foreach( gamipress_get_achievement_types() as $achievement_type => $data ) {
        $reports_sections[$achievement_type] = array(
            'title' => $data['plural_name'],
        );
    }

    return $reports_sections;

}
add_action( 'gamipress_reports_achievements_sections', 'gamipress_reports_register_achievements_reports_sections' );

/**
 * Dynamically load hooks by achievements type
 *
 * @since 1.0.0
 */
function gamipress_reports_load_achievements_reports() {

    foreach( gamipress_get_achievement_types() as $achievement_type => $data ) {
        // Dynamic meta boxes
        add_filter( "gamipress_reports_achievements_{$achievement_type}_meta_boxes", 'gamipress_reports_achievements_meta_boxes' );

        // Dynamic user list table filters
        add_filter( "gamipress_reports_{$achievement_type}_user_list_table_query", 'gamipress_reports_achievements_user_list_table_query', 10, 2 );
        add_filter( "gamipress_reports_manage_{$achievement_type}_user_list_table_columns", 'gamipress_reports_achievements_user_list_table_columns' );
        add_action( "gamipress_reports_manage_{$achievement_type}_user_list_table_custom_column", 'gamipress_reports_achievements_user_list_table_custom_column', 10, 2 );

        // Dynamic list table filters
        add_filter( "gamipress_reports_{$achievement_type}_list_table_query", 'gamipress_reports_achievements_list_table_query', 10, 2 );
        add_filter( "gamipress_reports_manage_{$achievement_type}_list_table_columns", 'gamipress_reports_achievements_list_table_columns' );
        add_action( "gamipress_reports_manage_{$achievement_type}_list_table_custom_column", 'gamipress_reports_achievements_list_table_custom_column', 10, 2 );

        // Dynamic chart filters
        add_filter( "gamipress_reports_{$achievement_type}_earned_chart_stats", 'gamipress_reports_achievements_earned_chart_stats', 10, 2 );
        add_filter( "gamipress_reports_{$achievement_type}_earned_chart_counters", 'gamipress_reports_achievements_earned_chart_counters', 10, 2 );

        add_filter( "gamipress_reports_{$achievement_type}_awarded_chart_stats", 'gamipress_reports_achievements_awarded_chart_stats', 10, 2 );
        add_filter( "gamipress_reports_{$achievement_type}_awarded_chart_counters", 'gamipress_reports_achievements_awarded_chart_counters', 10, 2 );
    }

}
add_action( 'cmb2_admin_init', 'gamipress_reports_load_achievements_reports', 5 );

/**
 * Points type reports meta boxes
 *
 * @since 1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_reports_achievements_meta_boxes( $meta_boxes ) {

    $achievement_type = str_replace( 'gamipress_reports_achievements_', '', str_replace( '_meta_boxes', '', current_filter() ) );

    // Widgets
    $meta_boxes[$achievement_type . '-active'] = array(
        'classes' => 'gamipress-reports-col-4',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Active', 'gamipress-reports' ),
            'icon' => 'dashicons-thumbs-up',
            'color' => 'blue'
        ) ),
    );

    $meta_boxes[$achievement_type . '-earned'] = array(
        'classes' => 'gamipress-reports-col-4',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Earned', 'gamipress-reports' ),
            'icon' => 'dashicons-flag',
            'color' => 'green'
        ) ),
    );

    $meta_boxes[$achievement_type . '-awarded'] = array(
        'classes' => 'gamipress-reports-col-4',
        'content' => gamipress_reports_widget_meta_box( array(
            'label' => __( 'Awarded', 'gamipress-reports' ),
            'icon' => 'dashicons-awards',
            'color' => 'purple'
        ) ),
    );

    // Earned chart
    $earned_chart = new GamiPress_Report_Comparison_Chart( array(
        'id' => $achievement_type . '_earned_chart',
        'type' => 'line',
        'achievement_type' => $achievement_type,
    ) );

    ob_start();
    $earned_chart->display();
    $earned_chart_html = ob_get_clean();

    $meta_boxes[$achievement_type . '-earned-stats'] = array(
        'title' => __( 'Earned Stats', 'gamipress-reports' ),
        'classes' => 'gamipress-reports-col-6',
        'content' => $earned_chart_html,
    );

    // Awarded chart
    $awarded_chart = new GamiPress_Report_Comparison_Chart( array(
        'id' => $achievement_type . '_awarded_chart',
        'type' => 'line',
        'achievement_type' => $achievement_type,
    ) );

    ob_start();
    $awarded_chart->display();
    $awarded_chart_html = ob_get_clean();

    $meta_boxes[$achievement_type . '-awarded-stats'] = array(
        'classes' => 'gamipress-reports-col-6',
        'content' => $awarded_chart_html,
    );

    // User list table
    $meta_boxes[$achievement_type . '-user-list-table'] = array(
        'title' => __( 'Users Report', 'gamipress-reports' ),
        'classes' => 'gamipress-reports-col-6',
        'content' => '<div></div>',
    );

    // Achievements list table
    $meta_boxes[$achievement_type . '-list-table'] = array(
        'title' => __( 'Achievements Report', 'gamipress-reports' ),
        'classes' => 'gamipress-reports-col-6',
        'content' => '<div></div>',
    );

    return $meta_boxes;

}

/**
 * Initial report tab load through ajax
 *
 * @since 1.0.0
 */
function gamipress_reports_achievements_load_tab() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    global $wpdb;

    // Setup the achievement type and the achievement type object
    $achievement_type = $_REQUEST['achievement_type'];

    $achievement_types = gamipress_get_achievement_types();
    $achievement_type_object = $achievement_types[$achievement_type];

    $active = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s",
        $achievement_type,
        'publish'
    ) );

    $ct_table = ct_setup_table( 'gamipress_user_earnings' );

    // Earned
    $earned = $wpdb->get_results( $wpdb->prepare(
            "SELECT post_id FROM {$ct_table->db->table_name} WHERE post_type = %s",
            $achievement_type
    ) );

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

    // Setup the users list table
    $user_table = new GamiPress_Report_List_Table( array(
        'id' => $achievement_type . '_user_list_table',
        'singular' => $achievement_type_object['singular_name'],
        'plural' => $achievement_type_object['plural_name'],
        'achievement_type' => $achievement_type,
        'classes' => '',
        'items_per_page' => 5,
    ) );

    $user_table->prepare_items();

    ob_start();
    $user_table->display();
    $user_list_table = ob_get_clean();

    // Setup the achievements list table
    $table = new GamiPress_Report_List_Table( array(
        'id' => $achievement_type . '_list_table',
        'singular' => $achievement_type_object['singular_name'],
        'plural' => $achievement_type_object['plural_name'],
        'achievement_type' => $achievement_type,
        'classes' => '',
        'items_per_page' => 5,
    ) );

    $table->prepare_items();

    ob_start();
    $table->display();
    $list_table = ob_get_clean();

    // Earned chart
    $earned_chart = new GamiPress_Report_Comparison_Chart( array(
        'id' => $achievement_type . '_earned_chart',
        'type' => 'line',
        'achievement_type' => $achievement_type,
    ) );

    $earned_chart->prepare_chart();

    // Awarded chart
    $awarded_chart = new GamiPress_Report_Comparison_Chart( array(
        'id' => $achievement_type . '_awarded_chart',
        'type' => 'line',
        'achievement_type' => $achievement_type,
    ) );

    $awarded_chart->prepare_chart();

    wp_send_json_success( array(
        'active'            => $active,
        'earned'            => count( $earned ),
        'awarded'           => $awarded,
        'expended'          => 0,
        'user_list_table'   => $user_list_table,
        'list_table'        => $list_table,
        'earned_chart' => array(
            'stats' => $earned_chart->get_stats(),
            'counters' => $earned_chart->counters,
        ),
        'awarded_chart' => array(
            'stats' => $awarded_chart->get_stats(),
            'counters' => $awarded_chart->counters,
        ),
    ) );

}
add_action( 'wp_ajax_gamipress_reports_load_achievements_tab', 'gamipress_reports_achievements_load_tab' );

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
function gamipress_reports_achievements_user_list_table_query( $query, $args ) {

    global $wpdb;

    // Initialize query vars
    $select = array( 'u.ID AS user_id' );
    $from = array( "{$wpdb->users} AS u" );
    $join = array();
    $where = array( '1=1' );
    $order_by = array();
    $query_args = array();

    // Multisite check
    if( is_multisite() ) {
        $join[] = "LEFT JOIN {$wpdb->usermeta} AS umcap ON ( umcap.user_id = u.ID ) ";
        $where[] = "umcap.meta_key = '" . $wpdb->get_blog_prefix() . "capabilities' ";
    }

    $achievement_type = $args['achievement_type'];

    $ct_table = ct_setup_table( 'gamipress_user_earnings' );

    $select[] = "( SELECT COUNT(*) FROM {$ct_table->db->table_name} AS e WHERE e.user_id = u.ID AND e.post_type = %s ) AS `achievements`";
    $query_args[] = $achievement_type;

    $select[] = "( SELECT e.date FROM {$ct_table->db->table_name} AS e WHERE e.user_id = u.ID AND e.post_type = %s ORDER BY e.date DESC LIMIT 1 ) AS `last_earned`";
    $query_args[] = $achievement_type;

    $order_by[] = "CAST( `achievements` AS UNSIGNED )";

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

/**
 * Custom report list table columns
 *
 * @since 1.0.0
 *
 * @param array $columns
 *
 * @return array
 */
function gamipress_reports_achievements_user_list_table_columns( $columns ) {

    $columns['user_id'] = __( 'User', 'gamipress-reports' );
    $columns['achievements'] = __( 'Earned', 'gamipress-reports' );
    $columns['last_earned'] = __( 'Last Earned', 'gamipress-reports' );

    return $columns;
}

/**
 * Custom report list table columns output
 *
 * @since 1.0.0
 *
 * @param string    $column_name
 * @param stdClass  $item
 */
function gamipress_reports_achievements_user_list_table_custom_column( $column_name, $item ) {

    switch( $column_name ) {
        case 'user_id':

            $user = get_userdata( $item->user_id );

            $can_edit_users = current_user_can( 'edit_users' );

            if( $can_edit_users ) {
                printf(
                    '<a href="%s" aria-label="%s">%s (%s)</a>',
                    get_edit_user_link( $user->ID ),
                    /* translators: %s: post title */
                    esc_attr( sprintf( __( '&#8220;%s&#8221; (Edit)' ), $user->user_login ) ),
                    $user->display_name,
                    $user->user_login
                );
            } else {
                echo $user->display_name;
            }

            break;
        case 'last_earned':

            if( ! empty( $item->last_earned ) ) {
                echo '<abbr title="' . $item->last_earned . '">' . date( 'Y/m/d', strtotime( $item->last_earned ) ) . '</abbr>';
            }

            break;
    }

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
function gamipress_reports_achievements_list_table_query( $query, $args ) {

    global $wpdb;

    // Initialize query vars
    $select = array( 'p.ID AS achievement_id' );
    $from = array( "{$wpdb->posts} AS p" );
    $join = array();
    $where = array( '1=1' );
    $order_by = array();
    $query_args = array();

    $achievement_type = $args['achievement_type'];

    $where[] = 'p.post_type = %s';
    $query_args[] = $achievement_type;

    $where[] = 'p.post_status = %s';
    $query_args[] = 'publish';

    $ct_table = ct_setup_table( 'gamipress_user_earnings' );

    $select[] = "( SELECT COUNT(*) FROM {$ct_table->db->table_name} AS e WHERE e.post_id = p.ID ) AS `earners`";
    $select[] = "( SELECT e.user_id FROM {$ct_table->db->table_name} AS e WHERE e.post_id = p.ID ORDER BY e.date DESC LIMIT 1 ) AS `last_earner`";

    $order_by[] = "CAST( `earners` AS UNSIGNED )";

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

/**
 * Custom report list table columns
 *
 * @since 1.0.0
 *
 * @param array $columns
 *
 * @return array
 */
function gamipress_reports_achievements_list_table_columns( $columns ) {

    $columns['achievement_id'] = __( 'Achievement', 'gamipress-reports' );
    $columns['earners'] = __( 'Earners', 'gamipress-reports' );
    $columns['last_earner'] = __( 'Last Earner', 'gamipress-reports' );

    return $columns;
}

/**
 * Custom report list table columns output
 *
 * @since 1.0.0
 *
 * @param string    $column_name
 * @param stdClass  $item
 */
function gamipress_reports_achievements_list_table_custom_column( $column_name, $item ) {

    switch( $column_name ) {
        case 'achievement_id':

            $post_title = get_post_field( 'post_title', $item->achievement_id );

            $can_edit_posts = current_user_can( 'edit_posts' );

            if( $can_edit_posts ) {
                printf(
                    '<a href="%s" aria-label="%s">%s</a>',
                    get_edit_post_link( $item->achievement_id ),
                    /* translators: %s: post title */
                    esc_attr( sprintf( __( '&#8220;%s&#8221; (Edit)' ), $post_title ) ),
                    $post_title
                );
            } else {
                echo $post_title;
            }

            break;
        case 'last_earner':

            if( $item->last_earner ) {

                $user = get_userdata( $item->last_earner );

                $can_edit_users = current_user_can( 'edit_users' );

                if( $can_edit_users ) {
                    printf(
                        '<a href="%s" aria-label="%s">%s (%s)</a>',
                        get_edit_user_link( $user->ID ),
                        /* translators: %s: post title */
                        esc_attr( sprintf( __( '&#8220;%s&#8221; (Edit)' ), $user->user_login ) ),
                        $user->display_name,
                        $user->user_login
                    );
                } else {
                    echo $user->display_name;
                }

            }

            break;
    }

}

/**
 * Earned report chart stats
 *
 * @since 1.0.0
 *
 * @param array $stats
 * @param array $args
 *
 * @return array
 */
function gamipress_reports_achievements_earned_chart_stats( $stats, $args ) {

    $achievement_type = $args['achievement_type'];
    $date_range = isset( $args['date_range'] ) ? $args['date_range'] : 'week';
    $date = isset( $args['date'] ) ? $args['date'] : 0;

    $achievement_ids = get_posts( array(
        'post_type'       => $achievement_type,
        'post_status'     => 'any',
        'fields'          => 'ids',
        'posts_per_page'  => -1
    ) );

    // Earned points
    $earned = gamipress_reports_get_logs_report( array(
        'select' => array(
            'date' => array(
                'cast' => 'DATE'
            ),
            'count' => array(
                'field' => 'log_id',
                'function' => 'COUNT'
            )
        ),
        'where' => array(
            'type' => array(
                'value' => 'achievement_earn',
            ),
            '_gamipress_achievement_id' => array(
                'value' => $achievement_ids,
                'compare' => 'IN'
            )
        ),
        'date_range' => $date_range,
        'since' => $date,
        'group_by' => $date_range === 'year' ? 'month' : 'day'
    ) );

    $stats['earned'] = array(
        'label' => __( 'Earned', 'gamipress-reports' ),
        'backgroundColor' => 'transparent',
        'borderColor' => '#34b75f',
        'data' => $earned
    );

    return $stats;

}

/**
 * Awarded report chart counters
 *
 * @since 1.0.0
 *
 * @param array $counters
 * @param array $args
 *
 * @return array
 */
function gamipress_reports_achievements_earned_chart_counters( $counters, $args ) {

    $counters['earned'] = __( 'Earned', 'gamipress-reports' );

    return $counters;

}

/**
 * Awarded report chart stats
 *
 * @since 1.0.0
 *
 * @param array $stats
 * @param array $args
 *
 * @return array
 */
function gamipress_reports_achievements_awarded_chart_stats( $stats, $args ) {

    $achievement_type = $args['achievement_type'];
    $date_range = isset( $args['date_range'] ) ? $args['date_range'] : 'week';
    $date = isset( $args['date'] ) ? $args['date'] : 0;

    $achievement_ids = get_posts( array(
        'post_type'       => $achievement_type,
        'post_status'     => 'any',
        'fields'          => 'ids',
        'posts_per_page'  => -1
    ) );

    // Awarded points
    $awarded = gamipress_reports_get_logs_report( array(
        'select' => array(
            'date' => array(
                'cast' => 'DATE'
            ),
            'count' => array(
                'field' => 'log_id',
                'function' => 'COUNT',
            )
        ),
        'where' => array(
            'type' => array(
                'value' => 'achievement_award',
            ),
            '_gamipress_achievement_id' => array(
                'value' => $achievement_ids,
                'compare' => 'IN'
            )
        ),
        'date_range' => $date_range,
        'since' => $date,
        'group_by' => $date_range === 'year' ? 'month' : 'day'
    ) );

    $stats['awarded'] = array(
        'label' => __( 'Awarded', 'gamipress-reports' ),
        'backgroundColor' => 'transparent',
        'borderColor' => '#8461b5',
        'data' => $awarded
    );

    return $stats;

}

/**
 * Awarded report chart counters
 *
 * @since 1.0.0
 *
 * @param array $counters
 * @param array $args
 *
 * @return array
 */
function gamipress_reports_achievements_awarded_chart_counters( $counters, $args ) {

    $counters['awarded'] = __( 'Awarded', 'gamipress-reports' );

    return $counters;

}