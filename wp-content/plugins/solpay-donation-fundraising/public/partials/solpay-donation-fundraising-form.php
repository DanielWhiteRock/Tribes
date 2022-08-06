<?php

$splTokenLabel = 'SOL';

if ( array_key_exists( $settings['spl_token'], SOLPAY_DONATION_FUNDRAISING_SPL_TOKENS ) ) {
    $splTokenLabel = SOLPAY_DONATION_FUNDRAISING_SPL_TOKENS [ $settings['spl_token'] ];
}

?>

<section class="solpay-donation-form">
    <input type="hidden" name="solpay_donation_wallet" value="<?php echo esc_attr( $settings['wallet_address'] ); ?>"/>
    <input type="hidden" name="solpay_donation_spl_token" value="<?php echo esc_attr( $settings['spl_token'] ); ?>"/>
    <input type="hidden" name="solpay_donation_label" value="<?php echo esc_attr( $transactionLabel ); ?>"/>

    <div class="solpay-donation-form__amount-container">
        <?php if ( !empty( $settings['predefined_amounts'] ) ) : ?>

            <div class="solpay-donation-form__row">
                <label class="solpay-donation-form__label solpay-donation-form__amount-type-label">
                    <input
                        type="radio" name="solpay_donation_amount_type"
                        class="solpay-donation-form__amount-type-input js-change-amount-type" value="predefined"
                    />

                    <?php if ( !empty( $settings['custom_amount_input'] ) ) : ?>
                        <span class="solpay-donation-form__amount-type-checkmark"></span>
                    <?php endif; ?>

                    <span class="solpay-donation-form__label__text">
                        <?php _e( 'Choose predefined amount', 'solpay-donation-fundraising' ); ?>
                    </span>
                </label>

                <div class="solpay-donation-form__predefined-amounts">
                    <?php foreach ( $settings['predefined_amounts'] as $amount ) : ?>
                        <label class="solpay-donation-form__predefined-amount-label">
                            <input
                                type="radio"
                                name="solpay_donation_predefined_amount"
                                value="<?php echo esc_attr( $amount ); ?>"
                                class="solpay-donation-form__input solpay-donation-form__predefined-amount-input"
                            />
                            <span class="solpay-donation-form__predefined-amount-text">
                                <?php echo esc_html( $amount . ' ' . $splTokenLabel ); ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php endif; ?>

        <?php if ( !empty( $settings['custom_amount_input'] ) ) : ?>

            <?php if ( !empty( $settings['predefined_amounts'] ) ) : ?>
                <div class="solpay-donation-form__amount-type-delimiter">
                    <?php _e( 'OR', 'solpay-donation-fundraising' ); ?>
                </div>
            <?php endif; ?>

            <div class="solpay-donation-form__row">
                <label class="solpay-donation-form__label solpay-donation-form__amount-type-label">
                    <input
                        type="radio" name="solpay_donation_amount_type"
                        class="solpay-donation-form__amount-type-input js-change-amount-type" value="custom"
                    />

                    <?php if ( !empty( $settings['predefined_amounts'] ) ) : ?>
                        <span class="solpay-donation-form__amount-type-checkmark"></span>
                    <?php endif; ?>

                    <span class="solpay-donation-form__label__text">
                        <?php echo __( 'Enter custom amount', 'solpay-donation-fundraising' ); ?>
                    </span>
                </label>

                <label class="solpay-donation-form__label">
                    <input
                        name="solpay_donation_custom_amount"
                        type="number"
                        min="0"
                        class="solpay-donation-form__input solpay-donation-form__custom-amount-input"
                    />
                </label>
            </div>

        <?php endif; ?>

        <div class="solpay-donation-form__row">
            <div class="solpay-donation-form__chosen-amount-info js-amount-info">
                <?php _e( 'Please choose an amount', 'solpay-donation-fundraising' ); ?>
            </div>

            <button class="solpay-donation-form__donate-button js-solpay-donate-button" disabled>
                <?php _e( 'Continue', 'solpay-donation-fundraising' ); ?>
            </button>
        </div>
    </div>

    <div class="solpay-donation-form__qr-container solpay-donation-form__qr-container--hidden">
        <div class="solpay-donation-form__chosen-amount-info js-amount-info">
            <?php _e( 'Please choose an amount to donate.', 'solpay-donation-fundraising' ); ?>
        </div>

        <div class="solpay-donation-form__instructions">
            <?php _e('Scan the QR code or tap it on mobile to donate.', 'solpay-donation-fundraising' ); ?>
        </div>

        <a href="#" class="js-solpay-donation-deeplink">
            <img src="" alt="" class="solpay-donation-form__qr js-solpay-donation-qr"/>
        </a>

        <div class="solpay-donation-form__timer-container">
            <?php

            $seconds    = SOLPAY_DONATION_FUNDRAISING_VERIFICATION_TIMEOUT / 1000;
            $timerValue = new DateTime( "@$seconds" );

            echo sprintf(
                __( 'This QR code will disappear in %s seconds.',
                    'solpay-donation-fundraising'
                ),
                sprintf(
                    '<span class="solpay-donation-form__timer js-solpay-donation-timer">%s</span>',
                    $timerValue->format( 'i:s' )
                )
            );

            ?>
        </div>
    </div>

    <div class="solpay-donation-form__feedback-message solpay-donation-form__feedback-message--hidden"></div>
</section>

<script>
    window.SOLPAY_DONATION_FORM_SETTINGS = {
        verificationServiceUrl: "<?php echo esc_attr( SOLPAY_DONATION_FUNDRAISING_VERIFICATION_SERVICE_URL ); ?>",
        verificationInterval: <?php echo esc_attr( SOLPAY_DONATION_FUNDRAISING_VERIFICATION_INTERVAL ); ?>,
        verificationTimeout: <?php echo esc_attr( SOLPAY_DONATION_FUNDRAISING_VERIFICATION_TIMEOUT ); ?>,
        messages: {
            thankYouMessage: "<?php echo esc_js( $settings['thank_you_message'] ); ?>",
            noAmountChosenMessage: "<?php echo esc_attr( __( 'Please choose an amount to donate.', 'solpay-donation-fundraising' ) ); ?>",
            chosenAmountMessage: "<?php echo esc_js(
                sprintf(
                    __( 'You chose  %s.', 'solpay-donation-fundraising' ),
                    "<span class='solpay-donation-form__chosen-amount js-amount-value'></span> "
                    . "<span class='solpay-donation-form__token-label'>$splTokenLabel</span>"
                ),
            ); ?>",
        },
    }
</script>
