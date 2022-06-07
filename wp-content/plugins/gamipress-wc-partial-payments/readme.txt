=== GamiPress - WooCommerce Partial Payments ===
Contributors: gamipress, tsunoa, rubengc, eneribs
Tags: gamipress, gamification, gamify, point, achievement, badge, award, reward, credit, engagement, ajax
Requires at least: 4.4
Tested up to: 6.0
Stable tag: 1.1.1
License: GNU AGPLv3
License URI: http://www.gnu.org/licenses/agpl-3.0.html

Let users partially pay a WooCommerce purchase by using points.

== Description ==

WooCommerce Partial Payments gives you the ability to enable GamiPress points types for partially pay any purchase.

In just a few minutes, your users will be able to reduce any purchase total (like a discount) by using an amount of points at checkout.

In addition, this add-on includes options to set different conversions per points type, limit the maximum discount per purchase or included customize the input to enter the points amount.

= Features =

* Enable any points type for partial payments.
* Ability to reduce any purchase total by multiples points types.
* Set different conversion rates per each points type.
* Ability to limit the maximum amount allowed per points type.
* Force users to use a fixed amount of points or let them introduce the amount they wish.
* Different inputs to let user choose exactly the amount of points he want to use.
* Settings to limit the maximum discount allowed on a single purchase (flat or percentage limit).
* Live controls to easily view the discount amount based on the points to use.
* Ability to restore user points on refund.
* Points used on a purchase will be displayed anywhere (cart, checkout, order details, invoice, etc).

In addition you can use the [WooCommerce integration](https://wordpress.org/plugins/gamipress-woocommerce-integration/) to add activity triggers related to WooCommerce actions.

== Installation ==

= From WordPress backend =

1. Navigate to Plugins -> Add new.
2. Click the button "Upload Plugin" next to "Add plugins" title.
3. Upload the downloaded zip file and activate it.

= Direct upload =

1. Upload the downloaded zip file into your `wp-content/plugins/` folder.
2. Unzip the uploaded zip file.
3. Navigate to Plugins menu on your WordPress admin area.
4. Activate this plugin.

== Frequently Asked Questions ==

= Does this add-on register points types as payment gateways? =

No, this add-on was designed to register points types to allow users reduce a purchase total by using an amount of points at checkout.
To register points types as a payment gateway (for full payments), you may check our [WooCommerce Points Gateway](https://gamipress.com/add-ons/gamipress-wc-points-gateway/) add-on.

== Changelog ==

= 1.1.1 =

* **Improvements**
* Update functions for compatibility with last versions of GamiPress and WordPress.

= 1.1.0 =

* **Improvements**
* Added support for WC Subscriptions recurring fees.

= 1.0.9 =

* **Improvements**
* Improved the preview function to bring more accurate conversions.

= 1.0.8 =

* **Improvements**
* Stop using PHP sessions to ensure maximum compatibility with any server configuration.

= 1.0.7 =

* **Improvements**
* Updated deprecated jQuery functions.

= 1.0.6 =

* **Security**
* Deduct points at the moment they are applied to avoid any kind of hacks.

= 1.0.5 =

* **Bug Fixes**
* Correctly apply the maximum amount allowed limit.
* **Improvements**
* Provide a more accurate message when user exceeds the maximum amount allowed.

= 1.0.4 =

* **Improvements**
* Prevent to deduct points twice.
* Improved the way to check that purchase has been completed to deduct the points.

= 1.0.3 =

* **Improvements**
* Ensure to start the session on the WordPress initialization to avoid PHP warnings.

= 1.0.2 =

* **New Features**
* Added support to GamiPress 1.8.0.
* **Improvements**
* Make use of WordPress security functions for ajax requests.

= 1.0.1 =

* **Bug Fixes**
* Fixed live discount preview when only 1 points type is allowed.

= 1.0.0 =

* Initial release.
