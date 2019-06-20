<?php
/**
 * @package     PublishPress\Content_checklist
 * @author      PublishPress <help@publishpress.com>
 * @copyright   Copyright (C) 2018 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Addon\Content_checklist;

/**
 * Class Utils
 *
 * @package PublishPress\Addon\Content_checklist
 */
class Utils
{
    /**
     * @param string $template
     * @param bool   $return
     *
     * @return string|void
     */
    public static function load_template($template, $return = false)
    {
        if ($return) {
            ob_start();
        }

        if ($overridden_template = locate_template($template)) {
            load_template($overridden_template);
        } else {
            load_template(PP_CONTENT_CHECKLIST_PATH_BASE . '/templates/' . $template);
        }

        if ($return) {
            return ob_get_clean();
        }
    }
}
