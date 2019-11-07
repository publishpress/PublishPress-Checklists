<?php
/**
 * @package     PublishPress\Checklists
 * @author      PublishPress <help@publishpress.com>
 * @copyright   copyright (C) 2019 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.4.8
 */

namespace PublishPress\Checklists\Core;


class TemplateLoader
{
    /**
     * Load template for modules.
     *
     * @param       $moduleName
     * @param       $templateName
     * @param bool  $requireOnce
     * @param array $context
     *
     * @param bool  $return
     *
     * @return false|string
     */
    public function load($moduleName, $templateName, $context = [], $return = false, $requireOnce = true)
    {
        if ($return) {
            ob_start();
        }

        $templatePath = $this->locate($moduleName, $templateName);

        if ( ! empty($templatePath)) {
            if ($requireOnce) {
                require_once $templatePath;
            } else {
                require $templatePath;
            }
        }

        if ($return) {
            return ob_get_clean();
        }
    }

    /**
     * Locate template for modules.
     *
     * @param $moduleName
     * @param $templateName
     *
     * @return string
     */
    public function locate($moduleName, $templateName)
    {
        $located = '';

        $paths = [
            STYLESHEETPATH . '/' . PUBLISHPRESS_CHECKLISTS_RELATIVE_PATH . '/' . $moduleName,
            TEMPLATEPATH . '/' . PUBLISHPRESS_CHECKLISTS_RELATIVE_PATH . '/' . $moduleName,
            ABSPATH . WPINC . '/theme-compat/' . PUBLISHPRESS_CHECKLISTS_RELATIVE_PATH . '/' . $moduleName,
            PUBLISHPRESS_CHECKLISTS_MODULES_PATH . '/' . $moduleName . '/templates',
        ];

        $paths = apply_filters('publishpress_checklists_template_paths', $paths);

        foreach ($paths as $path) {
            if (file_exists($path . '/' . $templateName)) {
                $located = $path . '/' . $templateName;
                break;
            }
        }

        return $located;
    }
}
