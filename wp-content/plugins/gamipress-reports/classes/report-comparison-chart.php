<?php
/**
 * Report Comparison Chart class
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'GamiPress_Report_Comparison_Chart' ) ) :

    class GamiPress_Report_Comparison_Chart extends GamiPress_Report_Chart {

        public $prev_counters;

        /**
         * Get things started
         *
         * @access public
         * @since  1.0
         *
         * @param array $args
         */
        public function __construct( $args = array() ) {

            parent::__construct( $args );

            $this->_args['is_comparison_chart'] = true;
        }

        public function get_chart_classes() {

            $parent_classes = parent::get_chart_classes();

            $parent_classes[] = 'gamipress-reports-comparison-chart';

            return $parent_classes;

        }

        public function prepare_chart() {

            parent::prepare_chart();

            $prev_date = strtotime( '-1' . $this->_args['date_range'], $this->_args['date'] );

            // Setup subtitle
            switch( $this->_args['date_range'] ) {

                case 'week':
                    $range = gamipress_reports_week_range( $prev_date );

                    // Jan 1 - Jan 7
                    $this->subtitle = date( 'M d', strtotime( $range['start'] ) ) . ' - ' . date( 'M d', strtotime( $range['end'] ) );
                    break;
                case 'month':
                    $range = gamipress_reports_month_range( $prev_date );

                    // January 2000
                    $this->subtitle = date( 'F Y', strtotime( $range['start'] ) );
                    break;
                case 'year':
                    $range = gamipress_reports_year_range( $prev_date );

                    // 2000
                    $this->subtitle = date( 'Y', strtotime( $range['start'] ) );
                    break;

            }

            $this->subtitle = 'vs ' . $this->subtitle;

            if( count( $this->counters ) ) {
                $this->prev_counters = $this->counters;

                foreach( $this->counters as $counter => $data ) {
                    $this->counters[$counter]['difference'] = 0;
                }
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

            // Get current range stats
            $current_stats = apply_filters( "gamipress_reports_{$this->chart_id}_stats", array(), $this->_args );

            // Setup the chart labels
            $labels = $this->get_period();

            // Setup a data pattern as array of elements like: array( 'Y-m-d' => 0 )
            $data_pattern = array();

            foreach ($labels as $label) {
                $data_pattern[$label] = 0;
            }

            $data_sets = array();

            // Turn current labels date format (Y-m-d) to a more friendly format
            $current_labels = $this->format_data_labels( $labels );

            // Setup current stats data set
            foreach( $current_stats as $stat_key => $stat ) {

                $data = $data_pattern;
                $total_count = 0;

                // Custom labels for this data set
                $stat['labels'] = $current_labels;

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

            // Setup previous range stats
            $prev_args = $this->_args;

            // Get the previous date (-1 week, month or year)
            $prev_args['date'] = strtotime( '-1' . $this->_args['date_range'], $this->_args['date'] );

            // Get previous range stats
            $prev_stats = apply_filters( "gamipress_reports_{$this->chart_id}_stats", array(), $prev_args );

            // Setup the previous chart labels
            $prev_labels = $this->get_period( $prev_args['date'] );

            // Turn previous labels date format (Y-m-d) to a more friendly format
            $prev_labels = $this->format_data_labels( $prev_labels );

            // Setup previous stats data set
            foreach( $prev_stats as $stat_key => $stat ) {

                $data = $data_pattern;
                $total_count = 0;

                // Set previous stats label
                switch( $this->_args['date_range'] ) {
                    case 'week':
                        $stat['label'] .= ' ' . __( '(Previous Week)', 'gamipress-reports' );
                        break;
                    case 'month':
                        // January
                        $stat['label'] .= ' (' . date( 'F', $prev_args['date'] ) . ')';
                        break;
                    case 'year':
                        // 2000
                        $stat['label'] .= ' (' . date( 'Y', $prev_args['date'] ) . ')';
                        break;
                }

                // Custom labels for this data set
                $stat['labels'] = $prev_labels;

                // Set opacity to previous stats
                $stat['borderColor'] = gamipress_reports_hex_to_rgb( $stat['borderColor'], 0.4 );

                // First parse each set of data, data should be an array like array( 'date' => 'Y-m-d', 'count' => 0 )
                foreach( $stat['data'] as $key => $value ) {

                    // For year range, data is stored on different days so we need to force to first day of month
                    if( $this->_args['date_range'] === 'year' ) {
                        $date = date( 'Y-m-01', strtotime( '+1' . $this->_args['date_range'], strtotime( $value['date'] ) ) );
                    } else {
                        // we need to turn previous dates to current dates
                        $date = date( 'Y-m-d', strtotime( '+1' . $this->_args['date_range'], strtotime( $value['date'] ) ) );
                    }

                    if( isset( $data[$date] ) ) {
                        $data[$date] = absint( $value['count'] );

                        $total_count += absint( $value['count'] );
                    }

                }

                $stat['data'] = array_values( $data );

                $data_sets[] = $stat;

                // If there are previous counters defined, then, update them
                if( count( $this->prev_counters ) ) {
                    $this->prev_counters[$stat_key]['count'] = $total_count;
                }

            }

            // Turn labels date format (Y-m-d) to a more friendly format
            $labels = $this->format_labels( $labels );

            // Calculate the counters difference
            foreach( $this->counters as $counter => $data ) {

                $prev_data = $this->prev_counters[$counter];

                $difference = $data['count'] - $prev_data['count'];

                $symbol = $data['count'] > $prev_data['count'] ? '+' : '';

                $percent = 0.00;

                if( $data['count'] !== 0 ) {

                    $percent = number_format( ( 1 - $prev_data['count'] / $data['count'] ) * 100, 2 );

                } else if( $data['count'] === 0 && $prev_data['count'] !== 0 ) {

                    $percent = -100.00;

                }

                $class = '';

                if( $data['count'] > $prev_data['count'] ) {
                    $class = 'gamipress-reports-chart-counter-difference-positive';
                } else if( $data['count'] < $prev_data['count'] ) {
                    $class = 'gamipress-reports-chart-counter-difference-negative';
                }


                $this->counters[$counter]['difference'] = '<span class="' . $class . '">' . $symbol . $difference . ' (' . $symbol . $percent . '%)' . '</span>';

            }

            return array(
                'labels' => $labels,
                'datasets' => $data_sets,
            );
        }

        public function format_data_labels( $labels ) {

            foreach( $labels as $key => $label ) {

                switch( $this->_args['date_range'] ) {

                    case 'week':
                        // Jan 1
                        $label = date( 'M j', strtotime( $label ) );
                        break;
                    case 'month':
                        // Jan 1
                        $label = date( 'M j', strtotime( $label ) );
                        break;
                    case 'year':
                        // January 2000
                        $label = date( 'F Y', strtotime( $label ) );
                        break;

                }

                $labels[$key] = $label;

            }

            return $labels;

        }

    }

endif;