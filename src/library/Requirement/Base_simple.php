<?php
/**
 * @package     PublishPress\Content_checklist
 * @author      PressShack <help@pressshack.com>
 * @copyright   Copyright (C) 2017 PressShack. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Addon\Content_checklist\Requirement;

use PublishPress\Addon\Content_checklist\Plugin;

defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

class Base_simple extends Base_requirement implements Interface_required {

	/**
	 * Injects the respective default options into the main add-on.
	 *
	 * @param  array  $default_options
	 * @return array
	 */
	public function filter_default_options( $default_options ) {
		$options = array(
			$this->name    => array(
				$this->post_type => static::VALUE_NO,
			),
			"{$this->name}_rule" => array(
				$this->post_type => Plugin::RULE_ONLY_DISPLAY,
			),
		);

		return array_merge( $default_options, $options );
	}

	/**
	 * Validates the option group, making sure the values are sanitized.
	 *
	 * @param  array   $new_options
	 *
	 * @return array
	 */
	public function filter_settings_validate( $new_options ) {
		if ( isset( $new_options[ $this->name ][ $this->post_type ] ) ) {
			if ( static::VALUE_YES !== $new_options[ $this->name ][ $this->post_type ] ) {
				$new_options[ $this->name ][ $this->post_type ] = static::VALUE_NO;
			}
		} else {
			$new_options[ $this->name ][ $this->post_type ] = static::VALUE_NO;
		}

		return $new_options;
	}

	/**
	 * Returns the value for the rule option. The default value is "Disabled"
	 *
	 * @return string
	 */
	public function get_option_rule() {
		$option_rule_property  = $this->name . '_rule';
		$options               = $this->module->options;

		// Rule
		$rule = Plugin::RULE_DISABLED;
		if ( isset( $options->{ $option_rule_property }[ $this->post_type ] ) ) {
			$rule = $options->{ $option_rule_property }[ $this->post_type ];
		}

		return $rule;
	}

	/**
	 * Returns true if the requirement is enabled in the settings.
	 *
	 * @return boolean
	 */
	public function is_enabled() {
		$rule = $this->get_option_rule();

		return in_array(
			$rule,
			array(
				Plugin::RULE_ONLY_DISPLAY,
				Plugin::RULE_WARNING,
				Plugin::RULE_BLOCK,
			)
		);
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
				'status' => $this->get_current_status( $post, $enabled ),
				'label'  => $this->lang['label'],
				'value'  => $enabled,
				'rule'   => $rule
			);
		}

		return $requirements;
	}
}
