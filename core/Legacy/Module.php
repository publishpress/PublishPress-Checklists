<?php
/**
 * @package     PublishPress\Checklistss
 * @author      PublishPress <help@publishpress.com>
 * @copyright   copyright (C) 2019 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.1.0
 */

namespace PublishPress\Checklists\Core\Legacy;

use PublishPress\Checklists\Core\Factory;

/**
 * Module
 */
class Module
{
    protected $debug = false;

    public $options;

    public $published_statuses = [
        'publish',
        // 'future',
        'private',
    ];

    /**
     * Returns whether the module with the given name is enabled.
     *
     * @param string module Slug of the module to check
     *
     * @return <code>true</code> if the module is enabled, <code>false</code> otherwise
     * @since  0.7
     *
     */
    public function module_enabled($slug)
    {
        $legacyPlugin = Factory::getLegacyPlugin();

        return isset($legacyPlugin->$slug) && $legacyPlugin->$slug->module->options->enabled == 'on';
    }

    /**
     * Gets an array of allowed post types for a module
     *
     * @return array post-type-slug => post-type-label
     */
    public function get_all_post_types()
    {
        $allowed_post_types = [
            'post' => __('Post'),
            'page' => __('Page'),
        ];
        $custom_post_types  = $this->get_supported_post_types_for_module();

        foreach ($custom_post_types as $custom_post_type => $args) {
            $allowed_post_types[$custom_post_type] = $args->label;
        }

        return $allowed_post_types;
    }

    /**
     * Cleans up the 'on' and 'off' for post types on a given module (so we don't get warnings all over)
     * For every post type that doesn't explicitly have the 'on' value, turn it 'off'
     * If add_post_type_support() has been used anywhere (legacy support), inherit the state
     *
     * @param array  $module_post_types Current state of post type options for the module
     * @param string $post_type_support What the feature is called for post_type_support (e.g. 'ppma_calendar')
     *
     * @return array $normalized_post_type_options The setting for each post type, normalized based on rules
     *
     * @since 0.7
     */
    public function clean_post_type_options($module_post_types = [], $post_type_support = null)
    {
        $normalized_post_type_options = [];
        $all_post_types               = array_keys($this->get_all_post_types());
        foreach ($all_post_types as $post_type) {
            if ((isset($module_post_types[$post_type]) && $module_post_types[$post_type] == 'on') || post_type_supports($post_type,
                    $post_type_support)) {
                $normalized_post_type_options[$post_type] = 'on';
            } else {
                $normalized_post_type_options[$post_type] = 'off';
            }
        }

        return $normalized_post_type_options;
    }

    /**
     * Get all of the possible post types that can be used with a given module
     *
     * @param object $module The full module
     *
     * @return array $post_types An array of post type objects
     *
     * @since 0.7.2
     */
    public function get_supported_post_types_for_module($module = null)
    {
        $pt_args = [
            '_builtin' => false,
            'public'   => true,
        ];
        $pt_args = apply_filters('publishpress_checklists_supported_module_post_types_args', $pt_args, $module);

        $postTypes = get_post_types($pt_args, 'objects');

        $postTypes = apply_filters('publishpress_checklists_supported_module_post_types', $postTypes);

        return $postTypes;
    }

    /**
     * Whether or not the current page is an PublishPress settings view (either main or module)
     * Determination is based on $pagenow, $_GET['page'], and the module's $settings_slug
     * If there's no module name specified, it will return true against all PublishPress settings views
     *
     * @param string $module_name (Optional) Module name to check against
     *
     * @return bool $is_settings_view Return true if it is
     * @since 0.7
     *
     */
    public function is_whitelisted_settings_view($module_name = null)
    {
        global $pagenow;

        // All of the settings views are based on admin.php and a $_GET['page'] parameter
        if ($pagenow != 'admin.php' || ! isset($_GET['page'])) {
            return false;
        }

        if (isset($_GET['page']) && $_GET['page'] === 'ppma-modules-settings') {
            if (empty($module_name)) {
                return true;
            }

            if ( ! isset($_GET['module']) || $_GET['module'] === 'ppma-modules-settings-settings') {
                if (in_array($module_name, ['editorial_comments', 'notifications', 'dashboard'])) {
                    return true;
                }
            }

            $slug = str_replace('_', '-', $module_name);
            if (isset($_GET['module']) && $_GET['module'] === 'ppma-' . $slug . '-settings') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the publicly accessible URL for the module based on the filename
     *
     * @param string $filepath File path for the module
     *
     * @return string $module_url Publicly accessible URL for the module
     * @since 0.7
     *
     */
    public function get_module_url($file)
    {
        $module_url = plugins_url('/', $file);

        return trailingslashit($module_url);
    }

    /**
     * Add settings help menus to our module screens if the values exist
     * Auto-registered in PublishPress::register_module()
     *
     * @since 0.7
     */
    public function action_settings_help_menu()
    {
        $screen = get_current_screen();

        if ( ! method_exists($screen, 'add_help_tab')) {
            return;
        }

        if ($screen->id != 'content_checklist_page_' . $this->module->settings_slug) {
            return;
        }

        // Make sure we have all of the required values for our tab
        if (isset($this->module->settings_help_tab['id'], $this->module->settings_help_tab['title'], $this->module->settings_help_tab['content'])) {
            $screen->add_help_tab($this->module->settings_help_tab);

            if (isset($this->module->settings_help_sidebar)) {
                $screen->set_help_sidebar($this->module->settings_help_sidebar);
            }
        }
    }
}
