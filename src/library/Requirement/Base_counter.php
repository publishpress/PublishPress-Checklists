<?php
/**
 * @package     PublishPress\Content_checklist
 * @author      PressShack <help@pressshack.com>
 * @copyright   Copyright (C) 2017 PressShack. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Addon\Content_checklist\Requirement;

defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

class Base_counter extends Base_simple implements Interface_required {
	/**
	 * The default value for the option related to the value.
	 */
	const DEFAULT_OPTION_VALUE = 0;

	/**
	 * Injects the respective default options into the main add-on.
	 *
	 * @param  array  $default_options
	 * @return array
	 */
	public function filter_default_options( $default_options ) {
		$default_options = parent::filter_default_options( $default_options );

		$options = array(
			"{$this->name}_value" => array(
				static::GROUP_GLOBAL => static::DEFAULT_OPTION_VALUE,
			),
		);

		return array_merge( $default_options, $options );
	}

	/**
	 * Validates the option group, making sure the values are sanitized.
	 * It runs for each option group, including "global".
	 *
	 * @param  array   $new_options
	 *
	 * @return array
	 */
	public function filter_settings_validate( $new_options ) {
		$new_options = parent::filter_settings_validate( $new_options, $this->post_type );

		$index = $this->name . '_value';
		if ( isset( $new_options[ $index ][ $this->post_type ] ) ) {
			$new_options[ $index ][ $this->post_type ] = filter_var(
				$new_options[ $index ][ $this->post_type ],
				FILTER_SANITIZE_NUMBER_INT
			);
		}

		// Make sure we don't have 0 as value if enabled
		if ( empty( $new_options[ $index ][ $this->post_type ] ) && static::VALUE_YES === $new_options[ $this->name ][ $this->post_type ] ) {
			$new_options[ $index ][ $this->post_type ] = 1;
		}

		return $new_options;
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
		$option_name = $this->name;
		$options     = $this->module->options;

		// The enabled status
		$enabled = $this->is_enabled();

		// If not enabled, bypass the method
		if ( ! $enabled ) {
			return $requirements;
		}

		// Legacy option. Only the "min" option were available
		$legacy_option_name = 'min_' . $this->name . '_value';

		// Option names
		$option_name_min = $this->name . '_min';
		$option_name_max = $this->name . '_max';
		$option_name_rule  = $this->name . '_rule';

		// Get the min value
		$min_value = 0;
		if ( isset( $this->module->options->{$option_name_min}[ static::GROUP_GLOBAL ] ) ) {
			$min_value = (int) $this->module->options->{$option_name_min}[ static::GROUP_GLOBAL ];
		}
		// If not set, we try the legacy option. At that time, we only had min values.
		if ( '' === $min_value ) {
			if ( isset( $this->module->options->{$legacy_option_name}[ static::GROUP_GLOBAL ] ) ) {
				$min_value = (int) $this->module->options->{$legacy_option_name}[ static::GROUP_GLOBAL ];
			}
		}

		// Get the max value
		$max_value = 0;
		if ( isset( $this->module->options->{$option_name_max}[ static::GROUP_GLOBAL ] ) ) {
			$max_value = (int) $this->module->options->{$option_name_max}[ static::GROUP_GLOBAL ];
		}

		// Check if both values are empty, to skip
		if ( empty( $min_value ) && empty( $max_value ) ) {
			return $requirements;
		}


		$label = '';

		// Both same value = exact
		if ( $min_value == $max_value ) {
			$label = sprintf(
				_n( $this->lang['label_exact_singular'], $this->lang['label_exact_plural'], $min_value, 'publishpress-content-checklist' ),
				$min_value
			);
		}

		// Min not empty, max empty or < min = only min
		if ( ! empty( $min_value ) && ( $max_value < $min_value ) ) {
			$label = sprintf(
				_n( $this->lang['label_min_singular'], $this->lang['label_min_plural'], $min_value, 'publishpress-content-checklist' ),
				$min_value
			);
		}

		// Min not empty, max not empty and > min = both min and max
		if ( ! empty( $min_value ) && ( $max_value > $min_value ) ) {
			$label = sprintf(
				__( $this->lang['label_between'], 'publishpress-content-checklist' ),
				$min_value,
				$max_value
			);
		}

		// Min empty, max not empty and > min = only max
		if ( empty( $min_value ) && ( $max_value > $min_value ) ) {
			$label = sprintf(
				_n( $this->lang['label_max_singular'], $this->lang['label_max_plural'], $max_value, 'publishpress-content-checklist' ),
				$max_value
			);
		}


		// The rule
		$rule = $this->get_option_rule();

		// Register in the requirements list
		$requirements[ $this->name ] = array(
			'status'    => $this->get_current_status( $post, array( $min_value, $max_value ) ),
			'label'     => $label,
			'value'     => array( $min_value, $max_value ),
			'rule'      => $rule,
		);

		return $requirements;
	}

	/**
	 * Get the HTML for the setting field for the specific post type.
	 *
	 * @return string
	 */
	public function get_setting_field_html( $css_class = '' ) {
		$post_type = esc_attr( $this->post_type );
		$css_class = esc_attr( $css_class );

		// Legacy option. Only the "min" option were available
		$legacy_option_name = 'min_' . $this->name . '_value';

		// Option names
		$option_name_min = $this->name . '_min';
		$option_name_max = $this->name . '_max';


		// Get the min value
		$min_value = '';
		if ( isset( $this->module->options->{$option_name_min}[ $post_type ] ) ) {
			$min_value = (int) $this->module->options->{$option_name_min}[ $post_type ];
		}
		// If not set, we try the legacy option. At that time, we only had min values.
		if ( '' === $min_value ) {
			if ( isset( $this->module->options->{$legacy_option_name}[ $post_type ] ) ) {
				$min_value = (int) $this->module->options->{$legacy_option_name}[ $post_type ];
			}
		}

		// Get the max value
		$max_value = '';
		if ( isset( $this->module->options->{$option_name_max}[ $post_type ] ) ) {
			$max_value = $this->module->options->{$option_name_max}[ $post_type ];
			$max_value = (int) $max_value;
		}

		// Make sure to do not display a 0 number
		if ( empty( $min_value ) ) {
			$min_value = '';
		}

		if ( empty( $max_value ) ) {
			$max_value = '';
		}

		// Make sure to do not display max_value, if less than min_value
		if ( $max_value < $min_value ) {
			$max_value = '';
		}

		// Get the field markup for min value
		$min_field = sprintf(
			'<input type="text" " id="%s" name="%s" value="%s" class="pp-checklist-small-input pp-checklist-number" />',
			"{$post_type}-{$this->module->slug}-{$option_name_min}",
			"{$this->module->options_group_name}[{$option_name_min}][{$post_type}]",
			$min_value
		);

		// Get the field markup for max value
		$max_field = sprintf(
			'<input type="text" " id="%s" name="%s" value="%s" class="pp-checklist-small-input pp-checklist-number" />',
			"{$post_type}-{$this->module->slug}-{$option_name_max}",
			"{$this->module->options_group_name}[{$option_name_max}][{$post_type}]",
			$max_value
		);

		$html = sprintf( __( 'Min %s Max %s', 'publishpress-content-checklist' ), $min_field, $max_field );

		return $html;
	}
}
