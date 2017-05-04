<?php
/**
 * @package     PublishPress\Content_checklist
 * @author      PressShack <help@pressshack.com>
 * @copyright   Copyright (C) 2017 Open Source Training, LLC. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Addon\Content_checklist\Requirement;

defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

class Tags_Count extends Base_counter {
	/**
	 * The name of this requirement.
	 */
	const NAME = 'min_tags_count';

	/**
	 * The label to be displayed in the metabox for value == 1
	 */
	const LABEL_SINGULAR = 'Minimum of %s tag';

	/**
	 * The label to be displayed in the metabox for value > 1 or 0
	 */
	const LABEL_PLURAL = 'Minimum of %s tags';

	/**
	 * Returns the current status of the requirement.
	 *
	 * @param  stdClass  $post
	 * @param  mixed     $option_value
	 *
	 * @return mixed
	 */
	public function get_current_status( $post, $option_value ) {
		$tags = wp_get_post_tags( $post->ID );

		return count( $tags ) >= $option_value;
	}
}