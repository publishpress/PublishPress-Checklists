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

class Pro_PublishTimeExact extends Base_time
{
    /**
     * The name of the requirement, in a slug format
     *
     * @var string
     */
    public $name = 'publish_time_exact';

    /**
     * The name of the group, used for the tabs
     *
     * @var string
     */
    public $group = 'publish_date_time';

    /**
     * Pro requirement
     *
     * @var bool
     */
    public $pro = true;

    /**
     * Position/order in the checklist group
     *
     * @var int
     */
    public $position = 161;

    /**
     * Initialize the language strings for the instance
     *
     * @return void
     */
    public function init_language()
    {
        
        $this->lang['label_settings'] = __('Publish time should be at a specific time', 'publishpress-checklists');
    }

    
}
