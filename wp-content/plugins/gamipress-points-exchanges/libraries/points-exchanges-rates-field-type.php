<?php

if( ! function_exists( 'gamipress_render_points_exchanges_rates_field_type' ) ) {

    /**
     * Adds a custom field type for points exchange rates.
     *
     * @param  object $field             The CMB2_Field type object.
     * @param  string $value             The saved (and escaped) value.
     * @param  int    $object_id         The current post ID.
     * @param  string $object_type       The current object type.
     * @param  object $field_type        The CMB2_Types object.
     *
     * @return void
     */
    function gamipress_render_points_exchanges_rates_field_type( $field, $value, $object_id, $object_type, $field_type ) {

        $current_points_type = get_post_field( 'post_name', $object_id );
        $points_types = gamipress_get_points_types();

        if( empty( $current_points_type ) ) {
            _e( 'Please, setup completely this points type to be able to configure the points exchangess.' );
            return;
        } ?>

        <ul>

            <?php foreach( $points_types as $points_type => $data ) :

                // Skip current points type input
                if( $current_points_type === $points_type ) { continue; } ?>

                <li>
                    <label for="<?php echo $field_type->_id( '_' . $points_type ); ?>">

                        <strong><?php echo $data['plural_name']; ?>:</strong>

                        <?php echo $field_type->input( array(
                            'name'  => $field_type->_name( '[' . $points_type . ']' ),
                            'id'    => $field_type->_id( '_' . $points_type ),
                            'value' => isset( $value[$points_type] ) ? $value[$points_type] : 1,
                            'desc'  => '',
                            'type' => 'number',
                            'step' => apply_filters( 'gamipress_points_exchanges_field_input_step', 0.01 ),
                            'min' => 0,
                            'class' => 'small-text'
                        ) ); ?>

                    </label>
                </li>

            <?php endforeach; ?>

        </ul>

        <?php

        $field_type->_desc( true, true );

    }
    add_action( 'cmb2_render_points_exchanges_rates', 'gamipress_render_points_exchanges_rates_field_type', 10, 5 );


    /**
     * Sanitize the selected value.
     */
    function gamipress_sanitize_points_exchanges_rates_callback( $override_value, $value ) {

        if ( is_array( $value ) ) {

            foreach ( $value as $key => $saved_value ) {
                $value[$key] = sanitize_text_field( $saved_value );
            }

            return $value;
        }

        return $value;

    }
    add_filter( 'cmb2_sanitize_points_exchanges_rates', 'gamipress_sanitize_points_exchanges_rates_callback', 10, 2 );

}