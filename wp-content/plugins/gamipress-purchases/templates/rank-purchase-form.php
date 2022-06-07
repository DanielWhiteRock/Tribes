<?php
/**
 * Rank Purchase Form template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/purchases/rank-purchase-form.php
 * To override a specific rank type just copy it as yourtheme/gamipress/purchases/rank-purchase-form-{rank-type}.php
 */
global $gamipress_purchases_template_args;

// Shorthand
$a = $gamipress_purchases_template_args;

// Setup vars
$user_id = get_current_user_id();
$user_details = gamipress_purchases_get_user_billing_details( $user_id );
$rank_id = $a['id'];
$rank_type = gamipress_get_post_type( $rank_id ); ?>

<fieldset id="gamipress-purchases-form-wrapper" class="gamipress-purchases-form-wrapper gamipress-purchases-points-purchase-form-wrapper">

    <form id="<?php echo $a['form_id']; ?>" class="gamipress-purchases-form gamipress-purchases-points-purchase-form" action="" method="POST">

        <?php
        /**
         * Before render rank purchase form
         *
         * @param $user_id          integer     User ID
         * @param $user_details     array       User billing details
         * @param $rank_id          integer     Rank to be purchased
         * @param $template_args    array       Template received arguments
         */
        do_action( 'gamipress_purchases_before_rank_purchase_form', $user_id, $user_details, $rank_id, $a ); ?>

        <fieldset class="gamipress-purchases-form-rank-wrapper">

            <legend><?php _e( 'Purchase Details', 'gamipress-purchases' ); ?></legend>

            <p>
                <?php echo gamipress_render_rank( $rank_id, $a['template_args'] ); ?>
            </p>

        </fieldset>

        <?php // Check if user already has a pending purchase ?>
        <?php if( $a['pending_purchase'] ) : ?>

            <p>
                <?php _e( 'You already has purchased this and your payment is pending.', 'gamipress-purchases' ); ?>

                <?php if( $a['purchase_details_link'] ) : ?>

                    <?php echo sprintf(
                        __( 'You can check the purchase details %s.', 'gamipress-purchases' ),
                        '<a href="' . $a['purchase_details_link'] . '">' . __( 'here', 'gamipress-purchases' ) . '</a>'
                    ); ?>

                <?php endif; ?>
            </p>

        <?php else : ?>

            <?php // Setup the personal info ?>
            <?php gamipress_get_template_part( 'personal-info-form', $rank_type ); ?>

            <?php // Setup the billing details ?>
            <?php gamipress_get_template_part( 'billing-address-form', $rank_type ); ?>

            <?php // Setup the payment gateways ?>
            <?php gamipress_get_template_part( 'gateways-form', $rank_type ); ?>

            <?php // Setup the acceptance form ?>
            <?php gamipress_get_template_part( 'acceptance-form', $rank_type ); ?>

            <?php // Purchase totals ?>

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
            <input type="hidden" name="purchase_type" value="rank">
            <input type="hidden" name="purchase_key" value="<?php echo $a['purchase_key']; ?>">
            <input type="hidden" name="rank_id" value="<?php echo $a['id']; ?>">
            <input type="hidden" name="referrer" value="<?php echo get_the_permalink(); ?>">

            <?php // Hidden fields to help live preview, without effect on server request ?>
            <input type="hidden" name="subtotal" value="<?php echo $a['subtotal']; ?>">
            <input type="hidden" name="tax_amount" value="<?php echo $a['tax_amount']; ?>">
            <input type="hidden" name="tax" value="<?php echo $a['tax']; ?>">
            <input type="hidden" name="total" value="<?php echo $a['total']; ?>">

        <?php endif; ?>

        <?php
        /**
         * After render rank purchase form
         *
         * @param $user_id          integer     User ID
         * @param $user_details     array       User billing details
         * @param $rank_id          integer     Rank to be purchased
         * @param $template_args    array       Template received arguments
         */
        do_action( 'gamipress_purchases_after_rank_purchase_form', $user_id, $user_details, $rank_id, $a ); ?>

    </form>

</fieldset>
