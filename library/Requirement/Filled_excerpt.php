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

class Filled_excerpt extends Base_simple
{
    /**
     * The name of the requirement, in a slug format
     *
     * @var string
     */
    public $name = 'filled_excerpt';

    /**
     * Initialize the language strings for the instance
     *
     * @return void
     */
    public function init_language()
    {
        $this->lang['label']          = __('Excerpt has text', 'publishpress-content-checklist');
        $this->lang['label_settings'] = __('Excerpt has text', 'publishpress-content-checklist');
    }

    /**
     * Returns the current status of the requirement.
     *
     * @param  stdClass $post
     * @param  mixed    $option_value
     *
     * @return mixed
     */
    public function get_current_status($post, $option_value)
    {
        $excerpt = trim(get_the_excerpt($post));

        return ! empty($excerpt);
    }
}
