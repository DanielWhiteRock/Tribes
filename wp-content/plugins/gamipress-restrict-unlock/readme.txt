=== GamiPress - Restrict Unlock ===
Contributors: gamipress, tsunoa, rubengc, eneribs
Tags: gamipress, gamification, point, achievement, rank, badge, award, reward, credit, engagement
Requires at least: 4.4
Tested up to: 5.9
Stable tag: 1.0.7
License: GNU AGPLv3
License URI: http://www.gnu.org/licenses/agpl-3.0.html

Restrict users to unlock any gamification element.

== Description ==

Restrict Unlock gives you the ability to configure several restrictions to any gamification element unlock until user completes all the requirements specified.

Add unlock restrictions to users by a set of requirements, by role or to specific users to any points awards, deducts, achievements, steps, ranks or rank requirements.

In addition, this add-on has the ability to restrict unlock by expending points, letting you setup a "pay-for-unlock" on any gamification element.

Also, this add-on adds new activity events and features to extend and expand the functionality of GamiPress.

= New Events =

* Get access to unlock an element: When an users get access to unlock an element.
* Get access to unlock a specific element: When an users get access to unlock a specific element.
* Get access to unlock an element by meeting all requirements: When an users get access to unlock an element by meeting all requirements.
* Get access to unlock a specific element by meeting all requirements: When an users get access to unlock a specific element by meeting all requirements.
* Get access to unlock an element using points: When an users get access to unlock an element using points.
* Get access to unlock a specific element using points: When an users get access to unlock a specific element using points.

= Features =

* Add unlock restrictions by the next requirements:
    * Points balance.
    * Rank reached.
    * Achievements earned.
    * Achievements of a specific type earned.
    * All achievements of a specific type earned.
* Let users to optionally get access to unlock an element without meet the requirements by expending points.
* Add unlock restrictions on any element to be accessed just by expending points.
* Restrict unlock to specific users or by role.
* Grant access to specific users or by role.

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

= 1.0.7 =

* **Bug Fixes**
* Fixed fields visibility issue.

= 1.0.6 =

* **Improvements**
* For requirements, check first if parent is restricted too.

= 1.0.5 =

* **Improvements**
* Updated deprecated jQuery functions.

= 1.0.4 =

* **Improvements**
* Apply points format on templates.

= 1.0.3 =

* **Bug Fixes**
* Fixed "headers already sent" PHP warning caused by a typo.

= 1.0.2 =

* **Developer Notes**
* Added more filters to allow override response when get access by expending points.

= 1.0.1 =

* **New Features**
* Added support to GamiPress 1.8.0.
* **Improvements**
* Make use of WordPress security functions for ajax requests.

= 1.0.0 =

* Initial release.