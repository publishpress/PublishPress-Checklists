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

class Pro_ApprovedByUser extends Base_multiple implements Interface_required
{
    /**
     * The name of the requirement, in a slug format
     *
     * @var string
     */
    public $name = 'approved_by_user';

    /**
     * The name of the group, used for the tabs
     * 
     * @var string
     */
    public $group = 'approval';

    /**
     * Pro requirement
     *
     * @var bool
     */
    public $pro = true;

    protected $field_name = 'users';

    const POST_META_PREFIX = 'pp_checklist_custom_item';

    /**
     * @var int
     */
    public $position = 171;

    /**
     * Initialize the language strings for the instance
     *
     * @return void
     */
    public function init_language()
    {
        $this->lang['label_settings'] = __('Approved by specific user', 'publishpress-checklists');
    }

    /**
     * Returns the current status of the requirement.
     *
     * @param \stdClass $post
     * @param mixed $option_value
     *
     * @return mixed
     */
    public function get_current_status($post, $option_value)
    {
        return self::VALUE_YES === get_post_meta($post->ID, static::POST_META_PREFIX . '_' . $this->name, true);
    }

    /**
     * Gets settings drop down labels.
     *
     * @return array.
     */
    public function get_setting_drop_down_labels()
    {
        // Get all users who can edit posts
        $users = get_users([
            'capability' => 'edit_posts',
            'orderby' => 'display_name',
            'order' => 'ASC'
        ]);

        $user_labels = [];
        foreach ($users as $user) {
            $user_labels[$user->ID] = $user->display_name;
        }

        return $user_labels;
    }

    
}
