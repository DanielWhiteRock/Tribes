<?php
/**
 * Functions
 *
 * @package GamiPress\Points_Payouts\Functions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the given points type slug conversion
 *
 * @since 1.0.0
 *
 * @param string $points_type
 *
 * @return array|bool
 */
function gamipress_points_payouts_get_conversion( $points_type = '' ) {

    $points_types = gamipress_get_points_types();

    if( ! isset( $points_types[$points_type] ) ) {
        return false;
    }

    $points_type = $points_types[$points_type];

    $conversion = gamipress_get_post_meta( $points_type['ID'], '_gamipress_points_payouts_conversion', true );

    if( empty( $conversion) ) {
        return false;
    }

    return $conversion;

}

/**
 * Convert an amount of money to points based on configured conversion rate
 *
 * @since 1.0.0
 *
 * @param string|float  $amount
 * @param string        $points_type
 *
 * @return float
 */
function gamipress_points_payouts_convert_to_points( $amount, $points_type = '' ) {

    $conversion = gamipress_points_payouts_get_conversion( $points_type );

    $conversion_rate  = $conversion['money'] / $conversion['points'];

    $converted_amount = $amount / $conversion_rate;

    /**
     * Filter the ability to round (rounding up) or not the converted amount (by default, true)
     *
     * @since 1.0.0
     *
     * @param bool          $round
     * @param string|float  $converted_amount
     * @param string|float  $amount
     * @param string        $points_type
     * @param int           $conversion_rate
     *
     * @return int|float
     */
    if( apply_filters( 'gamipress_points_payouts_convert_to_points_round', true, $converted_amount, $amount, $points_type, $conversion_rate ) )
        $converted_amount = ceil( $converted_amount );

    /**
     * Filters the converted amount of money to points based on configured conversion rate
     *
     * @since 1.0.0
     *
     * @param string|float  $converted_amount
     * @param string|float  $amount
     * @param string        $points_type
     * @param int           $conversion_rate
     *
     * @return int|float
     */
    return apply_filters( 'gamipress_points_payouts_convert_to_points', $converted_amount, $amount, $points_type, $conversion );

}

/**
 * Convert an amount of points to money based on configured conversion rate
 *
 * @since 1.0.0
 *
 * @param int       $amount
 * @param string    $points_type
 *
 * @return float
 */
function gamipress_points_payouts_convert_to_money( $amount, $points_type = '' ) {

    $amount = absint( $amount );

    $conversion = gamipress_points_payouts_get_conversion( $points_type );

    $conversion_rate  = $conversion['money'] / $conversion['points'];

    $converted_amount = $amount * $conversion_rate;

    /**
     * Filter the ability to round (rounding up) or not the converted amount (by default, false)
     *
     * @since 1.0.0
     *
     * @param bool          $round
     * @param string|float  $converted_amount
     * @param string|float  $amount
     * @param string        $points_type
     * @param int           $conversion_rate
     *
     * @return int|float
     */
    if( apply_filters( 'gamipress_points_payouts_convert_to_money_round', false, $converted_amount, $amount, $points_type, $conversion_rate ) )
        $converted_amount = ceil( $converted_amount );

    /**
     * Filters the converted amount of money to points based on configured conversion rate
     *
     * @since 1.0.0
     *
     * @param string|float  $converted_amount
     * @param string|float  $amount
     * @param string        $points_type
     * @param int           $conversion_rate
     *
     * @return int|float
     */
    return apply_filters( 'gamipress_points_payouts_convert_to_money', $converted_amount, $amount, $points_type, $conversion );

}

/**
 * Format the given amount into a price based on plugin configuration
 *
 * @since 1.0.0
 *
 * @param string|float $amount
 * @param bool $decimals
 *
 * @return string
 */
function gamipress_points_payouts_format_price( $amount, $decimals = true ) {

    $symbol = gamipress_points_payouts_get_currency_symbol();
    $position = gamipress_points_payouts_get_option( 'currency_position', 'before' );
    $formatted = gamipress_points_payouts_format_amount( $amount, $decimals );

    $price = $position === 'before' ? $symbol . $formatted : $formatted . $symbol;

    return apply_filters( 'gamipress_points_payouts_format_price', $price, $amount, $decimals, $formatted );

}

/**
 * Format the given amount into a formatted amount based on plugin configuration
 *
 * @since 1.0.0
 *
 * @param string|float $amount
 * @param bool $decimals
 *
 * @return string
 */
function gamipress_points_payouts_format_amount( $amount, $decimals = true ) {

    $thousands_sep  = gamipress_points_payouts_get_option( 'thousands_separator', ',' );
    $decimal_sep    = gamipress_points_payouts_get_option( 'decimal_separator', '.' );
    $decimals       = $decimals ? absint( gamipress_points_payouts_get_option( 'decimals', 2 ) ) : 0;

    $amount = gamipress_points_payouts_convert_to_float( $amount );

    $formatted = number_format( $amount, $decimals, $decimal_sep, $thousands_sep );

    return apply_filters( 'gamipress_points_payouts_format_amount', $formatted, $amount, $decimals, $decimal_sep, $thousands_sep );

}

/**
 * Format the given amount into a PHP float number
 *
 * @since 1.0.0
 *
 * @param string|float $amount
 *
 * @return float
 */
function gamipress_points_payouts_convert_to_float( $amount ) {

    $thousands_sep  = gamipress_points_payouts_get_option( 'thousands_separator', ',' );
    $decimal_sep    = gamipress_points_payouts_get_option( 'decimal_separator', '.' );

    // Format the amount
    if ( $decimal_sep === ',' && false !== ( $sep_found = strpos( $amount, $decimal_sep ) ) ) {
        $whole = substr( $amount, 0, $sep_found );
        $part = substr( $amount, $sep_found + 1, ( strlen( $amount ) - 1 ) );
        $amount = $whole . '.' . $part;
    }

    // Strip , from the amount (if set as the thousands separator)
    if ( $thousands_sep === ',' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
        $amount = str_replace( ',', '', $amount );
    }

    // Strip ' ' from the amount (if set as the thousands separator)
    if ( $thousands_sep === ' ' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
        $amount = str_replace( ' ', '', $amount );
    }

    if ( empty( $amount ) ) {
        $amount = 0;
    }

    return floatval( $amount );

}

/**
 * Get Base Currency Code.
 *
 * @since 1.0.0
 *
 * @return string
 */
function gamipress_points_payouts_get_currency() {
    return apply_filters( 'gamipress_points_payouts_currency', gamipress_points_payouts_get_option( 'currency', 'USD' ) );
}

/**
 * Get full list of currency codes.
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_points_payouts_get_currencies() {
    static $gamipress_points_payouts_currencies;

    if ( ! isset( $gamipress_points_payouts_currencies ) ) {
        $gamipress_points_payouts_currencies = array_unique(
            apply_filters( 'gamipress_points_payouts_currencies',
                array(
                    'AED' => __( 'United Arab Emirates dirham', 'gamipress-points-payouts' ),
                    'AFN' => __( 'Afghan afghani', 'gamipress-points-payouts' ),
                    'ALL' => __( 'Albanian lek', 'gamipress-points-payouts' ),
                    'AMD' => __( 'Armenian dram', 'gamipress-points-payouts' ),
                    'ANG' => __( 'Netherlands Antillean guilder', 'gamipress-points-payouts' ),
                    'AOA' => __( 'Angolan kwanza', 'gamipress-points-payouts' ),
                    'ARS' => __( 'Argentine peso', 'gamipress-points-payouts' ),
                    'AUD' => __( 'Australian dollar', 'gamipress-points-payouts' ),
                    'AWG' => __( 'Aruban florin', 'gamipress-points-payouts' ),
                    'AZN' => __( 'Azerbaijani manat', 'gamipress-points-payouts' ),
                    'BAM' => __( 'Bosnia and Herzegovina convertible mark', 'gamipress-points-payouts' ),
                    'BBD' => __( 'Barbadian dollar', 'gamipress-points-payouts' ),
                    'BDT' => __( 'Bangladeshi taka', 'gamipress-points-payouts' ),
                    'BGN' => __( 'Bulgarian lev', 'gamipress-points-payouts' ),
                    'BHD' => __( 'Bahraini dinar', 'gamipress-points-payouts' ),
                    'BIF' => __( 'Burundian franc', 'gamipress-points-payouts' ),
                    'BMD' => __( 'Bermudian dollar', 'gamipress-points-payouts' ),
                    'BND' => __( 'Brunei dollar', 'gamipress-points-payouts' ),
                    'BOB' => __( 'Bolivian boliviano', 'gamipress-points-payouts' ),
                    'BRL' => __( 'Brazilian real', 'gamipress-points-payouts' ),
                    'BSD' => __( 'Bahamian dollar', 'gamipress-points-payouts' ),
                    'BTC' => __( 'Bitcoin', 'gamipress-points-payouts' ),
                    'BTN' => __( 'Bhutanese ngultrum', 'gamipress-points-payouts' ),
                    'BWP' => __( 'Botswana pula', 'gamipress-points-payouts' ),
                    'BYR' => __( 'Belarusian ruble', 'gamipress-points-payouts' ),
                    'BZD' => __( 'Belize dollar', 'gamipress-points-payouts' ),
                    'CAD' => __( 'Canadian dollar', 'gamipress-points-payouts' ),
                    'CDF' => __( 'Congolese franc', 'gamipress-points-payouts' ),
                    'CHF' => __( 'Swiss franc', 'gamipress-points-payouts' ),
                    'CLP' => __( 'Chilean peso', 'gamipress-points-payouts' ),
                    'CNY' => __( 'Chinese yuan', 'gamipress-points-payouts' ),
                    'COP' => __( 'Colombian peso', 'gamipress-points-payouts' ),
                    'CRC' => __( 'Costa Rican col&oacute;n', 'gamipress-points-payouts' ),
                    'CUC' => __( 'Cuban convertible peso', 'gamipress-points-payouts' ),
                    'CUP' => __( 'Cuban peso', 'gamipress-points-payouts' ),
                    'CVE' => __( 'Cape Verdean escudo', 'gamipress-points-payouts' ),
                    'CZK' => __( 'Czech koruna', 'gamipress-points-payouts' ),
                    'DJF' => __( 'Djiboutian franc', 'gamipress-points-payouts' ),
                    'DKK' => __( 'Danish krone', 'gamipress-points-payouts' ),
                    'DOP' => __( 'Dominican peso', 'gamipress-points-payouts' ),
                    'DZD' => __( 'Algerian dinar', 'gamipress-points-payouts' ),
                    'EGP' => __( 'Egyptian pound', 'gamipress-points-payouts' ),
                    'ERN' => __( 'Eritrean nakfa', 'gamipress-points-payouts' ),
                    'ETB' => __( 'Ethiopian birr', 'gamipress-points-payouts' ),
                    'EUR' => __( 'Euro', 'gamipress-points-payouts' ),
                    'FJD' => __( 'Fijian dollar', 'gamipress-points-payouts' ),
                    'FKP' => __( 'Falkland Islands pound', 'gamipress-points-payouts' ),
                    'GBP' => __( 'Pound sterling', 'gamipress-points-payouts' ),
                    'GEL' => __( 'Georgian lari', 'gamipress-points-payouts' ),
                    'GGP' => __( 'Guernsey pound', 'gamipress-points-payouts' ),
                    'GHS' => __( 'Ghana cedi', 'gamipress-points-payouts' ),
                    'GIP' => __( 'Gibraltar pound', 'gamipress-points-payouts' ),
                    'GMD' => __( 'Gambian dalasi', 'gamipress-points-payouts' ),
                    'GNF' => __( 'Guinean franc', 'gamipress-points-payouts' ),
                    'GTQ' => __( 'Guatemalan quetzal', 'gamipress-points-payouts' ),
                    'GYD' => __( 'Guyanese dollar', 'gamipress-points-payouts' ),
                    'HKD' => __( 'Hong Kong dollar', 'gamipress-points-payouts' ),
                    'HNL' => __( 'Honduran lempira', 'gamipress-points-payouts' ),
                    'HRK' => __( 'Croatian kuna', 'gamipress-points-payouts' ),
                    'HTG' => __( 'Haitian gourde', 'gamipress-points-payouts' ),
                    'HUF' => __( 'Hungarian forint', 'gamipress-points-payouts' ),
                    'IDR' => __( 'Indonesian rupiah', 'gamipress-points-payouts' ),
                    'ILS' => __( 'Israeli new shekel', 'gamipress-points-payouts' ),
                    'IMP' => __( 'Manx pound', 'gamipress-points-payouts' ),
                    'INR' => __( 'Indian rupee', 'gamipress-points-payouts' ),
                    'IQD' => __( 'Iraqi dinar', 'gamipress-points-payouts' ),
                    'IRR' => __( 'Iranian rial', 'gamipress-points-payouts' ),
                    'IRT' => __( 'Iranian toman', 'gamipress-points-payouts' ),
                    'ISK' => __( 'Icelandic kr&oacute;na', 'gamipress-points-payouts' ),
                    'JEP' => __( 'Jersey pound', 'gamipress-points-payouts' ),
                    'JMD' => __( 'Jamaican dollar', 'gamipress-points-payouts' ),
                    'JOD' => __( 'Jordanian dinar', 'gamipress-points-payouts' ),
                    'JPY' => __( 'Japanese yen', 'gamipress-points-payouts' ),
                    'KES' => __( 'Kenyan shilling', 'gamipress-points-payouts' ),
                    'KGS' => __( 'Kyrgyzstani som', 'gamipress-points-payouts' ),
                    'KHR' => __( 'Cambodian riel', 'gamipress-points-payouts' ),
                    'KMF' => __( 'Comorian franc', 'gamipress-points-payouts' ),
                    'KPW' => __( 'North Korean won', 'gamipress-points-payouts' ),
                    'KRW' => __( 'South Korean won', 'gamipress-points-payouts' ),
                    'KWD' => __( 'Kuwaiti dinar', 'gamipress-points-payouts' ),
                    'KYD' => __( 'Cayman Islands dollar', 'gamipress-points-payouts' ),
                    'KZT' => __( 'Kazakhstani tenge', 'gamipress-points-payouts' ),
                    'LAK' => __( 'Lao kip', 'gamipress-points-payouts' ),
                    'LBP' => __( 'Lebanese pound', 'gamipress-points-payouts' ),
                    'LKR' => __( 'Sri Lankan rupee', 'gamipress-points-payouts' ),
                    'LRD' => __( 'Liberian dollar', 'gamipress-points-payouts' ),
                    'LSL' => __( 'Lesotho loti', 'gamipress-points-payouts' ),
                    'LYD' => __( 'Libyan dinar', 'gamipress-points-payouts' ),
                    'MAD' => __( 'Moroccan dirham', 'gamipress-points-payouts' ),
                    'MDL' => __( 'Moldovan leu', 'gamipress-points-payouts' ),
                    'MGA' => __( 'Malagasy ariary', 'gamipress-points-payouts' ),
                    'MKD' => __( 'Macedonian denar', 'gamipress-points-payouts' ),
                    'MMK' => __( 'Burmese kyat', 'gamipress-points-payouts' ),
                    'MNT' => __( 'Mongolian t&ouml;gr&ouml;g', 'gamipress-points-payouts' ),
                    'MOP' => __( 'Macanese pataca', 'gamipress-points-payouts' ),
                    'MRO' => __( 'Mauritanian ouguiya', 'gamipress-points-payouts' ),
                    'MUR' => __( 'Mauritian rupee', 'gamipress-points-payouts' ),
                    'MVR' => __( 'Maldivian rufiyaa', 'gamipress-points-payouts' ),
                    'MWK' => __( 'Malawian kwacha', 'gamipress-points-payouts' ),
                    'MXN' => __( 'Mexican peso', 'gamipress-points-payouts' ),
                    'MYR' => __( 'Malaysian ringgit', 'gamipress-points-payouts' ),
                    'MZN' => __( 'Mozambican metical', 'gamipress-points-payouts' ),
                    'NAD' => __( 'Namibian dollar', 'gamipress-points-payouts' ),
                    'NGN' => __( 'Nigerian naira', 'gamipress-points-payouts' ),
                    'NIO' => __( 'Nicaraguan c&oacute;rdoba', 'gamipress-points-payouts' ),
                    'NOK' => __( 'Norwegian krone', 'gamipress-points-payouts' ),
                    'NPR' => __( 'Nepalese rupee', 'gamipress-points-payouts' ),
                    'NZD' => __( 'New Zealand dollar', 'gamipress-points-payouts' ),
                    'OMR' => __( 'Omani rial', 'gamipress-points-payouts' ),
                    'PAB' => __( 'Panamanian balboa', 'gamipress-points-payouts' ),
                    'PEN' => __( 'Peruvian nuevo sol', 'gamipress-points-payouts' ),
                    'PGK' => __( 'Papua New Guinean kina', 'gamipress-points-payouts' ),
                    'PHP' => __( 'Philippine peso', 'gamipress-points-payouts' ),
                    'PKR' => __( 'Pakistani rupee', 'gamipress-points-payouts' ),
                    'PLN' => __( 'Polish z&#x142;oty', 'gamipress-points-payouts' ),
                    'PRB' => __( 'Transnistrian ruble', 'gamipress-points-payouts' ),
                    'PYG' => __( 'Paraguayan guaran&iacute;', 'gamipress-points-payouts' ),
                    'QAR' => __( 'Qatari riyal', 'gamipress-points-payouts' ),
                    'RON' => __( 'Romanian leu', 'gamipress-points-payouts' ),
                    'RSD' => __( 'Serbian dinar', 'gamipress-points-payouts' ),
                    'RUB' => __( 'Russian ruble', 'gamipress-points-payouts' ),
                    'RWF' => __( 'Rwandan franc', 'gamipress-points-payouts' ),
                    'SAR' => __( 'Saudi riyal', 'gamipress-points-payouts' ),
                    'SBD' => __( 'Solomon Islands dollar', 'gamipress-points-payouts' ),
                    'SCR' => __( 'Seychellois rupee', 'gamipress-points-payouts' ),
                    'SDG' => __( 'Sudanese pound', 'gamipress-points-payouts' ),
                    'SEK' => __( 'Swedish krona', 'gamipress-points-payouts' ),
                    'SGD' => __( 'Singapore dollar', 'gamipress-points-payouts' ),
                    'SHP' => __( 'Saint Helena pound', 'gamipress-points-payouts' ),
                    'SLL' => __( 'Sierra Leonean leone', 'gamipress-points-payouts' ),
                    'SOS' => __( 'Somali shilling', 'gamipress-points-payouts' ),
                    'SRD' => __( 'Surinamese dollar', 'gamipress-points-payouts' ),
                    'SSP' => __( 'South Sudanese pound', 'gamipress-points-payouts' ),
                    'STD' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'gamipress-points-payouts' ),
                    'SYP' => __( 'Syrian pound', 'gamipress-points-payouts' ),
                    'SZL' => __( 'Swazi lilangeni', 'gamipress-points-payouts' ),
                    'THB' => __( 'Thai baht', 'gamipress-points-payouts' ),
                    'TJS' => __( 'Tajikistani somoni', 'gamipress-points-payouts' ),
                    'TMT' => __( 'Turkmenistan manat', 'gamipress-points-payouts' ),
                    'TND' => __( 'Tunisian dinar', 'gamipress-points-payouts' ),
                    'TOP' => __( 'Tongan pa&#x2bb;anga', 'gamipress-points-payouts' ),
                    'TRY' => __( 'Turkish lira', 'gamipress-points-payouts' ),
                    'TTD' => __( 'Trinidad and Tobago dollar', 'gamipress-points-payouts' ),
                    'TWD' => __( 'New Taiwan dollar', 'gamipress-points-payouts' ),
                    'TZS' => __( 'Tanzanian shilling', 'gamipress-points-payouts' ),
                    'UAH' => __( 'Ukrainian hryvnia', 'gamipress-points-payouts' ),
                    'UGX' => __( 'Ugandan shilling', 'gamipress-points-payouts' ),
                    'USD' => __( 'United States dollar', 'gamipress-points-payouts' ),
                    'UYU' => __( 'Uruguayan peso', 'gamipress-points-payouts' ),
                    'UZS' => __( 'Uzbekistani som', 'gamipress-points-payouts' ),
                    'VEF' => __( 'Venezuelan bol&iacute;var', 'gamipress-points-payouts' ),
                    'VND' => __( 'Vietnamese &#x111;&#x1ed3;ng', 'gamipress-points-payouts' ),
                    'VUV' => __( 'Vanuatu vatu', 'gamipress-points-payouts' ),
                    'WST' => __( 'Samoan t&#x101;l&#x101;', 'gamipress-points-payouts' ),
                    'XAF' => __( 'Central African CFA franc', 'gamipress-points-payouts' ),
                    'XCD' => __( 'East Caribbean dollar', 'gamipress-points-payouts' ),
                    'XOF' => __( 'West African CFA franc', 'gamipress-points-payouts' ),
                    'XPF' => __( 'CFP franc', 'gamipress-points-payouts' ),
                    'YER' => __( 'Yemeni rial', 'gamipress-points-payouts' ),
                    'ZAR' => __( 'South African rand', 'gamipress-points-payouts' ),
                    'ZMW' => __( 'Zambian kwacha', 'gamipress-points-payouts' ),
                )
            )
        );
    }

    return $gamipress_points_payouts_currencies;
}


/**
 * Get Currency symbol.
 *
 * @param string $currency (default: '')
 * @return string
 */
function gamipress_points_payouts_get_currency_symbol( $currency = '' ) {
    if ( ! $currency ) {
        $currency = gamipress_points_payouts_get_currency();
    }

    $symbols = apply_filters( 'gamipress_points_payouts_currency_symbols', array(
        'AED' => '&#x62f;.&#x625;',
        'AFN' => '&#x60b;',
        'ALL' => 'L',
        'AMD' => 'AMD',
        'ANG' => '&fnof;',
        'AOA' => 'Kz',
        'ARS' => '&#36;',
        'AUD' => '&#36;',
        'AWG' => 'Afl.',
        'AZN' => 'AZN',
        'BAM' => 'KM',
        'BBD' => '&#36;',
        'BDT' => '&#2547;&nbsp;',
        'BGN' => '&#1083;&#1074;.',
        'BHD' => '.&#x62f;.&#x628;',
        'BIF' => 'Fr',
        'BMD' => '&#36;',
        'BND' => '&#36;',
        'BOB' => 'Bs.',
        'BRL' => '&#82;&#36;',
        'BSD' => '&#36;',
        'BTC' => '&#3647;',
        'BTN' => 'Nu.',
        'BWP' => 'P',
        'BYR' => 'Br',
        'BZD' => '&#36;',
        'CAD' => '&#36;',
        'CDF' => 'Fr',
        'CHF' => '&#67;&#72;&#70;',
        'CLP' => '&#36;',
        'CNY' => '&yen;',
        'COP' => '&#36;',
        'CRC' => '&#x20a1;',
        'CUC' => '&#36;',
        'CUP' => '&#36;',
        'CVE' => '&#36;',
        'CZK' => '&#75;&#269;',
        'DJF' => 'Fr',
        'DKK' => 'DKK',
        'DOP' => 'RD&#36;',
        'DZD' => '&#x62f;.&#x62c;',
        'EGP' => 'EGP',
        'ERN' => 'Nfk',
        'ETB' => 'Br',
        'EUR' => '&euro;',
        'FJD' => '&#36;',
        'FKP' => '&pound;',
        'GBP' => '&pound;',
        'GEL' => '&#x10da;',
        'GGP' => '&pound;',
        'GHS' => '&#x20b5;',
        'GIP' => '&pound;',
        'GMD' => 'D',
        'GNF' => 'Fr',
        'GTQ' => 'Q',
        'GYD' => '&#36;',
        'HKD' => '&#36;',
        'HNL' => 'L',
        'HRK' => 'Kn',
        'HTG' => 'G',
        'HUF' => '&#70;&#116;',
        'IDR' => 'Rp',
        'ILS' => '&#8362;',
        'IMP' => '&pound;',
        'INR' => '&#8377;',
        'IQD' => '&#x639;.&#x62f;',
        'IRR' => '&#xfdfc;',
        'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
        'ISK' => 'kr.',
        'JEP' => '&pound;',
        'JMD' => '&#36;',
        'JOD' => '&#x62f;.&#x627;',
        'JPY' => '&yen;',
        'KES' => 'KSh',
        'KGS' => '&#x441;&#x43e;&#x43c;',
        'KHR' => '&#x17db;',
        'KMF' => 'Fr',
        'KPW' => '&#x20a9;',
        'KRW' => '&#8361;',
        'KWD' => '&#x62f;.&#x643;',
        'KYD' => '&#36;',
        'KZT' => 'KZT',
        'LAK' => '&#8365;',
        'LBP' => '&#x644;.&#x644;',
        'LKR' => '&#xdbb;&#xdd4;',
        'LRD' => '&#36;',
        'LSL' => 'L',
        'LYD' => '&#x644;.&#x62f;',
        'MAD' => '&#x62f;.&#x645;.',
        'MDL' => 'MDL',
        'MGA' => 'Ar',
        'MKD' => '&#x434;&#x435;&#x43d;',
        'MMK' => 'Ks',
        'MNT' => '&#x20ae;',
        'MOP' => 'P',
        'MRO' => 'UM',
        'MUR' => '&#x20a8;',
        'MVR' => '.&#x783;',
        'MWK' => 'MK',
        'MXN' => '&#36;',
        'MYR' => '&#82;&#77;',
        'MZN' => 'MT',
        'NAD' => '&#36;',
        'NGN' => '&#8358;',
        'NIO' => 'C&#36;',
        'NOK' => '&#107;&#114;',
        'NPR' => '&#8360;',
        'NZD' => '&#36;',
        'OMR' => '&#x631;.&#x639;.',
        'PAB' => 'B/.',
        'PEN' => 'S/.',
        'PGK' => 'K',
        'PHP' => '&#8369;',
        'PKR' => '&#8360;',
        'PLN' => '&#122;&#322;',
        'PRB' => '&#x440;.',
        'PYG' => '&#8370;',
        'QAR' => '&#x631;.&#x642;',
        'RMB' => '&yen;',
        'RON' => 'lei',
        'RSD' => '&#x434;&#x438;&#x43d;.',
        'RUB' => '&#8381;',
        'RWF' => 'Fr',
        'SAR' => '&#x631;.&#x633;',
        'SBD' => '&#36;',
        'SCR' => '&#x20a8;',
        'SDG' => '&#x62c;.&#x633;.',
        'SEK' => '&#107;&#114;',
        'SGD' => '&#36;',
        'SHP' => '&pound;',
        'SLL' => 'Le',
        'SOS' => 'Sh',
        'SRD' => '&#36;',
        'SSP' => '&pound;',
        'STD' => 'Db',
        'SYP' => '&#x644;.&#x633;',
        'SZL' => 'L',
        'THB' => '&#3647;',
        'TJS' => '&#x405;&#x41c;',
        'TMT' => 'm',
        'TND' => '&#x62f;.&#x62a;',
        'TOP' => 'T&#36;',
        'TRY' => '&#8378;',
        'TTD' => '&#36;',
        'TWD' => '&#78;&#84;&#36;',
        'TZS' => 'Sh',
        'UAH' => '&#8372;',
        'UGX' => 'UGX',
        'USD' => '&#36;',
        'UYU' => '&#36;',
        'UZS' => 'UZS',
        'VEF' => 'Bs F',
        'VND' => '&#8363;',
        'VUV' => 'Vt',
        'WST' => 'T',
        'XAF' => 'CFA',
        'XCD' => '&#36;',
        'XOF' => 'CFA',
        'XPF' => 'Fr',
        'YER' => '&#xfdfc;',
        'ZAR' => '&#82;',
        'ZMW' => 'ZK',
    ) );

    $currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';

    return apply_filters( 'gamipress_points_payouts_currency_symbol', $currency_symbol, $currency );
}