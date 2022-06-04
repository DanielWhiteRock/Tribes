<?php
/**
 * Points Payout Form template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/points-payouts/points-payout-form.php
 * To override a specific points type just copy it as yourtheme/gamipress/points-payouts/points-payout-form-{points-type}.php
 */
global $gamipress_points_payouts_template_args;

// Shorthand
$a = $gamipress_points_payouts_template_args;

// Setup vars
$user_id = get_current_user_id();
$points_types = $a['points_types'];
$points_types_objects = $a['points_types_objects'];

// By default, the first points type is selected
$points_type = $points_types[0];
$points_type_obj = gamipress_get_points_type( $points_type );
$points_type_label_position = gamipress_get_points_type_label_position( $points_type );

// Get user points balance
$user_points = gamipress_get_user_points( $user_id, $points_type );
$amount = $points_types_objects[$points_type]['min_amount'];
$new_balance = ( $user_points - $amount );

// Currency settings
$money = gamipress_points_payouts_convert_to_money( $amount, $points_type );
$currency_symbol = gamipress_points_payouts_get_currency_symbol();
$currency_position = gamipress_points_payouts_get_option( 'currency_position', 'before' );
?>

<fieldset id="gamipress-points-payout-form-wrapper" class="gamipress-points-payout-form-wrapper">

    <form id="<?php echo $a['form_id']; ?>" class="gamipress-points-payout-form" action="" method="POST">

        <?php
        /**
         * Before render points points payout form
         *
         * @since 1.0.0
         *
         * @param integer     $user_id          User ID
         * @param integer     $amount           Points amount
         * @param integer     $user_points      User earned points
         * @param array       $points_types     Points type(s)
         * @param array       $template_args    Template received arguments
         */
        do_action( 'gamipress_points_payouts_before_form', $user_id, $amount, $user_points, $points_types, $a ); ?>

        <?php // Setup the points form ?>
        <fieldset class="gamipress-points-payout-form-points-input">

            <legend><?php _e( 'Enter the amount you want to withdrawal', 'gamipress-points-payout' ); ?></legend>

            <p class="gamipress-points-payout-form-points-input">

                <label for="<?php echo $a['form_id']; ?>-points-amount"><?php _e( 'Amount:', 'gamipress-points-payout' ); ?></label>

                <?php foreach( $points_types_objects as $type => $obj ) : ?>

                    <div class="gamipress-points-payout-form-points-input-<?php echo $type; ?> <?php if( $type === $points_type ) : ?>gamipress-points-payout-form-points-input-active<?php endif; ?>"
                         style="<?php if( $type !== $points_type ) : ?>display: none;<?php endif; ?>">

                        <input
                            id="<?php echo $a['form_id']; ?>-points-amount"
                            class="gamipress-points-payout-form-points-amount"
                            name="<?php echo $a['form_id']; ?>-amount"
                            type="number"
                            step="1"
                            min="<?php echo $obj['min_amount']; ?>"
                            <?php if( $obj['max_amount'] > 0 ) : ?>max="<?php echo $obj['max_amount']; ?>"<?php endif; ?>
                            value="<?php echo $obj['min_amount']; ?>">

                        <?php if( count( $points_types ) === 1 ) : ?>
                            <span class="gamipress-points-payout-form-points-label gamipress-points-payout-points-type-label"><?php echo $points_type_obj['plural_name']; ?></span>
                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            </p>

        </fieldset>

        <?php // Setup the points type form ?>
        <?php if( count( $points_types ) > 1 ) : ?>

            <fieldset class="gamipress-points-payout-points-type">

                <legend><?php _e( 'Choose the type to withdrawal', 'gamipress-points-payout' ); ?></legend>

                <p class="gamipress-points-payout-form-points-type-input">

                    <label for="<?php echo $a['form_id']; ?>-points-type"><?php _e( 'Type:', 'gamipress-points-payout' ); ?></label>

                    <select
                        id="<?php echo $a['form_id']; ?>-points-type"
                        class="gamipress-points-payout-form-points-type"
                        name="points_type">

                        <?php foreach( $points_types as $points_type_option ) :
                            $points_type_option_obj = gamipress_get_points_type( $points_type_option ); ?>

                            <option value="<?php echo $points_type_option; ?>"
                                    data-singular="<?php echo $points_type_option_obj['singular_name']; ?>"
                                    data-plural="<?php echo $points_type_option_obj['plural_name']; ?>"
                                    data-balance="<?php echo gamipress_get_user_points( $user_id, $points_type_option ); ?>"
                            ><?php echo $points_type_option_obj['plural_name']; ?></option>

                        <?php endforeach; ?>

                    </select>
                </p>

            </fieldset>

        <?php else : ?>
            <input type="hidden" name="points_type" value="<?php echo $points_type; ?>">
        <?php endif; ?>

        <?php // Setup the payment method ?>
        <?php if( $a['payment_method'] === 'yes' ) :
            $payment_method_text = gamipress_points_payouts_get_option( 'payment_method_text', __( 'Payment Method', 'gamipress-points-payouts' ) );
            $payment_method = gamipress_get_user_meta( $user_id, '_gamipress_points_payouts_payment_method', true );
        ?>

            <fieldset class="gamipress-points-payout-form-payment-method-input">

                <legend><?php echo $payment_method_text; ?></legend>

                <p class="gamipress-points-payout-form-payment-method-input">

                    <div class="gamipress-points-payout-form-payment-method-input">

                        <input id="<?php echo $a['form_id']; ?>-payment-method"
                                class="gamipress-points-payout-form-payment-method"
                                name="payment_method"
                                type="text"
                                value="<?php echo $payment_method; ?>">

                    </div>

                </p>

            </fieldset>

        <?php endif; ?>

        <?php
        /**
         * Before render points points payout form total
         *
         * @since 1.0.0
         *
         * @param integer     $user_id          User ID
         * @param integer     $amount           Points amount
         * @param integer     $user_points      User earned points
         * @param array       $points_types     Points type(s)
         * @param array       $template_args    Template received arguments
         */
        do_action( 'gamipress_points_payouts_before_form_total', $user_id, $amount, $user_points, $points_types, $a ); ?>

        <?php // Points payout total ?>

        <p class="gamipress-points-payout-form-total">
            <span class="gamipress-points-payout-form-total-label">
                <?php
                $points_total_amount = '<span class="gamipress-points-payout-form-total-amount">' . $amount . '</span>';
                $points_total_label = '<span class="gamipress-points-payout-form-total-points-label gamipress-points-payout-points-type-label">' . $points_type_obj['plural_name'] . '</span>';
                $points_total = ( $points_type_label_position === 'after' ? $points_total_amount . ' ' . $points_total_label : $points_total_label . ' ' . $points_total_amount );
                printf( __( 'You will withdrawal %s for %s', 'gamipress-points-payout' ),
                    $points_total,
                    ( $currency_position === 'before' ? $currency_symbol : '' ) .
                    '<span class="gamipress-points-payout-form-total-money">' . gamipress_points_payouts_format_amount( $money ) . '</span>'
                    . ( $currency_position === 'after' ? $currency_symbol : '' )
                ); ?>
            </span>
        </p>
        <p class="gamipress-points-payout-form-current-balance">
            <span class="gamipress-points-payout-form-current-balance-label"><?php _e( 'Current balance:', 'gamipress-points-payout' ); ?></span>
            <?php if( $points_type_label_position === 'after' ) : ?>
                <span class="gamipress-points-payout-form-current-balance-amount"><?php echo $user_points; ?></span>
                <span class="gamipress-points-payout-form-current-balance-points-label gamipress-points-payout-points-type-label"><?php echo $points_type_obj['plural_name']; ?></span>
            <?php else : ?>
                <span class="gamipress-points-payout-form-current-balance-points-label gamipress-points-payout-points-type-label"><?php echo $points_type_obj['plural_name']; ?></span>
                <span class="gamipress-points-payout-form-current-balance-amount"><?php echo $user_points; ?></span>
            <?php endif; ?>
        </p>
        <p class="gamipress-points-payout-form-new-balance">
            <span class="gamipress-points-payout-form-new-balance-label"><?php _e( 'New balance after withdrawal:', 'gamipress-points-payout' ); ?></span>
            <?php if( $points_type_label_position === 'after' ) : ?>
                <span class="gamipress-points-payout-form-new-balance-amount gamipress-points-payout-<?php echo ( $new_balance > 1 ? 'positive' : 'negative' ); ?>"><?php echo $new_balance; ?></span>
                <span class="gamipress-points-payout-form-new-balance-points-label gamipress-points-payout-points-type-label"><?php echo $points_type_obj['plural_name']; ?></span>
            <?php else : ?>
                <span class="gamipress-points-payout-form-new-balance-points-label gamipress-points-payout-points-type-label"><?php echo $points_type_obj['plural_name']; ?></span>
                <span class="gamipress-points-payout-form-new-balance-amount gamipress-points-payout-<?php echo ( $new_balance > 1 ? 'positive' : 'negative' ); ?>"><?php echo $new_balance; ?></span>
            <?php endif; ?>
        </p>

        <?php
        /**
         * Before render points points payout form submit
         *
         * @since 1.0.0
         *
         * @param integer     $user_id          User ID
         * @param integer     $amount           Points amount
         * @param integer     $user_points      User earned points
         * @param array       $points_types     Points type(s)
         * @param array       $template_args    Template received arguments
         */
        do_action( 'gamipress_points_payouts_before_form_submit', $user_id, $amount, $user_points, $points_types, $a ); ?>

        <?php // Setup submit actions ?>
        <p class="gamipress-points-payout-form-submit">
            <?php // Loading spinner ?>
            <span class="gamipress-spinner" style="display: none;"></span>
            <input type="submit" id="<?php echo $a['form_id']; ?>-submit-button" class="gamipress-points-payout-form-submit-button" value="<?php echo $a['button_text']; ?>">
        </p>

        <?php // Output hidden fields ?>
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'gamipress_points_payouts_form' ); ?>">
        <input type="hidden" name="referrer" value="<?php echo get_the_permalink(); ?>">
        <input type="hidden" name="amount" value="<?php echo $amount; ?>">
        <input type="hidden" name="user_points" value="<?php echo $user_points; ?>">

        <?php
        /**
         * After render points payout form
         *
         * @since 1.0.0
         *
         * @param integer     $user_id          User ID
         * @param integer     $amount           Points amount
         * @param integer     $user_points      User earned points
         * @param array       $points_types     Points type(s)
         * @param array       $template_args    Template received arguments
         */
        do_action( 'gamipress_points_payouts_after_form', $user_id, $amount, $user_points, $points_types, $a ); ?>

    </form>

</fieldset>

