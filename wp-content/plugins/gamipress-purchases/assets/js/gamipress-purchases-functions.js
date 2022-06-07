/*
 * GamiPress Purchases php functions in JS
 */

function gamipress_purchases_php_number_format( n, c, d, t ){
    var c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

function gamipress_purchases_format_amount( amount ) {

    var thousands_separator = gamipress_purchases_functions.thousands_separator;
    var decimal_separator = gamipress_purchases_functions.decimal_separator;
    var decimals = gamipress_purchases_functions.decimals;

    return gamipress_purchases_php_number_format( amount, decimals, decimal_separator, thousands_separator );
}

function gamipress_purchases_format_price( amount ) {

    var symbol = gamipress_purchases_functions.currency_symbol;
    var position = gamipress_purchases_functions.currency_position;

    var formatted_amount = gamipress_purchases_format_amount( amount );

    return position === 'before' ? symbol + formatted_amount : formatted_amount + symbol;

}

function gamipress_purchases_get_conversion( points_type ) {

    return gamipress_purchases_functions.conversions[points_type];
}

function gamipress_purchases_convert_to_points( amount, points_type ) {

    var conversion = gamipress_purchases_get_conversion( points_type );

    if( conversion === 'false' || conversion === undefined ) {
        return false;
    }

    var conversion_rate  = conversion['money'] / conversion['points'];

    return amount / conversion_rate;

}

function gamipress_purchases_convert_to_money( amount, points_type ) {

    var conversion = gamipress_purchases_get_conversion( points_type );

    if( conversion === 'false' || conversion === undefined ) {
        return false;
    }

    var conversion_rate  = conversion['points'] / conversion['money'];

    return amount / conversion_rate;

}

function gamipress_purchases_get_tax( country, state, postcode ) {

    var tax = 0;

    if( gamipress_purchases_functions.enable_taxes ) {

        if( country === undefined ) { country = ''; }
        if( state === undefined ) { state = ''; }
        if( postcode === undefined ) { postcode = ''; }

        jQuery.each( gamipress_purchases_functions.taxes, function( index, tax_rule ) {

            if( tax_rule.country === undefined ) { tax_rule.country = ''; }
            if( tax_rule.state === undefined ) { tax_rule.state = ''; }
            if( tax_rule.postcode === undefined ) { tax_rule.postcode = ''; }

            // First check the country
            if( tax_rule.country !== country ) {
                return true;
            }

            // Country tax rule
            if( ! tax_rule.state.length && ! tax_rule.postcode.length ) {
                tax = tax_rule.tax;
                return true;
            }

            // State tax rule
            if( tax_rule.state.length && tax_rule.state === state ) {

                if( tax_rule.postcode.length && tax_rule.postcode === postcode ) {

                    // Postcode tax rule
                    tax = tax_rule.tax;
                    return true;

                } else if( ! tax_rule.postcode.length ) {

                    // Just apply state tax rule if postcode rule is empty
                    tax = tax_rule.tax;
                    return true;

                }
            }
        });

        tax = parseFloat( tax );

        // If not tax applied, apply default tax
        if( tax === 0 || isNaN( tax ) ) {
            tax = parseFloat( gamipress_purchases_functions.default_tax );
        }

        if( isNaN( tax ) ) {
            tax = 0;
        }

    }

    return tax;
}

function gamipress_purchases_get_tax_rate( country, state, postcode ) {

    var tax = gamipress_purchases_get_tax( country, state, postcode );

    return tax / 100;

}