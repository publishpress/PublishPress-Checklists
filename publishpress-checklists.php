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
 * Version: 2.0.3-alpha.2
 * Description: Add support for checklists in WordPress
 * Author:      PublishPress
 * Author URI:  https://publishpress.com
 * Text Domain: publishpress-checklists
 * Domain Path: /languages
 */

require_once __DIR__ . '/includes.php';

if (is_admin() && !defined('PUBLISHPRESS_CHECKLISTS_SKIP_VERSION_NOTICES')) {
    $includesPath = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'publishpress' . DIRECTORY_SEPARATOR
        . 'wordpress-version-notices' . DIRECTORY_SEPARATOR . 'includes.php';

    if (file_exists($includesPath)) {
        require_once $includesPath;
    }

    add_filter(\PPVersionNotices\Module\TopNotice\Module::SETTINGS_FILTER, function ($settings) {
        $settings['publishpress-checklists'] = [
            'message' => 'You\'re using PublishPress Checklists Free. The Pro version has more features and support. %sUpgrade to Pro%s',
            'link'    => 'https://publishpress.com/links/checklists-banner',
            'screens' => [
                ['base' => 'toplevel_page_ppch-checklists', 'id' => 'toplevel_page_ppch-checklists',],
                ['base' => 'checklists_page_ppch-settings', 'id' => 'checklists_page_ppch-settings',],
            ]
        ];

        return $settings;
    });
}

if (defined('PPCH_LOADED')) {
    $plugin = new \PublishPress\Checklists\Core\Plugin();
    $plugin->init();
}
