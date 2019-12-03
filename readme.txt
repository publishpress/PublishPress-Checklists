=== PublishPress Checklists ===
Contributors: publishpress, andergmartins, stevejburge, pressshack
Author: PublishPress, PressShack
Author URI: https://publishpress.com
Tags: publishpress, checklist
Requires at least: 4.7.0
Requires PHP: 5.4
Tested up to: 5.3
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Extend PublishPress implementing a list of requirements to publish posts.

== Description ==
Extend PublishPress implementing a list of requirements to publish posts.

== Installation ==
There're two ways to install PublishPress plugin:

**Through your WordPress site's admin**

1. Go to your site's admin page;
2. Access the "Plugins" page;
3. Click on the "Add New" button;
4. Search for "PublishPress Checklists";
5. Install PublishPress Checklists plugin;
6. Activate the PublishPress Checklists plugin.

**Manually uploading the plugin to your repository**

1. Download the PublishPress Checklists plugin zip file;
2. Upload the plugin to your site's repository under the *"/wp-content/plugins/"* directory;
3. Go to your site's admin page;
4. Access the "Plugins" page;
5. Activate the PublishPress Checklists plugin.

== Usage ==
- Make sure you have PublishPress plugin installed and active;
- Go to PublishPress Settings page, click on "Checklist" tab and customize its options at will;
- That's it.

== Changelog ==

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

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
