<?php
/**
 * @package PublishPress
 * @author PressShack
 *
 * Copyright (c) 2017 PressShack
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

use PublishPress\Addon\Checklist\Plugin;

if ( ! class_exists( 'PP_Checklist' ) ) {
	/**
	 * class PP_Checklist
	 */
	class PP_Checklist extends PP_Module {


		const SETTINGS_SLUG = 'pp-editorial-metadata-settings';

		public $module_name = 'checklist';

		/**
		 * Construct the PP_Checklist class
		 */
		public function __construct() {
			$this->twigPath = dirname( dirname( dirname( __FILE__ ) ) ) . '/twig';

			$this->module_url = $this->get_module_url( __FILE__ );

			// Register the module with PublishPress
			$args = array(
				'title'                => __( 'Checklist', 'publishpress' ),
				'short_description'    => __( 'Description...', 'publishpress' ),
				'extended_description' => __( 'Checklist extended description...', 'publishpress' ),
				'module_url'           => $this->module_url,
				'icon_class'           => 'dashicons dashicons-feedback',
				'slug'                 => 'checklist',
				'default_options'      => array(
					'enabled' => 'on',
				),
				'configure_page_cb' => 'print_configure_view',
				'options_page'      => true,
			);
			PublishPress()->register_module( $this->module_name, $args );

			parent::__construct();

			$this->configure_twig();
		}

		protected function configure_twig() {
			$function = new Twig_SimpleFunction( 'settings_fields', function () {
				return settings_fields( $this->module->options_group_name );
			} );
			$this->twig->addFunction( $function );

			$function = new Twig_SimpleFunction( 'nonce_field', function ( $context ) {
				return wp_nonce_field( $context );
			} );
			$this->twig->addFunction( $function );

			$function = new Twig_SimpleFunction( 'submit_button', function () {
				return submit_button();
			} );
			$this->twig->addFunction( $function );

			$function = new Twig_SimpleFunction( '__', function ( $id ) {
				return __( $id, Plugin::LANGUAGE_CONTEXT );
			} );
			$this->twig->addFunction( $function );

			$function = new Twig_SimpleFunction( 'do_settings_sections', function ( $section ) {
				return do_settings_sections( $section );
			} );
			$this->twig->addFunction( $function );
		}

		/**
		 * Initialize the module. Conditionally loads if the module is enabled
		 */
		public function init() {
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}

		/**
		 * Load default editorial metadata the first time the module is loaded
		 *
		 * @since 0.7
		 */
		public function install() {

		}

		/**
		 * Upgrade our data in case we need to
		 *
		 * @since 0.7
		 */
		public function upgrade( $previous_version ) {

		}

		/**
		 * Generate a link to one of the editorial metadata actions
		 *
		 * @since 0.7
		 *
		 * @param array $args (optional) Action and any query args to add to the URL
		 * @return string $link Direct link to complete the action
		 */
		protected function get_link( $args = array() ) {
			$args['page']   = 'pp-modules-settings';
			$args['module'] = 'pp-checklist-settings';

			return add_query_arg( $args, get_admin_url( null, 'admin.php' ) );
		}

		/**
		 * Print the content of the configure tab.
		 */
		public function print_configure_view() {
			echo $this->twig->render(
				'tab.twig',
				array(
					'form_action'        => menu_page_url( $this->module->settings_slug, false ),
					'options_group_name' => $this->module->options_group_name,
					'module_name'        => $this->module->slug,
				)
			);
		}

		/**
		 * Register settings for notifications so we can partially use the Settings API
		 * (We use the Settings API for form generation, but not saving)
		 */
		public function register_settings() {
			/**
			 *
			 * Post types
			 */

			add_settings_section(
				$this->module->options_group_name . '_post_types',
				false,
				'__return_false',
				$this->module->options_group_name
			);

			add_settings_field(
				'post_types',
				__( 'Add to these post types:', 'publishpress-checklist' ),
				array( $this, 'settings_post_types_option' ),
				$this->module->options_group_name,
				$this->module->options_group_name . '_post_types'
			);

			/**
			 *
			 * Global settings
			 */

			add_settings_section(
				$this->module->options_group_name . '_global',
				__( 'Global Settings' ),
				'__return_false',
				$this->module->options_group_name
			);

			add_settings_field(
				'global_min_word_count',
				__( 'Minimum Word Count:', 'publishpress-checklist' ),
				array( $this, 'settings_min_word_count_option' ),
				$this->module->options_group_name,
				$this->module->options_group_name . '_global',
				array(
					'post_type'   => 'global',
					'description' => __( 'Leave empty to disable' ),
				)
			);
		}

		/**
		 * Displays the field to allow select the post types for checklist.
		 */
		public function settings_post_types_option() {
			global $publishpress;

			$publishpress->settings->helper_option_custom_post_type( $this->module );
		}

		/**
		 * Displays the field to set the minimum word count for a specific
		 * post type.
		 *
		 * Arguments:
		 *
		 *   - post_type [default: global] *
		 *   - description
		 *
		 * @array  $args
		 */
		public function settings_min_word_count_option( $args = array() ) {
			$defaults = array(
				'post_type'   => 'global',
				'description' => '',
			);
			$args = wp_parse_args( $args, $defaults );

			echo '<input id="' . esc_attr( $args['post_type'] ) . '-' . $this->module->slug . '" name="'
					. $this->module->options_group_name . '[min_word_count][' . esc_attr( $args['post_type'] ) . ']" '
					. 'value="' . $this->module->options->min_word_count[ $defaults['post_type'] ] . '" />';

			if ( ! empty( trim( $args['description'] ) ) ) {
				echo "<p class='description'>{$args['description']}</p>";
			}
		}

		/**
		 * Validate data entered by the user
		 *
		 * @param array $new_options New values that have been entered by the user
		 * @return array $new_options Form values after they've been sanitized
		 */
		public function settings_validate( $new_options ) {
			// Whitelist validation for the post type options
			if ( ! isset( $new_options['post_types'] ) ) {
				$new_options['post_types'] = array();
			}

			$new_options['post_types'] = $this->clean_post_type_options(
				$new_options['post_types'],
				$this->module->post_type_support
			);

			$option_groups = array_merge(
				array( 'global' ),
				array_keys( $new_options['post_types'] )
			);

			foreach ( $option_groups as $option_group ) {
				if ( isset( $new_options['min_word_count'][ $option_group ] ) ) {
					$new_options['min_word_count'][ $option_group ] = filter_var(
						$new_options['min_word_count'][ $option_group ],
						FILTER_SANITIZE_NUMBER_INT
					);
				}
			}

			return $new_options;
		}
	}
}// End if().
