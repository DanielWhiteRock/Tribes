=== GamiPress - Reports ===
Contributors: gamipress, tsunoa, rubengc, eneribs
Tags: gamipress, gamification, point, achievement, rank, badge, award, reward, credit, engagement
Requires at least: 4.4
Tested up to: 5.8
Stable tag: 1.0.8
License: GNU AGPLv3
License URI: http://www.gnu.org/licenses/agpl-3.0.html

Live reports for a quick view of points in circulation, achievements earned and user ranks.

== Description ==

Reports gives you the ability to easily view data statistics from your users points, their earned achievements and their current rank.

Real-time tools that will load statistics charts and tables with all the information about what's happening in you gamification environment.

Includes comparison charts to meet behavior trends of your users in the different time periods.

An useful information that will help you to detect increments or decrements in behavior related to points movements (awarded, deducted and expended), achievements interactions (earned and awarded by admins) or ranks positions (reached and awarded by admins).

= Features =

* Dashboard resume with full counters of everything.
* Comparison charts to compare different time periods of data statistics.
* Controls to easily navigate through time periods.
* Live data loading that makes statistics load quickly and without overloading the site.
* Per points type data: In circulation, awarded, deducted and expended.
* Per achievement type data: Active, earned by the user and awarded by administrators.
* Achievements data: Earned by the user and per achievement earners.
* Per rank type data: Active, users on highest rank and users on lowest rank.
* Ranks data: Current user rank and per rank current earners.

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

= 1.0.8 =

* **Improvements**
* Multisite: Ensure to load users only from the current site in all queries.
* Several style improvements.

= 1.0.7 =

* **Improvements**
* Style improvements.
* **Bug Fixes**
* Fixed issue that causes that reports dashboard meta boxes does not get correctly rendered.

= 1.0.6 =

* **Improvements**
* Correctly apply the minimum role to administer GamiPress permission to reports screens.

= 1.0.5 =

* **Bug Fixes**
* Fixed chart pagination caused by a nonce name collision.

= 1.0.4 =

* **New Features**
* Added support to GamiPress 1.8.0.
* **Improvements**
* Make use of WordPress security functions for ajax requests.

= 1.0.3 =

* **New Features**
* Added support to GamiPress 1.7.0.
* **Improvements**
* Great amount of code reduction thanks to the new GamiPress 1.7.0 API functions.

= 1.0.2 =

* **New Features**
* Added support to GamiPress points format options.
* **Bug Fixes**
* Fixed achievements last earned date when no one has earned it yet.
* Fixed achievements last earner user when no one has earned it yet.

= 1.0.1 =

* **Improvements**
* Added checks to prevent run code outside the reports pages.
* Improvements and speed performance on queries and functions.

= 1.0.0 =

* Initial release
