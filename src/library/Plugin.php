<?php
/**
 * File responsible for defining basic addon class
 *
 * @package     PublishPress\Checklist
 * @author      PressShack <help@pressshack.com>
 * @copyright   Copyright (C) 2017 Open Source Training, LLC. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Addon\Checklist;

defined('ABSPATH') or die("No direct script access allowed.");

class Plugin
{
	/**
	 * The constructor
	 */
	public function __construct()
	{

	}

	/**
	 * The method which runs the plugin
	 */
	public function init()
	{
		add_filter('pp_module_dirs', array($this, 'filter_module_dirs'));
	}

	/**
	 * Add custom module directory
	 * 
	 * @param  array
	 * @return array
	 */
	public function filter_module_dirs($dirs)
	{
		$dirs['checklist'] = rtrim(PUBLISHPRESS_PLG_CHECKLIST_PATH_BASE, '/');

		return $dirs;
	}
}