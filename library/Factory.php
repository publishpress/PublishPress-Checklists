<?php
/**
 * @package     PublishPress\Content_checklist
 * @author      PublishPress <help@publishpress.com>
 * @copyright   Copyright (C) 2018 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Addon\Content_checklist;

defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

if (!defined('PP_CONTENT_CHECKLIST_LOADED')) {
    require_once __DIR__ . '/../includes.php';
}

/**
 * Class Factory
 */
abstract class Factory {
	/**
     * @var Container
     */
    protected static $container = null;
    /**
     * @return Container
     */
    public static function get_container()
    {
        if (static::$container === null) {
	        $module   = PublishPress()->checklist;
        	$services = new Services($module);

            static::$container = new Container();
            static::$container->register( $services );
        }

        return static::$container;
    }
}
