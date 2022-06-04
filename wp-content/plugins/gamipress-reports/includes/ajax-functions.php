<?php
/**
 * Ajax Functions
 *
 * @package     GamiPress\Reports\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function gamipress_reports_list_table_request() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    @define( 'GAMIPRESS_REPORTS_AJAX_LIST_TABLE', true );

    if( is_array( $_GET['args'] ) ) {
        $args = $_GET['args'];
    } else {
        $args = json_decode( str_replace( "\\'", "\"", $_GET['args'] ), true );
    }

    $args = wp_parse_args( $args, array(
        'paged' => 1,
    ) );

    if( isset( $_GET['paged'] ) ) {
        $args['paged'] = $_GET['paged'];
    }

    $table = new GamiPress_Report_List_Table( $args );

    $table->prepare_items();

    ob_start();
    $table->display();
    $list_table = ob_get_clean();

    wp_send_json_success( $list_table );

}
add_action( 'wp_ajax_gamipress_reports_list_table_request', 'gamipress_reports_list_table_request' );

function gamipress_reports_chart_request() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    @define( 'GAMIPRESS_REPORTS_AJAX_CHART', true );

    if( is_array( $_GET['args'] ) ) {
        $args = $_GET['args'];
    } else {
        $args = json_decode( str_replace( "\\'", "\"", $_GET['args'] ), true );
    }

    $args = wp_parse_args( $args, array(
        'date_range' => 'week',
        'date' => 0,
    ) );

    if( isset( $_GET['date_range'] ) ) {
        $args['date_range'] = $_GET['date_range'];
    }

    if( isset( $_GET['date'] ) ) {
        $args['date'] = $_GET['date'];
    }

    if( isset( $args['is_comparison_chart'] ) ) {
        $chart = new GamiPress_Report_Comparison_Chart( $args );
    } else {
        $chart = new GamiPress_Report_Chart( $args );
    }

    $chart->prepare_chart();

    $stats = $chart->get_stats();

    wp_send_json_success( array(
        'stats' => $stats,
        'title' => $chart->title,
        'subtitle' => $chart->subtitle,
        'prev_date' => $chart->prev_date,
        'next_date' => $chart->next_date,
        'counters' => $chart->counters,
    ) );

}
add_action( 'wp_ajax_gamipress_reports_chart_request', 'gamipress_reports_chart_request' );