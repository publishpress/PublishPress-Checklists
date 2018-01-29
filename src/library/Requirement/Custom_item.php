<?php
/**
 * @package     PublishPress\Content_checklist
 * @author      PublishPress <help@publishpress.com>
 * @copyright   Copyright (C) 2018 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Addon\Content_checklist\Requirement;

use PP_Checklist;

defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

class Custom_item extends Base_simple implements Interface_required {
	const VALUE_YES = 'yes';

	/**
	 * The title.
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * The constructor. It adds the action to load the requirement.
	 *
	 * @param  string  $name
	 * @param  string  $module
	 * @param  string  $post_type
	 *
	 * @return  void
	 */
	public function __construct( $name, $module, $post_type ) {
		$this->name      = trim( (string) $name );
		$this->is_custom = true;

		parent::__construct( $module, $post_type );

	}

	/**
	 * Initialize the language strings for the instance
	 *
	 * @return void
	 */
	public function init_language() {
		$this->lang['label']          = __( 'Custom', 'publishpress-content-checklist' );
		$this->lang['label_settings'] = __( 'Custom', 'publishpress-content-checklist' );
	}

	/**
	 * Returns the title of this custom item.
	 *
	 * @return string
	 */
	public function get_title() {
		if ( ! empty( $this->title ) ) {
			return $this->title;
		}

		$title    = '';
		$var_name = $this->name . '_title';

		if ( isset( $this->module->options->{$var_name}[ $this->post_type ] ) ) {
			$title = stripslashes( $this->module->options->{$var_name}[ $this->post_type ] );
		}

		$this->title = $title;
		// echo '<pre>'; echo $var_name; print_r($this->module->options); die;

		return $this->title;
	}

	/**
	 * Get the HTML for the title setting field for the specific post type.
	 *
	 * @return string
	 */
	public function get_setting_title_html( $css_class = '' ) {
		$var_name = $this->name . '_title';

		$name = 'publishpress_checklist_options[' . $var_name . '][' . $this->post_type . ']';

		$html = sprintf(
			'<input type="text" name="%s" value="%s" data-id="%s" class="pp-checklist-custom-item-title" />',
			$name,
			esc_attr( $this->get_title() ),
			esc_attr( $this->name )
		);

		$html .= sprintf(
			'<input type="hidden" name="publishpress_checklist_options[custom_items][]" value="%s" />',
			esc_attr( $this->name )
		);

		return $html;
	}

	/**
	 * Get the HTML for the setting field for the specific post type.
	 *
	 * @return string
	 */
	public function get_setting_field_html( $css_class = '' ) {
		$html = sprintf(
			'<a href="javascript:void(0);" class="pp-checklist-remove-custom-item" data-id="%1$s"><span class="dashicons dashicons-trash" data-id="%1$s"></span></a>',
			esc_attr( $this->name )
		);

		return $html;
	}

	/**
	 * Returns the current status of the requirement.
	 *
	 * @param  stdClass  $post
	 * @param  mixed     $option_value
	 *
	 * @return mixed
	 */
	public function get_current_status( $post, $option_value ) {
		return self::VALUE_YES === get_post_meta( $post->ID, PP_Checklist::POST_META_PREFIX . $this->name, true );
	}

	/**
	 * Add the requirement to the list to be displayed in the metabox.
	 *
	 * @param  array      $requirements
	 * @param  stdClass   $post
	 *
	 * @return array
	 */
	public function filter_requirements_list( $requirements, $post ) {
		// Check if it is a compatible post type. If not, ignore this requirement.
		if ( $post->post_type !== $this->post_type ) {
			return $requirements;
		}

		// Rule
		$rule = $this->get_option_rule();

		// Enabled
		$enabled = $this->is_enabled();

		// Register in the requirements list
		if ( $enabled ) {
			$requirements[ $this->name ] = array(
				'status'    => $this->get_current_status( $post, $enabled ),
				'label'     => $this->get_title(),
				'value'     => $enabled,
				'rule'      => $rule,
				'id'        => $this->name,
				'is_custom' => true
			);
		}

		return $requirements;
	}

	/**
	 * Validates the option group, making sure the values are sanitized.
	 *
	 * @param  array   $new_options
	 *
	 * @return array
	 */
	public function filter_settings_validate( $new_options ) {
		if ( isset( $new_options[ $this->name . '_title' ][ $this->post_type ] )
			&& empty( $new_options[ $this->name . '_title' ][ $this->post_type ] ) ) {

			// Look for empty title
			$index = array_search( $this->name, $new_options[ 'custom_items' ] );
			if ( false !== $index ) {
				unset(
					$new_options[ $this->name . '_title' ][ $this->post_type ],
					$new_options[ $this->name . '_rule' ][ $this->post_type ],
					$new_options[ 'custom_items' ][ $index ]
				);
			}
		}

		// Check if we need to remove items
		if ( isset( $new_options['custom_items_remove'] )
			&& ! empty( $new_options['custom_items_remove'] ) ) {

			foreach ( $new_options['custom_items_remove'] as $id ) {
				$var_name = $id	. '_title';
				unset( $this->module->options->{$var_name} );

				$var_name = $id	. '_rule';
				unset( $this->module->options->{$var_name} );

				unset( $this->module->options->{$id} );

				$index_remove = array_search( $id, $this->module->options->custom_items );
				if ( false !== $index_remove ) {
					unset( $this->module->options->custom_items[ $index_remove ] );
				}
			}
		}

		unset( $new_options['custom_items_remove'] );

		return $new_options;
	}
}
