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

class Pro_ImageCount extends Base_counter
{

    /**
     * The name of the requirement, in a slug format
     *
     * @var string
     */
    public $name = 'image_count';

    /**
     * The name of the group, used for the tabs
     * 
     * @var string
     */
    public $group = 'images';

    /**
     * Pro requirement
     *
     * @var bool
     */
    public $pro = true;

    /**
     * @var int
     */
    public $position = 101;

    /**
     * Initialize the language strings for the instance
     *
     * @return void
     */
    public function init_language()
    {
        $this->lang['label_settings']       = __('Number of images in content', 'publishpress-checklists');
    }

   
}
