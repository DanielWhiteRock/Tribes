function gamipress_points_exchanges_get_points_type_exchange_rates( points_type ) {

    return gamipress_points_exchanges_functions.rates[points_type];

}

function gamipress_points_exchanges_get_user_points_balance( points_type ) {

    if( gamipress_points_exchanges_functions.user_points[points_type] === undefined ) {
        return 0;
    }

    return gamipress_points_exchanges_functions.user_points[points_type];

}

function gamipress_points_exchanges_get_points_type_label( points_type ) {

    if( gamipress_points_exchanges_functions.points_types[points_type] === undefined ) {
        return '';
    }

    return gamipress_points_exchanges_functions.points_types[points_type].plural_name;

}

function gamipress_points_exchanges_get_rate( from, to ) {

    var points_type_rates = gamipress_points_exchanges_get_points_type_exchange_rates( from );

    // Bail if FROM points type has no rates
    if( points_type_rates === undefined ) {
        return false;
    }

    // Bail if FROM points type has no rates for TO points type
    if( points_type_rates[to] === undefined ) {
        return false;
    }

    return points_type_rates[to];

}

function gamipress_points_exchanges_apply_rate( amount, from, to ) {

    var rate = gamipress_points_exchanges_get_rate( from, to );

    // Bail if not applicable rate
    if( ! rate ) {
        return 0;
    }

    return parseInt( amount * rate );

}