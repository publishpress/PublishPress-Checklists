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

class Required_categories extends Base_multiple
{
    /**
     * The name of the requirement, in a slug format
     *
     * @var string
     */
    public $name = 'required_categories';

    /**
     * @var int
     */
    public $position = 40;

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
        $this->lang['label']          = __('Required categories: %s', 'publishpress-checklists');
        $this->lang['label_settings'] = __('Required categories', 'publishpress-checklists');
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
        $categories = wp_get_post_categories($post->ID);
        $option_ids = $this->category_parser($option_value, 0);

        return !empty(array_intersect($option_ids, $categories));
    }

    /**
     * Get Categories Hierarchical List
     * 
     * @param array $args
     * @return WP_Term[]
     */
    private function get_categories_hierarchical($args = array())
    {
        if( !isset( $args[ 'parent' ] ) ) $args[ 'parent' ] = 0;

        $categories = get_categories( $args );
        foreach( $categories as $key => $category ) {
            $args['parent'] = $category->term_id;
            $categories[$key]->children = $this->get_categories_hierarchical($args);
        }

        return $categories;
    }

    /**
     * Transform categories to labels
     * 
     * @param WP_Term[] $categories
     * @return String[] $labels
     */
    private function transform_categories($categories = array())
    {
        $labels = [];

        foreach ($categories as $cat => $category) {
            $labels[$category->term_id . $this->DELIMITER . $category->name] = $category->name;
            if(isset($category->children)) {
                foreach ($category->children as $child) {
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
        $categories = $this->get_categories_hierarchical(array(
            'orderby'      => 'name',
            'order'        => 'ASC',
            'hide_empty'   => 0,
        ));

        return $this->transform_categories($categories);
    }

    /**
     * Parse categories
     * This method for remapping
     * example: 1__Category 1, 2__Category 2, 3__Category 3
     * result: [1, 2, 3] or ['Category 1', 'Category 2', 'Category 3'] based on $index
     * 
     * @param String[] $categories
     * @param int $index
     * @return String[] $categories
     */
    private function category_parser($categories = array(), $index = 0|1)
    {
        return array_map(function($value) use ($index) {
            return explode($this->DELIMITER, $value)[$index];
        }, $categories);
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

        $post_categories = wp_get_post_categories($post->ID);
        $required_categories = array();
        foreach ($option_value as $category_str) {
            [$category_id, $category_name] = explode($this->DELIMITER, $category_str);
            if (!in_array($category_id, $post_categories)) {
                $required_categories[] = $category_name;
            }
        }

        $required_category_names = implode(', ', $required_categories);

        if(empty($required_category_names)) {
            return $requirements;
        }

        // Register in the requirements list
        $requirements[$this->name]['label'] = sprintf($this->lang['label'], $required_category_names);

        return $requirements;
    }
}
