<?php
/**
 * @package     PublishPress\Checklists
 * @author      PublishPress <help@publishpress.com>
 * @copyright   copyright (C) 2019 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Checklists\Core\Requirement;

defined('ABSPATH') or die('No direct script access allowed.');

class Filled_excerpt extends Base_counter
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
        $this->lang['label']                = __('Excerpt has text', 'publishpress-checklists');
        $this->lang['label_settings']       = __('Excerpt has text', 'publishpress-checklists');
        $this->lang['label_min_singular']   = __('Minimum of %s excerpt text', 'publishpress-checklists');
        $this->lang['label_min_plural']     = __('Minimum of %s excerpt texts', 'publishpress-checklists');
        $this->lang['label_max_singular']   = __('Maximum of %s excerpt text', 'publishpress-checklists');
        $this->lang['label_max_plural']     = __('Maximum of %s excerpt texts', 'publishpress-checklists');
        $this->lang['label_exact_singular'] = __('%s excerpt text', 'publishpress-checklists');
        $this->lang['label_exact_plural']   = __('%s excerpt texts', 'publishpress-checklists');
        $this->lang['label_between']        = __('Between %s and %s excerpt texts', 'publishpress-checklists');
    }

    /**
     * Returns the current status of the requirement.
     *
     * @param stdClass $post
     * @param mixed $option_value
     *
     * @return mixed
     */
    public function get_current_status($post, $option_value)
    {
        $count = strlen(trim(get_the_excerpt($post)));

        return ($count >= $option_value[0]) && ($count <= $option_value[1]);
    }
}
