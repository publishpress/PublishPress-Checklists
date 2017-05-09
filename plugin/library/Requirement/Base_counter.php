<?php
/**
 * @package     PublishPress\Content_checklist
 * @author      PressShack <help@pressshack.com>
 * @copyright   Copyright (C) 2017 Open Source Training, LLC. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Addon\Content_checklist\Requirement;

defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

class Base_counter extends Base_bool implements Interface_required {
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
	 * @param  string  $option_group
	 *
	 * @return array
	 */
	public function filter_settings_validate( $new_options, $option_group ) {
		$new_options = parent::filter_settings_validate( $new_options, $option_group );

		$index = $this->name . '_value';
		if ( isset( $new_options[ $index ][ $option_group ] ) ) {
			$new_options[ $index ][ $option_group ] = filter_var(
				$new_options[ $index ][ $option_group ],
				FILTER_SANITIZE_NUMBER_INT
			);
		}

		// Make sure we don't have 0 as value if enabled
		if ( empty( $new_options[ $index ][ $option_group ] ) && static::VALUE_YES === $new_options[ $this->name ][ $option_group ] ) {
			$new_options[ $index ][ $option_group ] = 1;
		}

		return $new_options;
	}

	/**
	 * Add the requirement to the list to be displayed in the metabox.
	 *
	 * @param  array      $requirements
	 * @param  stdClass   $post
	 * @param  PP_Module  $module
	 *
	 * @return array
	 */
	public function filter_requirements_list( $requirements, $post, $module ) {
		$option_name = $this->name;
		$options     = $module->options;

		// The enabled status
		$enabled = isset( $options->{$option_name}[ static::GROUP_GLOBAL ] ) ?
			static::VALUE_YES === $options->{$option_name}[ static::GROUP_GLOBAL ] : false;

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
		$min_value = '';
		if ( isset( $this->module->options->{$option_name_min}[ static::GROUP_GLOBAL ] ) ) {
			$min_value = $this->module->options->{$option_name_min}[ static::GROUP_GLOBAL ];
		}
		// If empty, we try the legacy option. At that time, we only had min values.
		if ( empty( $min_value ) ) {
			if ( isset( $this->module->options->{$legacy_option_name}[ static::GROUP_GLOBAL ] ) ) {
				$min_value = $this->module->options->{$legacy_option_name}[ static::GROUP_GLOBAL ];
			}
		}
		$min_value = (int) $min_value;

		// Get the max value
		$max_value = '';
		if ( isset( $this->module->options->{$option_name_max}[ static::GROUP_GLOBAL ] ) ) {
			$max_value = $this->module->options->{$option_name_max}[ static::GROUP_GLOBAL ];
		}
		$max_value = (int) $max_value;
		if ( empty( $max_value ) ) {
			$max_value = $min_value;
		}

		// The rule
		$rule = isset( $options->{$option_name_rule}[ static::GROUP_GLOBAL ] ) ?
			$options->{$option_name_rule}[ static::GROUP_GLOBAL ] : static::RULE_ONLY_DISPLAY;

		// The Label
		// Register in the requirements list
		$requirements[ $this->name ] = array(
			'status'    => $this->get_current_status( $post, array( $min_value, $max_value ) ),
			'label'     => sprintf( $this->lang['label_between'], $min_value, $max_value ),
			'value'     => array( $min_value, $max_value ),
			'rule'      => $rule,
		);

		return $requirements;
	}

	/**
	 * Get the HTML for the setting field for the specific post type.
	 *
	 * @param  string $post_type
	 *
	 * @return string
	 */
	public function get_setting_field_html( $post_type, $css_class = '' ) {
		$post_type = esc_attr( $post_type );
		$css_class = esc_attr( $css_class );

		// Get the checkbox
		$html = parent::get_setting_field_html( $post_type, $css_class );

		// Legacy option. Only the "min" option were available
		$legacy_option_name = 'min_' . $this->name . '_value';

		// Option names
		$option_name_min = $this->name . '_min';
		$option_name_max = $this->name . '_max';


		// Get the min value
		$min_value = '';
		if ( isset( $this->module->options->{$option_name_min}[ $post_type ] ) ) {
			$min_value = $this->module->options->{$option_name_min}[ $post_type ];
		}
		// If empty, we try the legacy option. At that time, we only had min values.
		if ( empty( $min_value ) ) {
			if ( isset( $this->module->options->{$legacy_option_name}[ $post_type ] ) ) {
				$min_value = $this->module->options->{$legacy_option_name}[ $post_type ];
			}
		}
		$min_value = (int) $min_value;

		// Get the max value
		$max_value = '';
		if ( isset( $this->module->options->{$option_name_max}[ $post_type ] ) ) {
			$max_value = $this->module->options->{$option_name_max}[ $post_type ];
		}
		$max_value = (int) $max_value;
		if ( empty( $max_value ) ) {
			$max_value = $min_value;
		}


		// Get the field markup for min value
		$min_field = sprintf(
			'<input type="number" " id="%s" name="%s" value="%s" class="pp-checklist-small-input" />',
			"{$post_type}-{$this->module->slug}-{$option_name_min}",
			"{$this->module->options_group_name}[{$option_name_min}][{$post_type}]",
			$min_value
		);

		// Get the field markup for max value
		$max_field = sprintf(
			'<input type="number" " id="%s" name="%s" value="%s" class="pp-checklist-small-input" />',
			"{$post_type}-{$this->module->slug}-{$option_name_max}",
			"{$this->module->options_group_name}[{$option_name_max}][{$post_type}]",
			$max_value
		);

		$html .= '&nbsp;' . sprintf( __( 'Between %s and %s', PP_CONTENT_CHECKLIST_LANG_CONTEXT ), $min_field, $max_field );

		return $html;
	}
}