=== GamiPress - Purchases ===
Contributors: gamipress, tsunoa, rubengc, eneribs
Tags: gamipress, gamification, gamify, point, achievement, badge, award, reward, credit, engagement, ajax
Requires at least: 4.0
Tested up to: 5.9
Stable tag: 1.1.8
License: GNU AGPLv3
License URI: http://www.gnu.org/licenses/agpl-3.0.html

Allow your users purchase points, achievements or ranks access.

== Description ==

Purchases gives you the ability to bring the opportunity to your users to recharge their points, unlock achievements or unlock ranks by an amount of money.

In just a few minutes, you will be able to place purchase forms around your site and award your users for make purchases.

Purchases extends and expands GamiPress adding new activity events and features.

= New Events =

* New purchase: When an user makes a new purchase.
* Purchase a minimum amount of points: When an user purchases a minimum amount of the desired points type.
* Purchase access to an achievement: When an user purchases the access to any achievement of the desired achievement type.
* Purchase access to a specific achievement: When an user purchases the access to a specific achievement.
* Purchase access to a rank: When an user purchases the access to any rank of the desired rank type.
* Purchase access to a specific rank: When an user purchases the access to a specific rank.

= Features =

* Ability to define different conversion rates to each points type.
* Ability to define the access price to any achievement/rank.
* Frontend purchase history with details of each purchase.
* Currency options to show prices at format you want.
* Tax support to add taxes based on country, state and postal code/zip.
* Configurable acceptance checkbox to explicit consent of data collection to meet with GDPR.
* Configurable email to send the purchase receipt to the customer.
* Configurable email to notify admins about new purchases.
* Easily editable payments to allow you add new items, change taxes or add notes.
* Ability to resend the purchase receipt.
* Ability to refund payments.
* Support for WordPress privacy tools.

= Purchase Forms =

* Points: To let your users recharge a desired amount of points.
* Achievement: To let your users get an achievement without complete all the steps.
* Rank: To let your users reach a rank without complete all the requirements or previous ranks.

= Points Purchase Forms =

* Fixed Amount: Users will be able to purchase a predefined amount of points.
* Custom Amount: Users will be able to set a custom amount of points to purchase.
* Options: Users will be able to choose between a predefined amount of points to purchase.

Note: For each points purchase form, you will be able to set the amount type to work with, this means you can show a purchase form to buy an amount of points (with the cost in money) or a purchase form with the amount of money to expend (with the amount of points that user will get).

= Included Payment Gateways =

* Bank Transfer
* PayPal Standard

= Other Features =

* Integrated with the official add-ons that add new content to achievements and ranks.
* Shortcodes to place any purchase form anywhere (with support to GamiPress live shortcode embedder).
* Widgets to place any purchase form on any sidebar.

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

== Changelog ==

= 1.1.8 =

* **Improvements**
* Improved multisite support.

= 1.1.7 =

* **Bug Fixes**
* Fixed invalid referrer URL error.

= 1.1.6 =

* **Improvements**
* Improved compatibility with multisite installs.

= 1.1.5 =

* **Improvements**
* Updated deprecated jQuery functions.

= 1.1.4 =

* **Bug Fixes**
* Fixed purchase key mismatch that causes payments doesn't gets marked as completed.

= 1.1.3 =

* **New Features**
* Turn purchase history shortcode into a block and a widget.
* Added the ability to display the purchase history of the current logged in user or a specific user.
* **Improvements**
* Added support to shortcodes groups.

= 1.1.2 =

* **New Features**
* Added support to GamiPress 1.8.0.
* **Bug Fixes**
* Fixed achievement and rank selector.
* Fixed user selector.
* **Improvements**
* Make use of WordPress security functions for ajax requests.

= 1.1.1 =

* **Improvements**
* Updated log functions to the most up to date GamiPress log functions.

= 1.1.0 =

* **New Features**
* Added support to GamiPress 1.7.0.
* **Improvements**
* Improved post and user selector on widgets area and shortcode editor.
* Great amount of code reduction thanks to the new GamiPress 1.7.0 API functions.