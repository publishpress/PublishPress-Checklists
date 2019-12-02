<?php
/**
 * File responsible for defining basic addon class
 *
 * @package     PublishPress\Checklists
 * @author      PublishPress <help@publishpress.com>
 * @copyright   copyright (C) 2019 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Checklists\Core;

defined('ABSPATH') or die('No direct script access allowed.');

class Plugin
{

    /**
     * The rule that disables
     */
    const RULE_DISABLED = 'off';

    /**
     * The rule that do not warning, or block
     */
    const RULE_ONLY_DISPLAY = 'only_display';

    /**
     * The rule that displays a warning
     */
    const RULE_WARNING = 'warning';

    /**
     * The rule that blocks
     */
    const RULE_BLOCK = 'block';

    /**
     * Flag for debug
     *
     * @var boolean
     */
    protected $debug = false;

    /**
     * The method which runs the plugin
     */
    public function init()
    {
        add_filter('plugins_loaded', [$this, 'loadTextDomain']);

        Factory::getLegacyPlugin();
    }

    /**
     * Load the text domain.
     */
    public function loadTextDomain()
    {
        load_plugin_textdomain('publishpress-checklists', false,
            PPCH_RELATIVE_PATH . '/languages/');
    }
}
