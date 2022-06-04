<?php
/**
 * Acceptance Form template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/purchases/acceptance-form.php
 * To override a specific achievement/points/rank type just copy it as yourtheme/gamipress/purchases/acceptance-form-{type}.php
 */
global $gamipress_purchases_template_args;

// Shorthand
$a = $gamipress_purchases_template_args;

// Skip this template is acceptance is set to no
if( $a['acceptance'] !== 'yes' ) {
    return;
}

// Setup vars
$user_id = get_current_user_id();
$user_details = gamipress_purchases_get_user_billing_details( $user_id ); ?>

<?php
/**
 * Before render acceptance form
 *
 * @param $user_id          integer     User ID
 * @param $user_details     array       User billing details
 * @param $template_args    array       Template received arguments
 */
do_action( 'gamipress_purchases_before_acceptance_form', $user_id, $user_details, $a ); ?>

<fieldset class="gamipress-purchases-form-acceptance">

    <p class="gamipress-purchases-form-acceptance-checkbox">

        <label for="<?php echo $a['form_id']; ?>-acceptance-input">

            <input
                type="checkbox"
                id="<?php echo $a['form_id']; ?>-acceptance-input"
                class="gamipress-purchases-form-acceptance-input"
                name="<?php echo $a['form_id']; ?>-acceptance"
                required="required"
                value="1">

            <span class="gamipress-purchases-form-acceptance-text"><span class="gamipress-purchases-form-required">*</span><?php echo $a['acceptance_text']; ?></span>

        </label>

    </p>

</fieldset>

<?php
/**
 * After render acceptance form
 *
 * @param $user_id          integer     User ID
 * @param $user_details     array       User billing details
 * @param $template_args    array       Template received arguments
 */
do_action( 'gamipress_purchases_after_acceptance_form', $user_id, $user_details, $a ); ?>
