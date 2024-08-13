<?php
/**
 * @package     PublishPress\Checklists
 * @author      PublishPress <help@publishpress.com>
 * @copyright   copyright (C) 2019 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Checklists\Core\Utils;

class FieldsTabs {
    private static $instance = null;
    private $fields_tabs;

    private function __construct() {
        $this->fields_tabs = array(
            "title" => array(
                "label" => "Title",
                "icon" => "dashicons dashicons-edit"
            ),
            "content" => array(
                "label" => "Content",
                "icon" => "dashicons dashicons-welcome-write-blog"
            ),
            "images" => array(
                "label" => "Images",
                "icon" => "dashicons dashicons-format-image"
            ),
            "links" => array(
                "label" => "Links",
                "icon" => "dashicons dashicons-admin-links"
            ),
            "categories" => array(
                "label" => "Categories",
                "icon" => "dashicons dashicons-category"
            ),
            "tags" => array(
                "label" => "Tags",
                "icon" => "dashicons dashicons-tag"
            ),
            "custom" => array(
                "label" => "Custom",
                "icon" => "dashicons dashicons-admin-generic"
            )
        );
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new FieldsTabs();
        }
        return self::$instance;
    }

    public function getFieldsTabs() {
        return $this->fields_tabs;
    }

    public function addTab($key, $label, $icon, $position = null) {
        $new_tab = array(
            "label" => $label,
            "icon" => $icon
        );

        if ($position === null || $position >= count($this->fields_tabs)) {
            $this->fields_tabs[$key] = $new_tab;
        } else {
            $this->fields_tabs = array_slice($this->fields_tabs, 0, $position, true) +
                                 array($key => $new_tab) +
                                 array_slice($this->fields_tabs, $position, null, true);
        }
    }

    public function removeTab($key) {
        if (isset($this->fields_tabs[$key])) {
            unset($this->fields_tabs[$key]);
        }
    }
}