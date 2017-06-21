<?php
/**
 * @package     PublishPress\Content_checklist
 * @author      PressShack <help@pressshack.com>
 * @copyright   Copyright (C) 2017 PressShack. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Addon\Content_checklist\Requirement;

use PP_Checklist;

defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

class Custom_item extends Base_simple implements Interface_required {
	const VALUE_YES = 'yes';

	/**
	 * The custom title
	 *
	 * @var string
	 */
	public $title;

	/**
	 * The constructor. It adds the action to load the requirement.
	 *
	 * @var  string
	 *
	 * @return  void
	 */
	public function __construct( $name, $module ) {
		$this->name      = (string) $name;
		$this->is_custom = true;

		parent::__construct( $module );

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
	 * @param  string $post_type
	 * @return string
	 */
	public function get_title( $post_type ) {
		$title    = '';
		$var_name = $this->name . '_title';

		if ( isset( $this->module->options->{ $var_name }[ $post_type ] ) ) {
			$title = stripslashes( $this->module->options->{ $var_name }[ $post_type ] );
		}

		return $title;
	}

	/**
	 * Get the HTML for the title setting field for the specific post type.
	 *
	 * @param  string $post_type
	 *
	 * @return string
	 */
	public function get_setting_title_html( $post_type, $css_class = '' ) {
		$var_name = $this->name . '_title';

		$name = 'publishpress_checklist_options[' . $var_name . '][' . $post_type . ']';

		$html = sprintf(
			'<input type="text" name="%s" value="%s" data-id="%s" class="pp-checklist-custom-item-title" />',
			$name,
			esc_attr( $this->get_title( $post_type ) ),
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
	 * @param  string $post_type
	 *
	 * @return string
	 */
	public function get_setting_field_html( $post_type, $css_class = '' ) {
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
		// Rule
		$rule = $this->get_option_rule();

		// Enabled
		$enabled = $this->is_enabled();

		// Register in the requirements list
		if ( $enabled ) {
			$requirements[ $this->name ] = array(
				'status'    => $this->get_current_status( $post, $enabled ),
				'label'     => $this->get_title( 'global' ),
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
	 * It runs for each option group, including "global".
	 *
	 * @param  array   $new_options
	 * @param  string  $option_group
	 *
	 * @return array
	 */
	public function filter_settings_validate( $new_options, $option_group ) {
		if ( isset( $new_options[ $this->name . '_title' ][ $option_group ] )
			&& empty( $new_options[ $this->name . '_title' ][ $option_group ] ) ) {

			// Look for empty title
			$index = array_search( $this->name, $new_options[ 'custom_items' ] );
			if ( false !== $index ) {
				unset(
					$new_options[ $this->name . '_title' ][ $option_group ],
					$new_options[ $this->name . '_rule' ][ $option_group ],
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

		$plugin = PublishPress();
		update_option( $plugin->options_group . 'content_checklist_options', $this->module->options );

		return $new_options;
	}
}
