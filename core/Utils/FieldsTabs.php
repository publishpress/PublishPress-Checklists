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
            "approval" => array(
                "label" => "Approval",
                "icon" => "dashicons dashicons-yes"
            ),
            "images" => array(
                "label" => "Images",
                "icon" => "dashicons dashicons-format-image"
            ),
            "featured_image" => array(
                "label" => "Featured Image",
                "icon" => "dashicons dashicons-cover-image"
            ),
            "links" => array(
                "label" => "Links",
                "icon" => "dashicons dashicons-admin-links"
            ),
            "permalinks" => array(
                "label" => "Permalink",
                "icon" => "dashicons dashicons-editor-unlink"
            ),
            "categories" => array(
                "label" => "Categories",
                "icon" => "dashicons dashicons-category"
            ),
            "tags" => array(
                "label" => "Tags",
                "icon" => "dashicons dashicons-tag"
            ),
            "taxonomies" => array(
                "label" => "Taxonomies",
                "icon" => "dashicons dashicons-list-view"
            ),
            "yoastseo" => array(
                "label" => "Yoast SEO",
                "icon" => "pp-checklists-tab-custom-icon",
                "svg" => '<svg role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 466 500"><g fill="#655997"><path d="M80.13 444.1a73.98 73.98 0 0 1-7.17-1.86c-2.32-.73-4.63-1.58-6.88-2.53-1.11-.47-2.22-.98-3.32-1.51-1.63-.79-3.34-1.71-5.22-2.81-1.64-.96-2.97-1.79-4.19-2.62a65.68 65.68 0 0 1-2.94-2.1 79.203 79.203 0 0 1-5.56-4.59c-3.32-3.02-6.42-6.41-9.22-10.05-1-1.31-1.84-2.47-2.57-3.55-1.35-2-2.62-4.08-3.77-6.18a74.774 74.774 0 0 1-9.08-35.67V155.75c0-12.43 3.14-24.76 9.08-35.67 1.15-2.11 2.42-4.19 3.77-6.18a75.902 75.902 0 0 1 26.47-24.06 74.378 74.378 0 0 1 35.66-9.08h185.93l7.22-20.06H95.19C42.78 60.69.13 103.34.13 155.75v214.88c0 52.42 42.64 95.06 95.06 95.06h12.41v-20.06H95.19c-5.07 0-10.13-.52-15.06-1.53ZM404.01 66.68l-1.55-.58-7.02 18.83 1.54.58c3.29 1.24 6.49 2.7 9.5 4.34a75.902 75.902 0 0 1 26.47 24.06c1.35 1.99 2.62 4.07 3.77 6.18a74.803 74.803 0 0 1 9.08 35.67v289.88H256.06l-.48.83c-3.36 5.88-6.86 11.48-10.41 16.65l-1.77 2.59h222.46V155.75c0-39.44-24.86-75.24-61.86-89.07Z"/></g><path fill="#655997" d="M332.89 0 226.81 294.64l-52.14-163.3h-63.75l79.68 204.68c7.4 19 7.39 39.91 0 58.89-7.72 19.81-21.48 43.45-59.57 50.45l-1.71.31V500l2.17-.08c31.83-1.25 56.51-11.75 77.69-33.03 21.54-21.65 40.01-55.32 58.13-105.93L400.91 2.82 401.96 0h-69.07Z"/></svg>'
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