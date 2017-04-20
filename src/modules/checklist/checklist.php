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

		const METADATA_TAXONOMY     = 'pp_checklist_meta';
		const METADATA_POSTMETA_KEY = "_pp_checklist_meta";
		const SETTINGS_SLUG         = 'pp-checklist-settings';

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
			add_action( 'add_meta_boxes', array( $this, 'handle_post_metaboxes' ) );

			// Scripts
			add_action( 'admin_print_styles-post.php', array( $this, 'add_requirement_scripts' ) );
			add_action( 'admin_print_styles-post-new.php', array( $this, 'add_requirement_scripts' ) );

			// Editor
			add_filter( 'mce_external_plugins', array( $this, 'add_mce_plugin' ) );
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

		public function add_mce_plugin( $plugin_array ) {
			$plugin_array['pp_checklist_requirements'] =
				plugin_dir_url( 'publishpress-checklist/publishpress-checklist.php' )
				. 'modules/checklist/assets/js/tinymce-pp-checklist-requirements.js';

			return $plugin_array;
		}

		/*
		==================================
		=            Meta boxes          =
		==================================
		*/

		/**
		 * Load the post metaboxes for all of the post types that are supported
		 */
		public function handle_post_metaboxes() {
			/**

				TODO:
				- Check if there is any active requirement before display the box

			 */


			$title = __( 'Checklist', 'publishpress' );

			if ( current_user_can( 'manage_options' ) ) {
				// Make the metabox title include a link to edit the Editorial Metadata terms. Logic similar to how Core dashboard widgets work.
				$url = $this->get_link();

				$title .= ' <span class="postbox-title-action"><a href="' . esc_url( $url ) . '" class="edit-box open-box">' . __( 'Configure' ) . '</a></span>';
			}

			$supported_post_types = $this->get_post_types_for_module( $this->module );

			foreach ( $supported_post_types as $post_type ) {
				add_meta_box( self::METADATA_TAXONOMY, $title, array( $this, 'display_meta_box' ), $post_type, 'side' );
			}
		}

		/**
		 * Displays HTML output for Checklist post meta box
		 *
		 * @param object $post Current post
		 */
		public function display_meta_box( $post ) {
			/*
			====================================
			=            Requirements          =
			====================================
			*/
			$requirements = array();

			// Min Word Count
			if ( ! isset( $this->module->options->min_word_count[ $post->post_type ] )
				|| empty( $this->module->options->min_word_count[ $post->post_type ] )
			) {
				$req_min_word_count = $this->module->options->min_word_count['global'];
			} else {
				$req_min_word_count = $this->module->options->min_word_count[ $post->post_type ];
			}
			$req_min_word_count = (int) $req_min_word_count;

			if ( ! empty( $req_min_word_count ) ) {
				$requirements['min_word_count'] = array(
					'status' => str_word_count( $post->post_content ) >= $req_min_word_count,
					'label'  => sprintf( __('Minimum of %s words'), $req_min_word_count ),
				);

				// We are adding this empty script to have a handle to insert the
				// localize script with the min word count value. It is used by the
				// mce plugin pp-checklist-requirements.
				wp_enqueue_script(
					'pp-checklist-req-min-words',
					plugins_url( '/modules/checklist/assets/js/checklist.js', 'publishpress-checklist/publishpress-checklist.php' ),
					array( 'jquery' ),
					PUBLISHPRESS_PLG_CHECKLIST_VERSION,
					true
				);

				// Add localization data for the script
				wp_localize_script(
                    'pp-checklist-req-min-words',
                    'objectL10n_checklist_req_min_words',
                    array(
                        'req_min_words_count' => $req_min_word_count,
                    )
                );
			}

			/*=====  End of Requirements  ======*/

			// Apply filters to the list of requirements
			$requirements = apply_filters( 'pp_checklist_requirements', $requirements, $post, $this->module );

			// Render the box
			echo $this->twig->render(
				'checklist-metabox.twig',
				array(
					'metadata_taxonomy' => self::METADATA_TAXONOMY,
					'requirements'      => $requirements,
				)
			);
		}

		/*=====  End of Meta boxes  ======*/
	}
}// End if().
