<?php
/**
 * Points Exchange Form template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/points-exchanges/points-exchange-form.php
 * To override a specific points type just copy it as yourtheme/gamipress/points-exchanges/points-exchange-form-{points-type}.php
 */
global $gamipress_points_exchanges_template_args;

// Shorthand
$a = $gamipress_points_exchanges_template_args;

// Setup vars
$user_id = get_current_user_id();
$points_types = gamipress_get_points_types();
$points_types_enabled = gamipress_points_exchanges_get_exchanges_enabled_points_types(); ?>

<fieldset id="gamipress-points-exchanges-form-wrapper" class="gamipress-points-exchanges-form-wrapper gamipress-points-exchanges-points-exchange-form-wrapper">

    <form id="<?php echo $a['form_id']; ?>" class="gamipress-points-exchanges-form gamipress-points-exchanges-points-exchange-form" action="" method="POST">

        <?php
        /**
         * Before render points exchange form
         *
         * @param $user_id          integer     User ID
         * @param $template_args    array       Template received arguments
         */
        do_action( 'gamipress_points_exchange_before_points_exchange_form', $user_id, $a ); ?>

        <?php // From ?>
        <fieldset class="gamipress-points-exchanges-form-from">

            <p class="gamipress-points-exchanges-form-amount">

                <?php // Amount ?>

                <label for="<?php echo $a['form_id']; ?>-amount"><?php _e( 'Amount:', 'gamipress-points-exchanges' ); ?></label>

                <?php if( $a['allow_input_amount'] === 'yes' ) : ?>

                    <input
                        id="<?php echo $a['form_id']; ?>-amount"
                        class="gamipress-points-exchanges-form-amount-input"
                        name="<?php echo $a['form_id']; ?>-amount"
                        type="number"
                        step="1"
                        min="0"
                        value="<?php echo $a['amount']; ?>">

                <?php else : ?>

                    <?php if( $a['allow_input_from'] === 'yes' ) : ?>
                        <?php echo sprintf( __( 'Exchange %d', 'gamipress-points-exchanges' ), $a['amount'] ); ?>
                    <?php else : ?>
                        <?php echo sprintf( __( 'Exchange %d %s', 'gamipress-points-exchanges' ), $a['amount'], $points_types[$a['from']]['plural_name'] ); ?>
                    <?php endif; ?>

                <?php endif; ?>

                <?php // Amount's type ?>

                <?php if( $a['allow_input_from'] === 'yes' ) : ?>

                    <select
                        id="<?php echo $a['form_id']; ?>-from"
                        class="gamipress-points-exchanges-form-from-select"
                        name="<?php echo $a['form_id']; ?>-from">

                        <option value=""><?php _e( 'Choose the amount\'s type', 'gamipress-points-exchanges' ); ?></option>

                        <?php foreach( $points_types_enabled as $points_type => $data ) : ?>
                            <option value="<?php echo $points_type; ?>" <?php selected( $a['from'], $points_type ); ?>><?php echo $data['plural_name']; ?></option>
                        <?php endforeach; ?>

                    </select>

                <?php endif; ?>

            </p>

        </fieldset>

        <?php // To ?>
        <fieldset class="gamipress-points-exchanges-form-to">

            <p class="gamipress-points-exchanges-form-to">

                <?php // Exchange Type ?>

                <label for="<?php echo $a['form_id']; ?>-to"><?php _e( 'Exchange to:', 'gamipress-points-exchanges' ); ?></label>

                <?php if( $a['allow_input_to'] === 'yes' ) : ?>

                    <select
                        id="<?php echo $a['form_id']; ?>-to"
                        class="gamipress-points-exchanges-form-to-select"
                        name="<?php echo $a['form_id']; ?>-to">

                        <option value=""><?php _e( 'Choose the type to exchange', 'gamipress-points-exchanges' ); ?></option>

                        <?php if( $a['from_points_type'] ) : ?>

                            <?php foreach( $a['to_points_types_options'] as $points_type => $data ) : ?>
                                <option value="<?php echo $points_type; ?>" <?php selected( $a['to'], $points_type ); ?>><?php echo $data['plural_name']; ?></option>
                            <?php endforeach; ?>

                        <?php endif; ?>

                    </select>

                <?php else : ?>

                    <?php echo $points_types[$a['to']]['plural_name']; ?>

                <?php endif; ?>

            </p>

        </fieldset>

        <?php // Exchange from details ?>

        <fieldset class="gamipress-points-exchanges-form-from-details" style="<?php echo ( ! $a['from_points_type'] ? 'display: none;' : '' ); ?>">

            <label class="gamipress-points-exchanges-form-from-details-label"><?php echo ( $a['from_points_type'] ? $a['from_points_type']['plural_name'] : '' ); ?></label>

            <p class="gamipress-points-exchanges-form-from-current-balance">
                <span class="gamipress-points-exchanges-form-from-current-balance-label"><?php _e( 'Current balance:', 'gamipress-points-exchanges' ); ?></span>
                <span class="gamipress-points-exchanges-form-from-current-balance-amount"><?php echo $a['from_balance']; ?></span>
                <span class="gamipress-points-exchanges-form-from-current-balance-points-label"><?php echo ( $a['from_points_type'] ? $a['from_points_type']['plural_name'] : '' ); ?></span>
            </p>
            <p class="gamipress-points-exchanges-form-from-amount">
                <span class="gamipress-points-exchanges-form-from-amount-label"><?php _e( 'You will exchange:', 'gamipress-points-exchanges' ); ?></span>
                <span class="gamipress-points-exchanges-form-from-amount-amount"><?php echo $a['amount']; ?></span>
                <span class="gamipress-points-exchanges-form-from-amount-points-label"><?php echo ( $a['from_points_type'] ? $a['from_points_type']['plural_name'] : '' ); ?></span>
            </p>
            <p class="gamipress-points-exchanges-form-from-new-balance">
                <span class="gamipress-points-exchanges-form-from-new-balance-label"><?php _e( 'New balance:', 'gamipress-points-exchanges' ); ?></span>
                <span class="gamipress-points-exchanges-form-from-new-balance-amount gamipress-points-exchanges-<?php echo ( $a['from_new_balance'] > 1 ? 'positive' : 'negative' ); ?>"><?php echo $a['from_new_balance']; ?></span>
                <span class="gamipress-points-exchanges-form-from-new-balance-points-label"><?php echo ( $a['from_points_type'] ? $a['from_points_type']['plural_name'] : '' ); ?></span>
            </p>

        </fieldset>

        <?php // Exchange to details ?>

        <fieldset class="gamipress-points-exchanges-form-to-details" style="<?php echo ( ! $a['to_points_type'] ? 'display: none;' : '' ); ?>">

            <label class="gamipress-points-exchanges-form-to-details-label"><?php echo ( $a['to_points_type'] ? $a['to_points_type']['plural_name'] : '' ); ?></label>

            <p class="gamipress-points-exchanges-form-to-current-balance">
                <span class="gamipress-points-exchanges-form-to-current-balance-label"><?php _e( 'Current balance:', 'gamipress-points-exchanges' ); ?></span>
                <span class="gamipress-points-exchanges-form-to-current-balance-amount"><?php echo $a['to_balance']; ?></span>
                <span class="gamipress-points-exchanges-form-to-current-balance-points-label"><?php echo ( $a['to_points_type'] ? $a['to_points_type']['plural_name'] : '' ); ?></span>
            </p>
            <p class="gamipress-points-exchanges-form-to-amount">
                <span class="gamipress-points-exchanges-form-to-amount-label"><?php _e( 'You will get:', 'gamipress-points-exchanges' ); ?></span>
                <span class="gamipress-points-exchanges-form-to-amount-amount"><?php echo $a['to_amount']; ?></span>
                <span class="gamipress-points-exchanges-form-to-amount-points-label"><?php echo ( $a['to_points_type'] ? $a['to_points_type']['plural_name'] : '' ); ?></span>
            </p>
            <p class="gamipress-points-exchanges-form-to-new-balance">
                <span class="gamipress-points-exchanges-form-to-new-balance-label"><?php _e( 'New balance:', 'gamipress-points-exchanges' ); ?></span>
                <span class="gamipress-points-exchanges-form-to-new-balance-amount"><?php echo $a['to_new_balance']; ?></span>
                <span class="gamipress-points-exchanges-form-to-new-balance-points-label"><?php echo ( $a['to_points_type'] ? $a['to_points_type']['plural_name'] : '' ); ?></span>
            </p>

        </fieldset>

        <?php // Exchange rate details ?>

        <fieldset class="gamipress-points-exchanges-form-exchange-rate-details">

            <label class="gamipress-points-exchanges-form-exchange-rate-details-title"><?php _e( 'Exchange rate:' ); ?></label>

            <p class="gamipress-points-exchanges-form-exchange-rate" style="<?php echo ( ! $a['from_points_type'] || ! $a['to_points_type'] ? 'display: none;' : '' ); ?>">
                <span class="gamipress-points-exchanges-form-exchange-rate-from-amount">1</span>
                <span class="gamipress-points-exchanges-form-exchange-rate-from-label"><?php echo ( $a['from_points_type'] ? $a['from_points_type']['singular_name'] : '' ); ?></span>
                <span class="gamipress-points-exchanges-form-exchange-rate-operator">=</span>
                <span class="gamipress-points-exchanges-form-exchange-rate-to-amount"><?php echo $a['exchange_rate']; ?></span>
                <span class="gamipress-points-exchanges-form-exchange-rate-to-label"><?php echo ( $a['to_points_type'] ? _n( $a['to_points_type']['singular_name'], $a['to_points_type']['plural_name'], $a['exchange_rate'] ) : '' ); ?></span>
            </p>

            <p class="gamipress-points-exchanges-form-exchange-rate-error" style="<?php echo ( $a['from_points_type'] && $a['to_points_type'] ? 'display: none;' : '' ); ?>">
                <span class="gamipress-points-exchanges-form-exchange-rate-error-message"><?php _e( 'Not available, please choose the amount\'s and exchange types.', 'gamipress-points-exchanges' ); ?></span>
            </p>

        </fieldset>

        <?php // Setup submit actions ?>

        <p class="gamipress-points-exchanges-form-submit">
            <?php // Loading spinner ?>
            <span class="gamipress-spinner" style="display: none;"></span>
            <input type="submit" id="<?php echo $a['form_id']; ?>-submit-button" class="gamipress-points-exchanges-form-submit-button" value="<?php echo $a['button_text']; ?>">
        </p>

        <?php // Output hidden fields ?>
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'gamipress_points_exchanges_exchange_form' ); ?>">
        <input type="hidden" name="referrer" value="<?php echo gamipress_points_exchanges_get_referrer(); ?>">
        <input type="hidden" name="form_id" value="<?php echo $a['form_id']; ?>">
        <input type="hidden" name="amount" value="<?php echo $a['amount']; ?>">
        <input type="hidden" name="from" value="<?php echo $a['from']; ?>">
        <input type="hidden" name="to" value="<?php echo $a['to']; ?>">

        <?php
        /**
         * After render points exchange form
         *
         * @param $user_id          integer     User ID
         * @param $template_args    array       Template received arguments
         */
        do_action( 'gamipress_points_exchange_after_points_exchange_form', $user_id, $a ); ?>

    </form>

</fieldset>
