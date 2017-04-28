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

use PublishPress\Addon\Checklist\Requirement\Base_requirement;

if ( ! class_exists( 'PP_Checklist' ) ) {
	/**
	 * class PP_Checklist
	 */
	class PP_Checklist extends PP_Module {

		const METADATA_TAXONOMY     = 'pp_checklist_meta';
		const METADATA_POSTMETA_KEY = "_pp_checklist_meta";

		const SETTINGS_SLUG         = 'pp-checklist-settings';

		public $module_name = 'checklist';

		protected $requirement_instances;

		/**
		 * Construct the PP_Checklist class
		 */
		public function __construct() {
			$this->twigPath = dirname( dirname( dirname( __FILE__ ) ) ) . '/twig';

			$this->module_url = $this->get_module_url( __FILE__ );

			// Load the requirements
			$this->instantiate_requirement_classes();
			do_action( 'pp_checklist_load_requirements' );

			// Register the module with PublishPress
			$args = array(
				'title'                => __( 'Checklist', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
				'short_description'    => __( 'Define tasks that must be complete before content is published', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
				'extended_description' => __( 'Define tasks that must be complete before content is published', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
				'module_url'           => $this->module_url,
				'icon_class'           => 'dashicons dashicons-feedback',
				'slug'                 => 'checklist',
				'default_options'      => array(
					'enabled'                  => 'on',
					'post_types'               => array( 'post' ),
					'show_warning_icon_submit' => 'no',
				),
				'configure_page_cb' => 'print_configure_view',
				'options_page'      => true,
			);

			// Apply a filter to the default options
			$args['default_options'] = apply_filters( 'pp_checklist_requirements_default_options', $args['default_options'] );

			PublishPress()->register_module( $this->module_name, $args );

			parent::__construct();

			$this->configure_twig();
		}

		/**
		 * Finds the requirement class files at the library folder and
		 * instantiate the class
		 */
		protected function instantiate_requirement_classes() {
			$base_path = PUBLISHPRESS_CHECKLIST_LIB_PATH . '/Requirement/';
			$req_files = glob( $base_path . '*.php' );

			foreach ( $req_files as $path ) {
				// Extract the class name
				$class = str_replace( array( $base_path, '.php' ), '', $path );

				// Check if it is a base class or interface to bypass
				if ( preg_match( '/^Base_|^Interface_/i', $class) ) {
					continue;
				}

				// Append the namespace
				$class = '\\PublishPress\\Addon\\Checklist\\Requirement\\' . $class;

				// Create a reflection to check if the class is not abstract or interface
				$reflection = new \ReflectionClass( $class );

				if ( class_exists( $class ) && ! $reflection->isAbstract() && ! $reflection->isInterface() ) {
					new $class;
				}
			}
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
				return __( $id, PUBLISHPRESS_CHECKLIST_LANG_CONTEXT );
			} );
			$this->twig->addFunction( $function );

			$function = new Twig_SimpleFunction( 'do_settings_sections', function ( $section ) {
				return do_settings_sections( $section );
			} );
			$this->twig->addFunction( $function );

			$function = new Twig_SimpleFunction( 'html_list_of_actions', function ( $option_name, $post_type ) {
				return $this->html_list_of_actions( $option_name, $post_type );
			} );
			$this->twig->addFunction( $function );

			$function = new Twig_SimpleFunction( 'html_checkbox_bool', function ( $args ) {
				return $this->html_checkbox_bool( $args );
			} );
			$this->twig->addFunction( $function );

			$function = new Twig_SimpleFunction( 'html_small_input', function ( $args ) {
				return $this->html_small_input( $args );
			} );
			$this->twig->addFunction( $function );
		}

		/**
		 * Initialize the module. Conditionally loads if the module is enabled
		 */
		public function init() {
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'add_meta_boxes', array( $this, 'handle_post_metaboxes' ) );

			// Editor
			add_filter( 'mce_external_plugins', array( $this, 'add_mce_plugin' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts' ) );
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
				'settings-tab.twig',
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
				__( 'Add to these post types:', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
				array( $this, 'settings_post_types_option' ),
				$this->module->options_group_name,
				$this->module->options_group_name . '_post_types'
			);

			add_settings_field(
				'show_warning_icon_submit',
				__( 'Show warning icon:', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
				array( $this, 'settings_show_warning_icon_submit_option' ),
				$this->module->options_group_name,
				$this->module->options_group_name . '_post_types'
			);

			/**
			 *
			 * Global settings
			 */

			add_settings_section(
				$this->module->options_group_name . '_global',
				__( 'Requirements', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
				'__return_false',
				$this->module->options_group_name
			);

			// Min Words Count
			add_settings_field(
				'global_requirements',
				false,
				array( $this, 'settings_requirements' ),
				$this->module->options_group_name,
				$this->module->options_group_name . '_global',
				array(
					'post_type'   => 'global',
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
		 * Displays the field to choose between display or not the warning icon
		 * close to the submit button
		 *
		 * @param  array
		 */
		public function settings_show_warning_icon_submit_option( $args = array() ) {
			$id    = $this->module->options_group_name . '_show_warning_icon_submit';
			$value = isset( $this->module->options->show_warning_icon_submit ) ? $this->module->options->show_warning_icon_submit : 'no';

			echo '<label for="' . $id . '">';
			echo '<input type="checkbox" value="yes" id="' . $id . '" name="' . $this->module->options_group_name . '[show_warning_icon_submit]" '
				. checked( $value, 'yes', false ) . ' />';
			echo '&nbsp;&nbsp;&nbsp;' . __( 'This will display a warning icon in the "Publish" box', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT );
			echo '</label>';
		}

		/**
		 * Displays the table of requirements in the place of a field.
		 *
		 * @param  array  $args
		 */
		public function settings_requirements( $args = array() ) {
			$defaults = array(
				'post_type'   => 'global',
			);
			$args = wp_parse_args( $args, $defaults );

			echo $this->twig->render(
				'settings-requirements-table.twig',
				array(
					'metadata_taxonomy' => self::METADATA_TAXONOMY,
					'post_type'         => $args['post_type'],
					'lang'              => array(
						'description'     => __( 'Description', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
						'required'        => __( 'Required', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
						'action'          => __( 'Action', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
						'min_numb_words'  => __( 'Minimum number of words', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
						'type'            => __( 'type...', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
						'featured_image'  => __( 'Featured image', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
						'min_numb_tags'   => __( 'Minimum number of tags', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
						'min_numb_categs' => __( 'Minimum number of categories', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
					)
				)
			);
		}

		/**
		 * Returns the HTML tag for a list of actions. Used for settings fields,
		 * to specify if the requirement is required or not.
		 *
		 * @param  string  $option
		 * @param  string  $post_type
		 *
		 * @return string
		 */
		public function html_list_of_actions( $option, $post_type = 'global') {
			$output = '<select id="' . esc_attr( $post_type ) . '-' . $this->module->slug . '-' . $option . '-rule" name="'
						. $this->module->options_group_name . '[' . $option . '_rule][' . esc_attr( $post_type ) . ']">';

			$rules = array(
				'block'        => __( 'Prevent publishing', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
				'warn'         => __( 'Show a pop-up message', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
				'only_display' => __( 'Show a sidebar message', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
			);
			$attr = $option . '_rule';
			foreach ( $rules as $rule => $label ) {
				$output .= '<option value="' . $rule . '" ' . selected( $rule, $this->module->options->{$attr}[ $post_type ], false ) . '>'
					. $label . '</option>';
			}

			$output .= '</select>';

			return $output;
		}

		/**
		 * Displays the field to set the minimum words count.
		 *
		 * Arguments:
		 *
		 *   - post_type [default: global] *
		 *   - description
		 *
		 * @array  $args
		 */
		public function html_small_input( $args = array() ) {
			$defaults = array(
				'post_type'   => 'global',
				'id'          => 'settings-field',
				'name'        => 'settings_field',
				'class'       => '',
				'label'       => '',
				'type'        => 'text',
				'placeholder' => '',
			);
			$args = wp_parse_args( $args, $defaults );

			// Get the value
			$value = '';
			if ( isset( $this->module->options->{$args['name']} ) ) {
				if ( isset( $this->module->options->{$args['name']}[ $args['post_type'] ] ) ) {
					$value = $this->module->options->{$args['name']}[ $args['post_type'] ];
				}
			}

			// Output
			echo '<input type="' . $args['type'] . '" id="' . $args['post_type'] . '-' . $this->module->slug . '-' . $args['id'] . '" placeholder="' . $args['placeholder'] . '" name="'
					. $this->module->options_group_name . '[' . $args['name'] . '][' . $args['post_type'] . ']" '
					. 'value="' . $value . '" class="pp-checklist-small-input"/>';
		}

		/**
		 * Displays the field to set the featured image as requirement.
		 *
		 * Arguments:
		 *
		 *   - post_type [default: global] *
		 *   - description
		 *
		 * @array  $args
		 */
		public function html_checkbox_bool( $args = array() ) {
			$defaults = array(
				'post_type' => 'global',
				'id'        => 'settings-field',
				'name'      => 'settings_field',
				'class'     => '',
				'label'     => '',
				'value'     => 'yes'
			);
			$args = wp_parse_args( $args, $defaults );

			$id = esc_attr( $args['post_type'] ) . '-' . $this->module->slug . '-' . $args['id'];

			// Get the value
			$value = 'no';
			if ( isset( $this->module->options->{$args['name']} ) ) {
				if ( isset( $this->module->options->{$args['name']}[ $args['post_type'] ] ) ) {
					$value = $this->module->options->{$args['name']}[ $args['post_type'] ];
				}
			}

			// Output
			echo '<input type="checkbox" id="' . $id . '" class="' . $args['class'] . '" name="'
					. $this->module->options_group_name . '[' . $args['name'] . '][' . esc_attr( $args['post_type'] ) . ']" value="' . $args['value'] . '" '
					. checked( 'yes', $value, false ) . '/>';

			if ( ! empty( $args['label' ] ) ) {
				echo '<label for="' . $id . '">' . __( $args['label'], PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ) . '</label>';
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

			$new_options['show_warning_icon_submit'] = Base_requirement::VALUE_YES === $new_options['show_warning_icon_submit'] ? Base_requirement::VALUE_YES : Base_requirement::VALUE_NO;

			foreach ( $option_groups as $option_group ) {
				$new_options = apply_filters( 'pp_checklist_validate_option_group', $new_options, $option_group );
			}

			return $new_options;
		}

		/**
		 * Add the MCE plugin file to make the interface between the editor and
		 * the requirement meta box. This was the unique way that worked, making
		 * it loaded before the MCE is initialized, allowing to configure it.
		 *
		 * @param array  $plugin_array
		 */
		public function add_mce_plugin( $plugin_array ) {
			$plugin_array['pp_checklist_requirements'] =
				plugin_dir_url( PUBLISHPRESS_CHECKLIST_FILE )
				. 'modules/checklist/assets/js/tinymce-pp-checklist-requirements.js';

			return $plugin_array;
		}

		/**
		 * Enqueue scripts and stylesheets for the admin pages.
		 */
		public function add_admin_scripts() {
			wp_enqueue_style(
				'pp-checklist-requirements',
				$this->module_url . 'assets/css/checklist-requirements.css',
				false,
				PUBLISHPRESS_CHECKLIST_VERSION,
				'all'
			);

			wp_enqueue_script(
				'pp-checklist-admin',
				plugins_url( '/modules/checklist/assets/js/admin.js', PUBLISHPRESS_CHECKLIST_FILE ),
				array( 'jquery' ),
				PUBLISHPRESS_CHECKLIST_VERSION,
				true
			);

			wp_enqueue_style( 'pp-remodal-default-theme' );
			wp_enqueue_script( 'pp-remodal' );
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


			$title = __( 'Checklist', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT );

			if ( current_user_can( 'manage_options' ) ) {
				// Make the metabox title include a link to edit the Editorial Metadata terms. Logic similar to how Core dashboard widgets work.
				$url = $this->get_link();

				$title .= ' <span class="postbox-title-action"><a href="' . esc_url( $url ) . '" class="edit-box open-box">' . __( 'Configure', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ) . '</a></span>';
			}

			$supported_post_types = $this->get_post_types_for_module( $this->module );

			foreach ( $supported_post_types as $post_type ) {
				add_meta_box( self::METADATA_TAXONOMY, $title, array( $this, 'display_meta_box' ), $post_type, 'side', 'high' );
			}
		}

		/**
		 * Displays HTML output for Checklist post meta box
		 *
		 * @param object $post Current post
		 */
		public function display_meta_box( $post ) {
			$requirements = array();

			// Apply filters to the list of requirements
			$requirements = apply_filters( 'pp_checklist_requirements_metabox', $requirements, $post, $this->module );

			// Add the scripts
			if ( ! empty( $requirements ) ) {
				wp_enqueue_script(
					'pp-checklist-requirements',
					plugins_url( '/modules/checklist/assets/js/checklist-admin.js', PUBLISHPRESS_CHECKLIST_FILE ),
					array( 'jquery' ),
					PUBLISHPRESS_CHECKLIST_VERSION,
					true
				);

				wp_localize_script(
                    'pp-checklist-requirements',
                    'objectL10n_checklist_requirements',
                    array(
						'requirements'             => $requirements,
						'msg_missed_optional'      => __( 'The following requirements are not completed yet. Are you sure you want to publish?', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
						'msg_missed_required'      => __( 'The following requirements are not completed yet. Sorry, but you can not publish it.', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
						'msg_missed_important'     => __( 'Not required, but important: ', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
						'show_warning_icon_submit' => Base_requirement::VALUE_YES === $this->module->options->show_warning_icon_submit,
						'title_warning_icon'       => __( 'One or more items in the checklist are not completed. Are you sure you want to publish?' ),
                    )
                );
			}

			// Render the box
			echo $this->twig->render(
				'checklist-metabox.twig',
				array(
					'metadata_taxonomy' => self::METADATA_TAXONOMY,
					'requirements'      => $requirements,
					'configure_link'    => $this->get_link(),
					'lang'              => array(
						'to_use_checklists' => __( 'To use the checklist', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
						'please_choose_req' => __( 'please choose some requirements', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
						'required'          => __( 'Required', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
						'dont_publish'      => __( 'Don\'t publish', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
						'yes_publish'       => __( 'Yes, publish', PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),

					),
				)
			);
		}

		/*=====  End of Meta boxes  ======*/
	}
}// End if().
