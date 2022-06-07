=== GamiPress - Transfers ===
Contributors: gamipress, tsunoa, rubengc, eneribs
Tags: gamipress, gamification, gamify, point, achievement, badge, award, reward, credit, engagement, ajax
Requires at least: 4.0
Tested up to: 5.8
Stable tag: 1.2.5
License: GNU AGPLv3
License URI: http://www.gnu.org/licenses/agpl-3.0.html

Allow your users to transfer points, achievements or ranks between them.

== Description ==

Transfers gives you the ability to bring to your users the possibility to transfer their points, achievements or ranks between them.

In just a few minutes, you will be able to place transfer forms around your site and award your users for making transfers.

Transfers extends and expands GamiPress adding new activity events and features.

= New Events =

* New transfer: When an user makes a new transfer.
* Transfer a minimum amount of points: When an user transfers a minimum amount of the desired points type.
* Transfer an achievement: When an user transfers any achievement of the desired achievement type.
* Transfer a specific achievement: When an user transfers a specific achievement.
* Transfer a rank: When an user transfers the any rank of desired rank type.
* Transfer a specific rank: When an user transfers a specific rank.
* Receive a transfer: When an user receives a new transfer.
* Receive a transfer with a minimum amount of points: When an user receives a transfer with a minimum amount of the desired points type.
* Receive a transfer with an achievement: When an user receives a transfer with any achievement of the desired achievement type.
* Receive a transfer with a specific achievement: When an user receives a transfer with a specific achievement.
* Receive a transfer with a rank: When an user receives a transfer with the any rank of desired rank type.
* Receive a transfer with a specific rank: When an user receives a transfer with a specific rank.

= Features =

* Ability to place transfer forms to let users transfer anything.
* Set the transfer recipient or let user input the recipient (with support for auto-complete).
* Set the achievement or rank to transfer or let user choose which to transfer.
* Frontend transfer history with details of each transfer.
* Settings to auto-approve transfers or keep them pending awaiting for approval.
* Easily editable transfers to allow you add new items or add notes.
* Ability to refund transfers and restore transferred items between users.
* Support for WordPress privacy tools.

= Transfer Forms =

* Points: To let your users transfer a desired amount of points.
* Achievement: To let your users transfer an earned achievement.
* Rank: To let your users transfer a reached rank.

= Points Transfer Forms =

* Fixed Amount: Users will be able to transfer a predefined amount of points.
* Custom Amount: Users will be able to set a custom amount of points to transfer.
* Options: Users will be able to choose between a predefined amount of points to transfer.

= Other Features =

* Integrated with the official add-ons that add new content to achievements and ranks.
* Shortcodes to place any transfer form anywhere (with support to GamiPress live shortcode embedder).
* Widgets to place any transfer form on any sidebar.

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

= 1.2.5 =

* **Improvements**
* Full rework of the reward process to revoke items to sender even if transfer is in pending status.
* Added new subprocesses to the transfer workflow to revoke items on pending, award items on complete and restore items on refund.

= 1.2.4 =

* **Improvements**
* Ensure to make the transfer history shortcode work with the page URL where is placed.
* **Bug Fixes**
* Multisite: Make all functions multisite compatible to make transfers work on multisites.

= 1.2.3 =

* **Bug Fixes**
* Fixed the title field visibility in the Points Transfer widget.

= 1.2.2 =

* **Bug Fixes**
* Correctly register the transfer date and transfer notes date based in the WordPress timezone settings.

= 1.2.1 =

* **Improvements**
* Prevent PHP warnings while creating a transfer manually.

= 1.2.0 =

* **Improvements**
* Improved compatibility with multisite installs.

= 1.1.9 =

* **New Features**
* Added the ability to show transfers sent, received or both on the transfers history.
* Added the attribute "history" on the [gamipress_transfers_history] shortcode.
* Added the field "History" on the GamiPress: Transfers History block and widget.
* **Improvements**
* Updated deprecated jQuery functions.

= 1.1.8 =

* **Improvements**
* Style improvements on recipient, achievement and rank autocomplete.

= 1.1.7 =

* **New Features**
* Turn transfer history shortcode into a block and a widget.
* Added the ability to display the transfer history of the current logged in user or a specific user.
* Added the ability to setup the recipient placeholder text on transfers shortcodes, blocks and widgets.
* New setting to setup the recipient display on the autocomplete functionality.
* New setting to filter recipients by role to display on the autocomplete functionality.

= 1.1.6 =

* **New Features**
* Added support to GamiPress 1.8.0.
* **Bug Fixes**
* Fixed achievement and rank selector.
* Fixed user selector.
* **Improvements**
* Make use of WordPress security functions for ajax requests.

= 1.1.5 =

* **New Features**
* Added the ability to use multiples points types in the same transfer form.
* Added the field "Allow Select Points Type" to the Points Transfer block and widget.
* Added the attribute "select_points_type" to the [gamipress_points_transfer] shortcode.

= 1.1.4 =

* **Developer Notes**
* Added new hooks to improve transfers history extensibility.
* Improved transfers query functions to correctly query transfers by user and recipient.

= 1.1.3 =

* **Improvements**
* Added new hooks to improve add-on extensibility.
* **Developer Notes**
* Added support to GamiPress shortcode groups.

= 1.1.2 =

* **New Features**
* Added events to award or deduct to the transfer recipient for receiving a transfer.
* **Developer Notes**
* Added support to the brand new gamipress_select2() class.

= 1.1.1 =

* **Improvements**
* Improved compatibility of auto-complete results box position function.

= 1.1.0 =

* **New Features**
* Added support to GamiPress 1.7.0.
* **Improvements**
* Improved post and user selector on widgets area and shortcode editor.
* Great amount of code reduction thanks to the new GamiPress 1.7.0 API functions.
