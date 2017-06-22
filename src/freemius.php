<?php
defined('ABSPATH') or die("No direct script access allowed.");

/**
 * @package     PublishPress
 * @author      PressShack <help@pressshack.com>
 * @copyright   Copyright (C) 2017 PressShack. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

// Create a helper function for easy SDK access.
function ppch_fs() {
    global $ppch_fs;

    if ( ! isset( $ppch_fs ) ) {
        // Include Freemius SDK.
        if ( file_exists( dirname( dirname( __FILE__ ) ) . '/publishpress/freemius/start.php' ) ) {
            // Try to load SDK from parent plugin folder.
            require_once dirname( dirname( __FILE__ ) ) . '/publishpress/freemius/start.php';
        } else if ( file_exists( dirname( dirname( __FILE__ ) ) . '/publishpress-premium/freemius/start.php' ) ) {
            // Try to load SDK from premium parent plugin folder.
            require_once dirname( dirname( __FILE__ ) ) . '/publishpress-premium/freemius/start.php';
        } else {
            require_once dirname(__FILE__) . '/freemius/start.php';
        }

        $ppch_fs = fs_dynamic_init( array(
            'id'                  => '985',
            'slug'                => 'publishpress-content-checklist',
            'type'                => 'plugin',
            'public_key'          => 'pk_c005cb9f742a61050d20e932e37bf',
            'is_premium'          => false,
            'has_paid_plans'      => false,
            'is_org_compliant'    => false,
            'parent'              => array(
                'id'         => '984',
                'slug'       => 'publishpress',
                'public_key' => 'pk_e6bd6e574d5d8ca753f61e1a2d43c',
                'name'       => 'PublishPress',
            ),
            'menu'                => array(
                'slug'       => 'pp-calendar',
                'first-path' => 'admin.php?page=pp-calendar',
                'support'    => false,
                'account'    => false,
            ),
        ) );
    }

    return $ppch_fs;
}


function ppch_fs_is_parent_active_and_loaded() {
    // Check if the parent's init SDK method exists.
    return function_exists( 'pp_fs' );
}

function ppch_fs_is_parent_active() {
    $active_plugins_basenames = get_option( 'active_plugins' );

    foreach ( $active_plugins_basenames as $plugin_basename ) {
        if ( 0 === strpos( $plugin_basename, 'publishpress/' ) ||
             0 === strpos( $plugin_basename, 'publishpress-premium/' )
        ) {
            return true;
        }
    }

    return false;
}

function ppch_fs_init() {
    if ( ppch_fs_is_parent_active_and_loaded() ) {
        // Init Freemius.
        ppch_fs();

        // Parent is active, add your init code here.
    } else {
        // Parent is inactive, add your error handling here.
    }
}

if ( ppch_fs_is_parent_active_and_loaded() ) {
    // If parent already included, init add-on.
    ppch_fs_init();
} else if ( ppch_fs_is_parent_active() ) {
    // Init add-on only after the parent is loaded.
    add_action( 'pp_fs_loaded', 'ppch_fs_init' );
} else {
    // Even though the parent is not activated, execute add-on for activation / uninstall hooks.
    ppch_fs_init();
}
