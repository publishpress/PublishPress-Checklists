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

class Base_bool extends Base_requirement implements Interface_required {

	/**
	 * Injects the respective default options into the main add-on.
	 *
	 * @param  array  $default_options
	 * @return array
	 */
	public function filter_default_options( $default_options ) {
		$options = array(
			$this->name    => array(
				static::GROUP_GLOBAL => static::DEFAULT_OPTION_STATUS,
			),
			"{$this->name}_rule" => array(
				static::GROUP_GLOBAL => static::RULE_ONLY_DISPLAY,
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
		if ( isset( $new_options[ $this->name ][ $option_group ] ) ) {
			if ( static::VALUE_YES !== $new_options[ $this->name ][ $option_group ] ) {
				$new_options[ $this->name ][ $option_group ] = static::VALUE_NO;
			}
		} else {
			$new_options[ $this->name ][ $option_group ] = static::VALUE_NO;
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
		$option_rule_property  = $this->name . '_rule';
		$options               = $module->options;

		// The enabled status
		$enabled = false;
		if ( isset( $options->{ $option_property }[ static::GROUP_GLOBAL ] ) ) {
			$enabled = static::VALUE_YES === $options->{ $option_property }[ static::GROUP_GLOBAL ];
		}

		// Featured Image Rule
		$rule = static::RULE_ONLY_DISPLAY;
		if ( isset( $options->{ $option_rule_property }[ static::GROUP_GLOBAL ] ) ) {
			$rule = $options->{ $option_rule_property }[ static::GROUP_GLOBAL ];
		}

		// Register in the requirements list
		if ( $enabled ) {
			$requirements[ $this->name ] = array(
				'status' => $this->get_current_status( $post, $enabled ),
				'label'  => $this->lang['label'],
				'value'  => $enabled,
				'rule'   => $rule
			);
		}

		return $requirements;
	}
}