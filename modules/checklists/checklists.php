<?php
/**
 * @package PublishPress
 * @author  PublishPress
 *
 * copyright (C) 2019 PublishPress
 *
 * ------------------------------------------------------------------------------
 * Based on Edit Flow
 * Author: Daniel Bachhuber, Scott Bressler, Mohammad Jangda, Automattic, and
 * others
 * Copyright (c) 2009-2016 Mohammad Jangda, Daniel Bachhuber, et al.
 * ------------------------------------------------------------------------------
 *
 * This file is part of PublishPress
 *
 * PublishPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PublishPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PublishPress.  If not, see <http://www.gnu.org/licenses/>.
 */

use PublishPress\Checklists\Core\Factory;
use PublishPress\Checklists\Core\Requirement\Base_requirement;
use PublishPress\Checklists\Core\Requirement\Custom_item;
use PublishPress\Checklists\Core\Legacy\Module;
use PublishPress\Checklists\Core\Plugin;

if ( ! class_exists('PPCH_Checklists')) {
    /**
     * class PPCH_Checklists
     */
    class PPCH_Checklists extends Module
    {

        const METADATA_TAXONOMY = 'pp_checklist_meta';

        const METADATA_POSTMETA_KEY = "_pp_checklist_meta";

        const SETTINGS_SLUG = 'pp-checklists-settings';

        const POST_META_PREFIX = 'pp_checklist_custom_item_';

        /**
         * @var string
         */
        const MENU_SLUG = 'ppch-checklists';

        public $module_name = 'checklists';

        /**
         * List of requirements, filled with instances of requirement classes.
         * The list is indexed by post types.
         *
         * @var array
         */
        protected $requirements = [];

        /**
         * List of post types which supports checklists
         *
         * @var array
         */
        protected $post_types = [];

        /**
         * Instace for the module
         *
         * @var stdClass
         */
        public $module;

        /**
         * Construct the PPCH_Checklists class
         */
        public function __construct()
        {
            $legacyPlugin = Factory::getLegacyPlugin();

            $defaultChecked = $legacyPlugin->isBlockEditorActive() ? 'off' : 'on';

            $this->module_url = $this->getModuleUrl(__FILE__);

            // Register the module with PublishPress
            $args = [
                'title'             => __('Checklists', 'publishpress-checklists'),
                'short_description' => __('Define tasks that must be complete before content is published.',
                    'publishpress-checklists'),
                'module_url'        => $this->module_url,
                'icon_class'        => 'dashicons dashicons-feedback',
                'slug'              => 'checklists',
                'default_options'   => [
                    'enabled'                  => 'on',
                    'post_types'               => [
                        'post' => $defaultChecked,
                    ],
                    'show_warning_icon_submit' => 'no',
                    'hide_publish_button'      => 'no',
                    'custom_items'             => [],
                ],
                'autoload'          => true,
            ];

            // Apply a filter to the default options
            $args['default_options'] = apply_filters('publishpress_checklists_requirements_default_options',
                $args['default_options']);

            $this->module = $legacyPlugin->register_module($this->module_name, $args);
        }

        public function migrateLegacyOptions()
        {
            $legacyOptions = get_option('publishpress_checklist_options');
            if ( ! empty($legacyOptions)) {
                update_option('publishpress_checklists_checklists_options', $legacyOptions);
                delete_option('publishpress_checklist_options');
            }
        }

        /**
         * Loads the requirements for each post type
         */
        protected function instantiate_requirement_classes()
        {
            $post_types = $this->get_post_types();

            foreach ($post_types as $slug => $label) {
                $this->instantiate_post_type_requirements($slug);
            }
        }

        /**
         * Instantiates the requirements for the given post type
         *
         * @param string $post_type
         */
        protected function instantiate_post_type_requirements($post_type)
        {
            global $wp_taxonomies;

            $req_classes = apply_filters('publishpress_checklists_post_type_requirements', [], $post_type);

            if ( ! isset($this->requirements[$post_type])) {
                $this->requirements[$post_type] = [];
            }

            foreach ($req_classes as $class_name) {
                $params = null;

                // Some classes can be sent as serialized data, containing the class and params. If it is only string, it won't be affected.
                $class_name = maybe_unserialize($class_name);

                // Add support to additional arguments.
                if (is_array($class_name)) {
                    $params     = $class_name['params'];
                    $class_name = $class_name['class'];

                    // Check if the taxonomy is displayed in the UI.
                    if (isset($params['taxonomy'])) {
                        if (isset($wp_taxonomies[$params['taxonomy']])) {
                            $taxonomy = $wp_taxonomies[$params['taxonomy']];

                            if ( ! $taxonomy->show_ui) {
                                continue;
                            }

                            // Ignore multiple authors taxonomy
                            // @todo: add support for multiple authors
                            if ($taxonomy->query_var === 'ppma_author') {
                                continue;
                            }
                        }
                    }
                }

                if (class_exists($class_name)) {
                    // Instantiate the class
                    $instance = new $class_name($this->module, $post_type);

                    if ( ! is_null($params) && method_exists($instance, 'set_params')) {
                        $instance->set_params($params);
                    }

                    $this->requirements[$post_type][] = $instance;
                } else {
                    Factory::getErrorHandler()->add('PublishPress Checklist Requirement Class not found', $class_name);
                }
            }

            // Instantiate custom items
            if (isset($this->module->options->custom_items) && ! empty($this->module->options->custom_items)) {
                foreach ($this->module->options->custom_items as $id) {
                    $id = trim((string)$id);

                    // Check if there is a title set for this post type. If not, we do not instantiate
                    $var_name = $id . '_title';
                    if (isset($this->module->options->{$var_name}[$post_type])) {
                        $custom_item                      = new Custom_item($id, $this->module, $post_type);
                        $this->requirements[$post_type][] = $custom_item;
                    }
                }
            }
        }

        /**
         * Set the list of post types
         *
         * @param array $post_types
         *
         * @return array
         */
        public function filter_post_types($post_types)
        {
            $allowed_post_types = [
                'post' => __('Post'),
                'page' => __('Page'),
            ];

            $args = [
                '_builtin' => false,
                'public'   => true,
            ];

            $list = get_post_types($args);

            return array_merge($post_types, $allowed_post_types, $list);
        }

        protected function getPostTypeTaxonomies($post_type)
        {
            global $wp_taxonomies;

            $postTypeTaxonomies = [];

            foreach ($wp_taxonomies as $taxonomy) {
                if (in_array($post_type, $taxonomy->object_type)) {
                    $postTypeTaxonomies[] = $taxonomy->name;
                }
            }

            return $postTypeTaxonomies;
        }

        /**
         * Set the requirements list for the given post type
         *
         * @param array  $requirements
         * @param string $post_type
         *
         * @return array
         */
        public function filter_post_type_requirements($requirements, $post_type)
        {
            $classes = [];

            // Check the supported taxonomies for the post type.
            $taxonomies = $this->getPostTypeTaxonomies($post_type);

            $taxonomies_map = [
                'category' => '\\PublishPress\\Checklists\\Core\\Requirement\\Categories_count',
                'post_tag' => '\\PublishPress\\Checklists\\Core\\Requirement\\Tags_count',
            ];

            foreach ($taxonomies as $taxonomy) {
                if (array_key_exists($taxonomy, $taxonomies_map)) {
                    $classes[] = $taxonomies_map[$taxonomy];
                } else {
                    $classes[] = maybe_serialize([
                        'class'  => '\\PublishPress\\Checklists\\Core\\Requirement\\Taxonomies_count',
                        'params' => [
                            'post_type' => $post_type,
                            'taxonomy'  => $taxonomy,
                        ],
                    ]);
                }
            }

            // Check the "supports" for the post type.
            $supports_map = [
                'editor'    => [
                    '\\PublishPress\\Checklists\\Core\\Requirement\\Words_count',
                ],
                'thumbnail' => [
                    '\\PublishPress\\Checklists\\Core\\Requirement\\Featured_image',
                ],
                'excerpt'   => [
                    '\\PublishPress\\Checklists\\Core\\Requirement\\Filled_excerpt',
                ],
            ];
            foreach ($supports_map as $supports => $requirements) {
                foreach ($requirements as $requirement) {
                    if (post_type_supports($post_type, $supports)) {
                        $classes[] = $requirement;
                    }
                }
            }

            if ( ! empty($classes)) {
                $requirements = array_merge($requirements, $classes);
            }

            // Make sure we have only unique values.
            $requirements = array_unique($requirements);

            return $requirements;
        }

        /**
         * Initialize the module. Conditionally loads if the module is enabled
         */
        public function init()
        {
            add_action('publishpress_checklists_admin_menu_page', [$this, 'action_admin_menu_page']);
            add_action('publishpress_checklists_admin_submenu', [$this, 'action_admin_submenu']);
            add_action('add_meta_boxes', [$this, 'handle_post_meta_boxes']);
            add_action('save_post', [$this, 'save_post_meta_box'], 10, 2);
            add_action('enqueue_block_editor_assets', [$this, 'enqueue_block_editor_assets']);

            add_filter('publishpress_checklists_post_type_requirements', [$this, 'filter_post_type_requirements'], 10,
                2);
            add_filter('publishpress_checklists_post_types', [$this, 'filter_post_types']);

            add_action('admin_init', [$this, 'migrateLegacyOptions']);
            add_action('admin_init', [$this, 'save_global_checklist']);

            // Editor
            add_filter('mce_external_plugins', [$this, 'add_mce_plugin']);

            add_action('admin_enqueue_scripts', [$this, 'add_admin_scripts']);

            do_action('publishpress_checklists_load_addons');

            // Load the requirements
            $this->instantiate_requirement_classes();
            do_action('publishpress_checklists_load_requirements');

            add_filter('publishpress_checklists_rules_list', [$this, 'filterRulesList']);
        }

        /**
         * Load default editorial metadata the first time the module is loaded
         *
         * @since 0.7
         */
        public function install()
        {

        }

        /**
         * Upgrade our data in case we need to
         *
         * @since 0.7
         */
        public function upgrade($previous_version)
        {

        }

        /**
         * Generate a link to one of the editorial metadata actions
         *
         * @param array $args (optional) Action and any query args to add to the URL
         *
         * @return string $link Direct link to complete the action
         * @since 0.7
         *
         */
        protected function get_link($args = [])
        {
            $args['page']   = 'ppch-settings';
            $args['module'] = 'ppch-settings';

            return add_query_arg($args, get_admin_url(null, 'admin.php'));
        }

        /**
         * Add the MCE plugin file to make the interface between the editor and
         * the requirement meta box. This was the unique way that worked, making
         * it loaded before the MCE is initialized, allowing to configure it.
         *
         * @param array $plugin_array
         */
        public function add_mce_plugin($plugin_array)
        {
            if (is_admin()) {
                $plugin_array['pp_checklists_requirements'] =
                    plugin_dir_url(PUBLISHPRESS_CHECKLISTS_FILE)
                    . 'modules/checklists/assets/js/tinymce-pp-checklists-requirements.js';
            }

            return $plugin_array;
        }

        /**
         * Enqueue scripts and stylesheets for the admin pages.
         */
        public function add_admin_scripts()
        {
            wp_enqueue_style(
                'pp-checklists-requirements',
                $this->module_url . 'assets/css/global-checklists.css',
                false,
                PUBLISHPRESS_CHECKLISTS_VERSION,
                'all'
            );

            wp_register_style('pp-remodal', $this->module_url . 'assets/css/remodal.css', false,
                PUBLISHPRESS_CHECKLISTS_VERSION,
                'all');
            wp_register_style('pp-remodal-default-theme', $this->module_url . 'assets/css/remodal-default-theme.css',
                ['pp-remodal'], PUBLISHPRESS_CHECKLISTS_VERSION, 'all');

            wp_enqueue_style(
                'pp-checklists-admin',
                $this->module_url . 'assets/css/admin.css',
                ['pp-remodal', 'pp-remodal-default-theme'],
                PUBLISHPRESS_CHECKLISTS_VERSION,
                'all'
            );

            wp_enqueue_script(
                'pp-checklists-admin',
                plugins_url('/modules/checklists/assets/js/global-checklists.js', PUBLISHPRESS_CHECKLISTS_FILE),
                ['jquery', 'pp-remodal'],
                PUBLISHPRESS_CHECKLISTS_VERSION,
                true
            );

            wp_register_script('pp-remodal', $this->module_url . 'assets/js/remodal.min.js', ['jquery'],
                PUBLISHPRESS_CHECKLISTS_VERSION, true);

            $rules = apply_filters('publishpress_checklists_rules_list', []);

            // Get all the keys of post types, to select the first one for the JS script
            $postTypes = array_keys($this->get_post_types());
            // Make sure we are on the first item
            reset($postTypes);

            wp_localize_script(
                'pp-checklists-admin',
                'objectL10n_checklist_admin',
                [
                    'rules'           => $rules,
                    'first_post_type' => current($postTypes),
                ]
            );

            wp_enqueue_style('pp-remodal-default-theme');
            wp_enqueue_script('pp-remodal');
        }

        /*
        ==================================
        =            Meta boxes          =
        ==================================
        */

        /**
         * Load the post meta boxes for all of the post types that are supported
         */
        public function handle_post_meta_boxes()
        {
            /**
             *
             * TODO:
             * - Check if there is any active requirement before display the box
             */


            $title = __('Checklist', 'publishpress-checklists');

            if (current_user_can('manage_options')) {
                // Make the meta box title include a link to edit the Editorial Metadata terms. Logic similar to how Core dashboard widgets work.
                $url = $this->get_link();

                $title .= ' <span class="postbox-title-action"><a href="' . esc_url($url) . '" class="edit-box open-box">' . __('Configure',
                        'publishpress-checklists') . '</a></span>';
            }

            $supported_post_types = $this->getPostTypesForModule($this->module);

            foreach ($supported_post_types as $post_type) {
                add_meta_box(self::METADATA_TAXONOMY, $title, [$this, 'display_meta_box'], $post_type, 'side', 'high');
            }
        }

        /**
         * Displays HTML output for Checklist post meta box
         *
         * @param object $post Current post
         */
        public function display_meta_box($post)
        {
            $requirements = [];

            // Apply filters to the list of requirements
            $requirements = apply_filters('publishpress_checklists_requirement_list', $requirements, $post);

            $legacyPlugin = Factory::getLegacyPlugin();

            // Add the scripts
            if ( ! empty($requirements)) {
                wp_enqueue_script(
                    'pp-checklists-requirements',
                    plugins_url('/modules/checklists/assets/js/meta-box.js', PUBLISHPRESS_CHECKLISTS_FILE),
                    ['jquery', 'word-count'],
                    PUBLISHPRESS_CHECKLISTS_VERSION,
                    true
                );

                wp_localize_script(
                    'pp-checklists-requirements',
                    'ppChecklists',
                    [
                        'requirements'              => $requirements,
                        'msg_missed_optional'       => __('The following requirements are not completed yet. Are you sure you want to publish?',
                            'publishpress-checklists'),
                        'msg_missed_required'       => __('Please complete the following requirements before publishing:',
                            'publishpress-checklists'),
                        'msg_missed_important'      => __('Not required, but important: ',
                            'publishpress-checklists'),
                        'show_warning_icon_submit'  => Base_requirement::VALUE_YES === $legacyPlugin->settings->module->options->show_warning_icon_submit,
                        'hide_publish_button'       => Base_requirement::VALUE_YES === $legacyPlugin->settings->module->options->hide_publish_button,
                        'title_warning_icon'        => __('One or more items in the checklist are not completed'),
                        'gutenberg_warning_css'     => @file_get_contents(__DIR__ . '/assets/css/admin-gutenberg-warning.css'),
                        'gutenberg_hide_submit_css' => @file_get_contents(__DIR__ . '/assets/css/admin-gutenberg-hide-submit.css'),
                    ]
                );

                do_action('publishpress_checklists_enqueue_scripts');
            }

            // Render the box
            $templateLoader = Factory::getTemplateLoader();

            $templateLoader->load('checklists', 'meta-box', [
                'metadata_taxonomy' => self::METADATA_TAXONOMY,
                'requirements'      => $requirements,
                'configure_link'    => $this->get_link(),
                'nonce'             => wp_create_nonce(__FILE__),
                'lang'              => [
                    'to_use_checklists' => __('To use the checklist', 'publishpress-checklists'),
                    'please_choose_req' => __('please choose some requirements', 'publishpress-checklists'),
                    'required'          => __('Required', 'publishpress-checklists'),
                    'dont_publish'      => __('Don\'t publish', 'publishpress-checklists'),
                    'yes_publish'       => __('Yes, publish', 'publishpress-checklists'),

                ],
            ]);
        }

        /**
         * Save the state of custom items.
         *
         * @param int    $id   Unique ID for the post being saved
         * @param object $post Post object
         */
        public function save_post_meta_box($id, $post)
        {

            // Authentication checks: make sure data came from our meta box and that the current user is allowed to edit the post
            // TODO: switch to using check_admin_referrer? See core (e.g. edit.php) for usage
            if ( ! isset($_POST[self::METADATA_TAXONOMY . "_nonce"])
                 || ! wp_verify_nonce($_POST[self::METADATA_TAXONOMY . "_nonce"], __FILE__)) {
                return $id;
            }

            if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                || ! in_array($post->post_type, $this->getPostTypesForModule($this->module))
                || $post->post_type == 'post' && ! current_user_can('edit_post', $id)
                || $post->post_type == 'page' && ! current_user_can('edit_page', $id)) {
                return $id;
            }

            // Check if we have data coming from custom items
            if (isset($_POST['_PUBLISHPRESS_CHECKLISTS_custom_item'])) {
                if ( ! empty($_POST['_PUBLISHPRESS_CHECKLISTS_custom_item'])) {
                    foreach ($_POST['_PUBLISHPRESS_CHECKLISTS_custom_item'] as $item_id => $value) {
                        update_post_meta($id, self::POST_META_PREFIX . $item_id, $value);
                    }
                }
            }
        }

        /**
         * Creates the admin menu if there is no menu set.
         */
        public function action_admin_menu_page()
        {
            $legacyPlugin = Factory::getLegacyPlugin();

            $legacyPlugin->addMenuPage(
                esc_html__('Checklists', 'publishpress-checklists'),
                apply_filters('publishpress_checklists_manage_checklist_cap', 'manage_options'),
                self::MENU_SLUG,
                [$this, 'options_page_controller']
            );
        }

        /**
         * Add necessary things to the admin menu
         */
        public function action_admin_submenu()
        {
            $legacyPlugin = Factory::getLegacyPlugin();

            // Main Menu
            add_submenu_page(
                $legacyPlugin->getMenuSlug(),
                esc_html__('Checklists', 'publishpress-checklists'),
                esc_html__('Checklists', 'publishpress-checklists'),
                apply_filters('publishpress_checklists_manage_checklist_cap', 'manage_options'),
                self::MENU_SLUG,
                [$this, 'options_page_controller']
            );

            add_submenu_page(
                $legacyPlugin->getMenuSlug(),
                esc_html__('Checklists', 'publishpress-checklists'),
                esc_html__('Checklists', 'publishpress-checklists'),
                apply_filters('publishpress_checklists_manage_checklist_cap', 'manage_options'),
                self::MENU_SLUG,
                [$this, 'options_page_controller']
            );
        }

        public function options_page_controller()
        {
            // Apply filters to the list of requirements
            $post_types = $this->get_post_types();

            $templateLoader = Factory::getTemplateLoader();

            $this->printDefaultHeader($this->module);

            $templateLoader->load('checklists', 'global-checklists', [
                'requirements' => $this->requirements,
                'post_types'   => $post_types,
                'lang'         => [
                    'description'     => __('Description', 'publishpress-checklists'),
                    'params'          => __('Parameters', 'publishpress-checklists'),
                    'action'          => __('Action', 'publishpress-checklists'),
                    'add_custom_item' => __('Add custom item', 'publishpress-checklists'),
                ],
            ]);

            $this->printDefaultFooter($this->module);
        }

        /**
         * Get a list of rules for the requirement
         *
         * @param $rules
         *
         * @return array
         */
        public function filterRulesList($rules)
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
         * Enqueue Gutenberg assets.
         */
        public function enqueue_block_editor_assets()
        {
            // Required thing to build Gutenberg Blocks
            wp_enqueue_script(
                'pp-checklists-requirements-gutenberg',
                plugins_url('/modules/checklists/assets/js/gutenberg-warning.min.js', PUBLISHPRESS_CHECKLISTS_FILE),
                [
                    'wp-blocks',
                    'wp-i18n',
                    'wp-element',
                    'wp-hooks',
                    'react',
                    'react-dom',
                ],
                PUBLISHPRESS_CHECKLISTS_VERSION,
                true
            );
        }

        /**
         * Validate data entered by the user
         */
        public function save_global_checklist()
        {
            if ( ! isset($_GET['page']) || $_GET['page'] !== self::MENU_SLUG) {
                return;
            }

            if ( ! isset($_POST['publishpress_checklists_checklists_options']) || empty($_POST['publishpress_checklists_checklists_options'])) {
                return;
            }

            if ( ! wp_verify_nonce($_POST['_wpnonce'], 'ppch-global-checklists')) {
                return;
            }

            $new_options = $_POST['publishpress_checklists_checklists_options'];


            // Instantiate custom items so they are able to process the settings validations
            $this->instantiate_custom_items_to_validate_settings($new_options);


            $options = (array)get_option('publishpress_checklists_checklists_options');

            if (empty($options)) {
                $options = [];
            }

            $options = array_merge($options, $new_options);

            $options = apply_filters('publishpress_checklists_validate_requirement_settings', $options);

            $options = (object)$options;

            update_option('publishpress_checklists_checklists_options', $options);

            // Reload the module's options after saving.
            wp_redirect($_SERVER['HTTP_REFERER']);
        }

        /**
         * Instantiate custom items according to the new_options.
         *
         * @param array $new_options
         */
        protected function instantiate_custom_items_to_validate_settings($new_options)
        {
            if (isset($new_options['custom_items']) && ! empty($new_options['custom_items'])) {
                foreach ($new_options['custom_items'] as $id) {
                    if (isset($new_options[$id . '_title'])) {
                        foreach ($new_options[$id . '_title'] as $post_type => $title) {
                            $custom_item = new Custom_item($id, $this->module, $post_type);
                            $custom_item->init();
                        }
                    }
                }
            }
        }
    }
}
