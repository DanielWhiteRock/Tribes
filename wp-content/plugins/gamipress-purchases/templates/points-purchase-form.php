<?php
/**
 * Points Purchase Form template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/purchases/points-purchase-form.php
 * To override a specific points type just copy it as yourtheme/gamipress/purchases/points-purchase-form-{points-type}.php
 */
global $gamipress_purchases_template_args;

// Shorthand
$a = $gamipress_purchases_template_args;

// Setup vars
$user_id = get_current_user_id();
$user_details = gamipress_purchases_get_user_billing_details( $user_id );
$points_type = $a['points_type_object'];
$form_type = $a['form_type'];

// Get user points balance
$user_points = gamipress_get_user_points( $user_id, $a['points_type'] ); ?>

<fieldset id="gamipress-purchases-form-wrapper" class="gamipress-purchases-form-wrapper gamipress-purchases-points-purchase-form-wrapper">

    <form id="<?php echo $a['form_id']; ?>" class="gamipress-purchases-form gamipress-purchases-points-purchase-form" action="" method="POST">

        <?php
        /**
         * Before render points purchase form
         *
         * @param $user_id          integer     User ID
         * @param $user_details     array       User billing details
         * @param $user_points      integer     User earned points
         * @param $template_args    array       Template received arguments
         */
        do_action( 'gamipress_purchases_before_points_purchase_form', $user_id, $user_details, $user_points, $a ); ?>

        <?php // Setup the form based on form type ?>

        <?php if( $form_type === 'fixed' ) : ?>

            <fieldset class="gamipress-purchases-form-fixed">

                <legend><?php _e( 'Purchase details', 'gamipress-purchases' ); ?></legend>

                <p class="gamipress-purchases-form-fixed-amount">
                    <?php if( $a['amount_type'] === 'points' ) : ?>
                        <?php echo sprintf( __( 'You will purchase %s with a price of %s', 'gamipress-purchases' ), $a['fixed_points_label'], $a['fixed_money_label'] ); ?>
                    <?php else : ?>
                        <?php echo sprintf( __( 'You will spend %s to get %s', 'gamipress-purchases' ), $a['fixed_money_label'], $a['fixed_points_label'] ); ?>
                    <?php endif; ?>
                </p>

            </fieldset>

        <?php elseif( $form_type === 'custom' ) : ?>

            <fieldset class="gamipress-purchases-form-custom">

                <?php if( $a['amount_type'] === 'points' ) : ?>
                    <legend><?php _e( 'Enter the amount you want to purchase', 'gamipress-purchases' ); ?></legend>
                <?php else : ?>
                    <legend><?php _e( 'Enter the amount you want to spend', 'gamipress-purchases' ); ?></legend>
                <?php endif; ?>

                <p class="gamipress-purchases-form-custom-input">

                    <label for="<?php echo $a['form_id']; ?>-custom-amount"><?php _e( 'Amount:', 'gamipress-purchases' ); ?></label>

                    <?php if( $a['amount_type'] === 'money' && $a['currency_position'] === 'before' ) : ?>
                        <span class="gamipress-purchases-form-custom-currency-symbol"><?php echo gamipress_purchases_get_currency_symbol(); ?></span>
                    <?php endif; ?>

                    <input
                        id="<?php echo $a['form_id']; ?>-custom-amount"
                        class="gamipress-purchases-form-custom-amount"
                        name="<?php echo $a['form_id']; ?>-amount"
                        type="text"
                        value="<?php echo $a['initial_amount']; ?>">

                    <?php if( $a['amount_type'] === 'points' ) : ?>
                        <span class="gamipress-purchases-form-custom-points-label"><?php echo $points_type['plural_name']; ?></span>
                    <?php elseif( $a['amount_type'] === 'money' && $a['currency_position'] === 'after' ) : ?>
                        <span class="gamipress-purchases-form-custom-currency-symbol"><?php echo gamipress_purchases_get_currency_symbol(); ?></span>
                    <?php endif; ?>

                    <span class="gamipress-purchases-form-custom-preview-separator">/</span>
                    <span class="gamipress-purchases-form-custom-preview">
                        <?php if( $a['amount_type'] === 'points' ) : ?>
                            <?php echo gamipress_purchases_format_price( $a['subtotal'] ); ?>
                        <?php else : ?>
                            <?php echo $a['points_total']; ?>
                        <?php endif; ?>
                    </span>

                    <?php if( $a['amount_type'] === 'money' ) : ?>
                        <span class="gamipress-purchases-form-custom-preview-points-label"><?php echo $points_type['plural_name']; ?></span>
                    <?php endif; ?>
                </p>

            </fieldset>

        <?php elseif( $form_type === 'options' ) : ?>

            <fieldset class="gamipress-purchases-form-options">

                <?php if( $a['amount_type'] === 'points' ) : ?>
                    <legend><?php _e( 'Choose the amount you want to purchase', 'gamipress-purchases' ); ?></legend>
                <?php else : ?>
                    <legend><?php _e( 'Choose the amount you want to spend', 'gamipress-purchases' ); ?></legend>
                <?php endif; ?>

                <div class="gamipress-purchases-form-options-list">
                    <?php foreach( $a['options'] as $option_index => $option ) : ?>

                        <div id="gamipress-purchases-form-option-list-<?php echo $option_index; ?>" class="gamipress-purchases-form-option">

                            <input
                                id="<?php echo $a['form_id']; ?>-option-<?php echo $option_index; ?>"
                                name="<?php echo $a['form_id']; ?>-option"
                                type="radio"
                                value="<?php echo $option['value']; ?>"
                                <?php if( $option_index === 0 ) : ?>checked="checked"<?php endif; ?>>
                            <label for="<?php echo $a['form_id']; ?>-option-<?php echo $option_index; ?>"><?php echo $option['label']; ?></label>

                        </div>

                    <?php endforeach; ?>

                    <?php if( $a['allow_user_input'] === 'yes' ) : ?>

                        <div class="gamipress-purchases-form-option gamipress-purchases-form-option-list-custom">

                            <input id="<?php echo $a['form_id']; ?>-option-custom" name="<?php echo $a['form_id']; ?>-option" type="radio" value="custom">
                            <label for="<?php echo $a['form_id']; ?>-option-custom"><?php _e( 'Other', 'gamipress-purchases' ); ?></label>

                        </div>

                    <?php endif; ?>
                </div>

                <?php if( $a['allow_user_input'] === 'yes' ) : ?>

                    <p class="gamipress-purchases-form-options-custom-amount" style="display: none;">

                        <label for="<?php echo $a['form_id']; ?>-options-custom-amount-input"><?php _e( 'Enter amount:', 'gamipress-purchases' ); ?></label>

                        <?php if( $a['options_type'] === 'money' && $a['currency_position'] === 'before' ) : ?>
                            <span class="gamipress-purchases-form-options-currency-symbol"><?php echo gamipress_purchases_get_currency_symbol(); ?></span>
                        <?php endif; ?>

                        <input
                            id="<?php echo $a['form_id']; ?>-options-custom-amount-input"
                            class="gamipress-purchases-form-options-custom-amount-input"
                            name="<?php echo $a['form_id']; ?>-custom"
                            type="text"
                            value="<?php echo $a['initial_amount']; ?>">

                        <?php if( $a['options_type'] === 'points' ) : ?>
                            <span class="gamipress-purchases-form-options-points-label"><?php echo $points_type['plural_name']; ?></span>
                        <?php elseif( $a['options_type'] === 'money' && $a['currency_position'] === 'after' ) : ?>
                            <span class="gamipress-purchases-form-options-currency-symbol"><?php echo gamipress_purchases_get_currency_symbol(); ?></span>
                        <?php endif; ?>

                    </p>

                <?php endif; ?>

            </fieldset>

        <?php endif; ?>

        <?php // Setup the personal info ?>
        <?php gamipress_get_template_part( 'personal-info-form', $a['points_type'] ); ?>

        <?php // Setup the billing details ?>
        <?php gamipress_get_template_part( 'billing-address-form', $a['points_type'] ); ?>

        <?php // Setup the payment gateways ?>
        <?php gamipress_get_template_part( 'gateways-form', $a['points_type'] ); ?>

        <?php // Setup the acceptance form ?>
        <?php gamipress_get_template_part( 'acceptance-form', $a['points_type'] ); ?>

        <?php // Purchase totals ?>

        <p class="gamipress-purchases-form-points-total">
            <span class="gamipress-purchases-form-points-total-label"><?php _e( 'You will earn:', 'gamipress-purchases' ); ?></span>
            <span class="gamipress-purchases-form-points-total-amount"><?php echo $a['points_total']; ?></span>
            <span class="gamipress-purchases-form-points-total-points-type-label"><?php echo $points_type['plural_name']; ?></span>
        </p>

        <?php if( (bool) gamipress_purchases_get_option( 'enable_taxes', false ) ) : ?>
            <?php // Purchase subtotal and taxes (just if taxes are enabled) ?>

            <p class="gamipress-purchases-form-subtotal">
                <span class="gamipress-purchases-form-subtotal-label"><?php _e( 'Subtotal:', 'gamipress-purchases' ); ?></span>
                <span class="gamipress-purchases-form-subtotal-amount"><?php echo gamipress_purchases_format_price( $a['subtotal'] ); ?></span>
            </p>


            <p class="gamipress-purchases-form-tax">
                <span class="gamipress-purchases-form-tax-label"><?php _e( 'Tax:', 'gamipress-purchases' ); ?></span>
                <span class="gamipress-purchases-form-tax-amount"><?php echo gamipress_purchases_format_price( $a['tax_amount'] ); ?></span>
                <span class="gamipress-purchases-form-tax-percent">(<?php echo gamipress_purchases_convert_to_float( $a['tax'] ); ?>%)</span>
            </p>

        <?php endif; ?>

        <p class="gamipress-purchases-form-total">
            <span class="gamipress-purchases-form-total-label"><?php _e( 'Total:', 'gamipress-purchases' ); ?></span>
            <span class="gamipress-purchases-form-total-amount"><?php echo gamipress_purchases_format_price( $a['total'] ); ?></span>
        </p>

        <?php // Setup submit actions ?>

        <p class="gamipress-purchases-form-submit">
            <?php // Loading spinner ?>
            <span class="gamipress-spinner" style="display: none;"></span>
            <input type="submit" id="<?php echo $a['form_id']; ?>-submit-button" class="gamipress-purchases-form-submit-button" value="<?php echo $a['button_text']; ?>">
        </p>

        <?php // Output hidden fields ?>
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'gamipress_purchases_purchase_form' ); ?>">
        <input type="hidden" name="purchase_type" value="points">
        <input type="hidden" name="purchase_key" value="<?php echo $a['purchase_key']; ?>">
        <input type="hidden" name="points_type" value="<?php echo $a['points_type']; ?>">
        <input type="hidden" name="amount_type" value="<?php echo $a['amount_type']; ?>">
        <input type="hidden" name="amount" value="<?php echo $a['amount']; ?>">
        <input type="hidden" name="referrer" value="<?php echo get_the_permalink(); ?>">

        <?php // Hidden fields to help live preview, without effect on server request ?>
        <input type="hidden" name="subtotal" value="<?php echo $a['subtotal']; ?>">
        <input type="hidden" name="tax_amount" value="<?php echo $a['tax_amount']; ?>">
        <input type="hidden" name="tax" value="<?php echo $a['tax']; ?>">
        <input type="hidden" name="total" value="<?php echo $a['total']; ?>">

        <?php
        /**
         * After render points purchase form
         *
         * @param $user_id          integer     User ID
         * @param $user_details     array       User billing details
         * @param $user_points      integer     User earned points
         * @param $template_args    array       Template received arguments
         */
        do_action( 'gamipress_purchases_after_points_purchase_form', $user_id, $user_details, $user_points, $a ); ?>

    </form>

</fieldset>
