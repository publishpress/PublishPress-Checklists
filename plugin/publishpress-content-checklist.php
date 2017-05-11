<?php
/**
 * PublishPress Content Checklist plugin bootstrap file.
 *
 * @link        https://pressshack.com/publishpress/checklist/
 * @package     PublishPress\Content_checklist
 * @author      PressShack <help@pressshack.com>
 * @copyright   Copyright (C) 2017 Open Source Training, LLC. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 *
 * @publishpress-content-checklist
 * Plugin Name: PublishPress Content Checklist
 * Plugin URI:  https://pressshack.com/publishpress/
 * Version:     1.1.0
 * Description: Add a content checklist for posts
 * Author:      PressShack
 * Author URI:  https://pressshack.com
 */

require_once __DIR__ . '/includes.php';

if ( defined( 'PP_CONTENT_CHECKLIST_LOADED' ) ) {
	$plugin = new PublishPress\Addon\Content_checklist\Plugin;
	$plugin->init();
}
