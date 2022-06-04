<?php
/**
 * Rank Transfer Form template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/transfers/rank-transfer-form.php
 * To override a specific rank type just copy it as yourtheme/gamipress/transfers/rank-transfer-form-{rank-type}.php
 */
global $gamipress_transfers_template_args;

// Shorthand
$a = $gamipress_transfers_template_args;

// Setup vars
$user_id = get_current_user_id();
$transfer_type = $a['transfer_type'];
$recipient = get_userdata( $a['recipient_id'] );
$rank_id = $a['id'];

// An array with user ranks IDs that are not default ranks
$user_ranks = $a['user_ranks']; ?>

<fieldset id="gamipress-transfers-form-wrapper" class="gamipress-transfers-form-wrapper gamipress-transfers-rank-transfer-form-wrapper">

    <form id="<?php echo $a['form_id']; ?>" class="gamipress-transfers-form gamipress-transfers-rank-transfer-form" action="" method="POST">

        <?php
        /**
         * Before render rank transfer form
         *
         * @since 1.0.0
         *
         * @param integer     $user_id          User ID
         * @param integer     $recipient_id     Recipient ID (if is set)
         * @param integer     $rank_id          Rank ID (if is set)
         * @param array       $template_args    Template received arguments
         */
        do_action( 'gamipress_transfers_before_rank_transfer_form', $user_id, $a['recipient_id'], $rank_id, $a ); ?>

        <fieldset class="gamipress-transfers-form-rank-wrapper">

            <legend><?php _e( 'Transfer Details', 'gamipress-transfers' ); ?></legend>

            <div class="gamipress-transfers-form-rank-render">
                <?php if( $rank_id !== 0 ) : ?>
                    <?php echo gamipress_render_rank( $rank_id, $a['template_args'] ); ?>
                <?php endif; ?>
            </div>

            <?php // Render the template args on hidden fields to render the rank through ajax ?>
            <?php // Note: Input name is set as data to prevent to be submitted ?>
            <?php foreach( $a['template_args'] as $arg => $arg_value ) : ?>
                <input type="hidden" data-name="<?php echo $arg; ?>" value="<?php echo $arg_value; ?>">
            <?php endforeach; ?>

        </fieldset>

        <?php // Check if user already has a pending transfer ?>
        <?php // Note: If select rank is set to yes, pending transfer will be false ?>
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

            <?php // Setup the select rank form ?>
            <?php if( $a['select_rank'] === 'yes' ) : ?>


                <fieldset class="gamipress-transfers-rank-transfer-form-rank">

                    <p id="<?php echo $a['form_id']; ?>-rank" class="gamipress-transfers-rank-transfer-form-rank-input">

                        <label for="<?php echo $a['form_id']; ?>-rank-label"><?php _e( 'Rank to transfer:', 'gamipress-transfers' ); ?></label>

                        <input
                            id="<?php echo $a['form_id']; ?>-rank-input"
                            class="gamipress-transfers-rank-transfer-form-rank-input"
                            name="<?php echo $a['form_id']; ?>-rank"
                            type="text"
                            placeholder="<?php _e( 'Enter the rank name', 'gamipress-transfers' ) ?>"
                            value="<?php if( $rank_id ) : echo gamipress_get_post_field( 'post_title', $rank_id ); endif; ?>">

                    </p>

                </fieldset>

            <?php endif; ?>

            <?php // Setup the recipient form ?>
            <?php gamipress_get_template_part( 'recipient-form', $a['rank_type'] ); ?>

            <?php
            /**
             * Before render rank transfer form submit
             *
             * @since 1.1.3
             *
             * @param integer     $user_id          User ID
             * @param integer     $recipient_id     Recipient ID (if is set)
             * @param integer     $rank_id          Rank ID (if is set)
             * @param array       $template_args    Template received arguments
             */
            do_action( 'gamipress_transfers_before_rank_transfer_form_submit', $user_id, $a['recipient_id'], $rank_id, $a ); ?>

            <?php // Setup submit actions ?>
            <p class="gamipress-transfers-form-submit gamipress-transfers-rank-transfer-form-submit">
                <?php // Loading spinner ?>
                <span class="gamipress-spinner" style="display: none;"></span>
                <input type="submit" id="<?php echo $a['form_id']; ?>-submit-button" class="gamipress-transfers-form-submit-button gamipress-transfers-rank-transfer-form-submit-button" value="<?php echo $a['button_text']; ?>">
            </p>

            <?php // Output hidden fields ?>
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'gamipress_transfers_transfer_form' ); ?>">
            <input type="hidden" name="referrer" value="<?php echo get_the_permalink(); ?>">
            <input type="hidden" name="transfer_type" value="rank">
            <input type="hidden" name="transfer_key" value="<?php echo $a['transfer_key']; ?>">
            <input type="hidden" name="rank_type" value="<?php echo $a['rank_type']; ?>">
            <input type="hidden" name="recipient_id" value="<?php echo $a['recipient_id']; ?>">
            <input type="hidden" name="rank_id" value="<?php echo $rank_id; ?>">

        <?php endif; ?>

        <?php
        /**
         * After render transfer form
         *
         * @since 1.0.0
         *
         * @param integer     $user_id          User ID
         * @param integer     $recipient_id     Recipient ID (if is set)
         * @param integer     $rank_id          Rank ID (if is set)
         * @param array       $template_args    Template received arguments
         */
        do_action( 'gamipress_transfers_after_rank_transfer_form', $user_id, $a['recipient_id'], $rank_id, $a ); ?>

    </form>

</fieldset>
