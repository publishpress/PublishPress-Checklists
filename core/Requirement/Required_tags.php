<?php
/**
 * @package     PublishPress\Checklists
 * @author      PublishPress <help@publishpress.com>
 * @copyright   copyright (C) 2019 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Checklists\Core\Requirement;

use PPCH_Checklists;

defined('ABSPATH') or die('No direct script access allowed.');

class Required_tags extends Base_multiple
{
    /**
     * The name of the requirement, in a slug format
     *
     * @var string
     */
    public $name = 'required_tags';

    /**
     * @var int
     */
    public $position = 70;

    /**
     * @var string
     */
    private $DELIMITER = '__';

    /**
     * Initialize the language strings for the instance
     *
     * @return void
     */
    public function init_language()
    {
        $this->lang['label']          = __('Required tags: %s', 'publishpress-checklists');
        $this->lang['label_settings'] = __('Required tags', 'publishpress-checklists');
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
        $tags = wp_get_post_tags($post->ID, array('fields' => 'ids'));
        $option_ids = $this->tag_parser($option_value, 0);

        return !empty(array_intersect($option_ids, $tags));
    }

    /**
     * Transform tags to labels
     * 
     * @param WP_Term[] $tags
     * @return String[] $labels
     */
    private function _transform_tags($tags = array())
    {
        $labels = [];

        foreach ($tags as $tag) {
            $labels[$tag->term_id . $this->DELIMITER . $tag->name] = $tag->name;
            if(isset($tag->children)) {
                foreach ($tag->children as $child) {
                    $labels[$child->term_id . $this->DELIMITER . $child->name] = "- {$child->name}";
                }
            }
        }

        return $labels;
    }
    
    /**
     * Gets settings drop down labels.
     *
     * @return array.
     */
    public function get_setting_drop_down_labels()
    {
        $tags = get_tags(array(
            'orderby'      => 'name',
            'order'        => 'ASC',
            'hide_empty'   => 0,
        ));

        return $this->_transform_tags($tags);
    }

    /**
     * Parse tags
     * This method for remapping
     * example: 1__Tag 1, 2__Tag 2, 3__Tag 3
     * result: [1, 2, 3] or ['Tag 1', 'Tag 2', 'Tag 3'] based on $index
     * 
     * @param String[] $tags
     * @param int $index
     * @return String[] $tags
     */
    private function tag_parser($tags = array(), $index = 0|1)
    {
        return array_map(function($value) use ($index) {
            return explode($this->DELIMITER, $value)[$index];
        }, $tags);
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
        // Check if it is a compatible post type. If not, ignore this requirement.
        if ($post->post_type !== $this->post_type) {
            return $requirements;
        }

        $requirements = parent::filter_requirements_list($requirements, $post);

        // If not enabled, bypass the method
        if (!$this->is_enabled()) {
            return $requirements;
        }

        // Option names
        $option_name_multiple = $this->name . '_' . $this->field_name;

        // Get the value
        $option_value = array();
        if (isset($this->module->options->{$option_name_multiple}[$this->post_type])) {
            $option_value = $this->module->options->{$option_name_multiple}[$this->post_type];
        }

        if (empty($option_value)) {
            return $requirements;
        }

        $post_tags = wp_get_post_tags($post->ID, array('fields' => 'ids'));
        $required_tags = array();
        foreach ($option_value as $tag_str) {
            [$tag_id, $tag_name] = explode($this->DELIMITER, $tag_str);
            if (!in_array($tag_id, $post_tags)) {
                $required_tags[] = $tag_name;
            }
        }

        $required_tag_names = implode(', ', $required_tags);

        if(empty($required_tag_names)) {
            return $requirements;
        }

        // Register in the requirements list
        $requirements[$this->name]['label'] = sprintf($this->lang['label'], $required_tag_names);

        return $requirements;
    }
}
