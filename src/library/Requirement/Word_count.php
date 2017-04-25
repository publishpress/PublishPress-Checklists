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

class Word_Count extends Base_counter {
	/**
	 * The name of this requirement.
	 */
	const NAME = 'min_word_count';

	/**
	 * The label to be displayed in the metabox for value <= 1
	 */
	const LABEL_SINGULAR = 'Minimum of %s word';

	/**
	 * The label to be displayed in the metabox for value > 1
	 */
	const LABEL_PLURAL = 'Minimum of %s words';

	/**
	 * Returns the current status of the requirement.
	 *
	 * @param  stdClass  $post
	 * @param  mixed     $option_value
	 *
	 * @return mixed
	 */
	public function get_current_status( $post, $option_value ) {
		return str_word_count( $post->post_content ) >= $option_value;
	}
}