<?php
/**
 * Points Exchange Rates template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/points-exchanges/points-exchange-rates.php
 * To override a specific achievement/points/rank type just copy it as yourtheme/gamipress/points-exchanges/points-exchange-rates-{type}.php
 */
global $gamipress_points_exchanges_template_args;

// Shorthand
$a = $gamipress_points_exchanges_template_args;

// Setup vars
$points_types = gamipress_get_points_types();
$rates = $a['rates']; ?>

<?php
/**
 * Before render points exchange rates table
 *
 * @since 1.0.0
 *
 * @param array       $points_types     Points types to be rendered
 * @param array       $template_args    Template received arguments
 */
do_action( 'gamipress_points_exchanges_before_rates', $a['points_type'], $a ); ?>

<table class="gamipress-points-exchanges-rates-table">

    <thead>

        <tr>

            <th></th>

            <?php foreach( $a['points_type'] as $points_type ) : ?>

                <th><?php echo $points_types[$points_type]['plural_name']; ?></th>

            <?php endforeach; ?>

        </tr>

    </thead>

    <tbody>

        <?php foreach( $a['points_type'] as $points_type_a ) :

            // Get the points type rates
            $points_type_a_rates = $rates[$points_type_a]; ?>

            <tr>

                <th><?php echo $points_types[$points_type_a]['plural_name']; ?></th>

                <?php foreach( $a['points_type'] as $points_type_b ) :

                    // Setup the rate to show
                    $rate = ( isset( $points_type_a_rates[$points_type_b] ) ? $points_type_a_rates[$points_type_b] : 1 );

                    // Setup the rates classes
                    $classes = array(
                        'gamipress-points-exchanges-' . $points_type_a . '-to-' . $points_type_b,
                        ( $points_type_a === $points_type_b ? 'gamipress-points-exchanges-same-type' : '' ),
                        ( $rate === 0.00 ? 'gamipress-points-exchanges-exchange-disabled' : '' )
                    );

                    /**
                     * Rate to be rendered on points exchange rates table
                     *
                     * @since 1.0.0
                     *
                     * @param float       $rate             Points types rate
                     * @param string      $points_type_a    Points type A slug
                     * @param string      $points_type_b    Points type B slug
                     * @param array       $template_args    Template received arguments
                     */
                    $rate = apply_filters( 'gamipress_points_exchanges_rates_rate', $rate, $points_type_a, $points_type_b, $a ); ?>

                    <td class="<?php echo implode( ' ', $classes ); ?>"><?php echo $rate; ?></td>

                <?php endforeach; ?>

            </tr>

        <?php endforeach; ?>

    </tbody>

</table>

<?php
/**
 * After render points exchange rates table
 *
 * @since 1.0.0
 *
 * @param array       $points_types     Points types to be rendered
 * @param array       $template_args    Template received arguments
 */
do_action( 'gamipress_points_exchanges_after_rates', $a['points_type'], $a ); ?>
