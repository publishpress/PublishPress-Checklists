<?php
/**
 * File responsible for defining basic addon class
 *
 * @package     PublishPress\Content_checklist
 * @author      PressShack <help@pressshack.com>
 * @copyright   Copyright (C) 2017 Open Source Training, LLC. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Addon\Content_checklist;

use PublishPress\Addon\Content_checklist\Plugin;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Extension_Debug;

defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

class Plugin {

	/**
	 * The rule that disables
	 */
	const RULE_DISABLED = 'off';

	/**
	 * The rule that do not warning, or block
	 */
	const RULE_ONLY_DISPLAY = 'only_display';

	/**
	 * The rule that displays a warning
	 */
	const RULE_WARNING = 'warning';

	/**
	 * The rule that blocks
	 */
	const RULE_BLOCK = 'block';

	/**
	 * Twig instance
	 *
	 * @var Twig
	 */
	protected $twig;

	/**
	 * Flag for debug
	 *
	 * @var boolean
	 */
	protected $debug = false;

	/**
	 * The constructor
	 */
	public function __construct() {
		$twigPath = PP_CONTENT_CHECKLIST_PATH_BASE . 'twig';

		$loader = new Twig_Loader_Filesystem( $twigPath );
		$this->twig = new Twig_Environment( $loader, array(
			'debug' => $this->debug,
		) );

		if ( $this->debug ) {
			$this->twig->addExtension( new Twig_Extension_Debug() );
		}
	}

	/**
	 * The method which runs the plugin
	 */
	public function init() {
		if ( ! $this->checkRequirements() ) {
			add_action( 'admin_notices', array( $this, 'warning_requirements' ) );

			return false;
		}

		add_filter( 'pp_module_dirs', array( $this, 'filter_module_dirs' ) );
		add_filter( 'pp_checklist_rules_list', array( $this, 'filter_rules_list' ) );
	}

	/**
	 * Add custom module directory
	 *
	 * @param  array
	 * @return array
	 */
	public function filter_module_dirs( $dirs ) {
		$dirs['checklist'] = rtrim( PP_CONTENT_CHECKLIST_PATH_BASE, '/' );

		return $dirs;
	}

	/**
	 * Check if the system complies the requirements
	 *
	 * @return bool
	 */
	protected function checkRequirements() {
		return defined( 'PUBLISHPRESS_VERSION' ) && version_compare( PUBLISHPRESS_VERSION, '1.3.0', 'ge' );
	}

	public function warning_requirements() {
		echo $this->twig->render(
			'requirements-warning.twig',
			array(
				'lang' => array(
					'publishpress' => __( 'PublishPress', 'publishpress-content-checklist' ),
					'warning' => __('PublishPress Content Checklist requires __plugin__ 1.3.0 or later. Please, update.', 'publishpress-content-checklist' ),
				),
			)
		);
	}

	/**
	 * Get a list of rules for the requirement
	 *
	 * @return array
	 */
	public function filter_rules_list( $rules ) {
		$rules = array_merge(
			$rules,
			array(
				Plugin::RULE_DISABLED     => __( 'Disabled', 'publishpress-content-checklist' ),
				Plugin::RULE_ONLY_DISPLAY => __( 'Show a sidebar message', 'publishpress-content-checklist' ),
				Plugin::RULE_WARNING      => __( 'Show a pop-up message', 'publishpress-content-checklist' ),
				Plugin::RULE_BLOCK        => __( 'Prevent publishing', 'publishpress-content-checklist' ),
			)
		);

		return $rules;
	}
}
