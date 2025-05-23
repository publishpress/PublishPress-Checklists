<?php
/**
 * @package     PublishPress\\WooCommerce
 * @author      PublishPress <help@publishpress.com>
 * @copyright   Copyright (C) 2018 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Checklists\Core\Requirement;


class Pro_FeaturedImageWidth extends Base_counter
{
    /**
     * The priority for the action to load the requirement
     */
    const PRIORITY = 8;

    /**
     * The name of the requirement, in a slug format
     *
     * @var string
     */
    public $name = 'featured_image_width';

    /**
     * Pro requirement
     *
     * @var bool
     */
    public $pro = true;

    /**
     * The name of the group, used for the tabs
     * 
     * @var string
     */
    public $group = 'featured_image';

    /**
     * @var int
     */
    public $position = 109;

    /**
     * @var int
     */
    protected $thumbDataIndexForDimension = 2;

    /**
     * Initialize the language strings for the instance
     *
     * @return void
     */
    public function init_language()
    {
        $this->lang['label_settings']       = __('Featured image width', 'publishpress-checklists');
    }
}
