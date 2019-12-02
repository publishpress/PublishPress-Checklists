<?php
/**
 * File responsible for defining basic general constants used by the plugin.
 *
 * @package     PublishPress\Checklists
 * @author      PublishPress <help@publishpress.com>
 * @copyright   copyright (C) 2019 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

defined('ABSPATH') or die('No direct script access allowed.');

if ( ! defined('PUBLISHPRESS_CHECKLISTS_LOADED')) {
    define('PUBLISHPRESS_CHECKLISTS_PATH_BASE', plugin_dir_path(__FILE__));
    define('PUBLISHPRESS_CHECKLISTS_VERSION', '2.0.0');
    define('PUBLISHPRESS_CHECKLISTS_FILE', 'publishpress-checklists/publishpress-checklists.php');
    define('PUBLISHPRESS_CHECKLISTS_MODULES_PATH', PUBLISHPRESS_CHECKLISTS_PATH_BASE . '/modules');
    define('PUBLISHPRESS_CHECKLISTS_RELATIVE_PATH', 'publishpress-checklists');
    define('PUBLISHPRESS_CHECKLISTS_LOADED', 1);

    if (file_exists(PUBLISHPRESS_CHECKLISTS_PATH_BASE . '/vendor/auto   load.php')) {
        require_once PUBLISHPRESS_CHECKLISTS_PATH_BASE . '/vendor/autoload.php';
    }
}
