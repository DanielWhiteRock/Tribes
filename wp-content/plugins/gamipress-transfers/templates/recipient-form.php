<?php
/**
 * Recipient Form template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/transfers/recipient-form.php
 * To override a specific achievement/points/rank type just copy it as yourtheme/gamipress/transfers/recipient-form-{type}.php
 */
global $gamipress_transfers_template_args;

// Shorthand
$a = $gamipress_transfers_template_args;

// Setup vars
$user_id = get_current_user_id();
$recipient = get_userdata( $a['recipient_id'] ); ?>

<?php
/**
 * Before render recipient form
 *
 * @since 1.0.0
 *
 * @param integer     $user_id          User ID
 * @param integer     $recipient_id     Recipient ID (if is set)
 * @param array       $template_args    Template received arguments
 */
do_action( 'gamipress_transfers_before_recipient_form', $user_id, $a['recipient_id'], $a ); ?>

<fieldset class="gamipress-transfers-recipient-form-recipient">

    <p id="<?php echo $a['form_id']; ?>-recipient" class="gamipress-transfers-recipient-form-recipient-input">

        <label for="<?php echo $a['form_id']; ?>-recipient-label"><?php _e( 'To:', 'gamipress-transfers' ); ?></label>

        <?php if( $a['select_recipient'] === 'yes' ) : ?>
            <input
                id="<?php echo $a['form_id']; ?>-recipient-input"
                class="gamipress-transfers-recipient-form-recipient-input <?php if( $a['recipient_autocomplete'] === 'yes' ) : ?>gamipress-transfers-recipient-form-recipient-autocomplete<?php endif; ?>"
                name="<?php echo $a['form_id']; ?>-recipient"
                type="text"
                placeholder="<?php echo $a['recipient_placeholder']; ?>"
                value="<?php if( $recipient ) : echo gamipress_transfers_display_recipient( $recipient ); endif; ?>">
        <?php else : ?>

            <span class="gamipress-transfers-recipient-form-recipient-display"><?php if( $recipient ) : echo gamipress_transfers_display_recipient( $recipient ); endif; ?></span>

        <?php endif; ?>

    </p>

</fieldset>

<?php
/**
 * After render recipient form
 *
 * @since 1.0.0
 *
 * @param integer     $user_id          User ID
 * @param integer     $recipient_id     Recipient ID (if is set)
 * @param array       $template_args    Template received arguments
 */
do_action( 'gamipress_transfers_after_recipient_form', $user_id, $a['recipient_id'], $a ); ?>
