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

namespace PressShack\EDD_License;

/**
 * Class for license
 */
class License {
	/**
	 * Constant for missing license status
	 */
	const STATUS_EMPTY_LICENSE = '';

	/**
	 * Constant for valid status
	 */
	const STATUS_VALID = 'valid';

	/**
	 * Constant for expired status
	 */
	const STATUS_EXPIRED = 'expired';

	/**
	 * Constant for revoked status
	 */
	const STATUS_REVOKED = 'revoked';

	/**
	 * Constant for missing status
	 */
	const STATUS_MISSING = 'missing';

	/**
	 * Constant for invalid status
	 */
	const STATUS_INVALID = 'invalid';

	/**
	 * Constant for inactive status
	 */
	const STATUS_SITE_INACTIVE = 'site_inactive';

	/**
	 * Constant for mismatch status
	 */
	const STATUS_ITEM_NAME_MISMATCH = 'item_name_mismatch';

	/**
	 * Constant for no activations left status
	 */
	const STATUS_NO_ACTIVATIONS_LEFT = 'no_activations_left';

	/**
	 * The constructor
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
	}

	/**
	 * Method that validates a license key.
	 *
	 * @param string $license_key
	 * @param string $item_id
	 *
	 * @return  mixed
	 */
	public function validate_license_key( $license_key, $item_id ) {
		$response = wp_remote_post(
			PRESSSHACK_LICENSES_API_URL,
			array(
				'timeout'   => 30,
				'sslverify' => false,
				'body'      => array(
					'edd_action' => "activate_license",
					'license'    => $license_key,
					'item_id'    => $item_id,
					'url'        => home_url()
				)
			)
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$errMessage = $response->get_error_message();

			if ( ! is_wp_error( $response ) || empty( $errMessage ) ) {
				$errMessage = __( 'An error occurred. Please, try again.', 'wp-edd-license-integration' );
			}

			return $errMessage;
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( empty( $license_data ) || ! is_object( $license_data ) ) {
				$license_new_status = static::STATUS_INVALID;
			} else {
				if ( isset( $license_data->success ) && true === $license_data->success ) {
					$license_new_status = static::STATUS_VALID;
				} else {
					if ( isset( $license_data->license ) && static::STATUS_INVALID === $license_data->license ) {
						$license_new_status = static::STATUS_INVALID;
					} else {
						$license_new_status = isset( $license_data->error ) && ! empty( $license_data->error ) ? $license_data->error : static::STATUS_INVALID;
					}
				}
			}

			return $license_new_status;
		}
	}

	/**
	 * Sanitize the license key, returning the clean key.
	 *
	 * @param  string $license_key
	 * @return string
	 */
	public function sanitize_license_key( $license_key ) {
		return preg_replace('/[^a-z0-9\-_]/i', '', $license_key );
	}

	/**
	 * Enqueue JS scripts and CSS stylesheets
	 */
	public function enqueue_scripts_styles() {
		wp_enqueue_style(
			'wp-edd-license-integration',
			PRESSSHACK_LICENSES_ASSETS_PATH . '/css/edd-license-style.css',
			false,
			PRESSSHACK_LICENSES_VERSION,
			'all'
		);
	}
}
