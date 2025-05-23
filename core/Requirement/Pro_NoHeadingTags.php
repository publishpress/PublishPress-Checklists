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

class Pro_NoHeadingTags extends Base_multiple
{
    /**
     * The name of the requirement, in a slug format
     *
     * @var string
     */
    public $name = 'no_heading_tags';

    /**
     * The name of the group, used for the tabs
     *
     * @var string
     */
    public $group = 'content';

    /**
     * Pro requirement
     *
     * @var bool
     */
    public $pro = true;

    /**
     * @var int
     */
    public $position = 108;

    /**
     * Initialize the language strings for the instance
     *
     * @return void
     */
    public function init_language()
    {
        $this->lang['label_settings']         = __('Avoid heading tags in content', 'publishpress-checklists');
    }

    /**
     * Get the list of options to display in the settings field
     *
     * @return array
     */
    protected function get_setting_drop_down_labels()
    {
        return [
            'h1' => __('H1', 'publishpress-checklists'),
            'h2' => __('H2', 'publishpress-checklists'),
            'h3' => __('H3', 'publishpress-checklists'),
            'h4' => __('H4', 'publishpress-checklists'),
            'h5' => __('H5', 'publishpress-checklists'),
            'h6' => __('H6', 'publishpress-checklists'),
        ];
    }
}
