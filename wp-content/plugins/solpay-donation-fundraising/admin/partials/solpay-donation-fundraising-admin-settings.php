<?php if ( ! current_user_can( 'manage_options' ) ) : ?>

    <p> <?php __( 'You are not authorized to perform this operation.', 'solpay-donation-fundraising' ); ?> </p>

    <?php die; ?>

<?php endif; ?>

<?php

$formFieldPrefix = $this->plugin_name . '_settings';

$splTokens = SOLPAY_DONATION_FUNDRAISING_SPL_TOKENS;
$settings = get_option( SOLPAY_DONATION_FUNDRAISING_OPTION_NAME );
$nonce = wp_create_nonce( $this->plugin_name . '_nonce' );

$flashMessage = get_transient( SOLPAY_DONATION_FUNDRAISING_FLASH_TRANSIENT );

if ( $flashMessage !== false ) {
    delete_transient( SOLPAY_DONATION_FUNDRAISING_FLASH_TRANSIENT );
}

$defaultPredefinedAmounts = "1\n5\n10\n20";

?>

<div class="wrap">
    <h1>
        <?php _e( 'Solpay Donation & Fundraising Settings', 'solpay-donation-fundraising' ); ?>
    </h1>

    <p>
        <?php

        echo sprintf(
            __('After filling in your merchant wallet and updating your settings, you can use the following shortcode: %s in any page or sidebar / footer widget to display the form.', 'solpay-donation-fundraising'),
            '<code>[solpay_donation_form]</code>'
        );

        ?>
    </p>

    <?php if ( $flashMessage !== false ) : ?>

        <div id="solpay_donations_form_feedback">
            <div class="notice notice-<?php echo esc_attr( $flashMessage['type'] ); ?> is-dismissible">
                <p>
                    <strong>
                        <?php echo esc_html( $flashMessage[ 'text' ] ); ?>
                    </strong>
                </p>
            </div>
        </div>

    <?php endif; ?>

    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST">
        <input type="hidden" name="action" value="<?php echo esc_attr( $this->plugin_name . '_save_settings' ); ?>">
        <input type="hidden" name="<?php echo esc_attr( $formFieldPrefix . '_nonce' ); ?>" value="<?php echo esc_attr( $nonce ); ?>" />

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th>
                        <label for="<?php echo esc_attr( $formFieldPrefix . '_wallet_address' ); ?>">
                            <?php _e( 'Wallet address', 'solpay-donation-fundraising'); ?>
                        </label>
                    </th>
                    <td>
                        <input
                            id="<?php echo esc_attr( $formFieldPrefix . '_wallet_address' ); ?>"
                            name="<?php echo esc_attr( $formFieldPrefix . '_wallet_address' ); ?>"
                            type="text"
                            value="<?php echo !empty( $settings[ 'wallet_address' ] ) ? esc_attr( $settings[ 'wallet_address' ] ) : ''; ?>"
                            class="regular-text"
                            required
                        />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="<?php echo esc_attr( $formFieldPrefix . '_spl_token' ); ?>">
                            <?php _e( 'Payment coin', 'solpay-donation-fundraising'); ?>
                        </label>
                    </th>
                    <td>
                        <select
                            id="<?php echo esc_attr( $formFieldPrefix . '_spl_token' ); ?>"
                            name="<?php echo esc_attr( $formFieldPrefix . '_spl_token' ); ?>">
                            <?php foreach ( $splTokens as $splTokenAddress => $splTokenLabel ) : ?>
                                <option
                                    value="<?php echo esc_attr( $splTokenAddress ); ?>"
                                    <?php echo !empty( $settings[ 'spl_token' ] ) && $settings[ 'spl_token' ] === $splTokenAddress ? 'selected' : ''; ?>>
                                    <?php echo esc_html( $splTokenLabel ); ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="" <?php echo isset( $settings[ 'spl_token' ] ) && $settings[ 'spl_token' ] === ''  ? 'selected' : ''; ?>>SOL</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="<?php echo esc_attr( $formFieldPrefix . '_predefined_amounts' ); ?>">
                            <?php _e( 'Predefined donation amounts', 'solpay-donation-fundraising'); ?>
                        </label>
                    </th>
                    <td>
                        <?php

                        if ( isset( $settings[ 'predefined_amounts' ] ) ) {
                            $predefinedAmounts = implode( "\n", $settings[ 'predefined_amounts' ] );
                        } else {
                            $predefinedAmounts = $defaultPredefinedAmounts;
                        }

                        ?>
                        <textarea
                            id="<?php echo esc_attr( $formFieldPrefix . '_predefined_amounts' ); ?>"
                            name="<?php echo esc_attr( $formFieldPrefix . '_predefined_amounts' ); ?>"
                            rows="5"><?php echo esc_textarea( $predefinedAmounts ); ?></textarea>
                        <br/><br/>
                        <span>
                            <?php _e( 'Enter one value per line. Use "." (dot) for decimal values.', 'solpay-donation-fundraising'); ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="<?php echo esc_attr( $formFieldPrefix . '_custom_amount_input' ); ?>">
                            <?php _e( 'Allow custom amount input', 'solpay-donation-fundraising'); ?>
                        </label>
                    </th>
                    <td>
                        <input
                            id="<?php echo esc_attr( $formFieldPrefix . '_custom_amount_input' ); ?>"
                            name="<?php echo esc_attr( $formFieldPrefix . '_custom_amount_input' ); ?>"
                            type="checkbox"
                            <?php echo !empty( $settings[ 'custom_amount_input' ] ) && $settings[ 'custom_amount_input' ] === true ? 'checked' : '' ?>
                        />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="<?php echo esc_attr( $formFieldPrefix . '_thank_you_message' ); ?>">
                            <?php _e( 'Thank you message', 'solpay-donation-fundraising'); ?>
                        </label>
                    </th>
                    <td>
                        <?php

                        if ( !empty( $settings[ 'thank_you_message' ] ) ) {
                            $content = $settings[ 'thank_you_message' ];
                        } else {
                            $content = esc_html( __( 'Thank you for your donation!', 'solpay-donation-fundraising' ) );
                        }

                        wp_editor(
                            $content,
                            $formFieldPrefix . '_thank_you_message',
                            [
                                'textarea_name' => $formFieldPrefix . '_thank_you_message',
                            ]
                        );

                        ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <button class="button button-primary">
                <?php _e( 'Save settings', 'solpay-donation-fundraising' ); ?>
            </button>
        </p>
    </form>
</div>
