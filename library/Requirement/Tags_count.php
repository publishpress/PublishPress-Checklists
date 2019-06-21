<?php
/**
 * @package     PublishPress\Content_checklist
 * @author      PublishPress <help@publishpress.com>
 * @copyright   Copyright (C) 2018 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Addon\Content_checklist\Requirement;

defined('ABSPATH') or die('No direct script access allowed.');

class Tags_Count extends Base_counter
{
    /**
     * The priority for the action to load the requirement
     */
    const PRIORITY = 9;

    /**
     * The name of the requirement, in a slug format
     *
     * @var string
     */
    public $name = 'tags_count';

    /**
     * Initialize the language strings for the instance
     *
     * @return void
     */
    public function init_language()
    {
        $this->lang['label_settings']       = __('Number of tags', 'publishpress-content-checklist');
        $this->lang['label_min_singular']   = __('Minimum of %s tag', 'publishpress-content-checklist');
        $this->lang['label_min_plural']     = __('Minimum of %s tags', 'publishpress-content-checklist');
        $this->lang['label_max_singular']   = __('Maximum of %s tag', 'publishpress-content-checklist');
        $this->lang['label_max_plural']     = __('Maximum of %s tags', 'publishpress-content-checklist');
        $this->lang['label_exact_singular'] = __('%s tag', 'publishpress-content-checklist');
        $this->lang['label_exact_plural']   = __('%s tags', 'publishpress-content-checklist');
        $this->lang['label_between']        = __('Between %s and %s tags', 'publishpress-content-checklist');
    }

    /**
     * Returns the current status of the requirement.
     *
     * @param stdClass $post
     * @param mixed    $option_value
     *
     * @return mixed
     */
    public function get_current_status($post, $option_value)
    {
        $tags = wp_get_post_tags($post->ID);

        $count = count($tags);

        return ($count >= $option_value[0]) && ($count <= $option_value[1]);
    }
}
