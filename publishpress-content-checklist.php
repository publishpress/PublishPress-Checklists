<?php
/**
 * PublishPress Content Checklist plugin bootstrap file.
 *
 * @link        https://publishpress.com/checklist/
 * @package     PublishPress\Content_checklist
 * @author      PublishPress <help@publishpress.com>
 * @copyright   Copyright (C) 2018 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 *
 * @publishpress-content-checklist
 * Plugin Name: PublishPress Content Checklist
 * Plugin URI:  https://publishpress.com/
 * Version: 1.3.8-beta2
 * Description: Add a content checklist for posts
 * Author:      PublishPress
 * Author URI:  https://publishpress.com
 * Text Domain: publishpress-content-checklist
 * Domain Path: /languages
 */

require_once __DIR__ . '/includes.php';

if ( defined( 'PP_CONTENT_CHECKLIST_LOADED' ) ) {
	$plugin = new PublishPress\Addon\Content_checklist\Plugin;
	$plugin->init();
}
