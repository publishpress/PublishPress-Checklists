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

class Base_requirement {
	/**
	 * The default value for the option related to the status.
	 */
	const DEFAULT_OPTION_STATUS = 'no';

	/**
	 * The default value for the option related to the rule.
	 */
	const DEFAULT_OPTION_RULE = 'only_display';

	/**
	 * The Yes value
	 */
	const VALUE_YES = 'yes';

	/**
	 * The No value
	 */
	const VALUE_NO = 'no';

	/**
	 * The global group
	 */
	const GROUP_GLOBAL = 'global';

	/**
	 * The constructor. It adds the action to load the requirement.
	 *
	 * @return  void
	 */
	public function __construct() {
		add_action( 'pp_checklist_load_requirements', array( $this, 'init' ) );
	}

	/**
	 * Method to initialize the Requirement, adding filters and actions to
	 * interact with the Add-on.
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'pp_checklist_requirements_default_options', array( $this, 'filter_default_options' ) );
		add_filter( 'pp_checklist_validate_option_group', array( $this, 'filter_settings_validate' ), 10, 2 );
		add_filter( 'pp_checklist_requirements_metabox', array( $this, 'filter_requirements_metabox' ), 10, 3 );
	}

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
	public function get_current_status( $post, $option_value ) {
		return false;
	}
}