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

class Image_alt extends Base_simple
{

    /**
     * The name of the requirement, in a slug format
     *
     * @var string
     */
    public $name = 'image_alt';

    /**
     * @var int
     */
    public $position = 90;

    /**
     * Initialize the language strings for the instance
     *
     * @return void
     */
    public function init_language()
    {
        $this->lang['label']          = __('Alt text for all images', 'publishpress-checklists');
        $this->lang['label_settings'] = __('Alt text for all images', 'publishpress-checklists');
    }

    /**
     * Check for images without alt text from content and return result as array
     *
     * @param string $content
     * @param array $missing_alt
     *
     * @return array
     * @since  1.0.1
     */
    private function missing_alt_images($content, $missing_alt = array())
    {
        if ($content) {
            //remove ALT tag if it value is empty or whitespace without real text
            $content = preg_replace('!alt="\p{Z}*"|alt=\'\p{Z}*\'!s', '', $content);

            //look for images without ALT attribute at all
            preg_match_all('@<img(?:(?!alt=).)*?>@', $content, $images);

            //return the array
            $missing_alt = $images[0];
        }

        return $missing_alt;
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
        $count = count($this->missing_alt_images($post->post_content));

        return $count == 0;
    }
}
