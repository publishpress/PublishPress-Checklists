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
        add_filter('publishpress_checklists_rules_list', [$this, 'filter_rules_list']);
        add_filter('plugins_loaded', [$this, 'loadTextDomain']);

        Factory::getLegacyPlugin();
    }

    /**
     * Get a list of rules for the requirement
     *
     * @param $rules
     *
     * @return array
     */
    public function filter_rules_list($rules)
    {
        return array_merge(
            $rules,
            [
                Plugin::RULE_DISABLED     => __('Disabled', 'publishpress-checklists'),
                Plugin::RULE_ONLY_DISPLAY => __('Show a message in the sidebar while writing',
                    'publishpress-checklists'),
                Plugin::RULE_WARNING      => __('Show a message on the screen before publishing',
                    'publishpress-checklists'),
                Plugin::RULE_BLOCK        => __('Prevent publishing', 'publishpress-checklists'),
            ]
        );
    }

    /**
     * Load the text domain.
     */
    public function loadTextDomain()
    {
        load_plugin_textdomain('publishpress-checklists', false,
            PUBLISHPRESS_CHECKLISTS_RELATIVE_PATH . '/languages/');
    }
}
