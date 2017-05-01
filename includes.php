<?php
/**
 * File responsible for defining basic general constants used by the plugin.
 *
 * @package     PublishPress\Checklist
 * @author      PressShack <help@pressshack.com>
 * @copyright   Copyright (C) 2017 Open Source Training, LLC. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

use \PublishPress\Addon\Checklist\Auto_loader;

defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

require_once 'freemius.php';
require_once 'vendor/autoload.php';
require_once 'vendor/pressshack/wordpress-edd-license-integration/include.php';

if ( ! function_exists( 'is_plugin_inactive' ) ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

/*======================================================================
=            Check if PublishPress is installed and active             =
======================================================================*/
$publishpressPath = WP_PLUGIN_DIR . '/publishpress/publishpress.php';
if ( ! file_exists( $publishpressPath ) || is_plugin_inactive( 'publishpress/publishpress.php' ) ) {
	function pp_checklist_admin_error() {
		?>
		<div class="notice notice-error is-dismissible">
			Please, install and activate the <a href="https://wordpress.org/plugins/publishpress" target="_blank">PublishPress</a></strong> plugin in order to make <em>PublishPress Checklist</em> work.</p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'pp_checklist_admin_error' );

	define( 'PUBLISHPRESS_CHECKLIST_HALT', 1 );
}
/*=====  End of Check if PublishPress is installed and active   ======*/

if ( ! defined( 'PUBLISHPRESS_CHECKLIST_HALT' ) ) {
	require_once $publishpressPath;

	if ( ! defined( 'PUBLISHPRESS_CHECKLIST_MIN_PARENT_VERSION' ) ) {
		define( 'PUBLISHPRESS_CHECKLIST_MIN_PARENT_VERSION', '1.3.0' );
	}

	/*==========================================================
	=            Check PublishPress minimum version            =
	==========================================================*/
	if ( version_compare( PUBLISHPRESS_VERSION, PUBLISHPRESS_CHECKLIST_MIN_PARENT_VERSION, '<' ) ) {
		function pp_checklist_admin_version_error() {
			?>
			<div class="notice notice-error is-dismissible">
				Sorry, PublishPress Checklist requires <a href="https://wordpress.org/plugins/publishpress" target="_blank">PublishPress</a></strong> version <?php echo PUBLISHPRESS_CHECKLIST_MIN_PARENT_VERSION; ?> or later.</p>
			</div>
			<?php
		}
		add_action( 'admin_notices', 'pp_checklist_admin_version_error' );

		define( 'PUBLISHPRESS_CHECKLIST_HALT', 1 );
	}
	/*=====  End of Check PublishPress minimum version  ======*/

	if ( ! defined( 'PUBLISHPRESS_CHECKLIST_HALT' ) ) {
		if ( ! defined( 'PUBLISHPRESS_CHECKLIST' ) ) {
			define( 'PUBLISHPRESS_CHECKLIST', 'Checklist' );
		}

		if ( ! defined( 'PUBLISHPRESS_CHECKLIST_NAME' ) ) {
			define( 'PUBLISHPRESS_CHECKLIST_NAME', 'PublishPress Checklist' );
		}

		if ( ! defined( 'PUBLISHPRESS_CHECKLIST_SLUG' ) ) {
			define( 'PUBLISHPRESS_CHECKLIST_SLUG', strtolower( PUBLISHPRESS_CHECKLIST ) );
		}

		if ( ! defined( 'PUBLISHPRESS_CHECKLIST_NAMESPACE' ) ) {
			define( 'PUBLISHPRESS_CHECKLIST_NAMESPACE', 'PublishPress\\' . PUBLISHPRESS_CHECKLIST );
		}

		if ( ! defined( 'PUBLISHPRESS_CHECKLIST_PATH_BASE' ) ) {
			define( 'PUBLISHPRESS_CHECKLIST_PATH_BASE', plugin_dir_path( __FILE__ ) );
		}

		if ( ! defined( 'PUBLISHPRESS_CHECKLIST_PATH_CORE' ) ) {
			define( 'PUBLISHPRESS_CHECKLIST_PATH_CORE', PUBLISHPRESS_CHECKLIST_PATH_BASE . PUBLISHPRESS_CHECKLIST );
		}

		if ( ! defined( 'PUBLISHPRESS_CHECKLIST_VERSION' ) ) {
			define( 'PUBLISHPRESS_CHECKLIST_VERSION', '1.0.1' );
		}

		if ( ! defined( 'PUBLISHPRESS_CHECKLIST_MODULE_PATH' ) ) {
			define( 'PUBLISHPRESS_CHECKLIST_MODULE_PATH', __DIR__ . '/modules/checklist' );
		}

		if ( ! defined( 'PUBLISHPRESS_CHECKLIST_LANG_CONTEXT' ) ) {
			define( 'PUBLISHPRESS_CHECKLIST_LANG_CONTEXT', 'publishpress-checklist' );
		}

		if ( ! defined( 'PUBLISHPRESS_CHECKLIST_FILE' ) ) {
			define( 'PUBLISHPRESS_CHECKLIST_FILE', 'publishpress-checklist/publishpress-checklist.php' );
		}

		if ( ! defined( 'PUBLISHPRESS_CHECKLIST_ITEM_NAME' ) ) {
			define( 'PUBLISHPRESS_CHECKLIST_ITEM_NAME', 'PublishPress Content Checklist' );
		}

		if ( ! defined( 'PUBLISHPRESS_CHECKLIST_LIB_PATH' ) ) {
			define( 'PUBLISHPRESS_CHECKLIST_LIB_PATH', PUBLISHPRESS_CHECKLIST_PATH_BASE . '/library' );
		}

		if ( ! class_exists( 'PP_Module' ) ) {
			require_once( PUBLISHPRESS_ROOT . '/common/php/class-module.php' );
		}

		// Load the modules
		if ( ! class_exists( 'PP_Checklist' ) ) {
			require_once PUBLISHPRESS_CHECKLIST_MODULE_PATH . '/checklist.php';
		}

		// Register the autoloader
		if ( ! class_exists( '\\PublishPress\\Addon\\Checklist\\Auto_loader' ) ) {
			require_once PUBLISHPRESS_CHECKLIST_LIB_PATH . '/Auto_loader.php';
		}

		// Register the library
    	Auto_loader::register('\\PublishPress\\Addon\\Checklist', PUBLISHPRESS_CHECKLIST_PATH_BASE . '/library');

    	// Define the add-on as loaded
		if ( ! defined( 'PUBLISHPRESS_CHECKLIST_LOADED' ) ) {
			define( 'PUBLISHPRESS_CHECKLIST_LOADED', 1 );
		}
	}
}// End if().
