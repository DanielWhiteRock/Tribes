=== GamiPress - Points Exchanges ===
Contributors: gamipress, tsunoa, rubengc, eneribs
Tags: gamipress, gamification, gamify, point, achievement, badge, award, reward, credit, engagement, ajax
Requires at least: 4.0
Tested up to: 5.8
Stable tag: 1.0.9
License: GNU AGPLv3
License URI: http://www.gnu.org/licenses/agpl-3.0.html

Let your users exchange points between different points types.

== Description ==

Points Exchanges gives you the ability to bring to your users the possibility to exchange any or a predefined points amount to any other points type.

In just a few minutes, you will be able to place points exchange forms around your site and award your users for exchanging between different points types.

Points Exchanges extends and expands GamiPress adding new activity events and features.

= New Events =

* New exchange: When an user makes a new exchange.
* Exchange a minimum amount of points: When an user exchanges a minimum amount of the desired points type.

= Features =

* Configurable points exchange forms to let users exchange points between the predefined points types.
* Points exchange forms details will be live updated (without refresh the page).
* Set different exchange rates by each points type.
* Ability to disable exchanges between specific types.
* Shortcode to place any points exchange form anywhere (with support to GamiPress live shortcode embedder).
* Widget to place any points exchange form on any sidebar.
* Shortcode and Widget to place a points exchange rates table anywhere.

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

= 1.0.9 =

* **Bug Fixes**
* Fixed incorrect check for the "Exchange a minimum amount of points" event.

= 1.0.8 =

* **Developer Notes**
* Added new Javascript events to meet when a points exchange has been made successfully.

= 1.0.7 =

* **New Features**
* Added a filter to override the points exchange field step to extend custom allowed values.

= 1.0.6 =

* **Improvements**
* Updated deprecated jQuery functions.

= 1.0.5 =

* **Bug Fixes**
* Fixed invalid referrer URL error.

= 1.0.4 =

* **New Features**
* Added support to GamiPress 1.8.0.
* **Improvements**
* Added validation of server errors when processing an exchange.
* Make use of WordPress security functions for ajax requests.
* Code reduction on exchange processing function.

= 1.0.3 =

* **New Features**
* Added support to GamiPress 1.7.0.
* **Improvements**
* Great amount of code reduction thanks to the new GamiPress 1.7.0 API functions.

= 1.0.2 =

* **Improvements**
* Added extra checks on exchange processing to ensure that user meets the points amount that wants to exchange from.

= 1.0.1 =

* **New Features**
* Full support to GamiPress Gutenberg blocks.

= 1.0.0 =

* Initial release.
