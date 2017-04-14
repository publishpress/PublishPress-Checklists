<?php
/**
 * @package PublishPress
 * @author PressShack
 *
 * Copyright (c) 2017 PressShack
 *
 * ------------------------------------------------------------------------------
 * Based on Edit Flow
 * Author: Daniel Bachhuber, Scott Bressler, Mohammad Jangda, Automattic, and
 * others
 * Copyright (c) 2009-2016 Mohammad Jangda, Daniel Bachhuber, et al.
 * ------------------------------------------------------------------------------
 *
 * This file is part of PublishPress
 *
 * PublishPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PublishPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PublishPress.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!class_exists('PP_Checklist')) {
    /**
     * class PP_Checklist
     */
    class PP_Checklist extends PP_Module
    {
        public $module_name = 'checklist';

        /**
         * Construct the PP_Checklist class
         */
        public function __construct()
        {
            $this->twigPath = dirname(dirname(dirname(__FILE__))) . '/twig';

            $this->module_url = $this->get_module_url(__FILE__);

            // Register the module with PublishPress
            $args = array(
                'title'                => __('Checklist', 'publishpress'),
                'short_description'    => __('With Checklist...', 'publishpress'),
                'extended_description' => __('Checklist extended description...', 'publishpress'),
                'module_url'           => $this->module_url,
                'icon_class'           => 'dashicons dashicons-feedback',
                'slug'                 => 'checklist',
                'default_options'      => array(
                    'enabled'    => 'on',
                    'post_types' => array(
                        'post' => 'on',
                        'page' => 'off',
                    ),
                ),
                'configure_page_cb' => 'print_configure_view',
                'options_page'       => true,
            );
            PublishPress()->register_module($this->module_name, $args);

            parent::__construct();
        }

        /**
         * Initialize the module. Conditionally loads if the module is enabled
         */
        public function init()
        {

        }

        /**
         * Load default editorial metadata the first time the module is loaded
         *
         * @since 0.7
         */
        public function install()
        {
            
        }

        /**
         * Upgrade our data in case we need to
         *
         * @since 0.7
         */
        public function upgrade($previous_version)
        {
            
        }

        /**
         * Print the content of the configure tab.
         */
        public function print_configure_view()
        {
            echo $this->twig->render(
                'tab.twig',
                array(
                )
            );
        }
    }
}
