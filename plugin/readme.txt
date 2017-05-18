=== PublishPress Content Checklist ===
Contributors: PressShack
Tags: publishpress, checklist
Requires at least: 4.6
Tested up to: 4.7
Stable tag: 1.1.0
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
4. Search for "PublishPress Content Checklist";
5. Install PublishPress Content Checklist plugin;
6. Activate the PublishPress Content Checklist plugin.

**Manually uploading the plugin to your repository**

1. Download the PublishPress Content Checklist plugin zip file;
2. Upload the plugin to your site's repository under the *"/wp-content/plugins/"* directory;
3. Go to your site's admin page;
4. Access the "Plugins" page;
5. Activate the PublishPress Content Checklist plugin.

== Usage ==
- Make sure you have PublishPress plugin installed and active;
- Go to PublishPress Settings page, click on "Checklist" tab and customize its options at will;
- That's it.

== Changelog ==

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

= [1.1.1] = UNRELEASED =
* Fixed:
* Removed .DS_Store file from the package
* Fixed the "Hello Dolly" message in the Freemius opt-in dialog
* Increased the minimum WordPress version to 4.6

* Changed:
* Improved settings merging the checkbox and the action list for each requirement
* Changed order for Categories and Tags to stay together in the list
* Changed code to use correct language domain

= [1.1.0] = 2017-05-11 =

* Added:
* Added "Excerpt has text" as requirement
* Added option to set "max" value for the number of categories, tags and words - now you can have min, max or an interval for each requirement.

* Changed:
* Improved the JavaScript code for better readbility

= [1.0.1] - 2017-05-03 =

* Fixed:
* Fixed the name of plugin's main file
* Fixed WordPress-EDD-License-Integration library in the vendor dir

= [1.0.0] - 2017-04-27 =

* Added:
* Added requirement for minimum number of words
* Added requirement for featured image
* Added requirement for minimum number of tags
* Added requirement for minimum number of categories
* Added Freemius integration for feedback and contact form
* Added option to display a warning icon in the publish box
* Added checklist to the post form
* Added option to select specific post types
