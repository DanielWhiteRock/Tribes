<?php
/**
 * Billing Address Form template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/purchases/billing-address-form.php
 * To override a specific achievement/points/rank type just copy it as yourtheme/gamipress/purchases/billing-address-form-{type}.php
 */
global $gamipress_purchases_template_args;

// Shorthand
$a = $gamipress_purchases_template_args;

// Setup vars
$user_id = get_current_user_id();
$user_details = gamipress_purchases_get_user_billing_details( $user_id ); ?>

<?php
/**
 * Before render billing address form
 *
 * @param $user_id          integer     User ID
 * @param $user_details     array       User billing details
 * @param $template_args    array       Template received arguments
 */
do_action( 'gamipress_purchases_before_billing_address_form', $user_id, $user_details, $a ); ?>

<fieldset class="gamipress-purchases-form-billing-address">

    <legend><?php _e( 'Billing Address', 'gamipress-purchases' ); ?></legend>

    <p class="gamipress-purchases-form-address-1">

        <label for="<?php echo $a['form_id']; ?>-address-1-input"><?php _e( 'Address Line 1', 'gamipress-purchases' ); ?><span class="gamipress-purchases-form-required">*</span></label>

        <span class="gamipress-purchases-form-description"><?php _e( 'The primary billing address.', 'gamipress-purchases' ); ?></span>

        <input
            type="text"
            id="<?php echo $a['form_id']; ?>-address-1-input"
            class="gamipress-purchases-form-address-1-input"
            name="<?php echo $a['form_id']; ?>-address-1"
            value="<?php echo $user_details['address_1']; ?>">

    </p>

    <p class="gamipress-purchases-form-address-2">

        <label for="<?php echo $a['form_id']; ?>-address-2-input"><?php _e( 'Address Line 2 (Optional)', 'gamipress-purchases' ); ?></label>

        <span class="gamipress-purchases-form-description"><?php _e( 'The suite, apt no, PO box, etc, associated with your billing address.', 'gamipress-purchases' ); ?></span>

        <input
            type="text"
            id="<?php echo $a['form_id']; ?>-address-2-input"
            class="gamipress-purchases-form-address-2-input"
            name="<?php echo $a['form_id']; ?>-address-2"
            value="<?php echo $user_details['address_2']; ?>">

    </p>

    <p class="gamipress-purchases-form-city">

        <label for="<?php echo $a['form_id']; ?>-city-input"><?php _e( 'City', 'gamipress-purchases' ); ?><span class="gamipress-purchases-form-required">*</span></label>

        <span class="gamipress-purchases-form-description"><?php _e( 'The city for your billing address.', 'gamipress-purchases' ); ?></span>

        <input
            type="text"
            id="<?php echo $a['form_id']; ?>-city-input"
            class="gamipress-purchases-form-city-input"
            name="<?php echo $a['form_id']; ?>-city"
            value="<?php echo $user_details['city']; ?>">

    </p>

    <p class="gamipress-purchases-form-postcode">

        <label for="<?php echo $a['form_id']; ?>-postcode-input"><?php _e( 'Postal Code / Zip', 'gamipress-purchases' ); ?><span class="gamipress-purchases-form-required">*</span></label>

        <span class="gamipress-purchases-form-description"><?php _e( 'The postal code or zip for your billing address.', 'gamipress-purchases' ); ?></span>

        <input
            type="text"
            id="<?php echo $a['form_id']; ?>-postcode-input"
            class="gamipress-purchases-form-postcode-input"
            name="<?php echo $a['form_id']; ?>-postcode"
            value="<?php echo $user_details['postcode']; ?>">

    </p>

    <p class="gamipress-purchases-form-country">

        <label for="<?php echo $a['form_id']; ?>-country-select"><?php _e( 'Country', 'gamipress-purchases' ); ?><span class="gamipress-purchases-form-required">*</span></label>

        <span class="gamipress-purchases-form-description"><?php _e( 'The country for your billing address.', 'gamipress-purchases' ); ?></span>

        <select
            id="<?php echo $a['form_id']; ?>-country-select"
            class="gamipress-purchases-form-country-select"
            name="<?php echo $a['form_id']; ?>-country"
            value="<?php echo $user_details['country']; ?>">

            <?php
            $countries_options = gamipress_purchases_get_countries();
            $countries_options = array_merge( array( '' => __( 'Choose a country', 'gamipress-purchases' ) ),  $countries_options );

            foreach( $countries_options as $country_code => $country_label ) : ?>
                <option value="<?php echo $country_code; ?>" <?php selected( $user_details['country'], $country_code ); ?>><?php echo $country_label; ?></option>
            <?php endforeach; ?>

        </select>

    </p>

    <p class="gamipress-purchases-form-state">

        <label for="<?php echo $a['form_id']; ?>-state-input"><?php _e( 'State / Province', 'gamipress-purchases' ); ?><span class="gamipress-purchases-form-required">*</span></label>

        <span class="gamipress-purchases-form-description"><?php _e( 'The state or province for your billing address.', 'gamipress-purchases' ); ?></span>

        <input
            type="text"
            id="<?php echo $a['form_id']; ?>-state-input"
            class="gamipress-purchases-form-state-input"
            name="<?php echo $a['form_id']; ?>-state"
            value="<?php echo $user_details['state']; ?>">

    </p>

</fieldset>

<?php
/**
 * After render billing address form
 *
 * @param $user_id          integer     User ID
 * @param $user_details     array       User billing details
 * @param $template_args    array       Template received arguments
 */
do_action( 'gamipress_purchases_after_billing_address_form', $user_id, $user_details, $a ); ?>
