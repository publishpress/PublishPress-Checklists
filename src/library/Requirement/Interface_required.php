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

interface Interface_required {
	/**
	 * Injects the respective default options into the main add-on.
	 *
	 * @param  array  $default_options
	 * @return array
	 */
	public function filter_default_options( $default_options );

	/**
	 * Validates the option group, making sure the values are sanitized.
	 * It runs for each option group, including "global".
	 *
	 * @param  array   $new_options
	 * @param  string  $option_group
	 *
	 * @return array
	 */
	public function filter_settings_validate( $new_options, $option_group );

	/**
	 * Add the requirement to the list to be displayed in the metabox.
	 *
	 * @param  array      $requirements
	 * @param  stdClass   $post
	 * @param  PP_Module  $module
	 *
	 * @return array
	 */
	public function filter_requirements_metabox( $requirements, $post, $module );

	/**
	 * Returns the current status of the requirement.
	 *
	 * The child class should
	 * evaluate the status and override this method.
	 *
	 * @param  stdClass  $post
	 * @param  mixed     $option_value
	 *
	 * @return mixed
	 */
	public function get_current_status( $post, $option_value );
}