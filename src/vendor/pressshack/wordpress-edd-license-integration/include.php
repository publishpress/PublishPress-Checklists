<?php
/**
 * @package WordPress-EDD-License-Integration
 * @author PressShack
 *
 * Copyright (c) 2017 PressShack
 *
 * This file is part of WordPress-EDD-License-Integration
 *
 * WordPress-EDD-License-Integration is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * WordPress-EDD-License-Integration is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WordPress-EDD-License-Integration.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'PRESSSHACK_LICENSES_API_URL' ) ) {
    define( 'PRESSSHACK_LICENSES_API_URL', "http://pressshack.com" );
}

if ( ! defined( 'PRESSSHACK_LICENSES_BASE_PATH' ) ) {
	$path = str_replace( ABSPATH, '', __DIR__ );

    define( 'PRESSSHACK_LICENSES_BASE_PATH', $path );
}

if ( ! defined( 'PRESSSHACK_LICENSES_ASSETS_PATH' ) ) {
    define( 'PRESSSHACK_LICENSES_ASSETS_PATH', get_site_url() . '/' . PRESSSHACK_LICENSES_BASE_PATH . '/assets' );
}

if ( ! defined( 'PRESSSHACK_LICENSES_VERSION' ) ) {
    define( 'PRESSSHACK_LICENSES_VERSION', '1.0.0' );
}

$edd_license_language = new PressShack\EDD_License\Language;
