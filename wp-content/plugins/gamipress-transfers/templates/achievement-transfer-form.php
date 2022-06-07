<?php
/**
 * Achievement Transfer Form template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/transfers/achievement-transfer-form.php
 * To override a specific achievement type just copy it as yourtheme/gamipress/transfers/achievement-transfer-form-{achievement-type}.php
 */
global $gamipress_transfers_template_args;

// Shorthand
$a = $gamipress_transfers_template_args;

// Setup vars
$user_id = get_current_user_id();
$transfer_type = $a['transfer_type'];
$recipient = get_userdata( $a['recipient_id'] );
$achievement_id = $a['id']; ?>

<fieldset id="gamipress-transfers-form-wrapper" class="gamipress-transfers-form-wrapper gamipress-transfers-achievement-transfer-form-wrapper">

    <form id="<?php echo $a['form_id']; ?>" class="gamipress-transfers-form gamipress-transfers-achievement-transfer-form" action="" method="POST">

        <?php
        /**
         * Before render achievement transfer form
         *
         * @since 1.0.0
         *
         * @param integer     $user_id          User ID
         * @param integer     $recipient_id     Recipient ID (if is set)
         * @param integer     $achievement_id   Achievement ID (if is set)
         * @param array       $template_args    Template received arguments
         */
        do_action( 'gamipress_transfers_before_achievement_transfer_form', $user_id, $a['recipient_id'], $achievement_id, $a ); ?>

        <fieldset class="gamipress-transfers-form-achievement-wrapper">

            <legend><?php _e( 'Transfer Details', 'gamipress-transfers' ); ?></legend>

            <div class="gamipress-transfers-form-achievement-render">
                <?php if( $achievement_id !== 0 ) : ?>
                    <?php echo gamipress_render_achievement( $achievement_id, $a['template_args'] ); ?>
                <?php endif; ?>
            </div>

            <?php // Render the template args on hidden fields to render the achievement through ajax ?>
            <?php // Note: Input name is set as data to prevent to be submitted ?>
            <?php foreach( $a['template_args'] as $arg => $arg_value ) : ?>
                <input type="hidden" data-name="<?php echo $arg; ?>" value="<?php echo $arg_value; ?>">
            <?php endforeach; ?>

        </fieldset>

        <?php // Check if user already has a pending transfer ?>
        <?php // Note: If select achievement is set to yes, pending transfer will be false ?>
        <?php if( $a['pending_transfer'] ) : ?>

            <p>
                <?php _e( 'You already has transferred this and your transfer is pending.', 'gamipress-transfers' ); ?>

                <?php if( $a['transfer_details_link'] ) : ?>

                    <?php echo sprintf(
                        __( 'You can check the transfer details %s.', 'gamipress-transfers' ),
                        '<a href="' . $a['transfer_details_link'] . '">' . __( 'here', 'gamipress-transfers' ) . '</a>'
                    ); ?>

                <?php endif; ?>
            </p>

        <?php else : ?>

            <?php // Setup the select achievement form ?>
            <?php if( $a['select_achievement'] === 'yes' ) : ?>

                <fieldset class="gamipress-transfers-achievement-transfer-form-achievement">

                    <p id="<?php echo $a['form_id']; ?>-achievement" class="gamipress-transfers-achievement-transfer-form-achievement-input">

                        <label for="<?php echo $a['form_id']; ?>-achievement-label"><?php _e( 'Achievement to transfer:', 'gamipress-transfers' ); ?></label>

                        <input
                            id="<?php echo $a['form_id']; ?>-achievement-input"
                            class="gamipress-transfers-achievement-transfer-form-achievement-input"
                            name="<?php echo $a['form_id']; ?>-achievement"
                            type="text"
                            placeholder="<?php _e( 'Enter the achievement name', 'gamipress-transfers' ) ?>"
                            value="<?php if( $achievement_id ) : echo gamipress_get_post_field( 'post_title', $achievement_id ); endif; ?>">

                    </p>

                </fieldset>

            <?php endif; ?>

            <?php // Setup the recipient form ?>
            <?php gamipress_get_template_part( 'recipient-form', $a['achievement_type'] ); ?>

            <?php
            /**
             * Before render achievement transfer form submit
             *
             * @since 1.1.3
             *
             * @param integer     $user_id          User ID
             * @param integer     $recipient_id     Recipient ID (if is set)
             * @param integer     $achievement_id   Achievement ID (if is set)
             * @param array       $template_args    Template received arguments
             */
            do_action( 'gamipress_transfers_before_achievement_transfer_form_submit', $user_id, $a['recipient_id'], $achievement_id, $a ); ?>

            <?php // Setup submit actions ?>
            <p class="gamipress-transfers-form-submit gamipress-transfers-achievement-transfer-form-submit">
                <?php // Loading spinner ?>
                <span class="gamipress-spinner" style="display: none;"></span>
                <input type="submit" id="<?php echo $a['form_id']; ?>-submit-button" class="gamipress-transfers-form-submit-button gamipress-transfers-achievement-transfer-form-submit-button" value="<?php echo $a['button_text']; ?>">
            </p>

            <?php // Output hidden fields ?>
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'gamipress_transfers_transfer_form' ); ?>">
            <input type="hidden" name="referrer" value="<?php echo get_the_permalink(); ?>">
            <input type="hidden" name="transfer_type" value="achievement">
            <input type="hidden" name="transfer_key" value="<?php echo $a['transfer_key']; ?>">
            <input type="hidden" name="achievement_type" value="<?php echo $a['achievement_type']; ?>">
            <input type="hidden" name="recipient_id" value="<?php echo $a['recipient_id']; ?>">
            <input type="hidden" name="achievement_id" value="<?php echo $achievement_id; ?>">

        <?php endif; ?>

        <?php
        /**
         * After render transfer form
         *
         * @since 1.0.0
         *
         * @param integer     $user_id          User ID
         * @param integer     $recipient_id     Recipient ID (if is set)
         * @param integer     $achievement_id   Achievement ID (if is set)
         * @param array       $template_args    Template received arguments
         */
        do_action( 'gamipress_transfers_after_achievement_transfer_form', $user_id, $a['recipient_id'], $achievement_id, $a ); ?>

    </form>

</fieldset>
