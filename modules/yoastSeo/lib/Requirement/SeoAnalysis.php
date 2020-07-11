<?php
/**
 * @package     PublishPress\Checklists
 * @author      PublishPress <help@publishpress.com>
 * @copyright   Copyright (C) 2018 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Checklists\Yoastseo\Requirement;


use PublishPress\Checklists\Core\Requirement\Base_simple;
use stdClass;

class SeoAnalysis extends Base_simple
{
    /**
     * The name of the requirement, in a slug format
     *
     * @var string
     */
    public $name = 'yoast_seo_analysis';

    /**
     * Initialize the language strings for the instance
     *
     * @return void
     */
    public function init_language()
    {
        $this->lang['label_settings'] = esc_html(__('Yoast Seo Analysis Pass', 'publishpress-checklists'));
    }

    /**
     * Validates the option group, making sure the values are sanitized.
     *
     * @param array $new_options
     *
     * @return array
     */
    public function filter_settings_validate($new_options)
    {
        $new_options = parent::filter_settings_validate($new_options);

        return $new_options;
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

        $value = $this->get_option($this->name);

        // Register in the requirements list
        $requirements[$this->name] = [
            'status' => $this->get_current_status($post, $value),
            'label' => $this->lang['label_settings'],
            'value' => $value,
            'rule' => $this->get_option_rule(),
            'is_custom' => false,
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
     * Returns the current status of the requirement.
     *
     * @param stdClass $post
     * @param mixed $option_value
     *
     * @return mixed
     */
    public function get_current_status($post, $option_value)
    {
        $score = (int)get_post_meta($post->ID, '_yoast_wpseo_linkdex', true);

        return $score >= '71';
    }
}
