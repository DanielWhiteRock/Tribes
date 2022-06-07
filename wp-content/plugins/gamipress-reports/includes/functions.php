<?php
/**
 * Functions
 *
 * @package     GamiPress\Reports\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Count all user metas of a given meta key with a specific meta value
 *
 * @since 1.0.0
 *
 * @param string $meta_key
 * @param mixed $meta_value
 *
 * @return integer
 */
function gamipress_reports_get_user_meta_count( $meta_key, $meta_value ) {

    global $wpdb;

    $count =  $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*)
         FROM $wpdb->usermeta
         WHERE meta_key = %s
          AND meta_value = %s",
        $meta_key,
        $meta_value
    ) );

    return absint( $count );

}

/**
 * Return the HTML output for a dashboard widget
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return string
 */
function gamipress_reports_widget_meta_box( $args = array() ) {

    $args = wp_parse_args( $args, array(
        'data' => '<span class="spinner is-active"></span>',
        'label' => '',
        'icon' => '',
        'color' => '',
    ) );

    if ( strpos( $args['icon'], 'dashicons-' ) !== false ) {
        $args['icon'] = 'dashicons ' . $args['icon'];
    }

    if( ! empty( $args['color'] ) ) {
        $args['icon'] .= ' ' . $args['color'];
    }


    ob_start(); ?>

    <div class="gamipress-reports-widget <?php echo $args['color']; ?>">
        <strong><?php echo $args['data']; ?></strong>
        <span><?php echo $args['label']; ?></span>
        <?php if( ! empty( $args['icon'] ) ) : ?><i class="<?php echo $args['icon']; ?>"></i><?php endif; ?>
    </div>

    <?php $widget = ob_get_clean();

    return $widget;

}

/**
 * Helper function for advanced querying on logs
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return mixed
 */
function gamipress_reports_get_logs_report( $args = array() ) {

    global $wpdb;

    $default_args = array(
        'select'              => array(),
        'where'               => array(),
        'where_meta'          => array(),
        'group_by'            => '',
        'order_by'            => '',
        'limit'               => '',
        'date_range'          => false,
        'since'               => 0,
    );

    $args = wp_parse_args( $args, $default_args );

    // Setup the logs table
    $ct_table = ct_setup_table( 'gamipress_logs' );

    // Log table fields
    $log_fields = array(
        'log_id',
        'title',
        'description',
        'type',
        'access',
        'user_id',
        'date'
    );

    // Initialize query vars
    $select = array();
    $from = array( "{$ct_table->db->table_name} AS l" );
    $join = array();
    $where = array( '1=1' );
    $order_by = array();
    $group_by = array();
    $query_args = array();

    // SELECT
    foreach( $args['select'] as $key => $value ) {

        if( isset( $value['field'] ) ) {
            $field = $value['field'];
        } else {
            $field = $key;
        }

        if( in_array( $field, $log_fields ) ) {
            $get_key = "l.{$field}";
        } else {

            $index = count( $join );

            $get_key = "lm{$index}.meta_value";
            $join[] = "INNER JOIN {$ct_table->meta->db->table_name} AS lm{$index} ON ( lm{$index}.log_id = l.log_id AND lm{$index}.meta_key = %s )";
            $query_args[] = $field;
        }

        if ( $value['function'] ) {
            $get = "{$value['function']}({$get_key})";
        } else if ( $value['cast'] ) {
            $get = "CAST({$get_key} AS {$value['cast']})";
        } else {
            $get = "{$get_key}";
        }

        $select[] = "{$get} as `{$key}`";

    }

    // WHERE
    foreach( $args['where'] as $key => $value ) {

        $compare = isset( $value['compare'] ) ? $value['compare'] : '=';

        if( isset( $value['field'] ) ) {
            $field = $value['field'];
        } else {
            $field = $key;
        }

        if( in_array( $field, $log_fields ) ) {

            if( is_array( $value['value'] ) ) {
                $where[] = "l.{$field} {$compare} ('" . implode( "', '", $value['value'] ) . "')";
            } else {
                $where[] = "l.{$field} {$compare} '{$value['value']}'";
            }

        } else {

            $index = count( $join );

            $join[] = "LEFT JOIN {$ct_table->meta->db->table_name} AS lm{$index} ON ( lm{$index}.log_id = l.log_id )";

            if( is_array( $value['value'] ) ) {
                $where[] = "lm{$index}.meta_key = %s AND lm{$index}.meta_value {$compare} ('" . implode( "', '", $value['value'] ) . "')";
            } else {
                $where[] = "lm{$index}.meta_key = %s AND lm{$index}.meta_value {$compare} '{$value['value']}'";
            }

            $query_args[] = $field;

        }

    }

    // Date range utility
    if( $args['date_range'] ) {
        $range = gamipress_reports_get_date_range( $args['date_range'], $args['since'] );

        $where[] = "l.date >= %s AND l.date <= %s";

        $query_args[] = $range['start'];
        $query_args[] = $range['end'];
    }

    if( is_array( $args['group_by'] ) ) {
        foreach( $args['group_by'] as $key => $value ) {
            switch( $value ) {
                case 'day':
                    // Group by day
                    $group_by[] = 'YEAR(l.date), MONTH(l.date), DAY(l.date)';
                    break;
                case 'month':
                    // Group by month
                    $group_by[] = 'YEAR(l.date), MONTH(l.date)';
                    break;
                default:
                    $group_by[$key] = $value;
                    break;
            }
        }
    } else {

        // Group by utility
        switch( $args['group_by'] ) {
            case 'day':
                // Group by day
                $group_by[] = 'YEAR(l.date), MONTH(l.date), DAY(l.date)';
                break;
            case 'month':
                // Group by month
                $group_by[] = 'YEAR(l.date), MONTH(l.date)';
                break;
        }

    }

    // Process query vars
    $select = ( ! empty( $select ) ? implode( ', ', $select ) : '' );
    $from = ( ! empty( $from ) ? implode( ', ', $from ) : '' );
    $join = ( ! empty( $join ) ? implode( ' ', $join ) : '' );
    $where = ( ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '' );
    $order_by = ( ! empty( $order_by ) ? 'ORDER BY ' . implode( ', ', $order_by ) . ' DESC' : '' );
    $group_by = ( ! empty( $group_by ) ? 'GROUP BY ' . implode( ', ', $group_by ) : '' );

    // Execute the query
    return $wpdb->get_results( $wpdb->prepare(
        "SELECT {$select}
         FROM {$from}
         {$join}
         {$where}
         {$order_by}
         {$group_by}",
        $query_args
    ), ARRAY_A );

}

/**
 * Helper function for advanced querying on user earnings
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return mixed
 */
function gamipress_reports_get_user_earnings_report( $args = array() ) {

    global $wpdb;

    $default_args = array(
        'select'              => array(),
        'where'               => array(),
        'where_meta'          => array(),
        'group_by'            => '',
        'order_by'            => '',
        'limit'               => '',
        'date_range'          => false,
        'since'               => 0,
    );

    $args = wp_parse_args( $args, $default_args );

    // Setup the logs table
    $ct_table = ct_setup_table( 'gamipress_user_earnings' );

    // Log table fields
    $log_fields = array(
        'user_earning_id',
        'user_id',
        'post_id',
        'post_type',
        'points',
        'points_type',
        'date'
    );

    // Initialize query vars
    $select = array();
    $from = array( "{$ct_table->db->table_name} AS e" );
    $join = array();
    $where = array( '1=1' );
    $order_by = array();
    $group_by = array();
    $query_args = array();

    // SELECT
    foreach( $args['select'] as $key => $value ) {

        if( isset( $value['field'] ) ) {
            $field = $value['field'];
        } else {
            $field = $key;
        }

        if( in_array( $field, $log_fields ) ) {
            $get_key = "e.{$field}";
        } else {

            $index = count( $join );

            $get_key = "em{$index}.meta_value";
            $join[] = "INNER JOIN {$ct_table->meta->db->table_name} AS em{$index} ON ( em{$index}.user_earning_id = e.user_earning_id AND em{$index}.meta_key = %s )";
            $query_args[] = $field;
        }

        if ( $value['function'] ) {
            $get = "{$value['function']}({$get_key})";
        } else if ( $value['cast'] ) {
            $get = "CAST({$get_key} AS {$value['cast']})";
        } else {
            $get = "{$get_key}";
        }

        $select[] = "{$get} as `{$key}`";

    }

    // WHERE
    foreach( $args['where'] as $key => $value ) {

        $compare = isset( $value['compare'] ) ? $value['compare'] : '=';

        if( isset( $value['field'] ) ) {
            $field = $value['field'];
        } else {
            $field = $key;
        }

        if( in_array( $field, $log_fields ) ) {

            if( is_array( $value['value'] ) ) {
                $where[] = "e.{$field} {$compare} ('" . implode( "', '", $value['value'] ) . "')";
            } else {
                $where[] = "e.{$field} {$compare} '{$value['value']}'";
            }

        } else {

            $index = count( $join );

            $join[] = "LEFT JOIN {$ct_table->meta->db->table_name} AS em{$index} ON ( em{$index}.user_earning_id = e.user_earning_id )";

            if( is_array( $value['value'] ) ) {
                $where[] = "em{$index}.meta_key = %s AND em{$index}.meta_value {$compare} ('" . implode( "', '", $value['value'] ) . "')";
            } else {
                $where[] = "em{$index}.meta_key = %s AND em{$index}.meta_value {$compare} '{$value['value']}'";
            }

            $query_args[] = $field;

        }

    }

    // Date range utility
    if( $args['date_range'] ) {
        $range = gamipress_reports_get_date_range( $args['date_range'], $args['since'] );

        $where[] = "e.date >= %s AND e.date <= %s";

        $query_args[] = $range['start'];
        $query_args[] = $range['end'];
    }

    if( is_array( $args['group_by'] ) ) {
        foreach( $args['group_by'] as $key => $value ) {



        }
    } else {

        // Group by utility
        switch( $args['group_by'] ) {
            case 'day':
                // Group by day
                $group_by[] = 'YEAR(e.date), MONTH(e.date), DAY(e.date)';
                break;
            case 'month':
                // Group by month
                $group_by[] = 'YEAR(e.date), MONTH(e.date)';
                break;
        }

    }

    // Process query vars
    $select = ( ! empty( $select ) ? implode( ', ', $select ) : '' );
    $from = ( ! empty( $from ) ? implode( ', ', $from ) : '' );
    $join = ( ! empty( $join ) ? implode( ' ', $join ) : '' );
    $where = ( ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '' );
    $order_by = ( ! empty( $order_by ) ? 'ORDER BY ' . implode( ', ', $order_by ) . ' DESC' : '' );
    $group_by = ( ! empty( $group_by ) ? 'GROUP BY ' . implode( ', ', $group_by ) : '' );

    // Execute the query
    return $wpdb->get_results( $wpdb->prepare(
        "SELECT {$select}
         FROM {$from}
         {$join}
         {$where}
         {$order_by}
         {$group_by}",
        $query_args
    ), ARRAY_A );

}

/**
 * Helper function to get a range date based on the given date
 *
 * @since 1.0.0
 *
 * @param string            $range (week|month|year)
 * @param integer|string    $date
 *
 * @return array
 */
function gamipress_reports_get_date_range( $range = '', $date = 0 ) {

    if( gettype( $date ) === 'string' ) {
        $date = strtotime( $date );
    }

    if( ! $date ) {
        $date = current_time( 'timestamp' );
    }

    $start_date = 0;
    $end_date = 0;

    switch( $range ) {
        case 'week':

            // Weekly range
            $start_date    = strtotime( 'last monday', $date );
            $end_date      = strtotime( 'midnight', strtotime( 'next sunday', $date ) );

            break;
        case 'month':

            // Monthly range
            $start_date    = strtotime( date( 'Y-m-01', $date ) );
            $end_date      = strtotime( 'midnight', strtotime( 'last day of this month', $date ) );

            break;
        case 'year':

            // Yearly range
            $start_date    = strtotime( date( 'Y-01-01', $date ) );
            $end_date      = strtotime( date( 'Y-12-31', $date ) );

            break;
    }

    return array(
        'start'    => date( 'Y-m-d H:i:s', $start_date ),
        'end'      => date( 'Y-m-d H:i:s', $end_date )
    );

}

/**
 * Helper function to get a week range based on the given date
 *
 * @since 1.0.0
 *
 * @param integer|string $date
 *
 * @return array            Array with start and end dates of the range
 */
function gamipress_reports_week_range( $date = 0 ) {

    date_default_timezone_set( date_default_timezone_get() );

    if( gettype( $date ) === 'string' ) {
        $date = strtotime( $date );
    }

    if( ! $date ) {
        $date = current_time( 'timestamp' );
    }

    return array(
        'start' => date( 'N', $date ) == 1 ? date( 'Y-m-d', $date ) : date( 'Y-m-d', strtotime( 'last monday', $date ) ),
        'end' => date( 'N', $date ) == 7 ? date( 'Y-m-d', $date ) : date( 'Y-m-d', strtotime( 'next sunday', $date ) )
    );

}

/**
 * Helper function to get a week range period (period interval per day)
 *
 * @since 1.0.0
 *
 * @param integer $date
 *
 * @return array                    Array with the full dates range in Y-m-d format
 */
function gamipress_reports_week_period( $date = 0 ) {

    $range = gamipress_reports_week_range( $date );

    return gamipress_reports_get_range_period( $range, 'day' );

}

/**
 * Helper function to get a month range based on the given date
 *
 * @since 1.0.0
 *
 * @param integer|string $date
 *
 * @return array            Array with start and end dates of the range
 */
function gamipress_reports_month_range( $date = 0 ) {

    date_default_timezone_set( date_default_timezone_get() );

    if( gettype( $date ) === 'string' ) {
        $date = strtotime( $date );
    }

    if( ! $date ) {
        $date = current_time( 'timestamp' );
    }

    return array(
        "start" => date( 'Y-m-01', $date ),
        "end" => date( 'Y-m-d', strtotime( 'last day of this month', $date ) )
    );

}

/**
 * Helper function to get a month range period (period interval per day)
 *
 * @since 1.0.0
 *
 * @param integer $date
 *
 * @return array                    Array with the full dates range in Y-m-d format
 */
function gamipress_reports_month_period( $date = 0 ) {

    $range = gamipress_reports_month_range( $date );

    return gamipress_reports_get_range_period( $range, 'day' );

}

/**
 * Helper function to get a year range based on the given date
 *
 * @since 1.0.0
 *
 * @param integer|string $date
 *
 * @return array            Array with start and end dates of the range
 */
function gamipress_reports_year_range( $date = 0 ) {

    date_default_timezone_set( date_default_timezone_get() );

    if( gettype( $date ) === 'string' ) {
        $date = strtotime( $date );
    }

    if( ! $date ) {
        $date = current_time( 'timestamp' );
    }

    return array(
        "start" => date( 'Y-01-01', $date ),
        "end" => date( 'Y-12-01', $date )
    );

}

/**
 * Helper function to get a year range period (period interval per months)
 *
 * @since 1.0.0
 *
 * @param integer $date
 *
 * @return array                    Array with the full dates range in Y-m-d format
 */
function gamipress_reports_year_period( $date = 0 ) {

    $range = gamipress_reports_year_range( $date );

    return gamipress_reports_get_range_period( $range, 'month' );

}

/**
 * Helper function to get a date range period
 *
 * @since 1.0.0
 *
 * @param array     $date_range
 * @param string    $interval   (day|month)
 *
 * @return array                    Array with the full dates range in Y-m-d format
 */
function gamipress_reports_get_range_period( $date_range, $interval = 'day' ) {

    try{
        $period_obj = new DatePeriod(
            new DateTime( $date_range['start'] ),
            new DateInterval( ( $interval === 'day' ? 'P1D' : 'P1M' ) ),
            new DateTime( $date_range['end'] )
        );
    } catch(Exception $e) {
        // If there is any exception return the date range
        return $date_range;
    }

    $period = array();

    foreach ($period_obj as $key => $value) {
        $period[] = $value->format('Y-m-d');
    }

    $period[] = $date_range['end'];

    return $period;

}

/**
 * Helper function to turn a hexadecimal color into a rgba CSS rule
 *
 * @since 1.0.0
 *
 * @param string    $hex
 * @param integer   $opacity
 *
 * @return string
 */
function gamipress_reports_hex_to_rgb( $hex, $opacity = 1 ) {

    list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");

    return "rgba( $r, $g, $b, $opacity )";

}