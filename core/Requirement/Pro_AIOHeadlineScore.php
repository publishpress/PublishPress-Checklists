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

/**
 * Class AIOHeadlineScore
 * 
 * Implements a requirement to check the All in One SEO Headline Analyzer score for a post
 * Note: This only works in the Block Editor (Gutenberg)
 */
class Pro_AIOHeadlineScore extends Base_counter
{
    /**
     * The name of the requirement, in a slug format
     *
     * @var string
     */
    public $name = 'all_in_one_seo_headline_score';

    /**
     * The name of the group, used for the tabs
     * 
     * @var string
     */
    public $group = 'all_in_one_seo';

    /**
     * Pro requirement
     *
     * @var bool
     */
    public $pro = true;

    /**
     * @var int
     */
     public $position = 150;

    /**
     * Initialize the language strings for the instance
     *
     * @return void
     */
    public function init_language()
    {
        $this->lang['label_settings']       = __('All in One SEO Headline Score', 'publishpress-checklists');
    }

    
}
