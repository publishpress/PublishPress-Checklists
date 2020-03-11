<?php
/**
 * PublishPress Checklists plugin bootstrap file.
 *
 * @link        https://publishpress.com/checklists/
 * @package     PublishPress\Checklists
 * @author      PublishPress <help@publishpress.com>
 * @copyright   Copyright (C) 2019 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 *
 * @publishpress-checklists
 * Plugin Name: PublishPress Checklists
 * Plugin URI:  https://publishpress.com/
 * Version: 2.0.2-beta.1
 * Description: Add support for checklists in WordPress
 * Author:      PublishPress
 * Author URI:  https://publishpress.com
 * Text Domain: publishpress-checklists
 * Domain Path: /languages
 */

require_once __DIR__ . '/includes.php';

if (defined('PPCH_LOADED')) {
    $plugin = new \PublishPress\Checklists\Core\Plugin();
    $plugin->init();
}
