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

class Featured_image_alt extends Base_simple
{

    /**
     * The name of the requirement, in a slug format
     *
     * @var string
     */
    public $name = 'featured_image_alt';

    /**
     * @var int
     */
    public $position = 110;

    /**
     * Initialize the language strings for the instance
     *
     * @return void
     */
    public function init_language()
    {
        $this->lang['label']          = __('Alt text for featured images', 'publishpress-checklists');
        $this->lang['label_settings'] = __('Alt text for featured images', 'publishpress-checklists');
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
        $thumbnail_id = get_post_thumbnail_id($post);
        $img_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

        return !empty($img_alt);
    }
}
