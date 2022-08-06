const mix = require( 'laravel-mix' );

mix
    .sass('sass/solpay-donation-fundraising.scss', 'dist/css/solpay-donation-fundraising.css')
    .ts('js/index.ts', 'dist/js/solpay-donation-fundraising.js');
