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

class Max_categories_count extends Base_counter {
	/**
	 * The name of this requirement.
	 */
	const NAME = 'max_categories_count';

	/**
	 * Initialize the language strings for the instance
	 *
	 * @return void
	 */
	public function init_language() {
		$this->lang['label_singular'] = __( 'Maximum of %s category', PP_CONTENT_CHECKLIST_LANG_CONTEXT );
		$this->lang['label_plural']   = __( 'Maximum of %s categories', PP_CONTENT_CHECKLIST_LANG_CONTEXT );
		$this->lang['label_settings'] = __( 'Maximum number of categories', PP_CONTENT_CHECKLIST_LANG_CONTEXT );
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
		$categories = wp_get_post_categories( $post->ID );

		return count( $categories ) <= $option_value;
	}
}