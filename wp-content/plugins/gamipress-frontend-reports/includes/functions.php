<?php
/**
 * Functions
 *
 * @package     GamiPress\Frontend_Reports\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

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
function gamipress_frontend_reports_get_date_range( $range = '', $date = 0 ) {

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
function gamipress_frontend_reports_week_range( $date = 0 ) {

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
function gamipress_frontend_reports_week_period( $date = 0 ) {

    $range = gamipress_frontend_reports_week_range( $date );

    return gamipress_frontend_reports_get_range_period( $range, 'day' );

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
function gamipress_frontend_reports_month_range( $date = 0 ) {

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
function gamipress_frontend_reports_month_period( $date = 0 ) {

    $range = gamipress_frontend_reports_month_range( $date );

    return gamipress_frontend_reports_get_range_period( $range, 'day' );

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
function gamipress_frontend_reports_year_range( $date = 0 ) {

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
function gamipress_frontend_reports_year_period( $date = 0 ) {

    $range = gamipress_frontend_reports_year_range( $date );

    return gamipress_frontend_reports_get_range_period( $range, 'month' );

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
function gamipress_frontend_reports_get_range_period( $date_range, $interval = 'day' ) {

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