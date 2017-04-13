<?php
defined('ABSPATH') or die("No direct script access allowed.");

/**
 * File responsible for defining basic general constants used by the plugin.
 *
 * @package     PublishPress\Checklist
 * @author      PressShack <help@pressshack.com>
 * @copyright   Copyright (C) 2017 Open Source Training, LLC. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

require_once 'freemius.php';

if (!function_exists('is_plugin_inactive')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

$publishpressPath = WP_PLUGIN_DIR . '/publishpress/publishpress.php';
if (!file_exists($publishpressPath) || is_plugin_inactive('publishpress/publishpress.php')) {
    function pp_checklist_admin_error() {
        ?>
        <div class="notice notice-error is-dismissible">
            Please, install and activate the <a href="https://wordpress.org/plugins/publishpress" target="_blank">PublishPress</a></strong> plugin in order to make <em>PublishPress Checklist</em> work.</p>
        </div>
        <?php 
    }
    add_action( 'admin_notices', 'pp_checklist_admin_error' );

    define('PUBLISHPRESS_PLG_CHECKLIST_HALT', 1);
}

if (!defined('PUBLISHPRESS_PLG_CHECKLIST_HALT')) {
    require_once $publishpressPath;

    if (!defined('PUBLISHPRESS_PLG_CHECKLIST')) {
        define('PUBLISHPRESS_PLG_CHECKLIST', "Checklist");
    }

    if (!defined('PUBLISHPRESS_PLG_CHECKLIST_NAME')) {
        define('PUBLISHPRESS_PLG_CHECKLIST_NAME', "PublishPress Checklist");
    }

    if (!defined('PUBLISHPRESS_PLG_CHECKLIST_SLUG')) {
        define('PUBLISHPRESS_PLG_CHECKLIST_SLUG', strtolower(PUBLISHPRESS_PLG_CHECKLIST));
    }

    if (!defined('PUBLISHPRESS_PLG_CHECKLIST_NAMESPACE')) {
        define('PUBLISHPRESS_PLG_CHECKLIST_NAMESPACE', PUBLISHPRESS_NAMESPACE ."\\". PUBLISHPRESS_PLG_CHECKLIST);
    }

    if (!defined('PUBLISHPRESS_PLG_CHECKLIST_PATH_BASE')) {
        define('PUBLISHPRESS_PLG_CHECKLIST_PATH_BASE', plugin_dir_path(__FILE__));
    }

    if (!defined('PUBLISHPRESS_PLG_CHECKLIST_PATH_CORE')) {
        define('PUBLISHPRESS_PLG_CHECKLIST_PATH_CORE', PUBLISHPRESS_PLG_CHECKLIST_PATH_BASE . PUBLISHPRESS_PLG_CHECKLIST);
    }

    if (!defined('PUBLISHPRESS_PLG_CHECKLIST_VERSION')) {
        define('PUBLISHPRESS_PLG_CHECKLIST_VERSION', "1.0.0");
    }

    if (!defined('PUBLISHPRESS_PLG_CHECKLIST_LOADED')) {
        define('PUBLISHPRESS_PLG_CHECKLIST_LOADED', 1);
    }
}