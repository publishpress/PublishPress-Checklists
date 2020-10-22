=== PublishPress Checklists: Pre-Publishing Approval Task Checklist for WordPress Content ===

Contributors: publishpress, kevinB, stevejburge, andergmartins
Author: PublishPress
Author URI: https://publishpress.com
Tags: approval, checklist, maximum, minimum, requirement
Requires at least: 4.7
Requires PHP: 5.6
Tested up to: 5.5
Stable tag: 2.4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

You can define tasks that must be complete before content is published. Do you get a red X or a green checkmark?

== Description ==

[PublishPress Checklists](https://publishpress.com/authors/) is the best plugin to make sure your content is ready to go live. With PublishPress Checklists, you can choose pre-publishing requirements for your content. Using PublishPress Checklists, you define tasks that must be completed before content is published.

You can make sure your posts have a minimum or maximum number of words. You can ensure that all your posts have a featured image. You can force authors to use a specific number of Tags or Categories.

Next to every post and page, writers see a checklist box, showing the tasks they need to complete. Tasks can either be recommended or required. As authors complete each task, the red X automatically turns to a green checkmark.

[Click here to read the Getting Started guide for PublishPress Checklists](https://publishpress.com/knowledge-base/checklists-started/).

= The Default Checklist Requirements =

Each task on your pre-publish checklist can be configured to meet your site’s needs. You can also set maximum and minimum values. Here are the default tasks:

* Require a maximum or minimum number of characters in Excerpt
* Require a maximum or minimum number of categories
* Require a maximum or minimum number of tags
* Require a maximum or minimum number of words
* Require a featured image

You can configure each requirement, depending on whether you want to require writers to complete the tasks. Here are the four options:

* Disabled
* Recommended: show only in the sidebar
* Recommended: show in the sidebar and before publishing
* Required

If you choose the “Required” option, it will be impossible to publish without completing the task.

= Creating New Checklist Requirements =

You can create new requirements for your checklists by clicking the “Add custom task” link. For example, you can require authors to get a green Yoast sign, or force them to run a spell-check before publishing.

[Click here to see how to create custom requirements](https://publishpress.com/knowledge-base/custom-requirements-checklist/).

It is also possible to create more powerful requirements using a custom plugin. We have created a sample plugin to show how to do this. The sample plugin will automatically check that your site’s authors have included a specific word in their main content. If this new requirement is enabled, it will automatically search the text of your content to make sure it contains the word you choose.

[Click here to see how to create custom requirements with our sample plugin](https://publishpress.com/knowledge-base/custom-requirements-plugin/).

= Checklists for WooCommerce Products =

The Pro version of PublishPress Checklists has support for WooCommerce. There are 18 requirements you can choose:

* Number of characters in Excerpt
* Number of Product tags
* Number of Product categories
* Number of words
* Featured image
* Check the “Virtual” box
* Check the “Downloadable” box
* Enter a “Regular price”
* Enter a “Sale price”
* Schedule the “Sale price”
* Discount for the “Sale price”
* Enter a “SKU”
* Check the “Manage stock?” box
* Check the “Sold individually” box
* Check the “Allow backorders?” box
* Select some products for “Upsells”
* Select some products for “Cross-sells”
* Product image

[Click here to read more about WooCommerce checklists](https://publishpress.com/knowledge-base/use-woocommerce-checklist-add-publishpress/).

= Join PublishPress and get the Pro plugins =

The Pro versions of the PublishPress plugins are well worth your investment. The Pro versions have extra features and faster support. [Click here to join PublishPress](https://publishpress.com/pricing/).

Join PublishPress and you’ll get access to these six Pro plugins:

* [PublishPress Authors Pro](https://publishpress.com/authors) allows you to add multiple authors and guest authors to WordPress posts.
* [PublishPress Capabilities Pro](https://publishpress.com/capabilities) is the plugin to manage your WordPress user roles, permissions, and capabilities.
* [PublishPress Checklists Pro](https://publishpress.com/checklists) enables you to define tasks that must be completed before content is published.
* [PublishPress Permissions Pro](https://publishpress.com/permissions) is the plugin for advanced WordPress permissions.
* [PublishPress Pro](https://publishpress.com/publishpress) is the plugin for managing and scheduling WordPress content.
* [PublishPress Revisions Pro](https://publishpress.com/revisions) allows you to update your published pages with teamwork and precision.

Together, these plugins are a suite of powerful publishing tools for WordPress. If you need to create a professional workflow in WordPress, with moderation, revisions, permissions and more … then you should try PublishPress.

=  Bug Reports =
Bug reports for PublishPress Checklists are welcomed in our [repository on GitHub](https://github.com/publishpress/publishpress-checklists). Please note that GitHub is not a support forum, and that issues that aren’t properly qualified as bugs will be closed.

= Follow the PublishPress team =

Follow PublishPress on [Facebook](https://www.facebook.com/publishpress), [Twitter](https://www.twitter.com/publishpresscom) and [YouTube](https://www.youtube.com/publishpress)

== Screenshots ==

1. Create your own checklists
2. Custom checklist rules
3. Configure your requirements
4. Feedback before publishing
5. WooCommerce products checklist - available in the Pro version

== Changelog ==

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

= [2.4.2] - 2020-10-22 =

* Fixed: Remove unexistent dependencies for met-box.js, #231;

= [2.4.1] - 2020-10-08 =

* Fixed: Fix JS error Uncaught TypeError: Cannot read property 'doAction' of undefined, #224;
* Fixed: Fix broken menu item if the user doesn't have permissions to see the menu, #226;

= [2.4.0] - 2020-09-22 =

* Added: Added a new task for validating links in the content, #200;
* Added: Added a new task for checking the number of external links, #201;
* Added: Added form validation for required fields in the checklists page, #175;
* Added: Added a new task for requiring approval for specific roles, #104;
* Added: Added new field for custom tasks to select which role can check/uncheck the box, #104;
* Changed: Changed the order of tasks in the settings page, #223;
* Removed: The option "Recommended: show only in the sidebar" were removed and current settings fallback to "Recommended: show in the sidebar and before publishing", which was renamed to just: "Recommended", #195.

= [2.3.2] - 2020-08-20 =

* Fixed: Fixed warnings related to missed dependencies for scripts when the post type is not selected to use checklists, #208;

= [2.3.1] - 2020-08-14 =

* Fixed: Fixed compatibility with WP 5.5;
* Fixed: Fixed Gutenberg and Classic Editor detection, #203, #202;
* Fixed: Fixed invalid selector in jQuery, #197;
* Fixed: Fixed the publishing button that was stuck sometimes making impossible to publish a post, #191;

= [2.3.0] - 2020-08-06 =

* Added: Added new task for checking if all the images in the post has an "alt" attribute, #164;
* Fixed: Fixed the verification for custom taxonomies in the post editor page, #114;
* Fixed: Fixed style for unchecked custom tasks, #184;
* Fixed: Updated language files;
* Changed: Hide Yoast SEO tasks if Yoast's plugin is not activated, #164;
* Changed: Updated translation strings;
* Changed: Changed the algorithm of the Yoast SEO readability and SEO analysis verification, considering the selected score as the minimum score, #169;
* Changed: Change the label of the "Add custom item" button to "Add custom task", #181;

= [2.2.0] - 2020-07-21 =

* Added: Add support to Yoast SEO readability and SEO analysis pass task in the checklists - #86;
* Added: Add new task for checking the limit of chars in the excerpt test - #150;
* Added: Add new task for checking the number of internal links in the text - #52;
* Fixed: Remove not used transient for checking data migration;
* Fixed: JS error message related to missed PP_Checklists object;
* Fixed: Enqueue scripts only when required - #106;
* Fixed: Fixed translation support adding French and British English translations;
* Changed: Updated the PHP min requirement from 5.4 to 5.6;
* Changed: Updated the WordPress tested up to version to 5.4;
* Changed: Updated the label and text for some tasks;

= [2.1.0] - 2020-05-07 =

* Added: Add permalink validation rule for the checklists - #115;
* Added: Add option to select user roles to skip specific requirements - #131;
* Added: Add a menu link to upgrade to the Pro plan;
* Changed: Improve UI for custom items in the checklist, removing the "X" icon - #126;
* Removed: Remove the option to hide the Publish Button due to conflicts with Gutenberg;
* Fixed: Fixed the tabs for post types in the Checklists admin page. If you have too many post types the second line of tabs was overlaying the first line - #132;
* Fixed: Fixed the checklist warning popup when you are updating a published post and has unchecked required tasks in the checklist, for the classic editor - #124;
* Fixed: Fixed the list of available post types for the checklists to display any post type that has the show_ui = true. Non public post types are now recognized - #127;
* Fixed: Fixed the list of post types in the Checklists page hiding the tabs of post types that are not selected in the settings - #136;
* Fixed: Fixed the error displayed on Windows servers when the constant DIRECTORY_SEPARATOR is not defined;
* Fixed: Fixed empty checklists on fresh installs due to no post type being selected. Posts is selected by default now - #140;
* Fixed: Fix warning icon on Gutenberg moving it from the side to over the publish button - #138;

= [2.0.2] - 2020-03-16 =

* Fixed: Fix Checklist for custom hierarquical taxonomies when using Gutenberg;
* Fixed: Small improvements to the UI;
* Fixed: Fix compatibility with Rank Math fixing error in Gutenberg;
* Added: Added hooks to extend the interface for the Pro version;

= [2.0.1] - 2020-02-07 =

* Fixed: Fixed the prefix of post types in the post_type_support variable;
* Fixed: Adjusted the plugin URL for assets when working as vendor dependency;
* Fixed: Fix the suffix of the settings section from _post_types, to _general;
* Fixed: Fixed an undefined index error when the index "type" is not defined;
* Fixed: Fixed a JS error when you type in the editor and the word count requirement is set;
* Fixed: Fixed the verification for custom taxonomies on Gutenberg;
* Added: Added filters to allow using the plugin as base for the Pro plugin;

= [2.0.0] - 2019-12-03 =
* Fixed: Fixed the word counter for the Text tab in the Classic Editor;
* Changed: Renamed from "PublishPress Content Checklist" to "PublishPress Checklists";
* Changed: Refactored to be a standalone plugin, not requiring PublishPress anymore;
* Changed: Plugin name and text domain changed from "publishpress-content-checklist" to "publishpress-checklists";
* Changed: Namespace changed from "PublishPress\Addon\Content_checklist" to "PublishPress\Checklists\". The requirements' namespace changed from "PublishPress\Addon\Content_checklist\Requirement" to "PublishPress\Checklists\Core\Requirement";
* Changed: JavaScript object changed from "PP_Content_Checklist" to "PP_Checklists";
* Changed: New admin menu added with the checklists options and settings page;
* Changed: The checklists options section was removed from the settings page to an specific menu item;

= [1.4.7] - 2019-07-21 =
* Fixed: A JS error was preventing to block the post save action when displaying a popup with missed requirements on Classic Editor;

= [1.4.6] - 2019-06-20 =
* Fixed: Avoid JS white screen on Gutenberg "New Post" access by Author with Multiple Authors plugin active and "Remove author from new posts" setting enabled;
* Changed: Change minimum required version of PublishPress to 1.20.0;

= [1.4.5] - 2019-02-22 =
* Fixed: Fixed the pre-publishing check to avoid blocking save when not publishing;

= [1.4.4] - 2019-02-12 =
* Fixed: Fixed JS error that was preventing the Preview button to work properly in the classic editor;

= [1.4.3] - 2019-02-11 =
* Fixed: Fixed translation to PT-BR (thanks to Dionizio Bach);
* Fixed: Fixed bug when word-count script was not loaded;
* Fixed: Fixed JS error if an editor is not found;
* Changed: Changed the label for checklist options in the settings panel;

= [1.4.2] - 2019-01-30 =
* Fixed: Fixed the checklist for the block editor;
* Changed: Removed license key field from the settings tab;

= [1.4.1] - 2019-01-24 =
* Changed: Disable post types by default, if Gutenberg is installed;

= [1.4.0] - 2019-01-14 =
* Fixed: Fixed the TinyMCE plugin to count words to not load in the front-end when TinyMCE is initialized;
* Fixed: Fixed the assets loading to load tinymce-pp-checklists-requirements.js only in the admin;
* Fixed: Fixed conflict between custom taxonomies and tags in the checklist while counting items;
* Added: Added better support for custom post types and custom taxonomies which use WordPress default UI;
* Changed: Update POT file and fixed translations loading the text domain;
* Changed: Updated PT-BT language files;

= [1.3.8] - 2018-04-18 =
* Fixed: Fixed wrong reference to a legacy EDD library's include file;
* Fixed: Fixed PHP warning about undefined property and constant;

= [1.3.7] - 2018-02-21 =
* Fixed: Fixed support for custom post types;

= [1.3.6] - 2018-02-07 =
* Fixed: Fixed error about class EDD_SL_Plugin_Updater being loaded twice;

= [1.3.5] - 2018-02-06 =
* Fixed: Fixed saving action for custom items on the checklist;
* Fixed: Fixed license validation and automatic update;

= [1.3.4] - 2018-01-26 =
* Changed: Changed plugin headers, fixing author and text domain;

= [1.3.3] - 2018-01-26 =
* Fixed: Fixed JS error when the checklist is empty (no requirements are selected);
* Fixed: Fixed compatibility with PHP 5.4 (we will soon require min 5.6);
* Fixed: Fixed custom requirements;
* Fixed: Fixed the requirement of tags;
* Fixed: Fixed PHP Fatal error on some PHP on the featured image requirement;
* Fixed: Fixed category count in the checklist;
* Added: Added action to load plugins' script files;
* Changed: Rebranded to PublishPress;

= [1.3.2] - 2017-08-31 =
* Fixed: Fixed EDD integration and updates;
* Changed: Removed Freemius integration;

= [1.3.1] - 2017-07-13 =
* Fixed: Fixed support for custom post types allowing to use custom items as requirements;

= [1.3.0] - 2017-07-12 =
* Fixed: Fixed the delete button for custom items in the settings. It was remocing wrong items, in an odd pattern;
* Fixed: Fixed PHP warning in the settings page about undefined index in array;
* Fixed: Fixed the menu slug in the Freemius integration;
* Added: Added support for setting specific requirements for each post type, instead of global only;
* Changed: Changed the required minimum version of PublishPress to 1.6.0;
* Changed: Improved extensibility for add-ons;

= [1.2.1] - 2017-06-21 =
* Fixed: Fixed PHP warnings after install and activate
* Fixed: Fixed PHP warnings about wrong index type
* Fixed: Fixed the license and update checker
* Added: Added pt-BR translations
* Changed: Removed English language files
* Changed: Updated Tested Up to 4.8

= [1.2.0] - 2017-06-06 =
* Fixed: Fixes the mask for numeric input fields in the settings tab on Firefox
* Fixed: Fixes the license key validation
* Fixed: Fixes the update system
* Added: Added the option to hide the Publish button if the checklist is not completed
* Added: Added the option to add custom items for the checklist
* Added: Added POT file and English PO files
* Changed: The warning icon in the publish box now appears even for published content

= [1.1.2] - 2017-05-23 =
* Fixed: Fixes the word count feature
* Changed: Displays empty value in the max fields when max is less than min
* Changed: Improves the min and max fields for value equal 0. Displays empty fields.

= [1.1.1] - 2017-05-18 =
* Fixed: Removed .DS_Store file from the package
* Fixed: Fixed the "Hello Dolly" message in the Freemius opt-in dialog
* Fixed: Increased the minimum WordPress version to 4.6
* Changed: Improved settings merging the checkbox and the action list for each requirement
* Changed: Changed order for Categories and Tags to stay together in the list
* Changed: Changed code to use correct language domain

= [1.1.0] - 2017-05-11 =
* Added: Added "Excerpt has text" as requirement
* Added: Added option to set "max" value for the number of categories, tags and words - now you can have min, max or an interval for each requirement.
* Changed: Improved the JavaScript code for better readbility

= [1.0.1] - 2017-05-03 =
* Fixed: Fixed the name of plugin's main file
* Fixed: Fixed WordPress-EDD-License-Integration library in the vendor dir

= [1.0.0] - 2017-04-27 =
* Added: Added requirement for minimum number of words
* Added: Added requirement for featured image
* Added: Added requirement for minimum number of tags
* Added: Added requirement for minimum number of categories
* Added: Added Freemius integration for feedback and contact form
* Added: Added option to display a warning icon in the publish box
* Added: Added checklist to the post form
* Added: Added option to select specific post types
