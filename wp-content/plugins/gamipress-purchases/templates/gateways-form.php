<?php
/**
 * Gateways Form template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/purchases/gateways-form.php
 * To override a specific achievement/points/rank type just copy it as yourtheme/gamipress/purchases/gateways-form-{type}.php
 */
global $gamipress_purchases_template_args;

// Shorthand
$a = $gamipress_purchases_template_args;

// Setup vars
$user_id = get_current_user_id();
$user_details = gamipress_purchases_get_user_billing_details( $user_id ); ?>

<?php
/**
 * Before render gateways form
 *
 * @param $user_id          integer     User ID
 * @param $user_details     array       User billing details
 * @param $template_args    array       Template received arguments
 */
do_action( 'gamipress_purchases_before_gateways_form', $user_id, $user_details, $a ); ?>

<fieldset class="gamipress-purchases-form-gateways">

    <legend><?php _e( 'Payment Method', 'gamipress-purchases' ); ?></legend>

    <?php if( count( $a['gateways'] ) === 1 ) :
        // Setup vars
        $gateway_id = array_keys( $a['gateways'] )[0];
        $gateway = $a['gateways'][$gateway_id]; ?>

        <div class="gamipress-purchases-form-single-gateway">

            <input
                id="<?php echo $a['form_id']; ?>-gateway-<?php echo $gateway_id; ?>"
                name="<?php echo $a['form_id']; ?>-gateway"
                type="hidden"
                value="<?php echo $gateway_id; ?>">

            <div id="<?php echo $a['form_id']; ?>-gateway-<?php echo $gateway_id; ?>-form" class="gamipress-purchases-form-gateway-form gamipress-purchases-form-gateway-<?php echo $gateway_id; ?>-form">

                <?php
                /**
                 * Render a specific gateway form
                 *
                 * @param $user_id          integer     User ID
                 * @param $user_details     array       User billing details
                 * @param $template_args    array       Template received arguments
                 */
                do_action( "gamipress_purchases_{$gateway_id}_form", $user_id, $user_details, $a ); ?>

            </div>

        </div>

    <?php else : ?>

        <div class="gamipress-purchases-form-gateways-list">

            <?php
            $selected_gateway = array_keys( $a['gateways'] );
            $selected_gateway = $selected_gateway[0];
            foreach( $a['gateways'] as $gateway_id => $gateway ) : ?>

                <div class="gamipress-purchases-form-gateway-option gamipress-purchases-form-gateway-<?php echo $gateway_id; ?>-option">

                    <input
                        id="<?php echo $a['form_id']; ?>-gateway-<?php echo $gateway_id; ?>"
                        name="<?php echo $a['form_id']; ?>-gateway"
                        type="radio"
                        value="<?php echo $gateway_id; ?>"
                        <?php if( $selected_gateway === $gateway_id ) : ?>checked="checked"<?php endif; ?>>

                    <label for="<?php echo $a['form_id']; ?>-gateway-<?php echo $gateway_id; ?>"><?php echo $gateway; ?></label>

                </div>

            <?php endforeach; ?>

        </div>

        <div class="gamipress-purchases-form-gateways-forms">

            <?php foreach( $a['gateways'] as $gateway_id => $gateway ) : ?>

                <div id="<?php echo $a['form_id']; ?>-gateway-<?php echo $gateway_id; ?>-form" class="gamipress-purchases-form-gateway-form gamipress-purchases-form-gateway-<?php echo $gateway_id; ?>-form" style="display: none;">

                    <?php
                    /**
                     * Render a specific gateway form
                     *
                     * @param $user_id          integer     User ID
                     * @param $user_details     array       User billing details
                     * @param $template_args    array       Template received arguments
                     */
                    do_action( "gamipress_purchases_{$gateway_id}_form", $user_id, $user_details, $a ); ?>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</fieldset>

<?php
/**
 * After render gateways form
 *
 * @param $user_id          integer     User ID
 * @param $user_details     array       User billing details
 * @param $template_args    array       Template received arguments
 */
do_action( 'gamipress_purchases_after_gateways_form', $user_id, $user_details, $a ); ?>