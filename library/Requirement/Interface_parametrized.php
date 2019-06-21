<?php
/**
 * @package     PublishPress\Content_checklist
 * @author      PublishPress <help@publishpress.com>
 * @copyright   Copyright (C) 2018 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Addon\Content_checklist\Requirement;

defined('ABSPATH') or die('No direct script access allowed.');

interface Interface_parametrized
{
    /**
     * Set the parameters
     *
     * @param array $params
     *
     * @return array
     */
    public function set_params($params);
}
