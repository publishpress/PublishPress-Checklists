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

use PublishPress\Checklists\Core\Legacy\Module;
use PublishPress\Checklists\Core\Legacy\Util;
use PublishPress\Checklists\Core\Factory;

if ( ! class_exists('PPCH_Settings')) {
    class PPCH_Settings extends Module
    {
        const SETTINGS_SLUG = 'ppma-settings';

        /**
         * @var string
         */
        const MENU_SLUG = 'ppma-modules-settings';

        public $module;

        /**
         * Register the module with PublishPress but don't do anything else
         */
        public function __construct()
        {
            $this->twigPath = __DIR__ . '/twig';

            // Register the module with PublishPress
            $this->module_url = $this->get_module_url(__FILE__);
            $args             = [
                'title'                => __('PublishPress Checklists', 'publishpress-checklists'),
                'extended_description' => false,
                'module_url'           => $this->module_url,
                'icon_class'           => 'dashicons dashicons-admin-settings',
                'slug'                 => 'settings',
                'settings_slug'        => self::SETTINGS_SLUG,
                'default_options'      => [
                    'enabled' => 'on',
                ],
                'configure_page_cb'    => 'print_default_settings',
                'autoload'             => true,
                'add_menu'             => true,
            ];

            $legacyPlugin = Factory::getLegacyPlugin();

            $this->module = $legacyPlugin->register_module('settings', $args);
        }

        /**
         * Initialize the rest of the stuff in the class if the module is active
         */
        public function init()
        {
            add_action('admin_init', [$this, 'helper_settings_validate_and_save'], 100);

            add_action('publishpress_checklists_admin_submenu', [$this, 'action_admin_submenu'], 990);

            add_action('admin_print_styles', [$this, 'action_admin_print_styles']);
            add_action('admin_print_scripts', [$this, 'action_admin_print_scripts']);
            add_action('admin_enqueue_scripts', [$this, 'action_admin_enqueue_scripts']);
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
                esc_html__('PublishPress Settings', 'publishpress-checklists'),
                esc_html__('Settings', 'publishpress-checklists'),
                apply_filters('publishpress_checklists_view_settings_cap', 'manage_options'),
                self::MENU_SLUG,
                [$this, 'options_page_controller']
            );
        }

        public function action_admin_enqueue_scripts()
        {
            if ($this->is_whitelisted_settings_view()) {
                // Enqueue scripts
            }
        }

        /**
         * Add settings styles to the settings page
         */
        public function action_admin_print_styles()
        {
            if ($this->is_whitelisted_settings_view()) {
                wp_enqueue_style('publishpress-settings-css', $this->module_url . 'lib/settings.css', false, PUBLISHPRESS_CHECKLISTS_VERSION);
            }
        }

        /**
         * Extra data we need on the page for transitions, etc.
         *
         * @since 0.7
         */
        public function action_admin_print_scripts()
        {
            ?>
			<script type="text/javascript">
				var ma_admin_url = '<?php echo get_admin_url(); ?>';
			</script>
			<?php
        }

        /**
         *
         */
        public function print_default_header($current_module, $custom_text = null)
        {
            $display_text = '';

            // If there's been a message, let's display it
            if (isset($_GET['message'])) {
                $message = $_GET['message'];
            } elseif (isset($_REQUEST['message'])) {
                $message = $_REQUEST['message'];
            } elseif (isset($_POST['message'])) {
                $message = $_POST['message'];
            } else {
                $message = false;
            }

            if ($message && isset($current_module->messages[$message])) {
                $display_text .= '<div class="is-dismissible notice notice-info"><p>' . esc_html($current_module->messages[$message]) . '</p></div>';
            }

            // If there's been an error, let's display it
            if (isset($_GET['error'])) {
                $error = $_GET['error'];
            } elseif (isset($_REQUEST['error'])) {
                $error = $_REQUEST['error'];
            } elseif (isset($_POST['error'])) {
                $error = $_POST['error'];
            } else {
                $error = false;
            }
            if ($error && isset($current_module->messages[$error])) {
                $display_text .= '<div class="is-dismissible notice notice-error"><p>' . esc_html($current_module->messages[$error]) . '</p></div>';
            }
            ?>

			<div class="publishpress-admin pressshack-admin-wrapper wrap">
				<header>
                    <h1 class="wp-heading-inline"><?php echo $current_module->title; ?></h1>

					<?php echo !empty($display_text) ? $display_text : ''; ?>
					<?php // We keep the H2 tag to keep notices tied to the header?>
					<h2>

						<?php if ($current_module->short_description && empty($custom_text)): ?>
							<?php echo $current_module->short_description; ?>
						<?php endif; ?>

						<?php if (!empty($custom_text)) : ?>
							<?php echo $custom_text; ?>
						<?php endif; ?>
					</h2>

				</header>
			<?php
        }

        /**
         * Adds Settings page for PublishPress.
         */
        public function print_default_settings()
        {
            ?>
			<div class="publishpress-modules">
				<?php $this->print_modules(); ?>
			</div>
			<?php
        }

        public function print_modules()
        {
            $legacyPlugin = Factory::getLegacyPlugin();

            if (!count($legacyPlugin->modules)) {
                echo '<div class="message error">' . __('There are no PublishPress modules registered', 'publishpress-checklists') . '</div>';
            } else {
                foreach ($legacyPlugin->modules as $mod_name => $mod_data) {
                    $add_menu = isset($mod_data->add_menu) && $mod_data->add_menu === true;

                    if ($mod_data->autoload || !$add_menu) {
                        continue;
                    }

                    if ($mod_data->options->enabled !== 'off') {
                        $url = '';

                        if ($mod_data->configure_page_cb && (!isset($mod_data->show_configure_btn) || $mod_data->show_configure_btn === true)) {
                            $url = add_query_arg('page', $mod_data->settings_slug, get_admin_url(null, 'admin.php'));
                        } elseif ($mod_data->page_link) {
                            $url = $mod_data->page_link;
                        }

                        echo $this->twig->render(
                            'module.twig',
                            [
                                'has_config_link' => isset($mod_data->configure_page_cb) && !empty($mod_data->configure_page_cb),
                                'slug'            => $mod_data->slug,
                                'icon_class'      => isset($mod_data->icon_class) ? $mod_data->icon_class : false,
                                'form_action'     => get_admin_url(null, 'options.php'),
                                'title'           => $mod_data->title,
                                'description'     => wp_kses($mod_data->short_description, 'a'),
                                'url'             => $url,
                            ]
                        );
                    }
                }
            }
        }

        /**
         * Given a form field and a description, prints either the error associated with the field or the description.
         *
         * @param string $field The form field for which to check for an error
         * @param string $description Unlocalized string to display if there was no error with the given field
         *
         *@since 0.7
         *
         */
        public function helper_print_error_or_description($field, $description)
        {
            if (isset($_REQUEST['form-errors'][$field])): ?>
				<div class="form-error">
					<p><?php echo esc_html($_REQUEST['form-errors'][$field]); ?></p>
				</div>
			<?php else: ?>
				<p class="description"><?php echo esc_html($description); ?></p>
			<?php endif;
        }

        /**
         * Generate an option field to turn post type support on/off for a given module
         *
         * @param object $module      PublishPress module we're generating the option field for
         * @param array  $post_types  If empty, we consider all post types
         *
         * @since 0.7
         */
        public function helper_option_custom_post_type($module, $post_types = [])
        {
            if (empty($post_types)) {
                $post_types = [
                    'post' => __('Posts'),
                    'page' => __('Pages'),
                ];
                $custom_post_types = $this->get_supported_post_types_for_module();
                if (count($custom_post_types)) {
                    foreach ($custom_post_types as $custom_post_type => $args) {
                        $post_types[$custom_post_type] = $args->label;
                    }
                }
            }

            foreach ($post_types as $post_type => $title) {
                echo '<label for="' . esc_attr($post_type) . '-' . $module->slug . '">';
                echo '<input id="' . esc_attr($post_type) . '-' . $module->slug . '" name="'
                    . $module->options_group_name . '[post_types][' . esc_attr($post_type) . ']"';
                if (isset($module->options->post_types[$post_type])) {
                    checked($module->options->post_types[$post_type], 'on');
                }
                // Defining post_type_supports in the functions.php file or similar should disable the checkbox
                disabled(post_type_supports($post_type, $module->post_type_support), true);
                echo ' type="checkbox" value="on" />&nbsp;&nbsp;&nbsp;' . esc_html($title) . '</label>';
                // Leave a note to the admin as a reminder that add_post_type_support has been used somewhere in their code
                if (post_type_supports($post_type, $module->post_type_support)) {
                    echo '&nbsp&nbsp;&nbsp;<span class="description">' . sprintf(__('Disabled because add_post_type_support(\'%1$s\', \'%2$s\') is included in a loaded file.', 'publishpress-checklists'), $post_type, $module->post_type_support) . '</span>';
                }
                echo '<br />';
            }
        }

        /**
         * Validation and sanitization on the settings field
         * This method is called automatically/ doesn't need to be registered anywhere
         *
         * @since 0.7
         */
        public function helper_settings_validate_and_save()
        {
            if (!isset($_POST['action'], $_POST['_wpnonce'], $_POST['option_page'], $_POST['_wp_http_referer'], $_POST['content_checklist_module_name'], $_POST['submit']) || !is_admin()) {
                return false;
            }

            if ($_POST['action'] != 'update'
                || $_GET['page'] != 'ppma-modules-settings') {
                return false;
            }

            if (!current_user_can('manage_options') || !wp_verify_nonce($_POST['_wpnonce'], 'edit-publishpress-settings')) {
                wp_die(__('Cheatin&#8217; uh?'));
            }

            $legacyPlugin = Factory::getLegacyPlugin();

            foreach ($_POST['content_checklist_module_name'] as $moduleSlug) {
                $module_name = sanitize_key(Util::sanitize_module_name($moduleSlug));

                $new_options = (isset($_POST[$legacyPlugin->$module_name->module->options_group_name])) ? $_POST[$legacyPlugin->$module_name->module->options_group_name] : [];

                /**
                 * Legacy way to validate the settings. Hook to the filter
                 * publishpress_checklists_validate_module_settings instead.
                 *
                 * @deprecated
                 */
                if (method_exists($legacyPlugin->$module_name, 'settings_validate')) {
                    $new_options = $legacyPlugin->$module_name->settings_validate($new_options);
                }

                // New way to validate settings
                $new_options = apply_filters('publishpress_checklists_validate_module_settings', $new_options, $module_name);

                // Cast our object and save the data.
                $new_options = (object)array_merge((array)$legacyPlugin->$module_name->module->options, $new_options);
                $legacyPlugin->update_all_module_options($legacyPlugin->$module_name->module->name, $new_options);

                // Check if the module has a custom save method
                if (method_exists($legacyPlugin->$module_name, 'settings_save')) {
                    $legacyPlugin->$module_name->settings_save($new_options);
                }
            }

            // Redirect back to the settings page that was submitted without any previous messages
            $goback = add_query_arg('message', 'settings-updated', remove_query_arg(['message'], wp_get_referer()));
            wp_safe_redirect($goback);

            exit;
        }

        public function options_page_controller()
        {
            $legacyPlugin = Factory::getLegacyPlugin();

            $module_settings_slug = isset($_GET['module']) && !empty($_GET['module']) ? $_GET['module'] : PPCH_Modules_Settings::SETTINGS_SLUG . '-settings';
            $requested_module     = $legacyPlugin->get_module_by('settings_slug', $module_settings_slug);
            $display_text         = '';

            // If there's been a message, let's display it
            if (isset($_GET['message'])) {
                $message = $_GET['message'];
            } elseif (isset($_REQUEST['message'])) {
                $message = $_REQUEST['message'];
            } elseif (isset($_POST['message'])) {
                $message = $_POST['message'];
            } else {
                $message = false;
            }
            if ($message && isset($requested_module->messages[$message])) {
                $display_text .= '<div class="is-dismissible notice notice-info"><p>' . esc_html($requested_module->messages[$message]) . '</p></div>';
            }

            // If there's been an error, let's display it
            if (isset($_GET['error'])) {
                $error = $_GET['error'];
            } elseif (isset($_REQUEST['error'])) {
                $error = $_REQUEST['error'];
            } elseif (isset($_POST['error'])) {
                $error = $_POST['error'];
            } else {
                $error = false;
            }
            if ($error && isset($requested_module->messages[$error])) {
                $display_text .= '<div class="is-dismissible notice notice-error"><p>' . esc_html($requested_module->messages[$error]) . '</p></div>';
            }

            $this->print_default_header($requested_module);

            // Get module output
            ob_start();
            $configure_callback    = $requested_module->configure_page_cb;

            if ( ! empty($configure_callback)) {
                $requested_module_name = $requested_module->name;

                $legacyPlugin->$requested_module_name->$configure_callback();
                $module_output = ob_get_clean();
            }

            /*
             * Check if we have more than one tab to display.
             */
            $show_tabs = false;
            foreach ($legacyPlugin->modules as $module) {
                if ( ! empty($module->options_page) && $module->options->enabled == 'on') {
                    $show_tabs = true;
                }
            }

            echo $this->twig->render(
                'settings.twig',
                [
                    'modules'        => (array)$legacyPlugin->modules,
                    'settings_slug'  => $module_settings_slug,
                    'slug'           => PPCH_Modules_Settings::SETTINGS_SLUG,
                    'module_output'  => $module_output,
                    'sidebar_output' => '',
                    'text'           => $display_text,
                    'show_sidebar'   => false,
                    'show_tabs'      => $show_tabs,
                ]
            );
        }
    }
}
