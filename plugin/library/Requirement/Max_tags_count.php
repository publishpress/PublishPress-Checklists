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

class Max_tags_count extends Base_counter {
	/**
	 * The name of this requirement.
	 */
	const NAME = 'max_tags_count';

	/**
	 * Initialize the language strings for the instance
	 *
	 * @return void
	 */
	public function init_language() {
		$this->lang['label_singular'] = __( 'Maximum of %s tag', PP_CONTENT_CHECKLIST_LANG_CONTEXT );
		$this->lang['label_plural']   = __( 'Maximum of %s tags', PP_CONTENT_CHECKLIST_LANG_CONTEXT );
		$this->lang['label_settings'] = __( 'Maximum number of tags', PP_CONTENT_CHECKLIST_LANG_CONTEXT );
	}

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

		return count( $tags ) <= $option_value;
	}
}