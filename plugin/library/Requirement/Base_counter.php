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
		$option_property       = $this->name;
		$option_value_property = $this->name . '_value';
		$option_rule_property  = $this->name . '_rule';
		$options               = $module->options;

		// The enabled status
		$enabled = isset( $options->{$option_property}[ static::GROUP_GLOBAL ] ) ?
			static::VALUE_YES === $options->{$option_property}[ static::GROUP_GLOBAL ] : false;

		// If not enabled, bypass the method
		if ( ! $enabled ) {
			return $requirements;
		}

		// The Value
		$value = isset( $options->{$option_value_property}[ static::GROUP_GLOBAL ] ) ?
			(int) $options->{$option_value_property}[ static::GROUP_GLOBAL ] : 0;

		// The rule
		$rule = isset( $options->{$option_rule_property}[ static::GROUP_GLOBAL ] ) ?
			$options->{$option_rule_property}[ static::GROUP_GLOBAL ] : static::RULE_ONLY_DISPLAY;

		// The Label
		$label = _n(
			$this->lang['label_singular'],
			$this->lang['label_plural'],
			$value,
			PP_CONTENT_CHECKLIST_LANG_CONTEXT
		);

		// Register in the requirements list
		$requirements[ $this->name ] = array(
			'status' => $this->get_current_status( $post, $value ),
			'label'  => sprintf( $label, $value ),
			'value'  => $value,
			'rule'   => $rule
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

		$html = parent::get_setting_field_html( $post_type, $css_class );

		$option_name = $this->name . '_value';

		// Get the value
		$value = '';
		if ( isset( $this->module->options->{$option_name} ) ) {
			if ( isset( $this->module->options->{$option_name}[ $post_type ] ) ) {
				$value = $this->module->options->{$option_name}[ $post_type ];
			}
		}

		$id   = "{$post_type}-{$this->module->slug}-{$option_name}";
		$name = "{$this->module->options_group_name}[{$option_name}][{$post_type}]";

		// Output
		$html .= sprintf(
			'<input type="number" " id="%s" name="%s" value="%s" class="pp-checklist-small-input" />',
			$id,
			$name,
			$value

		);

		return $html;
	}
}