<?php
/**
 * PublishPress Checklist plugin bootstrap file.
 *
 * @link        https://pressshack.com/publishpress/checklist/
 * @package     PublishPress\Checklist
 * @author      PressShack <help@pressshack.com>
 * @copyright   Copyright (C) 2017 Open Source Training, LLC. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 *
 * @embedpress-wistia
 * Plugin Name: PublishPress Checklist
 * Plugin URI:  https://pressshack.com/embedpress/wistia/
 * Version:     1.0.0
 * Description: Add a checklist for posts before publish
 * Author:      PressShack
 * Author URI:  https://pressshack.com
*/

require_once __DIR__ . "/includes.php";

$plugin = new PublishPress\Addon\Checklist\Plugin;
$plugin->init();