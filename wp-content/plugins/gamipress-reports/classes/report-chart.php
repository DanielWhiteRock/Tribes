<?php
/**
 * Report Chart class
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'GamiPress_Report_Chart' ) ) :

    class GamiPress_Report_Chart {

        public $chart_id;
        public $title;
        public $subtitle;
        public $prev_date;
        public $next_date;
        public $counters;
        public $_args;

        /**
         * Get things started
         *
         * @access public
         * @since  1.0
         *
         * @param array $args Optional. Arbitrary display and query arguments to pass through
         *                    the list table. Default empty array.
         */
        public function __construct( $args = array() ) {

            $args = wp_parse_args( $args, array(
                'id' => '',
                'type' => '',
                'date_range' => 'week',
                'date' => 0,
            ) );

            $this->chart_id = $args['id'];

            // Setup chart current date
            if( gettype( $args['date'] ) === 'string' ) {
                $args['date'] = strtotime( $args['date'] );
            }

            if( ! $args['date'] ) {
                $args['date'] = current_time( 'timestamp' );
            }

            $this->_args = $args;

        }

        public function get_chart_classes() {

            return array( 'gamipress-reports-chart-' . str_replace( '_', '-', $this->chart_id ) );

        }

        public function display() {

            $this->prepare_chart(); ?>

            <div class="gamipress-reports-chart <?php echo implode( ' ', $this->get_chart_classes() ); ?>" data-args="<?php echo str_replace( '"', "'", json_encode( $this->_args ) ); ?>">

                <input type="hidden" name="date" value="<?php echo date( 'Y-m-d', $this->_args['date'] ); ?>">

                <div class="gamipress-reports-chart-navigation">

                    <div class="gamipress-reports-chart-title">
                        <strong><?php echo $this->title; ?></strong>

                        <?php if( ! empty( $this->subtitle ) ) : ?>
                            <span><?php echo $this->subtitle; ?></span>
                        <?php endif; ?>

                    </div>

                    <div class="gamipress-reports-chart-date-navigation">
                        <span class="gamipress-reports-chart-date gamipress-reports-chart-prev-date" data-date="<?php echo $this->prev_date; ?>"><i class="dashicons dashicons-arrow-left-alt2"></i></span>
                        <span class="gamipress-reports-chart-date gamipress-reports-chart-next-date" data-date="<?php echo $this->next_date; ?>"><i class="dashicons dashicons-arrow-right-alt2"></i></span>
                    </div>

                </div>

                <div class="gamipress-reports-chart-navigation">

                    <div class="gamipress-reports-chart-range-navigation">
                        <span class="gamipress-reports-chart-range" data-range="year"><?php _e( 'Year', 'gamipress-reports' ); ?></span>
                        <span class="gamipress-reports-chart-range" data-range="month"><?php _e( 'Month', 'gamipress-reports' ); ?></span>
                        <span class="gamipress-reports-chart-range active" data-range="week"><?php _e( 'Week', 'gamipress-reports' ); ?></span>
                    </div>

                </div>

                <canvas id="<?php echo $this->chart_id; ?>" data-type="<?php echo $this->_args['type']; ?>"></canvas>

                <?php $this->display_counters(); ?>

            </div>

            <?php

        }

        public function display_counters() {

            if( count( $this->counters ) ) : ?>

                <div class="gamipress-reports-chart-counters">

                    <?php foreach( $this->counters as $counter => $data ) : ?>

                        <div class="gamipress-reports-chart-counter gamipress-reports-chart-counter-<?php echo $counter; ?>">
                            <span class="gamipress-reports-chart-counter-count"><?php echo $data['count']; ?></span>
                            <?php if( isset( $data['difference'] ) ) : ?>
                                <span class="gamipress-reports-chart-counter-difference"><?php echo $data['difference']; ?></span>
                            <?php endif; ?>
                            <span class="gamipress-reports-chart-counter-label"><?php echo $data['label']; ?></span>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif;

        }

        public function prepare_chart() {

            switch( $this->_args['date_range'] ) {

                case 'week':
                    $range = gamipress_reports_week_range( $this->_args['date'] );

                    // Jan 1 - Jan 7
                    $this->title = date( 'M d', strtotime( $range['start'] ) ) . ' - ' . date( 'M d', strtotime( $range['end'] ) );

                    $this->prev_date = date( 'Y-m-d', strtotime( '-7days', strtotime( $range['start'] ) ) );
                    $this->next_date = date( 'Y-m-d', strtotime( '+1day', strtotime( $range['end'] ) ) );
                    break;
                case 'month':
                    $range = gamipress_reports_month_range( $this->_args['date'] );

                    // January 2000
                    $this->title = date( 'F Y', strtotime( $range['start'] ) );

                    $this->prev_date = date( 'Y-m-01', strtotime( 'first day of last month', strtotime( $range['start'] ) ) );
                    $this->next_date = date( 'Y-m-01', strtotime( '+1day', strtotime( $range['end'] ) ) );
                    break;
                case 'year':
                    $range = gamipress_reports_year_range( $this->_args['date'] );

                    // 2000
                    $this->title = date( 'Y', strtotime( $range['start'] ) );

                    $this->prev_date = date( 'Y-01-01', strtotime( '-1year', strtotime( $range['start'] ) ) );
                    $this->next_date = date( 'Y-01-01', strtotime( '+1year', strtotime( $range['end'] ) ) );
                    break;

            }

            // Setup counters, filtered functions should return an array like: array( 'key' => 'label' )
            $counters = apply_filters( "gamipress_reports_{$this->chart_id}_counters", array(), $this->_args );

            $this->counters = array();

            foreach( $counters as $counter => $label ) {
                $this->counters[$counter] = array(
                    'label' => $label,
                    'count' => 0
                );
            }

        }

        public function get_stats() {

            // Retrieve the stats from this filter, filtered functions should return an array like:
            // array(
            //     'label' => '',
            //     'backgroundColor' => '',
            //     'borderColor' => '',
            //     'data' => array(
            //          array( 'date' => 'Y-m-d', 'count' => 0 )
            //     )
            // ),
            $stats = apply_filters( "gamipress_reports_{$this->chart_id}_stats", array(), $this->_args );

            // Setup the chart labels
            $labels = $this->get_period();

            // Setup a data pattern as array of elements like: array( 'Y-m-d' => 0 )
            $data_pattern = array();

            foreach ($labels as $label) {
                $data_pattern[$label] = 0;
            }

            $data_sets = array();

            // Setup stats data set
            foreach( $stats as $stat_key => $stat ) {

                $data = $data_pattern;
                $total_count = 0;

                // First parse each set of data, data should be an array like array( 'date' => 'Y-m-d', 'count' => 0 )
                foreach( $stat['data'] as $key => $value ) {

                    // For year range, data is stored on different days so we need to force to first day of month
                    if( $this->_args['date_range'] === 'year' ) {
                        $date = date( 'Y-m-01', strtotime( $value['date'] ) );
                    } else {
                        $date = $value['date'];
                    }

                    if( isset( $data[$date] ) ) {
                        $data[$date] = absint( $value['count'] );

                        $total_count += absint( $value['count'] );
                    }

                }

                $stat['data'] = array_values( $data );

                $data_sets[] = $stat;

                // If there are counters defined, then, update them
                if( count( $this->counters ) ) {
                    $this->counters[$stat_key]['count'] = $total_count;
                }

            }

            // Turn labels date format (Y-m-d) to a more friendly format
            $labels = $this->format_labels( $labels );

            return array(
                'labels' => $labels,
                'datasets' => $data_sets
            );
        }

        public function get_period( $date = 0 ) {

            if( $date === 0 ) {
                $date = $this->_args['date'];
            }

            $period = array();

            switch( $this->_args['date_range'] ) {
                case 'week':
                    $period = gamipress_reports_week_period( $date );
                    break;
                case 'month':
                    $period = gamipress_reports_month_period( $date );
                    break;
                case 'year':
                    $period = gamipress_reports_year_period( $date );
                    break;
            }

            return $period;

        }

        public function format_labels( $labels ) {

            foreach( $labels as $key => $label ) {

                switch( $this->_args['date_range'] ) {

                    case 'week':
                        // Jan 1
                        $label = date( 'M j', strtotime( $label ) );
                        break;
                    case 'month':
                        // For months, just show days 1, 5, 10, 15, 20, 25, {end day of month}
                        $month_allowed_days = array( '1', '5', '10', '15', '20', '25' );
                        $month_allowed_days[] = date( 'j', strtotime( 'last day of this month', strtotime( $label )  ) );

                        // 1
                        $current_label = date( 'j', strtotime( $label ) );

                        if( in_array( $current_label, $month_allowed_days ) ) {
                            $label = $current_label;
                        } else {
                            $label = '';
                        }
                        break;
                    case 'year':
                        // Jan
                        $label = date( 'M', strtotime( $label ) );
                        break;

                }

                $labels[$key] = $label;

            }

            return $labels;

        }

    }

endif;