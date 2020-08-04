<?php
/**
 * @package     PublishPress\Checklists
 * @author      PublishPress <help@publishpress.com>
 * @copyright   Copyright (C) 2018 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Checklists\Customs\Requirement;

use PublishPress\Checklists\Core\Requirement\Base_multiple;
use stdClass;
use PPCH_Checklists;

class Role_Approval extends Base_multiple
{
    const VALUE_YES = 'yes';

    /**
     * The name of the requirement, in a slug format
     *
     * @var string
     */
    public $name = 'customs_role_approval';

    /**
     * Initialize the language strings for the instance
     *
     * @return void
     */
    public function init_language()
    {
        $this->lang['label_settings'] = esc_html(__('Role approval', 'publishpress-checklists'));
    }

    /**
     * Add the requirement to the list to be displayed in the metabox.
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

        if (!$this->is_enabled()) {
            return $requirements;
        }

        // Option names
        $option_name_multiple = $this->name . '_multiple';

        // Check value is empty, to skip
        if (empty($option_name_multiple)) {
            return $requirements;
        }

        // Get the value
        $value = $this->get_option($option_name_multiple);

        //set custom to false if user is not permitted to prevent any validation
        if ($this->isUserPermitted()) {
            $is_custom = true;
        } else {
            $is_custom = false;
        }

        //set unique requirement id per post
        $requirment_id = $this->name . '_' . $post->ID;

        // Register in the requirements list
        $requirements[$this->name] = [
            'status' => $this->get_current_status($post, $value),
            'label' => $this->get_requirement_drop_down_label($post->ID),
            'value' => $value,
            'rule' => $this->get_option_rule(),
            'id' => $requirment_id,
            'is_custom' => $is_custom,
            'type' => $this->type,
        ];

        return $requirements;
    }

    /**
     * Returns the value of the given option. The option name should
     * be in the short form, without the name of the requirement as
     * the prefix.
     *
     * @param string $option_name
     *
     * @return mixed
     */
    public function get_option($option_name)
    {
        $options = $this->module->options;

        if (isset($options->{$option_name}) && isset($options->{$option_name}[$this->post_type])) {
            return $options->{$option_name}[$this->post_type];
        }

        return null;
    }

    /**
     * Check if user is permitted to approve post
     */
    private function isUserPermitted()
    {

        // Option name
        $option_name_multiple = $this->name . '_multiple';

        //Saved value
        $option_value = isset($this->module->options->{$option_name_multiple}[$this->post_type]) ? $this->module->options->{$option_name_multiple}[$this->post_type] : array();

        return array_intersect($option_value, wp_get_current_user()->roles);
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
        return self::VALUE_YES === get_post_meta($post->ID, PPCH_Checklists::POST_META_PREFIX . $this->name . '_' . $post->ID, true);
    }

    /**
     * Gets the requirement drop down label.
     * @param integer $post_id
     *
     * @return string
     */
    public function get_requirement_drop_down_label($post_id)
    {
        //Option name
        $option_name_multiple = $this->name . '_multiple';

        //Saved value
        $option_value = isset($this->module->options->{$option_name_multiple}[$this->post_type]) ? $this->module->options->{$option_name_multiple}[$this->post_type] : array();

        //Permitted role list
        $permitted_role = $this->arrayToSentence($option_value);

        if ($this->isUserPermitted()) {
            $label = esc_html(__('Approve post', 'publishpress-checklists'));
        } elseif (self::VALUE_YES === get_post_meta($post_id, PPCH_Checklists::POST_META_PREFIX . $this->name . '_' . $post_id, true)) {
            $label = esc_html(__('Approved', 'publishpress-checklists'));
        } else {
            $label = esc_html(sprintf(__('Sorry, this post needs the approval of a user in role %s', 'publishpress-checklists'), $permitted_role));
        }

        return $label;
    }

    /**
     * Form readable sentence from array list
     * @param array $array
     *
     * @return string
     */
    private function arrayToSentence($array, $string = '')
    {
        if (is_array($array)) {
            $items = array();

            foreach ($array as $item) {
                $items[] = $this->get_setting_drop_down_labels()[$item];
            }

            $last = array_pop($items);

            $string = count($items) ? implode(", ", $items) . " " . __('or', 'publishpress-checklists') . " " . $last : $last;
        }

        return $string;
    }

    /**
     * Gets settings drop down labels.
     *
     * @return array.
     */
    public function get_setting_drop_down_labels()
    {
        $labels = [];

        $userRoles = get_editable_roles();

        foreach ($userRoles as $slug => $role) {
            $labels[$slug] = $role['name'];
        }

        return $labels;
    }
}
