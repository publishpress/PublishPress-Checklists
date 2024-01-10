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

class Words_count extends Base_counter
{

    /**
     * The name of the requirement, in a slug format
     *
     * @var string
     */
    public $name = 'words_count';

    /**
     * @var int
     */
    public $position = 20;

    /**
     * Initialize the language strings for the instance
     *
     * @return void
     */
    public function init_language()
    {
        $this->lang['label_settings']       = __('Number of words in content', 'publishpress-checklists');
        $this->lang['label_min_singular']   = __('Minimum of %d word in content', 'publishpress-checklists');
        $this->lang['label_min_plural']     = __('Minimum of %d words in content', 'publishpress-checklists');
        $this->lang['label_max_singular']   = __('Maximum of %d word in content', 'publishpress-checklists');
        $this->lang['label_max_plural']     = __('Maximum of %d words in content', 'publishpress-checklists');
        $this->lang['label_exact_singular'] = __('%d word in content', 'publishpress-checklists');
        $this->lang['label_exact_plural']   = __('%d words in content', 'publishpress-checklists');
        $this->lang['label_between']        = __('Between %d and %d words in content', 'publishpress-checklists');
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
        $count = str_word_count(strip_tags($post->post_content));

        return ($count >= $option_value[0]) && ($option_value[1] == 0 || $count <= $option_value[1]);
    }
}
