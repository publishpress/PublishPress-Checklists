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
    define('PUBLISHPRESS_CHECKLISTS_ITEM_ID', '6465');
    define('PUBLISHPRESS_CHECKLISTS_PATH_BASE', plugin_dir_path(__FILE__));
    define('PUBLISHPRESS_CHECKLISTS_VERSION', '2.0.0-alpha.1');
    define('PUBLISHPRESS_CHECKLISTS_FILE', 'publishpress-checklists/publishpress-checklists.php');
    define('PUBLISHPRESS_CHECKLISTS_MODULES_PATH', __DIR__ . '/modules');
    define('PUBLISHPRESS_CHECKLISTS_MODULE_PATH', __DIR__ . '/modules/checklists');
    define('PUBLISHPRESS_CHECKLISTS_ITEM_NAME', 'Checklists');
    define('PUBLISHPRESS_CHECKLISTS_LIB_PATH', PUBLISHPRESS_CHECKLISTS_PATH_BASE . '/library');
    define('PUBLISHPRESS_CHECKLISTS_RELATIVE_PATH', 'publishpress-checklists');
    define('PUBLISHPRESS_CHECKLISTS_LOADED', 1);

    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
    }
}
