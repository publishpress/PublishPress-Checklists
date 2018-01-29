<?php
/**
 * File responsible for defining basic general constants used by the plugin.
 *
 * @package     PublishPress\Content_checklist
 * @author      PublishPress <help@publishpress.com>
 * @copyright   Copyright (C) 2018 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

use \PublishPress\Addon\Content_checklist\Auto_loader;

defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

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
			Please, install and activate the <a href="https://wordpress.org/plugins/publishpress" target="_blank">PublishPress</a></strong> plugin in order to make <em>PublishPress Content Checklist</em> work.</p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'pp_checklist_admin_error' );

	define( 'PP_CONTENT_CHECKLIST_HALT', 1 );
}
/*=====  End of Check if PublishPress is installed and active   ======*/

if ( ! defined( 'PP_CONTENT_CHECKLIST_HALT' ) && ! defined( 'PP_CONTENT_CHECKLIST_LOADED' ) ) {
	require_once $publishpressPath;

	if ( ! defined( 'PP_CONTENT_CHECKLIST_MIN_PARENT_VERSION' ) ) {
		define( 'PP_CONTENT_CHECKLIST_MIN_PARENT_VERSION', '1.6.0' );
	}

	/*==========================================================
	=            Check PublishPress minimum version            =
	==========================================================*/
	if ( version_compare( PUBLISHPRESS_VERSION, PP_CONTENT_CHECKLIST_MIN_PARENT_VERSION, '<' ) ) {
		function pp_checklist_admin_version_error() {
			?>
			<div class="notice notice-error is-dismissible">
				Sorry, PublishPress Content Checklist requires <a href="https://wordpress.org/plugins/publishpress" target="_blank">PublishPress</a></strong> version <?php echo PP_CONTENT_CHECKLIST_MIN_PARENT_VERSION; ?> or later.</p>
			</div>
			<?php
		}
		add_action( 'admin_notices', 'pp_checklist_admin_version_error' );

		define( 'PP_CONTENT_CHECKLIST_HALT', 1 );
	}
	/*=====  End of Check PublishPress minimum version  ======*/

	if ( ! defined( 'PP_CONTENT_CHECKLIST_HALT' ) ) {
		define( 'PP_CONTENT_CHECKLIST_ITEM_ID', '6465' );
		define( 'PP_CONTENT_CHECKLIST_PATH_BASE', plugin_dir_path( __FILE__ ) );
		define( 'PP_CONTENT_CHECKLIST_VERSION', '1.3.5' );
		define( 'PP_CONTENT_CHECKLIST_FILE', 'publishpress-content-checklist/publishpress-content-checklist.php');
		define( 'PP_CONTENT_CHECKLIST_MODULE_PATH', __DIR__ . '/modules/checklist' );
		define( 'PP_CONTENT_CHECKLIST_ITEM_NAME', 'Content Checklist for PublishPress' );
		define( 'PP_CONTENT_CHECKLIST_LIB_PATH', PP_CONTENT_CHECKLIST_PATH_BASE . '/library' );
		define( 'PP_CONTENT_CHECKLIST_LOADED', 1 );

		if ( ! class_exists( 'PP_Module' ) ) {
			require_once( PUBLISHPRESS_ROOT . '/common/php/class-module.php' );
		}

		// Load the modules
		if ( ! class_exists( 'PP_Checklist' ) ) {
			require_once PP_CONTENT_CHECKLIST_MODULE_PATH . '/checklist.php';
		}

		// Register the autoloader
		if ( ! class_exists( '\\PublishPress\\Addon\\Content_checklist\\Auto_loader' ) ) {
			require_once PP_CONTENT_CHECKLIST_LIB_PATH . '/Auto_loader.php';
		}

		// Register the library
    	Auto_loader::register('\\PublishPress\\Addon\\Content_checklist', PP_CONTENT_CHECKLIST_PATH_BASE . '/library');

	}
}// End if().
