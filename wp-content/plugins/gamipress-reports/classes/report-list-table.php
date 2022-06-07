<?php
/**
 * Report List Table class
 *
 * Based on WP_Posts_List_Table class
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'GamiPress_Report_List_Table' ) ) :

    class GamiPress_Report_List_Table extends WP_List_Table {

        public $table_id;

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

            // WP_List_Table dependencies
            if( ! function_exists( 'convert_to_screen' ) ) {
                require_once ABSPATH . 'wp-admin/includes/template.php';
            }

            if( ! function_exists( 'get_column_headers' ) ) {
                require_once ABSPATH . 'wp-admin/includes/screen.php';
            }

            $args = wp_parse_args( $args, array(
                'id' => '',
                'singular' => '',
                'plural' => '',
                'paged' => 1,
                'items_per_page' => 10,
            ) );

            if( absint( $args['paged'] ) === 0 ) {
                $args['paged'] = 1;
            }

            $this->table_id = $args['id'];

            parent::__construct( $args );
        }

        /**
         * Retrieve view counts
         *
         * @since 1.0.0
         *
         * @return void
         */
        public function get_views_counts() {

            $search = isset( $_GET['s'] ) ? $_GET['s'] : '';


        }

        /**
         * Show the search field
         *
         * @since 1.0.0
         *
         * @param string $text Label for the search box
         * @param string $input_id ID of the search box
         *
         * @return void
         */
        public function search_box( $text, $input_id ) {
            if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
                return;

            $input_id = $input_id . '-search-input';

            if ( ! empty( $_REQUEST['orderby'] ) )
                echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
            if ( ! empty( $_REQUEST['order'] ) )
                echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
            ?>
            <p class="search-box">
                <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
                <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
                <?php submit_button( $text, 'button', false, false, array( 'ID' => 'search-submit' ) ); ?>
            </p>
            <?php
        }

        /**
         * Retrieve the view types
         *
         * @access public
         * @since 1.0.0
         *
         * @return array $views All the views available
         */
        public function get_views() {
            return array();
        }

        /**
         *
         * @return array
         */
        protected function get_bulk_actions() {
            return array();
        }

        /**
         *
         * @return array
         */
        protected function get_table_classes() {
            return array( 'widefat', 'fixed', 'striped', 'gamipress-reports-' . str_replace( '_', '-', $this->table_id ) );
        }

        /**
         * Retrieve the table columns
         *
         * @access public
         * @since 1.0
         * @return array $columns Array of all the list table columns
         */
        public function get_columns() {
            $columns = array();
            $bulk_actions = $this->get_bulk_actions();

            if( ! empty( $bulk_actions ) ) {
                $columns['cb'] = '<input type="checkbox" />';
            }

            /**
             * Filters the columns displayed in the Posts list table.
             *
             * @since 1.0.0
             *
             * @param array  $posts_columns An array of column names.
             * @param string $post_type     The post type slug.
             */
            return apply_filters( "gamipress_reports_manage_{$this->table_id}_columns", $columns );
        }

        /**
         * Retrieve the table's sortable columns
         *
         * @access public
         * @since 1.0
         * @return array Array of all the sortable columns
         */
        public function get_sortable_columns() {
            $sortable_columns = array();

            /**
             * Filters the columns displayed in the Posts list table.
             *
             * @since 1.5.0
             *
             * @param array  $posts_columns An array of column names.
             * @param string $post_type     The post type slug.
             */
            return apply_filters( "gamipress_reports_manage_{$this->table_id}_sortable_columns", $sortable_columns );
        }

        /**
         * This function renders most of the columns in the list table.
         *
         * @access public
         * @since 1.0
         *
         * @param stdClass  $item           The current object.
         * @param string    $column_name    The name of the column
         * @return string                   The column value.
         */
        public function column_default( $item, $column_name ) {

            $value = isset( $item->$column_name ) ? $item->$column_name : '';

            /**
             * Fires for each custom column of a specific post type in the Posts list table.
             *
             * The dynamic portion of the hook name, `$post->post_type`, refers to the post type.
             *
             * @since 3.1.0
             *
             * @param string    $column_name   The name of the column to display.
             * @param stdClass  $item          The current item.
             */
            ob_start();
            do_action( "gamipress_reports_manage_{$this->table_id}_custom_column", $column_name, $item, $this->_args );
            $custom_output = ob_get_clean();

            if( ! empty( $custom_output ) ) {
                return $custom_output;
            }

            $bulk_actions = $this->get_bulk_actions();

            $first_column_index = ( ! empty( $bulk_actions ) ) ? 1 : 0;

            $columns = $this->get_columns();
            $columns_keys = array_keys( $columns );

            if( $column_name === $columns_keys[$first_column_index] ) {

                // Small screens toggle
                $value .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details' ) . '</span></button>';

            }

            return $value;
        }

        /**
         * Generates and displays row action links.
         *
         * @since 4.3.0
         * @access protected
         *
         * @param object $item        The item being acted upon.
         * @param string $column_name Current column name.
         * @param string $primary     Primary column name.
         *
         * @return string Row actions output for posts.
         */
        protected function handle_row_actions( $item, $column_name, $primary ) {
            if ( $primary !== $column_name ) {
                return '';
            }

            $actions = array();

            /**
             * Filters the array of row action links on the Posts list table.
             *
             * The filter is evaluated only for non-hierarchical post types.
             *
             * @since 2.8.0
             *
             * @param array $actions An array of row action links. Defaults are
             *                         'Edit', 'Quick Edit', 'Restore, 'Trash',
             *                         'Delete Permanently', 'Preview', and 'View'.
             * @param WP_Post $post The post object.
             */
            $actions = apply_filters( "{$this->table_id}_row_actions", $actions, $item );

            return $this->row_actions( $actions );
        }

        /**
         * Handles the checkbox column output.
         *
         * @since 1.0.0
         *
         * @param WP_Post $item The current WP_Post object.
         */
        public function column_cb( $item ) {
            return;
        }

        /**
         * Renders the message to be displayed when there are no results.
         *
         * @since  1.0.0
         */
        function no_items() {
            echo __( 'No results found', 'gamipress-reports' );
        }

        public function prepare_items() {

            global $wpdb;

            $query = apply_filters( "gamipress_reports_{$this->table_id}_query", '', $this->_args );

            $query_parts = explode( 'FROM ', $query );

            $count_query = 'SELECT COUNT(*) FROM ' . end( $query_parts );

            $count_query = explode( 'ORDER BY ', $count_query )[0];

            $count_query = apply_filters( "gamipress_reports_{$this->table_id}_count_query", $count_query, $this->_args );

            // Add offset and limit to the main query
            $offset = absint( ( $this->_args['paged'] - 1 ) * $this->_args['items_per_page'] );
            $limit = $this->_args['items_per_page'];

            $query .= " LIMIT {$offset}, {$limit}";

            $this->items = $wpdb->get_results( $query );

            $total_items = absint( $wpdb->get_var( $count_query ) );

            $per_page = $this->_args['items_per_page'];

            $this->set_pagination_args( array(
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil( $total_items / $per_page )
            ) );
        }

        /**
         * Display the table
         *
         * @since 3.1.0
         */
        public function display() {
            $singular = $this->_args['singular']; ?>

            <div class="gamipress-reports-list-table" data-args="<?php echo str_replace( '"', "'", json_encode( $this->_args ) ); ?>">

                <?php $this->display_tablenav( 'top' ); ?>

                <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
                    <thead>
                    <tr>
                        <?php $this->print_column_headers(); ?>
                    </tr>
                    </thead>

                    <tbody id="the-list"<?php
                    if ( $singular ) {
                        echo " data-wp-lists='list:$singular'";
                    } ?>>
                    <?php $this->display_rows_or_placeholder(); ?>
                    </tbody>

                    <tfoot>
                    <tr>
                        <?php $this->print_column_headers( false ); ?>
                    </tr>
                    </tfoot>

                </table>

                <?php $this->display_tablenav( 'bottom' ); ?>

            </div>

            <?php
        }

        /**
         * Get a list of all, hidden and sortable columns, with filter applied
         *
         * @since 3.1.0
         *
         * @return array
         */
        protected function get_column_info() {
            // $_column_headers is already set / cached
            if ( isset( $this->_column_headers ) && is_array( $this->_column_headers ) ) {
                // Back-compat for list tables that have been manually setting $_column_headers for horse reasons.
                // In 4.3, we added a fourth argument for primary column.
                $column_headers = array( array(), array(), array(), $this->get_primary_column_name() );
                foreach ( $this->_column_headers as $key => $value ) {
                    $column_headers[ $key ] = $value;
                }

                return $column_headers;
            }

            //$columns = get_column_headers( $this->screen );
            $columns = $this->get_columns();
            $hidden = get_hidden_columns( $this->screen );

            $sortable_columns = $this->get_sortable_columns();
            /**
             * Filters the list table sortable columns for a specific screen.
             *
             * The dynamic portion of the hook name, `$this->screen->id`, refers
             * to the ID of the current screen, usually a string.
             *
             * @since 3.5.0
             *
             * @param array $sortable_columns An array of sortable columns.
             */
            $_sortable = apply_filters( "manage_{$this->screen->id}_sortable_columns", $sortable_columns );

            $sortable = array();
            foreach ( $_sortable as $id => $data ) {
                if ( empty( $data ) )
                    continue;

                $data = (array) $data;
                if ( !isset( $data[1] ) )
                    $data[1] = false;

                $sortable[$id] = $data;
            }

            $primary = $this->get_primary_column_name();
            $this->_column_headers = array( $columns, $hidden, $sortable, $primary );

            return $this->_column_headers;
        }

        /**
         * Display the pagination.
         *
         * @since 3.1.0
         *
         * @param string $which
         */
        protected function pagination( $which ) {
            if ( empty( $this->_pagination_args ) ) {
                return;
            }

            $total_items = $this->_pagination_args['total_items'];
            $total_pages = $this->_pagination_args['total_pages'];
            $infinite_scroll = false;
            if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
                $infinite_scroll = $this->_pagination_args['infinite_scroll'];
            }

            //if ( 'top' === $which && $total_pages > 1 ) {
                //$this->screen->render_screen_reader_content( 'heading_pagination' );
            //}

            $output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

            $current = $this->get_pagenum();
            $removable_query_args = wp_removable_query_args();

            $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

            $current_url = remove_query_arg( $removable_query_args, $current_url );

            $page_links = array();

            $total_pages_before = '<span class="paging-input">';
            $total_pages_after  = '</span></span>';

            $disable_first = $disable_last = $disable_prev = $disable_next = false;

            if ( $current == 1 ) {
                $disable_first = true;
                $disable_prev = true;
            }
            if ( $current == 2 ) {
                $disable_first = true;
            }
            if ( $current == $total_pages ) {
                $disable_last = true;
                $disable_next = true;
            }
            if ( $current == $total_pages - 1 ) {
                $disable_last = true;
            }

            if ( $disable_first ) {
                $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>';
            } else {
                $page_links[] = sprintf( "<a class='first-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                    esc_url( remove_query_arg( 'paged', $current_url ) ),
                    __( 'First page' ),
                    '&laquo;'
                );
            }

            if ( $disable_prev ) {
                $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>';
            } else {
                $page_links[] = sprintf( "<a class='prev-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                    esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
                    __( 'Previous page' ),
                    '&lsaquo;'
                );
            }

            if ( 'bottom' === $which ) {
                $html_current_page  = $current;
                $total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
            } else {
                $html_current_page = sprintf( "%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                    '<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
                    $current,
                    strlen( $total_pages )
                );
            }
            $html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
            $page_links[] = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;

            if ( $disable_next ) {
                $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>';
            } else {
                $page_links[] = sprintf( "<a class='next-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                    esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
                    __( 'Next page' ),
                    '&rsaquo;'
                );
            }

            if ( $disable_last ) {
                $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span>';
            } else {
                $page_links[] = sprintf( "<a class='last-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                    esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
                    __( 'Last page' ),
                    '&raquo;'
                );
            }

            $pagination_links_class = 'pagination-links';
            if ( ! empty( $infinite_scroll ) ) {
                $pagination_links_class .= ' hide-if-js';
            }
            $output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

            if ( $total_pages ) {
                $page_class = $total_pages < 2 ? ' one-page' : '';
            } else {
                $page_class = ' no-pages';
            }
            $this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

            echo $this->_pagination;
        }

    }

endif;