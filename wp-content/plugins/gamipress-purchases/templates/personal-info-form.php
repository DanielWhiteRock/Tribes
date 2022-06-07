<?php
/**
 * Personal Info Form template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/purchases/personal-info-form.php
 * To override a specific achievement/points/rank type just copy it as yourtheme/gamipress/purchases/personal-info-form-{type}.php
 */
global $gamipress_purchases_template_args;

// Shorthand
$a = $gamipress_purchases_template_args;

// Setup vars
$user_id = get_current_user_id();
$user_details = gamipress_purchases_get_user_billing_details( $user_id ); ?>

<?php
/**
 * Before render personal info form
 *
 * @param $user_id          integer     User ID
 * @param $user_details     array       User billing details
 * @param $template_args    array       Template received arguments
 */
do_action( 'gamipress_purchases_before_personal_info_form', $user_id, $user_details, $a ); ?>

<fieldset class="gamipress-purchases-form-personal-info">

    <legend><?php _e( 'Personal Info', 'gamipress-purchases' ); ?></legend>

    <p class="gamipress-purchases-form-email">

        <label for="<?php echo $a['form_id']; ?>-email-input"><?php _e( 'Email Address', 'gamipress-purchases' ); ?><span class="gamipress-purchases-form-required">*</span></label>

        <span class="gamipress-purchases-form-description"><?php _e( 'Address to send the purchase receipt.', 'gamipress-purchases' ); ?></span>

        <input
            type="email"
            id="<?php echo $a['form_id']; ?>-email-input"
            class="gamipress-purchases-form-email-input"
            name="<?php echo $a['form_id']; ?>-email"
            value="<?php echo $user_details['email']; ?>">

    </p>

    <p class="gamipress-purchases-form-first-name">

        <label for="<?php echo $a['form_id']; ?>-first-name-input"><?php _e( 'First Name', 'gamipress-purchases' ); ?><span class="gamipress-purchases-form-required">*</span></label>

        <span class="gamipress-purchases-form-description"><?php _e( 'Used to personalize your account experience.', 'gamipress-purchases' ); ?></span>

        <input
            type="text"
            id="<?php echo $a['form_id']; ?>-first-name-input"
            class="gamipress-purchases-form-first-name-input"
            name="<?php echo $a['form_id']; ?>-first-name"
            value="<?php echo $user_details['first_name']; ?>">

    </p>

    <p class="gamipress-purchases-form-last-name">

        <label for="<?php echo $a['form_id']; ?>-last-name-input"><?php _e( 'Last Name', 'gamipress-purchases' ); ?><span class="gamipress-purchases-form-required">*</span></label>

        <span class="gamipress-purchases-form-description"><?php _e( 'Used as well to personalize your account experience.', 'gamipress-purchases' ); ?></span>

        <input
            type="text"
            id="<?php echo $a['form_id']; ?>-last-name-input"
            class="gamipress-purchases-form-last-name-input"
            name="<?php echo $a['form_id']; ?>-last-name"
            value="<?php echo $user_details['last_name']; ?>">

    </p>

</fieldset>

<?php
/**
 * After render personal info form
 *
 * @param $user_id          integer     User ID
 * @param $user_details     array       User billing details
 * @param $template_args    array       Template received arguments
 */
do_action( 'gamipress_purchases_after_personal_info_form', $user_id, $user_details, $a ); ?>
