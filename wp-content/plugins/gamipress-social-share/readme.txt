=== GamiPress - Social Share ===
Contributors: gamipress, tsunoa, rubengc, eneribs
Tags: gamipress, gamification, point, achievement, badge, award, reward, credit, engagement, ajax
Requires at least: 4.4
Tested up to: 6.0
Stable tag: 1.2.4
License: GNU AGPLv3
License URI: http://www.gnu.org/licenses/agpl-3.0.html

Award your users for sharing your website content on social networks.

== Description ==

Social Share gives you the ability to award your users with digital rewards for sharing content from your website on social networks.

Place the share buttons anywhere, including in-line on any page or post, using a simple shortcode or on any sidebar through a configurable widget.

Also, this add-on adds new activity events and features to extend and expand the functionality of GamiPress.

= New Events =

= URL sharing =

* Share any url on any social network: When an users shares an url on a social network.
* Share specific url on any social network: When an users shares a specific url on a social network.
* Share any url on specific social network: When an users shares an url on a specific social network.
* Share specific url on specific social network: When an users shares a specific url on a specific social network.

= Post sharing =

* Share any post on any social network: When an users shares a post on a social network.
* Share specific post on any social network: When an users shares a specific post on a social network.
* Share any post on specific social network: When an users shares a post on a specific social network.
* Share specific post on specific social network: When an users shares a specific post on a specific social network.

= Post author =

* Get a share on any social network on any post: When an author gets a share on any social network on a post.
* Get a share on any social network on a specific post: When an author gets a share on any social network on a specific post.
* Get a share on a specific social network on any post: When an author gets a share on a specific social network on a post.
* Get a share on a specific social network on a specific post: When an author gets a share on a specific social network on a specific post.

= Features =

* Ability to award your users for sharing content from your website through the new activity events.
* Ability to award your content creators for getting shares on their posts through the new activity events.
* Settings to automatically append the share buttons on any post type.
* Shortcode to show the share buttons anywhere (with support to GamiPress live shortcode embedder).
* Widget to show the share buttons on any sidebar.

= Supported Social Networks =

* Twitter
* Facebook
* LinkedIn
* Pinterest

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

= 1.2.4 =

* **Improvements**
* Update functions for compatibility with last versions of GamiPress and WordPress.

= 1.2.3 =

* **Improvements**
* Removed backward compatibility support with Google+.

= 1.2.2 =

* **Improvements**
* Updated deprecated jQuery functions.

= 1.2.1 =

* **Developer Notes**
* Added filterable events delays.

= 1.2.0 =

* **Improvements**
* Style improvements for the Facebook "share" button.

= 1.1.9 =

* **Improvements**
* Style fixes to keep buttons correctly aligned.
* Fixed some incorrect styles to the Facebook "share" button.

= 1.1.8 =

* **New Features**
* Added support to GamiPress 1.8.0.
* **Improvements**
* Make use of WordPress security functions for ajax requests.

= 1.1.7 =

* **New Features**
* Moved Social Share shortcode, widget and block output to the share-buttons.php template file.

= 1.1.6 =

* **Bug Fixes**
* Fixed a wrong check on events that require share on specific network.

= 1.1.5 =

* **New Features**
* Added the ability to define an empty title on Social Share shortcode, widget and block.
* Added support to latest LinkedIn API release.

= 1.1.4 =

* **New Features**
* Added 4 new events based on URL sharing.
* Added "URL" and "Thumbnail URL" settings on GamiPress -> Settings -> Add-ons -> Social Share.
* Added the fields "URL" and "Thumbnail URL" on Social Share widget and block.
* Added the attributes "url" and "pinterest_thumbnail" on [gamipress_social_share] shortcode.
* Added support to multiples Social Share buttons with different URLs on the same page.
* **Improvements**
* Improved detection of URL shared on twitter.
* Make all social network URL shared detection more flexible.

= 1.1.3 =

* **New Features**
* Added support to GamiPress 1.7.0.
* **Improvements**
* Great amount of code reduction thanks to the new GamiPress 1.7.0 API functions.

= 1.1.2 =

* **Improvements**
* Improved Facebook share event detection.

= 1.1.1 =

* **New Features**
* Added support again for the Facebook "share" action.
* **Improvements**
* Added the ability to differentiate Facebook like/recommend actions from Facebook share action allowing to award in different ways for both actions.
* **Developer Notes**
* Added more filters to allow extend each network template parameters.


= 1.1.0 =

* **New Features**
* Full support to GamiPress Gutenberg blocks.