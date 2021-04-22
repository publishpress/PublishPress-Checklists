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

if (!defined('PPCH_LOADED')) {
    define('PPCH_PATH_BASE', plugin_dir_path(__FILE__));
    define('PPCH_VERSION', '2.5.0');
    define('PPCH_FILE', __DIR__ . '/publishpress-checklists.php');
    define('PPCH_MODULES_PATH', PPCH_PATH_BASE . '/modules');
    define('PPCH_RELATIVE_PATH', 'publishpress-checklists');
    define('PPCH_LOADED', 1);

    if (file_exists(PPCH_PATH_BASE . '/vendor/autoload.php') && !class_exists('ComposerAutoloaderInit268738d24a0d425d84d7297c2840e1ce')) {
        require_once PPCH_PATH_BASE . '/vendor/autoload.php';
    }
}

