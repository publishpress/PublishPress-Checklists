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
	 * The label to be displayed in the metabox for value == 1
	 */
	const LABEL_SINGULAR = 'My Requirement';

	/**
	 * The label to be displayed in the metabox for value > 1 or 0
	 */
	const LABEL_PLURAL = 'My Requirements';

	/**
	 * Injects the respective default options into the main add-on.
	 *
	 * @param  array  $default_options
	 * @return array
	 */
	public function filter_default_options( $default_options ) {
		$name = static::NAME;

		$default_options = parent::filter_default_options( $default_options );

		$options = array(
			"{$name}_value" => array(
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

		$index = static::NAME . '_value';
		if ( isset( $new_options[ $index ][ $option_group ] ) ) {
			$new_options[ $index ][ $option_group ] = filter_var(
				$new_options[ $index ][ $option_group ],
				FILTER_SANITIZE_NUMBER_INT
			);
		}

		// Make sure we don't have 0 as value if enabled
		if ( empty( $new_options[ $index ][ $option_group ] ) && static::VALUE_YES === $new_options[ static::NAME ][ $option_group ] ) {
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
	public function filter_requirements_metabox( $requirements, $post, $module ) {
		$option_property       = static::NAME;
		$option_value_property = static::NAME . '_value';
		$option_rule_property  = static::NAME . '_rule';
		$options               = $module->options;

		// The status
		$status = static::VALUE_YES === $options->{ $option_property }[ static::GROUP_GLOBAL ];

		// The Value
		$value = (int) $options->{ $option_value_property }[ static::GROUP_GLOBAL ];

		// The rule
		$rule = $options->{ $option_rule_property }[ static::GROUP_GLOBAL ];

		// Register in the requirements list
		if ( $status ) {
			$requirements[ static::NAME ] = array(
				'status' => $this->get_current_status( $post, $value ),
				'label'  => sprintf( _n( static::LABEL_SINGULAR, static::LABEL_PLURAL, $value, PP_CONTENT_CHECKLIST_LANG_CONTEXT ), $value ),
				'value'  => $status ? $value : '',
				'rule'   => $rule
			);
		}

		return $requirements;
	}
}