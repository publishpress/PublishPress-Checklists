<?php
/**
 * @package     PublishPress\Checklist
 * @author      PressShack <help@pressshack.com>
 * @copyright   Copyright (C) 2017 Open Source Training, LLC. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Addon\Checklist\Requirement;

defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

class Base_bool extends Base_requirement implements Interface_required {
	/**
	 * The label to be displayed in the metabox
	 */
	const LABEL = 'My Requirement';

	/**
	 * Injects the respective default options into the main add-on.
	 *
	 * @param  array  $default_options
	 * @return array
	 */
	public function filter_default_options( $default_options ) {
		$name = static::NAME;

		$options = array(
			$name          => array(
				static::GROUP_GLOBAL => static::DEFAULT_OPTION_STATUS,
			),
			"{$name}_rule" => array(
				static::GROUP_GLOBAL => static::DEFAULT_OPTION_RULE,
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
		if ( isset( $new_options[ static::NAME ][ $option_group ] ) ) {
			if ( static::VALUE_YES !== $new_options[ static::NAME ][ $option_group ] ) {
				$new_options[ static::NAME ][ $option_group ] = static::VALUE_NO;
			}
		} else {
			$new_options[ static::NAME ][ $option_group ] = static::VALUE_NO;
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
		$option_rule_property  = static::NAME . '_rule';
		$options               = $module->options;

		$status = static::VALUE_YES === $options->{ $option_property }[ static::GROUP_GLOBAL ];

		// Featured Image Rule
		$rule = $options->{ $option_rule_property }[ static::GROUP_GLOBAL ];

		// Register in the requirements list
		if ( $status ) {
			$requirements[ static::NAME ] = array(
				'status' => $this->get_current_status( $post, $status ),
				'label'  => __( static::LABEL, PUBLISHPRESS_CHECKLIST_LANG_CONTEXT ),
				'value'  => $status,
				'rule'   => $rule
			);
		}

		return $requirements;
	}
}