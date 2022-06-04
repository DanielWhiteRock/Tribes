<?php
/**
 * Functions
 *
 * @package     GamiPress\Purchases\Functions
 * @since       1.0.0
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
 * @return bool|array
 */
function gamipress_purchases_get_conversion( $points_type = '' ) {

    $points_types = gamipress_get_points_types();

    if( ! isset( $points_types[$points_type] ) ) {
        return false;
    }

    $points_type = $points_types[$points_type];

    $conversion = gamipress_get_post_meta( $points_type['ID'], '_gamipress_purchases_conversion', true );

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
 * @param string|float $amount
 * @param string $points_type
 *
 * @return bool|float
 */
function gamipress_purchases_convert_to_points( $amount, $points_type = '' ) {

    $conversion = gamipress_purchases_get_conversion( $points_type );

    if( ! $conversion ) {
        return false;
    }

    $conversion_rate  = $conversion['money'] / $conversion['points'];

    $converted_amount = $amount / $conversion_rate;

    return apply_filters( 'gamipress_purchases_convert_to_points', $converted_amount, $amount, $points_type, $conversion );

}

/**
 * Convert an amount of points to money based on configured conversion rate
 *
 * @since 1.0.0
 *
 * @param string|float $amount
 * @param string $points_type
 *
 * @return bool|float
 */
function gamipress_purchases_convert_to_money( $amount, $points_type = '' ) {

    $conversion = gamipress_purchases_get_conversion( $points_type );

    if( ! $conversion ) {
        return false;
    }

    $conversion_rate  = $conversion['points'] / $conversion['money'];

    $converted_amount = $amount / $conversion_rate;

    return apply_filters( 'gamipress_purchases_convert_to_points', $converted_amount, $amount, $points_type, $conversion );

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
function gamipress_purchases_format_price( $amount, $decimals = true ) {

    $symbol = gamipress_purchases_get_currency_symbol();
    $position = gamipress_purchases_get_option( 'currency_position', 'before' );
    $formatted = gamipress_purchases_format_amount( $amount, $decimals );

    $price = $position === 'before' ? $symbol . $formatted : $formatted . $symbol;

    return apply_filters( 'gamipress_purchases_format_price', $price, $amount, $decimals, $formatted );

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
function gamipress_purchases_format_amount( $amount, $decimals = true ) {

    $thousands_sep  = gamipress_purchases_get_option( 'thousands_separator', ',' );
    $decimal_sep    = gamipress_purchases_get_option( 'decimal_separator', '.' );
    $decimals       = $decimals ? absint( gamipress_purchases_get_option( 'decimals', 2 ) ) : 0;

    $amount = gamipress_purchases_convert_to_float( $amount );

    $formatted = number_format( $amount, $decimals, $decimal_sep, $thousands_sep );

    return apply_filters( 'gamipress_purchases_format_amount', $formatted, $amount, $decimals, $decimal_sep, $thousands_sep );

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
function gamipress_purchases_convert_to_float( $amount ) {

    $thousands_sep  = gamipress_purchases_get_option( 'thousands_separator', ',' );
    $decimal_sep    = gamipress_purchases_get_option( 'decimal_separator', '.' );

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
 * Return the IP address of the current visitor
 *
 * @since 1.0.0
 *
 * @return string $ip User's IP address
 */
function gamipress_purchases_get_ip() {

    $ip = '127.0.0.1';

    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
        //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        //to check ip is pass from proxy
        // can include more than 1 ip, first is the public one
        $ip = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ip[0]);
    } elseif( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Fix potential CSV returned from $_SERVER variables
    $ip_array = explode( ',', $ip );
    $ip_array = array_map( 'trim', $ip_array );

    return apply_filters( 'gamipress_purchases_get_ip', $ip_array[0] );

}

/**
 * Get the tax (as percent) based on user address
 *
 * Not: use gamipress_purchases_get_tax_rate() to get the correct rate to apply
 *
 * @since 1.0.0
 *
 * @param string $country
 * @param string $state
 * @param string $postcode
 *
 * @return float
 */
function gamipress_purchases_get_tax( $country = '', $state = '', $postcode = '' ) {

    $tax = 0.00;

    // If taxes not enabled, then return 0
    if( (bool) gamipress_purchases_get_option( 'enable_taxes', false ) ) {

        $taxes_rules = gamipress_purchases_get_option( 'taxes', array() );

        // Loop all tax rules looking for the best match
        foreach( $taxes_rules as $tax_rule ) {

            // First check the country
            if( $tax_rule['country'] !== $country ) {
                continue;
            }

            // Country tax rule
            if( empty( $tax_rule['state'] ) && empty( $tax_rule['postcode'] ) ) {
                $tax = $tax_rule['tax'];
                continue;
            }

            // State tax rule
            if( ! empty( $tax_rule['state'] ) && $tax_rule['state'] === $state ) {

                if( ! empty( $tax_rule['postcode'] ) && $tax_rule['postcode'] === $postcode ) {

                    // Postcode tax rule
                    $tax = $tax_rule['tax'];
                    continue;

                } elseif( empty( $tax_rule['postcode'] ) ) {

                    // Just apply state tax rule if postcode rule is empty
                    $tax = $tax_rule['tax'];
                    continue;

                }
            }

        }

        $tax = gamipress_purchases_convert_to_float( $tax );

        // If not tax applied, apply default tax
        if( $tax === 0 ) {
            $default_tax = gamipress_purchases_get_option( 'default_tax', '' );

            $tax = gamipress_purchases_convert_to_float( $default_tax );
        }

    }

    return apply_filters( 'gamipress_purchases_get_tax', $tax, $country, $state, $postcode );

}

/**
 * Get the tax rate based on user address tax
 *
 * @since 1.0.0
 *
 * @param string $country
 * @param string $state
 * @param string $postcode
 *
 * @return float
 */
function gamipress_purchases_get_tax_rate( $country = '', $state = '', $postcode = '' ) {

    $tax = gamipress_purchases_get_tax( $country, $state, $postcode );

    $rate = $tax / 100;

    return apply_filters( 'gamipress_purchases_get_tax_rate', $rate, $tax, $country, $state, $postcode );

}

/**
 * Get Base Currency Code.
 *
 * @since 1.0.0
 *
 * @return string
 */
function gamipress_purchases_get_currency() {
    return apply_filters( 'gamipress_purchases_currency', gamipress_purchases_get_option( 'currency', 'USD' ) );
}

/**
 * Get full list of currency codes.
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_purchases_get_currencies() {
    static $gamipress_purchases_currencies;

    if ( ! isset( $gamipress_purchases_currencies ) ) {
        $gamipress_purchases_currencies = array_unique(
            apply_filters( 'gamipress_purchases_currencies',
                array(
                    'AED' => __( 'United Arab Emirates dirham', 'gamipress-purchases' ),
                    'AFN' => __( 'Afghan afghani', 'gamipress-purchases' ),
                    'ALL' => __( 'Albanian lek', 'gamipress-purchases' ),
                    'AMD' => __( 'Armenian dram', 'gamipress-purchases' ),
                    'ANG' => __( 'Netherlands Antillean guilder', 'gamipress-purchases' ),
                    'AOA' => __( 'Angolan kwanza', 'gamipress-purchases' ),
                    'ARS' => __( 'Argentine peso', 'gamipress-purchases' ),
                    'AUD' => __( 'Australian dollar', 'gamipress-purchases' ),
                    'AWG' => __( 'Aruban florin', 'gamipress-purchases' ),
                    'AZN' => __( 'Azerbaijani manat', 'gamipress-purchases' ),
                    'BAM' => __( 'Bosnia and Herzegovina convertible mark', 'gamipress-purchases' ),
                    'BBD' => __( 'Barbadian dollar', 'gamipress-purchases' ),
                    'BDT' => __( 'Bangladeshi taka', 'gamipress-purchases' ),
                    'BGN' => __( 'Bulgarian lev', 'gamipress-purchases' ),
                    'BHD' => __( 'Bahraini dinar', 'gamipress-purchases' ),
                    'BIF' => __( 'Burundian franc', 'gamipress-purchases' ),
                    'BMD' => __( 'Bermudian dollar', 'gamipress-purchases' ),
                    'BND' => __( 'Brunei dollar', 'gamipress-purchases' ),
                    'BOB' => __( 'Bolivian boliviano', 'gamipress-purchases' ),
                    'BRL' => __( 'Brazilian real', 'gamipress-purchases' ),
                    'BSD' => __( 'Bahamian dollar', 'gamipress-purchases' ),
                    'BTC' => __( 'Bitcoin', 'gamipress-purchases' ),
                    'BTN' => __( 'Bhutanese ngultrum', 'gamipress-purchases' ),
                    'BWP' => __( 'Botswana pula', 'gamipress-purchases' ),
                    'BYR' => __( 'Belarusian ruble', 'gamipress-purchases' ),
                    'BZD' => __( 'Belize dollar', 'gamipress-purchases' ),
                    'CAD' => __( 'Canadian dollar', 'gamipress-purchases' ),
                    'CDF' => __( 'Congolese franc', 'gamipress-purchases' ),
                    'CHF' => __( 'Swiss franc', 'gamipress-purchases' ),
                    'CLP' => __( 'Chilean peso', 'gamipress-purchases' ),
                    'CNY' => __( 'Chinese yuan', 'gamipress-purchases' ),
                    'COP' => __( 'Colombian peso', 'gamipress-purchases' ),
                    'CRC' => __( 'Costa Rican col&oacute;n', 'gamipress-purchases' ),
                    'CUC' => __( 'Cuban convertible peso', 'gamipress-purchases' ),
                    'CUP' => __( 'Cuban peso', 'gamipress-purchases' ),
                    'CVE' => __( 'Cape Verdean escudo', 'gamipress-purchases' ),
                    'CZK' => __( 'Czech koruna', 'gamipress-purchases' ),
                    'DJF' => __( 'Djiboutian franc', 'gamipress-purchases' ),
                    'DKK' => __( 'Danish krone', 'gamipress-purchases' ),
                    'DOP' => __( 'Dominican peso', 'gamipress-purchases' ),
                    'DZD' => __( 'Algerian dinar', 'gamipress-purchases' ),
                    'EGP' => __( 'Egyptian pound', 'gamipress-purchases' ),
                    'ERN' => __( 'Eritrean nakfa', 'gamipress-purchases' ),
                    'ETB' => __( 'Ethiopian birr', 'gamipress-purchases' ),
                    'EUR' => __( 'Euro', 'gamipress-purchases' ),
                    'FJD' => __( 'Fijian dollar', 'gamipress-purchases' ),
                    'FKP' => __( 'Falkland Islands pound', 'gamipress-purchases' ),
                    'GBP' => __( 'Pound sterling', 'gamipress-purchases' ),
                    'GEL' => __( 'Georgian lari', 'gamipress-purchases' ),
                    'GGP' => __( 'Guernsey pound', 'gamipress-purchases' ),
                    'GHS' => __( 'Ghana cedi', 'gamipress-purchases' ),
                    'GIP' => __( 'Gibraltar pound', 'gamipress-purchases' ),
                    'GMD' => __( 'Gambian dalasi', 'gamipress-purchases' ),
                    'GNF' => __( 'Guinean franc', 'gamipress-purchases' ),
                    'GTQ' => __( 'Guatemalan quetzal', 'gamipress-purchases' ),
                    'GYD' => __( 'Guyanese dollar', 'gamipress-purchases' ),
                    'HKD' => __( 'Hong Kong dollar', 'gamipress-purchases' ),
                    'HNL' => __( 'Honduran lempira', 'gamipress-purchases' ),
                    'HRK' => __( 'Croatian kuna', 'gamipress-purchases' ),
                    'HTG' => __( 'Haitian gourde', 'gamipress-purchases' ),
                    'HUF' => __( 'Hungarian forint', 'gamipress-purchases' ),
                    'IDR' => __( 'Indonesian rupiah', 'gamipress-purchases' ),
                    'ILS' => __( 'Israeli new shekel', 'gamipress-purchases' ),
                    'IMP' => __( 'Manx pound', 'gamipress-purchases' ),
                    'INR' => __( 'Indian rupee', 'gamipress-purchases' ),
                    'IQD' => __( 'Iraqi dinar', 'gamipress-purchases' ),
                    'IRR' => __( 'Iranian rial', 'gamipress-purchases' ),
                    'IRT' => __( 'Iranian toman', 'gamipress-purchases' ),
                    'ISK' => __( 'Icelandic kr&oacute;na', 'gamipress-purchases' ),
                    'JEP' => __( 'Jersey pound', 'gamipress-purchases' ),
                    'JMD' => __( 'Jamaican dollar', 'gamipress-purchases' ),
                    'JOD' => __( 'Jordanian dinar', 'gamipress-purchases' ),
                    'JPY' => __( 'Japanese yen', 'gamipress-purchases' ),
                    'KES' => __( 'Kenyan shilling', 'gamipress-purchases' ),
                    'KGS' => __( 'Kyrgyzstani som', 'gamipress-purchases' ),
                    'KHR' => __( 'Cambodian riel', 'gamipress-purchases' ),
                    'KMF' => __( 'Comorian franc', 'gamipress-purchases' ),
                    'KPW' => __( 'North Korean won', 'gamipress-purchases' ),
                    'KRW' => __( 'South Korean won', 'gamipress-purchases' ),
                    'KWD' => __( 'Kuwaiti dinar', 'gamipress-purchases' ),
                    'KYD' => __( 'Cayman Islands dollar', 'gamipress-purchases' ),
                    'KZT' => __( 'Kazakhstani tenge', 'gamipress-purchases' ),
                    'LAK' => __( 'Lao kip', 'gamipress-purchases' ),
                    'LBP' => __( 'Lebanese pound', 'gamipress-purchases' ),
                    'LKR' => __( 'Sri Lankan rupee', 'gamipress-purchases' ),
                    'LRD' => __( 'Liberian dollar', 'gamipress-purchases' ),
                    'LSL' => __( 'Lesotho loti', 'gamipress-purchases' ),
                    'LYD' => __( 'Libyan dinar', 'gamipress-purchases' ),
                    'MAD' => __( 'Moroccan dirham', 'gamipress-purchases' ),
                    'MDL' => __( 'Moldovan leu', 'gamipress-purchases' ),
                    'MGA' => __( 'Malagasy ariary', 'gamipress-purchases' ),
                    'MKD' => __( 'Macedonian denar', 'gamipress-purchases' ),
                    'MMK' => __( 'Burmese kyat', 'gamipress-purchases' ),
                    'MNT' => __( 'Mongolian t&ouml;gr&ouml;g', 'gamipress-purchases' ),
                    'MOP' => __( 'Macanese pataca', 'gamipress-purchases' ),
                    'MRO' => __( 'Mauritanian ouguiya', 'gamipress-purchases' ),
                    'MUR' => __( 'Mauritian rupee', 'gamipress-purchases' ),
                    'MVR' => __( 'Maldivian rufiyaa', 'gamipress-purchases' ),
                    'MWK' => __( 'Malawian kwacha', 'gamipress-purchases' ),
                    'MXN' => __( 'Mexican peso', 'gamipress-purchases' ),
                    'MYR' => __( 'Malaysian ringgit', 'gamipress-purchases' ),
                    'MZN' => __( 'Mozambican metical', 'gamipress-purchases' ),
                    'NAD' => __( 'Namibian dollar', 'gamipress-purchases' ),
                    'NGN' => __( 'Nigerian naira', 'gamipress-purchases' ),
                    'NIO' => __( 'Nicaraguan c&oacute;rdoba', 'gamipress-purchases' ),
                    'NOK' => __( 'Norwegian krone', 'gamipress-purchases' ),
                    'NPR' => __( 'Nepalese rupee', 'gamipress-purchases' ),
                    'NZD' => __( 'New Zealand dollar', 'gamipress-purchases' ),
                    'OMR' => __( 'Omani rial', 'gamipress-purchases' ),
                    'PAB' => __( 'Panamanian balboa', 'gamipress-purchases' ),
                    'PEN' => __( 'Peruvian nuevo sol', 'gamipress-purchases' ),
                    'PGK' => __( 'Papua New Guinean kina', 'gamipress-purchases' ),
                    'PHP' => __( 'Philippine peso', 'gamipress-purchases' ),
                    'PKR' => __( 'Pakistani rupee', 'gamipress-purchases' ),
                    'PLN' => __( 'Polish z&#x142;oty', 'gamipress-purchases' ),
                    'PRB' => __( 'Transnistrian ruble', 'gamipress-purchases' ),
                    'PYG' => __( 'Paraguayan guaran&iacute;', 'gamipress-purchases' ),
                    'QAR' => __( 'Qatari riyal', 'gamipress-purchases' ),
                    'RON' => __( 'Romanian leu', 'gamipress-purchases' ),
                    'RSD' => __( 'Serbian dinar', 'gamipress-purchases' ),
                    'RUB' => __( 'Russian ruble', 'gamipress-purchases' ),
                    'RWF' => __( 'Rwandan franc', 'gamipress-purchases' ),
                    'SAR' => __( 'Saudi riyal', 'gamipress-purchases' ),
                    'SBD' => __( 'Solomon Islands dollar', 'gamipress-purchases' ),
                    'SCR' => __( 'Seychellois rupee', 'gamipress-purchases' ),
                    'SDG' => __( 'Sudanese pound', 'gamipress-purchases' ),
                    'SEK' => __( 'Swedish krona', 'gamipress-purchases' ),
                    'SGD' => __( 'Singapore dollar', 'gamipress-purchases' ),
                    'SHP' => __( 'Saint Helena pound', 'gamipress-purchases' ),
                    'SLL' => __( 'Sierra Leonean leone', 'gamipress-purchases' ),
                    'SOS' => __( 'Somali shilling', 'gamipress-purchases' ),
                    'SRD' => __( 'Surinamese dollar', 'gamipress-purchases' ),
                    'SSP' => __( 'South Sudanese pound', 'gamipress-purchases' ),
                    'STD' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'gamipress-purchases' ),
                    'SYP' => __( 'Syrian pound', 'gamipress-purchases' ),
                    'SZL' => __( 'Swazi lilangeni', 'gamipress-purchases' ),
                    'THB' => __( 'Thai baht', 'gamipress-purchases' ),
                    'TJS' => __( 'Tajikistani somoni', 'gamipress-purchases' ),
                    'TMT' => __( 'Turkmenistan manat', 'gamipress-purchases' ),
                    'TND' => __( 'Tunisian dinar', 'gamipress-purchases' ),
                    'TOP' => __( 'Tongan pa&#x2bb;anga', 'gamipress-purchases' ),
                    'TRY' => __( 'Turkish lira', 'gamipress-purchases' ),
                    'TTD' => __( 'Trinidad and Tobago dollar', 'gamipress-purchases' ),
                    'TWD' => __( 'New Taiwan dollar', 'gamipress-purchases' ),
                    'TZS' => __( 'Tanzanian shilling', 'gamipress-purchases' ),
                    'UAH' => __( 'Ukrainian hryvnia', 'gamipress-purchases' ),
                    'UGX' => __( 'Ugandan shilling', 'gamipress-purchases' ),
                    'USD' => __( 'United States dollar', 'gamipress-purchases' ),
                    'UYU' => __( 'Uruguayan peso', 'gamipress-purchases' ),
                    'UZS' => __( 'Uzbekistani som', 'gamipress-purchases' ),
                    'VEF' => __( 'Venezuelan bol&iacute;var', 'gamipress-purchases' ),
                    'VND' => __( 'Vietnamese &#x111;&#x1ed3;ng', 'gamipress-purchases' ),
                    'VUV' => __( 'Vanuatu vatu', 'gamipress-purchases' ),
                    'WST' => __( 'Samoan t&#x101;l&#x101;', 'gamipress-purchases' ),
                    'XAF' => __( 'Central African CFA franc', 'gamipress-purchases' ),
                    'XCD' => __( 'East Caribbean dollar', 'gamipress-purchases' ),
                    'XOF' => __( 'West African CFA franc', 'gamipress-purchases' ),
                    'XPF' => __( 'CFP franc', 'gamipress-purchases' ),
                    'YER' => __( 'Yemeni rial', 'gamipress-purchases' ),
                    'ZAR' => __( 'South African rand', 'gamipress-purchases' ),
                    'ZMW' => __( 'Zambian kwacha', 'gamipress-purchases' ),
                )
            )
        );
    }

    return $gamipress_purchases_currencies;
}


/**
 * Get Currency symbol.
 *
 * @param string $currency (default: '')
 * @return string
 */
function gamipress_purchases_get_currency_symbol( $currency = '' ) {
    if ( ! $currency ) {
        $currency = gamipress_purchases_get_currency();
    }

    $symbols = apply_filters( 'gamipress_purchases_currency_symbols', array(
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

    return apply_filters( 'gamipress_purchases_currency_symbol', $currency_symbol, $currency );
}

/**
 * Get full list of countries codes.
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_purchases_get_countries() {
    static $gamipress_purchases_countries;

    if ( ! isset( $gamipress_purchases_countries ) ) {
        $gamipress_purchases_countries = array_unique(
            apply_filters( 'gamipress_purchases_currencies',
            array(
                'AF' => __( 'Afghanistan', 'gamipress-purchases' ),
                'AX' => __( '&#197;land Islands', 'gamipress-purchases' ),
                'AL' => __( 'Albania', 'gamipress-purchases' ),
                'DZ' => __( 'Algeria', 'gamipress-purchases' ),
                'AS' => __( 'American Samoa', 'gamipress-purchases' ),
                'AD' => __( 'Andorra', 'gamipress-purchases' ),
                'AO' => __( 'Angola', 'gamipress-purchases' ),
                'AI' => __( 'Anguilla', 'gamipress-purchases' ),
                'AQ' => __( 'Antarctica', 'gamipress-purchases' ),
                'AG' => __( 'Antigua and Barbuda', 'gamipress-purchases' ),
                'AR' => __( 'Argentina', 'gamipress-purchases' ),
                'AM' => __( 'Armenia', 'gamipress-purchases' ),
                'AW' => __( 'Aruba', 'gamipress-purchases' ),
                'AU' => __( 'Australia', 'gamipress-purchases' ),
                'AT' => __( 'Austria', 'gamipress-purchases' ),
                'AZ' => __( 'Azerbaijan', 'gamipress-purchases' ),
                'BS' => __( 'Bahamas', 'gamipress-purchases' ),
                'BH' => __( 'Bahrain', 'gamipress-purchases' ),
                'BD' => __( 'Bangladesh', 'gamipress-purchases' ),
                'BB' => __( 'Barbados', 'gamipress-purchases' ),
                'BY' => __( 'Belarus', 'gamipress-purchases' ),
                'BE' => __( 'Belgium', 'gamipress-purchases' ),
                'PW' => __( 'Belau', 'gamipress-purchases' ),
                'BZ' => __( 'Belize', 'gamipress-purchases' ),
                'BJ' => __( 'Benin', 'gamipress-purchases' ),
                'BM' => __( 'Bermuda', 'gamipress-purchases' ),
                'BT' => __( 'Bhutan', 'gamipress-purchases' ),
                'BO' => __( 'Bolivia', 'gamipress-purchases' ),
                'BQ' => __( 'Bonaire, Saint Eustatius and Saba', 'gamipress-purchases' ),
                'BA' => __( 'Bosnia and Herzegovina', 'gamipress-purchases' ),
                'BW' => __( 'Botswana', 'gamipress-purchases' ),
                'BV' => __( 'Bouvet Island', 'gamipress-purchases' ),
                'BR' => __( 'Brazil', 'gamipress-purchases' ),
                'IO' => __( 'British Indian Ocean Territory', 'gamipress-purchases' ),
                'VG' => __( 'British Virgin Islands', 'gamipress-purchases' ),
                'BN' => __( 'Brunei', 'gamipress-purchases' ),
                'BG' => __( 'Bulgaria', 'gamipress-purchases' ),
                'BF' => __( 'Burkina Faso', 'gamipress-purchases' ),
                'BI' => __( 'Burundi', 'gamipress-purchases' ),
                'KH' => __( 'Cambodia', 'gamipress-purchases' ),
                'CM' => __( 'Cameroon', 'gamipress-purchases' ),
                'CA' => __( 'Canada', 'gamipress-purchases' ),
                'CV' => __( 'Cape Verde', 'gamipress-purchases' ),
                'KY' => __( 'Cayman Islands', 'gamipress-purchases' ),
                'CF' => __( 'Central African Republic', 'gamipress-purchases' ),
                'TD' => __( 'Chad', 'gamipress-purchases' ),
                'CL' => __( 'Chile', 'gamipress-purchases' ),
                'CN' => __( 'China', 'gamipress-purchases' ),
                'CX' => __( 'Christmas Island', 'gamipress-purchases' ),
                'CC' => __( 'Cocos (Keeling) Islands', 'gamipress-purchases' ),
                'CO' => __( 'Colombia', 'gamipress-purchases' ),
                'KM' => __( 'Comoros', 'gamipress-purchases' ),
                'CG' => __( 'Congo (Brazzaville)', 'gamipress-purchases' ),
                'CD' => __( 'Congo (Kinshasa)', 'gamipress-purchases' ),
                'CK' => __( 'Cook Islands', 'gamipress-purchases' ),
                'CR' => __( 'Costa Rica', 'gamipress-purchases' ),
                'HR' => __( 'Croatia', 'gamipress-purchases' ),
                'CU' => __( 'Cuba', 'gamipress-purchases' ),
                'CW' => __( 'Cura&ccedil;ao', 'gamipress-purchases' ),
                'CY' => __( 'Cyprus', 'gamipress-purchases' ),
                'CZ' => __( 'Czech Republic', 'gamipress-purchases' ),
                'DK' => __( 'Denmark', 'gamipress-purchases' ),
                'DJ' => __( 'Djibouti', 'gamipress-purchases' ),
                'DM' => __( 'Dominica', 'gamipress-purchases' ),
                'DO' => __( 'Dominican Republic', 'gamipress-purchases' ),
                'EC' => __( 'Ecuador', 'gamipress-purchases' ),
                'EG' => __( 'Egypt', 'gamipress-purchases' ),
                'SV' => __( 'El Salvador', 'gamipress-purchases' ),
                'GQ' => __( 'Equatorial Guinea', 'gamipress-purchases' ),
                'ER' => __( 'Eritrea', 'gamipress-purchases' ),
                'EE' => __( 'Estonia', 'gamipress-purchases' ),
                'ET' => __( 'Ethiopia', 'gamipress-purchases' ),
                'FK' => __( 'Falkland Islands', 'gamipress-purchases' ),
                'FO' => __( 'Faroe Islands', 'gamipress-purchases' ),
                'FJ' => __( 'Fiji', 'gamipress-purchases' ),
                'FI' => __( 'Finland', 'gamipress-purchases' ),
                'FR' => __( 'France', 'gamipress-purchases' ),
                'GF' => __( 'French Guiana', 'gamipress-purchases' ),
                'PF' => __( 'French Polynesia', 'gamipress-purchases' ),
                'TF' => __( 'French Southern Territories', 'gamipress-purchases' ),
                'GA' => __( 'Gabon', 'gamipress-purchases' ),
                'GM' => __( 'Gambia', 'gamipress-purchases' ),
                'GE' => __( 'Georgia', 'gamipress-purchases' ),
                'DE' => __( 'Germany', 'gamipress-purchases' ),
                'GH' => __( 'Ghana', 'gamipress-purchases' ),
                'GI' => __( 'Gibraltar', 'gamipress-purchases' ),
                'GR' => __( 'Greece', 'gamipress-purchases' ),
                'GL' => __( 'Greenland', 'gamipress-purchases' ),
                'GD' => __( 'Grenada', 'gamipress-purchases' ),
                'GP' => __( 'Guadeloupe', 'gamipress-purchases' ),
                'GU' => __( 'Guam', 'gamipress-purchases' ),
                'GT' => __( 'Guatemala', 'gamipress-purchases' ),
                'GG' => __( 'Guernsey', 'gamipress-purchases' ),
                'GN' => __( 'Guinea', 'gamipress-purchases' ),
                'GW' => __( 'Guinea-Bissau', 'gamipress-purchases' ),
                'GY' => __( 'Guyana', 'gamipress-purchases' ),
                'HT' => __( 'Haiti', 'gamipress-purchases' ),
                'HM' => __( 'Heard Island and McDonald Islands', 'gamipress-purchases' ),
                'HN' => __( 'Honduras', 'gamipress-purchases' ),
                'HK' => __( 'Hong Kong', 'gamipress-purchases' ),
                'HU' => __( 'Hungary', 'gamipress-purchases' ),
                'IS' => __( 'Iceland', 'gamipress-purchases' ),
                'IN' => __( 'India', 'gamipress-purchases' ),
                'ID' => __( 'Indonesia', 'gamipress-purchases' ),
                'IR' => __( 'Iran', 'gamipress-purchases' ),
                'IQ' => __( 'Iraq', 'gamipress-purchases' ),
                'IE' => __( 'Ireland', 'gamipress-purchases' ),
                'IM' => __( 'Isle of Man', 'gamipress-purchases' ),
                'IL' => __( 'Israel', 'gamipress-purchases' ),
                'IT' => __( 'Italy', 'gamipress-purchases' ),
                'CI' => __( 'Ivory Coast', 'gamipress-purchases' ),
                'JM' => __( 'Jamaica', 'gamipress-purchases' ),
                'JP' => __( 'Japan', 'gamipress-purchases' ),
                'JE' => __( 'Jersey', 'gamipress-purchases' ),
                'JO' => __( 'Jordan', 'gamipress-purchases' ),
                'KZ' => __( 'Kazakhstan', 'gamipress-purchases' ),
                'KE' => __( 'Kenya', 'gamipress-purchases' ),
                'KI' => __( 'Kiribati', 'gamipress-purchases' ),
                'KW' => __( 'Kuwait', 'gamipress-purchases' ),
                'KG' => __( 'Kyrgyzstan', 'gamipress-purchases' ),
                'LA' => __( 'Laos', 'gamipress-purchases' ),
                'LV' => __( 'Latvia', 'gamipress-purchases' ),
                'LB' => __( 'Lebanon', 'gamipress-purchases' ),
                'LS' => __( 'Lesotho', 'gamipress-purchases' ),
                'LR' => __( 'Liberia', 'gamipress-purchases' ),
                'LY' => __( 'Libya', 'gamipress-purchases' ),
                'LI' => __( 'Liechtenstein', 'gamipress-purchases' ),
                'LT' => __( 'Lithuania', 'gamipress-purchases' ),
                'LU' => __( 'Luxembourg', 'gamipress-purchases' ),
                'MO' => __( 'Macao S.A.R., China', 'gamipress-purchases' ),
                'MK' => __( 'Macedonia', 'gamipress-purchases' ),
                'MG' => __( 'Madagascar', 'gamipress-purchases' ),
                'MW' => __( 'Malawi', 'gamipress-purchases' ),
                'MY' => __( 'Malaysia', 'gamipress-purchases' ),
                'MV' => __( 'Maldives', 'gamipress-purchases' ),
                'ML' => __( 'Mali', 'gamipress-purchases' ),
                'MT' => __( 'Malta', 'gamipress-purchases' ),
                'MH' => __( 'Marshall Islands', 'gamipress-purchases' ),
                'MQ' => __( 'Martinique', 'gamipress-purchases' ),
                'MR' => __( 'Mauritania', 'gamipress-purchases' ),
                'MU' => __( 'Mauritius', 'gamipress-purchases' ),
                'YT' => __( 'Mayotte', 'gamipress-purchases' ),
                'MX' => __( 'Mexico', 'gamipress-purchases' ),
                'FM' => __( 'Micronesia', 'gamipress-purchases' ),
                'MD' => __( 'Moldova', 'gamipress-purchases' ),
                'MC' => __( 'Monaco', 'gamipress-purchases' ),
                'MN' => __( 'Mongolia', 'gamipress-purchases' ),
                'ME' => __( 'Montenegro', 'gamipress-purchases' ),
                'MS' => __( 'Montserrat', 'gamipress-purchases' ),
                'MA' => __( 'Morocco', 'gamipress-purchases' ),
                'MZ' => __( 'Mozambique', 'gamipress-purchases' ),
                'MM' => __( 'Myanmar', 'gamipress-purchases' ),
                'NA' => __( 'Namibia', 'gamipress-purchases' ),
                'NR' => __( 'Nauru', 'gamipress-purchases' ),
                'NP' => __( 'Nepal', 'gamipress-purchases' ),
                'NL' => __( 'Netherlands', 'gamipress-purchases' ),
                'NC' => __( 'New Caledonia', 'gamipress-purchases' ),
                'NZ' => __( 'New Zealand', 'gamipress-purchases' ),
                'NI' => __( 'Nicaragua', 'gamipress-purchases' ),
                'NE' => __( 'Niger', 'gamipress-purchases' ),
                'NG' => __( 'Nigeria', 'gamipress-purchases' ),
                'NU' => __( 'Niue', 'gamipress-purchases' ),
                'NF' => __( 'Norfolk Island', 'gamipress-purchases' ),
                'MP' => __( 'Northern Mariana Islands', 'gamipress-purchases' ),
                'KP' => __( 'North Korea', 'gamipress-purchases' ),
                'NO' => __( 'Norway', 'gamipress-purchases' ),
                'OM' => __( 'Oman', 'gamipress-purchases' ),
                'PK' => __( 'Pakistan', 'gamipress-purchases' ),
                'PS' => __( 'Palestinian Territory', 'gamipress-purchases' ),
                'PA' => __( 'Panama', 'gamipress-purchases' ),
                'PG' => __( 'Papua New Guinea', 'gamipress-purchases' ),
                'PY' => __( 'Paraguay', 'gamipress-purchases' ),
                'PE' => __( 'Peru', 'gamipress-purchases' ),
                'PH' => __( 'Philippines', 'gamipress-purchases' ),
                'PN' => __( 'Pitcairn', 'gamipress-purchases' ),
                'PL' => __( 'Poland', 'gamipress-purchases' ),
                'PT' => __( 'Portugal', 'gamipress-purchases' ),
                'PR' => __( 'Puerto Rico', 'gamipress-purchases' ),
                'QA' => __( 'Qatar', 'gamipress-purchases' ),
                'RE' => __( 'Reunion', 'gamipress-purchases' ),
                'RO' => __( 'Romania', 'gamipress-purchases' ),
                'RU' => __( 'Russia', 'gamipress-purchases' ),
                'RW' => __( 'Rwanda', 'gamipress-purchases' ),
                'BL' => __( 'Saint Barth&eacute;lemy', 'gamipress-purchases' ),
                'SH' => __( 'Saint Helena', 'gamipress-purchases' ),
                'KN' => __( 'Saint Kitts and Nevis', 'gamipress-purchases' ),
                'LC' => __( 'Saint Lucia', 'gamipress-purchases' ),
                'MF' => __( 'Saint Martin (French part)', 'gamipress-purchases' ),
                'SX' => __( 'Saint Martin (Dutch part)', 'gamipress-purchases' ),
                'PM' => __( 'Saint Pierre and Miquelon', 'gamipress-purchases' ),
                'VC' => __( 'Saint Vincent and the Grenadines', 'gamipress-purchases' ),
                'SM' => __( 'San Marino', 'gamipress-purchases' ),
                'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'gamipress-purchases' ),
                'SA' => __( 'Saudi Arabia', 'gamipress-purchases' ),
                'SN' => __( 'Senegal', 'gamipress-purchases' ),
                'RS' => __( 'Serbia', 'gamipress-purchases' ),
                'SC' => __( 'Seychelles', 'gamipress-purchases' ),
                'SL' => __( 'Sierra Leone', 'gamipress-purchases' ),
                'SG' => __( 'Singapore', 'gamipress-purchases' ),
                'SK' => __( 'Slovakia', 'gamipress-purchases' ),
                'SI' => __( 'Slovenia', 'gamipress-purchases' ),
                'SB' => __( 'Solomon Islands', 'gamipress-purchases' ),
                'SO' => __( 'Somalia', 'gamipress-purchases' ),
                'ZA' => __( 'South Africa', 'gamipress-purchases' ),
                'GS' => __( 'South Georgia/Sandwich Islands', 'gamipress-purchases' ),
                'KR' => __( 'South Korea', 'gamipress-purchases' ),
                'SS' => __( 'South Sudan', 'gamipress-purchases' ),
                'ES' => __( 'Spain', 'gamipress-purchases' ),
                'LK' => __( 'Sri Lanka', 'gamipress-purchases' ),
                'SD' => __( 'Sudan', 'gamipress-purchases' ),
                'SR' => __( 'Suriname', 'gamipress-purchases' ),
                'SJ' => __( 'Svalbard and Jan Mayen', 'gamipress-purchases' ),
                'SZ' => __( 'Swaziland', 'gamipress-purchases' ),
                'SE' => __( 'Sweden', 'gamipress-purchases' ),
                'CH' => __( 'Switzerland', 'gamipress-purchases' ),
                'SY' => __( 'Syria', 'gamipress-purchases' ),
                'TW' => __( 'Taiwan', 'gamipress-purchases' ),
                'TJ' => __( 'Tajikistan', 'gamipress-purchases' ),
                'TZ' => __( 'Tanzania', 'gamipress-purchases' ),
                'TH' => __( 'Thailand', 'gamipress-purchases' ),
                'TL' => __( 'Timor-Leste', 'gamipress-purchases' ),
                'TG' => __( 'Togo', 'gamipress-purchases' ),
                'TK' => __( 'Tokelau', 'gamipress-purchases' ),
                'TO' => __( 'Tonga', 'gamipress-purchases' ),
                'TT' => __( 'Trinidad and Tobago', 'gamipress-purchases' ),
                'TN' => __( 'Tunisia', 'gamipress-purchases' ),
                'TR' => __( 'Turkey', 'gamipress-purchases' ),
                'TM' => __( 'Turkmenistan', 'gamipress-purchases' ),
                'TC' => __( 'Turks and Caicos Islands', 'gamipress-purchases' ),
                'TV' => __( 'Tuvalu', 'gamipress-purchases' ),
                'UG' => __( 'Uganda', 'gamipress-purchases' ),
                'UA' => __( 'Ukraine', 'gamipress-purchases' ),
                'AE' => __( 'United Arab Emirates', 'gamipress-purchases' ),
                'GB' => __( 'United Kingdom (UK)', 'gamipress-purchases' ),
                'US' => __( 'United States (US)', 'gamipress-purchases' ),
                'UM' => __( 'United States (US) Minor Outlying Islands', 'gamipress-purchases' ),
                'VI' => __( 'United States (US) Virgin Islands', 'gamipress-purchases' ),
                'UY' => __( 'Uruguay', 'gamipress-purchases' ),
                'UZ' => __( 'Uzbekistan', 'gamipress-purchases' ),
                'VU' => __( 'Vanuatu', 'gamipress-purchases' ),
                'VA' => __( 'Vatican', 'gamipress-purchases' ),
                'VE' => __( 'Venezuela', 'gamipress-purchases' ),
                'VN' => __( 'Vietnam', 'gamipress-purchases' ),
                'WF' => __( 'Wallis and Futuna', 'gamipress-purchases' ),
                'EH' => __( 'Western Sahara', 'gamipress-purchases' ),
                'WS' => __( 'Samoa', 'gamipress-purchases' ),
                'YE' => __( 'Yemen', 'gamipress-purchases' ),
                'ZM' => __( 'Zambia', 'gamipress-purchases' ),
                'ZW' => __( 'Zimbabwe', 'gamipress-purchases' ),
                )
            )
        );

    }

    return $gamipress_purchases_countries;
}