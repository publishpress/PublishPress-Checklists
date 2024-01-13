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
     * The thumbnail id
     * 
     * @var int
     */
    protected $thumbnail_id = 0;

    /**
     * The thumbnail alt
     * 
     * @var string
     */
    protected $thumbnail_alt = '';

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
     * Get the thumbnail id
     * 
     * @param stdClass $post
     * 
     * @return void
     */
    public function get_thumbnail_id($post)
    {
        $thumb_id = get_post_thumbnail_id($post);
        $this->thumbnail_id = $thumb_id;
    }

    /**
     * Get the thumbnail alt
     * 
     * @param stdClass $post
     * 
     * @return void
     */
    public function get_thumbnail_alt($post)
    {
        $thumb_alt = get_post_meta($this->thumbnail_id, '_wp_attachment_image_alt', true);
        $this->thumbnail_alt = $thumb_alt;
    }

    /**
     * Add the requirement to the list to be displayed in the meta box.
     *
     * @param array $requirements
     * @param stdClass $post
     *
     * @return array
     */
    public function filter_requirements_list($requirements, $post)
    {
        if ($post->post_type !== $this->post_type) {
            return $requirements;
        }

        // Rule
        $rule = $this->get_option_rule();

        // Enabled
        $enabled = $this->is_enabled();
        
        // Register in the requirements list
        if ($enabled) {
            // get thumbnail_id
            $this->get_thumbnail_id($post);

            // get thumbnail_alt
            $this->get_thumbnail_alt($post);

            $requirements[$this->name] = [
                'status'    => $this->get_current_status($post, $enabled),
                'label'     => $this->lang['label'],
                'value'     => $enabled,
                'rule'      => $rule,
                'type'      => $this->type,
                'is_custom' => false,
                'attribute' => ['thumbnail_id' => $this->thumbnail_id, 'alt' => $this->thumbnail_alt],
            ];
        }

        return $requirements;
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
        /**
         * check if new post
         * new post will have no thumbnail in initialize page
         */
        if($this->thumbnail_id === 0) return true;

        return !empty($this->thumbnail_alt);
    }
}
