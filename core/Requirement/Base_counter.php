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

class Base_counter extends Base_simple implements Interface_required
{
    /**
     * The default value for the option related to the value.
     */
    const DEFAULT_OPTION_VALUE = 0;

    /**
     * @var string
     */
    protected $type = 'counter';

    /**
     * @var string
     */
    protected $extra = '';

    /**
     * @var string
     */
    protected $unitText;

    /**
     * Injects the respective default options into the main add-on.
     *
     * @param array $default_options
     *
     * @return array
     */
    public function filter_default_options($default_options)
    {
        $default_options = parent::filter_default_options($default_options);

        $options = [
            "{$this->name}_value" => [
                $this->post_type => static::DEFAULT_OPTION_VALUE,
            ],
        ];

        return array_merge($default_options, $options);
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

        $index = $this->name . '_value';
        if (isset($new_options[$index][$this->post_type])) {
            $new_options[$index][$this->post_type] = filter_var(
                $new_options[$index][$this->post_type],
                FILTER_SANITIZE_NUMBER_INT
            );
        }

        // Make sure we don't have 0 as value if enabled
        if (empty($new_options[$index][$this->post_type]) && static::VALUE_YES === $new_options[$this->name][$this->post_type]) {
            $new_options[$index][$this->post_type] = 1;
        }

        return $new_options;
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

        $option_name = $this->name;
        $options     = $this->module->options;

        // The enabled status
        $enabled = $this->is_enabled();

        // If not enabled, bypass the method
        if (!$enabled) {
            return $requirements;
        }

        // Legacy option. Only the "min" option were available
        $legacy_option_name = 'min_' . $this->name . '_value';

        // Option names
        $option_name_min  = $this->name . '_min';
        $option_name_max  = $this->name . '_max';
        $option_name_rule = $this->name . '_rule';

        // Get the min value
        $min_value = 0;
        if (isset($this->module->options->{$option_name_min}[$this->post_type])) {
            $min_value = (int)$this->module->options->{$option_name_min}[$this->post_type];
        }
        // If not set, we try the legacy option. At that time, we only had min values.
        if ('' === $min_value) {
            if (isset($this->module->options->{$legacy_option_name}[$this->post_type])) {
                $min_value = (int)$this->module->options->{$legacy_option_name}[$this->post_type];
            }
        }

        // Get the max value
        $max_value = 0;
        if (isset($this->module->options->{$option_name_max}[$this->post_type])) {
            $max_value = (int)$this->module->options->{$option_name_max}[$this->post_type];
        }

        // Check if both values are empty, to skip
        if (empty($min_value) && empty($max_value)) {
            return $requirements;
        }


        $label = '';

        // Both same value = exact
        if ($min_value == $max_value) {
            $label = sprintf(
                _n(
                    $this->lang['label_exact_singular'],
                    $this->lang['label_exact_plural'],
                    $min_value,
                    'publishpress-checklists'
                ),
                $min_value
            );
        }

        // Min not empty, max empty or < min = only min
        if (!empty($min_value) && ($max_value < $min_value)) {
            $label = sprintf(
                _n(
                    $this->lang['label_min_singular'],
                    $this->lang['label_min_plural'],
                    $min_value,
                    'publishpress-checklists'
                ),
                $min_value
            );
        }

        // Min not empty, max not empty and > min = both min and max
        if (!empty($min_value) && ($max_value > $min_value)) {
            $label = sprintf(
                __($this->lang['label_between'], 'publishpress-checklists'),
                $min_value,
                $max_value
            );
        }

        // Min empty, max not empty and > min = only max
        if (empty($min_value) && ($max_value > $min_value)) {
            $label = sprintf(
                _n(
                    $this->lang['label_max_singular'],
                    $this->lang['label_max_plural'],
                    $max_value,
                    'publishpress-checklists'
                ),
                $max_value
            );
        }


        // The rule
        $rule = $this->get_option_rule();

        // Register in the requirements list
        $requirements[$this->name] = [
            'status'    => $this->get_current_status($post, [$min_value, $max_value]),
            'label'     => $label,
            'value'     => [$min_value, $max_value],
            'rule'      => $rule,
            'type'      => $this->type,
            'is_custom' => false,
            'extra'     => $this->extra,
        ];

        return $requirements;
    }

    protected function setUnitText($unit)
    {
        $this->unitText = $unit;
    }

    /**
     * Get the HTML for the setting field for the specific post type.
     *
     * @return string
     */
    public function get_setting_field_html($css_class = '')
    {
        $html = parent::get_setting_field_html($css_class);

        $post_type = esc_attr($this->post_type);
        $css_class = esc_attr($css_class);

        // Legacy option. Only the "min" option were available
        $legacy_option_name = 'min_' . $this->name . '_value';

        // Option names
        $option_name_min = $this->name . '_min';
        $option_name_max = $this->name . '_max';


        // Get the min value
        $min_value = '';
        if (isset($this->module->options->{$option_name_min}[$post_type])) {
            $min_value = (int)$this->module->options->{$option_name_min}[$post_type];
        }
        // If not set, we try the legacy option. At that time, we only had min values.
        if ('' === $min_value) {
            if (isset($this->module->options->{$legacy_option_name}[$post_type])) {
                $min_value = (int)$this->module->options->{$legacy_option_name}[$post_type];
            }
        }

        // Get the max value
        $max_value = '';
        if (isset($this->module->options->{$option_name_max}[$post_type])) {
            $max_value = $this->module->options->{$option_name_max}[$post_type];
            $max_value = (int)$max_value;
        }

        // Make sure to do not display a 0 number
        if (empty($min_value)) {
            $min_value = '';
        }

        if (empty($max_value)) {
            $max_value = '';
        }

        // Make sure to do not display max_value, if less than min_value
        if ($max_value < $min_value) {
            $max_value = '';
        }

        // Get the field markup for min value
        $min_field = sprintf(
            '<input type="text" " id="%s" name="%s" value="%s" class="pp-checklists-small-input pp-checklists-number" />',
            "{$post_type}-{$this->module->slug}-{$option_name_min}",
            "{$this->module->options_group_name}[{$option_name_min}][{$post_type}]",
            $min_value
        );

        // Get the field markup for max value
        $max_field = sprintf(
            '<input type="text" " id="%s" name="%s" value="%s" class="pp-checklists-small-input pp-checklists-number" />',
            "{$post_type}-{$this->module->slug}-{$option_name_max}",
            "{$this->module->options_group_name}[{$option_name_max}][{$post_type}]",
            $max_value
        );


        $html .= '<div class="pp-checklists-number">';
        $html .= '<label>'. esc_html__('Min', 'publishpress-checklists') .'</label>' . $min_field;

        if (!empty($this->unitText)) {
            $html .= '<span class="pp-checklists-unit">' . $this->unitText . '</span>';
        }

        $html .= '</div>';
        $html .= '<div class="pp-checklists-number">';
        $html .= '<label>'. esc_html__('Max', 'publishpress-checklists') .'</label>' . $max_field;

        if (!empty($this->unitText)) {
            $html .= '<span class="pp-checklists-unit">' . $this->unitText . '</span>';
        }

        $html .= '</div>';

        return $html;
    }
}
