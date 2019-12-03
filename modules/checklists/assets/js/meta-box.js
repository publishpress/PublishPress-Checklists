/**
 * @package PublishPress
 * @author PublishPress
 *
 * Copyright (C) 2018 PublishPress
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

(function ($, window, document, counter) {
    'use strict';

    /**
     * This variable is deprecated. Use ppChecklists instead.
     * Added here just for backward compatibility with other
     * plugins.
     *
     * @deprecated 1.4.0
     */
    window.objectL10n_checklist_requirements = ppChecklists;

    /*----------  Handler  ----------*/

    /**
     * Object for handling the requirements in the post form.
     * @type {Object}
     */
    var PP_Checklists = {
        /**
         * Constant for the event validate_requirements
         * @type {String}
         */
        EVENT_VALIDATE_REQUIREMENTS: 'pp-checklists:validate_requirements',

        /**
         * Constant for the event tick. Triggered by a setInterval
         * @type {String}
         */
        EVENT_TIC: 'pp-checklists:tic',

        /**
         * Constant for the event update_requirement_state
         * @type {String}
         */
        EVENT_UPDATE_REQUIREMENT_STATE: 'pp-checklists:update_requirement_state',

        /**
         * Constant for the event toggle_custom_item
         * @type {String}
         */
        EVENT_TOGGLE_CUSTOM_ITEM: 'pp-checklists:toggle_custom_item',

        /**
         * Constant for the event tinymce_loaded
         * @type {String}
         */
        EVENT_TINYMCE_LOADED: 'pp-checklists:tinymce_loaded',

        /**
         * Constant for the interval of the tic event
         * @type {Number}
         */
        TIC_INTERVAL: 300,

        /**
         * List of interface elements
         * @type {Object}
         */
        elems: {
            'original_post_status': $('#original_post_status'),
            'publish_button': $('#publish'),
            'document': $(document)
        },

        /**
         * Stores the states for the object.
         * @type {Object}
         */
        state: {
            /**
             * Flag for the publishing state
             * @type {Boolean}
             */
            is_publishing: false,

            /**
             * Flag for the confirmed state
             * @type {Boolean}
             */
            is_confirmed: false,

            /**
             * Flag for the should_block state
             * @type {Boolean}
             */
            should_block: false
        },

        /**
         * Initialize the object and events
         * @return {void}
         */
        init: function () {
            // Create a custom event
            this.elems.document.on(this.EVENT_VALIDATE_REQUIREMENTS, function (event) {
                this.validate_requirements(event);
            }.bind(this));

            // On clicking the submit button
            this.elems.publish_button.click(function () {
                this.state.is_publishing = true;
            }.bind(this));

            // On clicking the confirmation button in the modal window
            this.elems.document.on('confirmation', '.remodal', function () {
                this.state.is_confirmed = true;

                // Trigger the publish button
                this.elems.publish_button.trigger('click');
            }.bind(this));

            if (!PP_Checklists.is_gutenberg_active()) {
                // Hook to the submit button
                $('form#post').submit(function (event) {
                    // Reset the should_block state
                    this.state.should_block = false;

                    this.elems.document.trigger(this.EVENT_VALIDATE_REQUIREMENTS);

                    return !this.state.should_block;
                }.bind(this));
            }

            // Hook to the requirement items
            $('[id^=pp-checklists-req]').on(this.EVENT_UPDATE_REQUIREMENT_STATE, function (event, state) {
                this.update_requirement_icon(state, $(event.target));
            }.bind(this));

            // Add event to the custom items
            $('.pp-checklists-custom-item').click(function (event) {
                var target = event.target;

                if ('LI' !== target.nodeNAME) {
                    target = $(target).parent('li')[0];
                }

                if (typeof target !== 'undefined') {
                    this.elems.document.trigger(this.EVENT_TOGGLE_CUSTOM_ITEM, $(target));
                }
            }.bind(this));

            // On clicking the confimation button in the modal window
            this.elems.document.on(this.EVENT_TOGGLE_CUSTOM_ITEM, function (event, item) {
                var $item = $(item),
                    $icon = $item.children('.dashicons'),
                    checked = $icon.hasClass('dashicons-yes');

                if (checked) {
                    $icon
                        .removeClass('dashicons-yes')
                        .addClass('dashicons-no');

                    $item
                        .removeClass('status-yes')
                        .addClass('status-no');
                } else {
                    $icon
                        .removeClass('dashicons-no')
                        .addClass('dashicons-yes');

                    $item
                        .removeClass('status-no')
                        .addClass('status-yes');
                }

                $item.children('input[type="hidden"]').val($item.hasClass('status-yes') ? 'yes' : 'no');
            }.bind(this));

            // Start the tic event
            setInterval(function () {
                $(document).trigger(this.EVENT_TIC);
            }.bind(this), this.TIC_INTERVAL);
        },

        /**
         * Check if the current post is already published
         * @return {Boolean} True if published.
         */
        is_published: function () {
            return 'publish' === this.elems.original_post_status.val();
        },

        /**
         * Validates the requirements and show the warning, blocking or not the
         * submission, according to the config. Returns false if the submission
         * should be blocked.
         *
         * @param  {[type]} event
         * @return {Boolean}
         */
        validate_requirements: function (event) {
            this.state.should_block = false;

            // Bypass all checks because the confirmation button was clicked.
            if (this.state.is_confirmed) {
                this.state.is_confirmed = false;

                return;
            }

            // Check if the post is already published, to bypass the check
            if (this.is_published()) {
                return;
            }

            // Check if the publish button was pressed, without Gutenberg.
            if (!this.state.is_publishing && !PP_Checklists.is_gutenberg_active()) {
                return;
            }

            var list_unchecked = {
                'block': [],
                'warning': []
            };

            /**
             * Check the element of the requirement, to see if it is marked
             * as incomplete.
             *
             * @param  {Object} $req The DOM element
             * @param  {Array}  list The list to inject the requirement, if incomplete
             * @return {void}
             */
            var check_requirement = function ($req, list) {
                if ($req.hasClass('status-no')) {
                    // Check if the requirement is not ok
                    var $unchecked_req = $req.find('.status-label');

                    if ($unchecked_req.length > 0) {
                        list.push($unchecked_req.html().trim());
                    }
                }
            }.bind(this);

            var check_requirement_action = function (action_type) {
                var $elems = $('.pp-checklists-req.' + action_type),
                    $unchecked_label,
                    $req;

                for (var i = 0; i < $elems.length; i++) {
                    check_requirement($($elems[i]), list_unchecked[action_type]);
                }
            }.bind(this);

            // Check if any of the requirements is set to trigger warnings
            check_requirement_action('warning');
            check_requirement_action('block');

            // Check if we have warnings to display
            if (list_unchecked.warning.length > 0 || list_unchecked.block.length > 0) {
                var message = '';

                // Check if we don't have any unchecked block req
                if (0 === list_unchecked.block.length) {
                    if (!PP_Checklists.is_gutenberg_active()) {
                        // Only display a warning
                        message = ppChecklists.msg_missed_optional + '<div class="pp-checklists-modal-list"><ul><li>' + list_unchecked.warning.join('</li><li>') + '</li></ul></div>';

                        // Display the confirm
                        $('#pp-checklists-modal-confirm-content').html(message);
                        $('[data-remodal-id=pp-checklists-modal-confirm]').remodal().open();
                    } else {
                        wp.data.dispatch('core/editor').unlockPostSaving('pp-checklists');
                    }
                } else {
                    if (PP_Checklists.is_gutenberg_active()) {
                        wp.data.dispatch('core/editor').lockPostSaving('pp-checklists');
                    } else {
                        message = ppChecklists.msg_missed_required + '<div class="pp-checklists-modal-list"><ul><li>' + list_unchecked.block.join('</li><li>') + '</li></ul></div>';

                        if (list_unchecked.warning.length > 0) {
                            message += '' + ppChecklists.msg_missed_important + '<div class="pp-checklists-modal-list"><ul><li>' + list_unchecked.warning.join('</li><li>') + '</li></ul></div>';
                        }

                        // Display the alert
                        $('#pp-checklists-modal-alert-content').html(message);
                        $('[data-remodal-id=pp-checklists-modal-alert]').remodal().open();
                    }
                }

                this.state.should_block = true;
            } else {
                if (PP_Checklists.is_gutenberg_active()) {
                    wp.data.dispatch('core/editor').unlockPostSaving('pp-checklists');
                }
            }

            wp.hooks.doAction('pp-checklists.update-failed-requirements', list_unchecked.warning.concat(list_unchecked.block));

            this.state.is_publishing = false;
        },

        /**
         * Updates the icon in the requirement checklist according to the
         * current state.
         *
         * @param  {Boolean} is_completed
         * @param  {Object}  $element
         * @return {void}
         */
        update_requirement_icon: function (is_completed, $element) {
            var $icon_element = $element.find('.dashicons');

            if (is_completed) {
                // Ok
                $icon_element.removeClass('dashicons-no');
                $icon_element.addClass('dashicons-yes');
                $icon_element.parent().removeClass('status-no');
                $icon_element.parent().addClass('status-yes');
            } else {
                // Not ok
                $icon_element.removeClass('dashicons-yes');
                $icon_element.addClass('dashicons-no');
                $icon_element.parent().removeClass('status-yes');
                $icon_element.parent().addClass('status-no');
            }
        },

        /**
         * Check if the value is valid, based on the min and max values.
         * It makes a smarty check based on the following:
         *
         *  - Both same value = exact
         *  - Min not empty, max empty or < min = only min
         *  - Min not empty, max not empty and > min = both min and max
         *  - Min empty, max not empty and > min = only max
         *
         * @param  {Float} count
         * @param  {Float} min_value
         * @param  {Float} max_value
         *
         * @return {Bool}
         */
        check_valid_quantity: function (count, min_value, max_value) {
            var is_valid = false;

            // Both same value = exact
            if (min_value === max_value) {
                is_valid = count === min_value;
            }

            // Min not empty, max empty or < min = only min
            if (min_value > 0 && max_value < min_value) {
                is_valid = count >= min_value;
            }

            // Min not empty, max not empty and > min = both min and max
            if (min_value > 0 && max_value > min_value) {
                is_valid = count >= min_value && count <= max_value;
            }

            // Min empty, max not empty and > min = only max
            if (min_value === 0 && max_value > min_value) {
                is_valid = count <= max_value;
            }

            return is_valid;
        },

        /**
         * Returns true if the Gutenberg editor is active on the page.
         *
         * @returns {boolean}
         */
        is_gutenberg_active: function () {
            return typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined';
        },

        /**
         * Add a style tag.
         *
         * @param id
         * @param css
         */
        add_style_tag: function (id, css) {
            var $head = $('head');

            if ($head.find('#' + id).length === 0) {
                var $style = $('<style>');

                $style.attr('type', 'text/css');
                $style.text(css);
                $style.attr('id', id);
                $head.append($style);
            }
        },

        /**
         *
         * @param id
         */
        remove_style_tag: function (id) {
            $('#' + id).remove();
        },

        /**
         * Return the editor
         *
         * @returns {object}
         */
        getEditor: function () {
            return wp.data.select('core/editor');
        }


    };

    // Exposes and initialize the object
    window.PP_Checklists = PP_Checklists;
    PP_Checklists.init();

    /*----------  Warning icon in submit button  ----------*/

    // Show warning icon close to the submit button
    if (ppChecklists.show_warning_icon_submit) {
        if (PP_Checklists.is_gutenberg_active()) {
            var styleTagId = 'ppChecklistsWarningIcon';

            // For Gutenberg, we don't inject an element, but change the style of the submit button.
            $(document).on(PP_Checklists.EVENT_TIC, function (event) {
                var has_unchecked = $('#pp-checklists-req-box').children('.status-no');
                if (has_unchecked.length > 0) {
                    PP_Checklists.add_style_tag(styleTagId, ppChecklists.gutenberg_warning_css);
                } else {
                    PP_Checklists.remove_style_tag(styleTagId);
                }
            });
        } else {
            var $icon = $('<span>')
                .addClass('dashicons dashicons-warning pp-checklists-warning-icon')
                .hide()
                .prependTo($('#publishing-action'))
                .attr('title', ppChecklists.title_warning_icon);

            $(document).on(PP_Checklists.EVENT_TIC, function (event) {
                var has_unchecked = $('#pp-checklists-req-box').children('.status-no');
                if (has_unchecked.length > 0) {
                    // Not ok
                    $icon.show();
                } else {
                    // Ok
                    $icon.hide();
                }
            });
        }
    }

    /*----------  Hide submit button  ----------*/

    // Hide the submit button
    if (ppChecklists.hide_publish_button) {
        if (PP_Checklists.is_gutenberg_active()) {
            var styleTagId = 'ppChecklistsHideSubmit';

            $(document).on(PP_Checklists.EVENT_TIC, function (event) {
                var has_unchecked = $('#pp-checklists-req-box').children('.status-no');
                if (has_unchecked.length > 0) {
                    PP_Checklists.add_style_tag(styleTagId, ppChecklists.gutenberg_hide_submit_css);
                } else {
                    PP_Checklists.remove_style_tag(styleTagId);
                }
            });
        } else {
            $(document).on(PP_Checklists.EVENT_TIC, function (event) {
                var has_unchecked = $('#pp-checklists-req-box').children('.status-no'),
                    $button = $('#publish');

                if (has_unchecked.length > 0) {
                    // Not ok
                    $button.hide();
                } else {
                    // Ok
                    $button.show();
                }
            });
        }
    }

    /*----------  Featured Image  ----------*/

    if ($('#pp-checklists-req-featured_image').length > 0) {
        $(document).on(PP_Checklists.EVENT_TIC, function (event) {
            var has_image = false;

            if (PP_Checklists.is_gutenberg_active()) {
                has_image = PP_Checklists.getEditor().getEditedPostAttribute('featured_media') > 0;
            } else {
                has_image = $('#postimagediv').find('#set-post-thumbnail').find('img').length > 0;
            }

            $('#pp-checklists-req-featured_image').trigger(
                PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                has_image
            );
        });
    }

    /*---------- Tags Number  ----------*/

    if ($('#pp-checklists-req-tags_count').length > 0) {
        $(document).on(PP_Checklists.EVENT_TIC, function (event) {
            var count = 0,
                min_value = parseInt(ppChecklists.requirements.tags_count.value[0]),
                max_value = parseInt(ppChecklists.requirements.tags_count.value[1]);

            if (PP_Checklists.is_gutenberg_active()) {
                // @todo: why does Multiple Authors "Remove author from new posts" setting cause this to return null?
                var obj = PP_Checklists.getEditor().getEditedPostAttribute('tags');
            } else {
                var obj = $('#post_tag.tagsdiv ul.tagchecklist').children('li');
            }

            if (typeof obj !== 'undefined') {
                count = obj.length;

                $('#pp-checklists-req-tags_count').trigger(
                    PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                    PP_Checklists.check_valid_quantity(count, min_value, max_value)
                );
            }
        });
    }

    /*----------  Categories Number  ----------*/

    if ($('#pp-checklists-req-categories_count').length > 0) {
        $(document).on(PP_Checklists.EVENT_TIC, function (event) {
            var count = 0,
                min_value = parseInt(ppChecklists.requirements.categories_count.value[0]),
                max_value = parseInt(ppChecklists.requirements.categories_count.value[1]);

            if (PP_Checklists.is_gutenberg_active()) {
                // @todo: why does Multiple Authors "Remove author from new posts" setting cause this to return null?
                var obj = PP_Checklists.getEditor().getEditedPostAttribute('categories');
            } else {
                var obj = $('#categorychecklist input:checked');
            }

            if (typeof obj !== 'undefined') {
                count = obj.length;

                $('#pp-checklists-req-categories_count').trigger(
                    PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                    PP_Checklists.check_valid_quantity(count, min_value, max_value)
                );
            }
        });
    }

    /*----------  Hierarchical Taxonomies Number  ----------*/

    if ($('[data-type^="taxonomy_counter_hierarchical_"]').length > 0) {
        $('[data-type^="taxonomy_counter_hierarchical_"]').each(function (index, elem) {
            $(document).on(PP_Checklists.EVENT_TIC, function (event) {
                var taxonomy = $(elem).data('type').replace('taxonomy_counter_hierarchical_', ''),
                    count = $('#' + taxonomy + 'checklist input:checked').length,
                    min_value = parseInt(ppChecklists.requirements[taxonomy + '_count'].value[0]),
                    max_value = parseInt(ppChecklists.requirements[taxonomy + '_count'].value[1]);

                $('#pp-checklists-req-' + taxonomy + '_count').trigger(
                    PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                    PP_Checklists.check_valid_quantity(count, min_value, max_value)
                );
            });
        });
    }

    /*----------  Non-hierarchical Taxonomies Number  ----------*/

    if ($('[data-type^="taxonomy_counter_non_hierarchical_"]').length > 0) {
        $('[data-type^="taxonomy_counter_non_hierarchical_"]').each(function (index, elem) {
            $(document).on(PP_Checklists.EVENT_TIC, function (event) {
                var taxonomy = $(elem).data('type').replace('taxonomy_counter_non_hierarchical_', ''),
                    count = $('#' + taxonomy + ' .tagchecklist').children('li').length,
                    min_value = parseInt(ppChecklists.requirements[taxonomy + '_count'].value[0]),
                    max_value = parseInt(ppChecklists.requirements[taxonomy + '_count'].value[1]);

                $('#pp-checklists-req-' + taxonomy + '_count').trigger(
                    PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                    PP_Checklists.check_valid_quantity(count, min_value, max_value)
                );
            });
        });
    }

    /*----------  Filled in Excerpt  ----------*/

    if ($('#pp-checklists-req-filled_excerpt').length > 0) {
        $(document).on(PP_Checklists.EVENT_TIC, function (event) {
            var has_excerpt = false;

            if (PP_Checklists.is_gutenberg_active()) {
                // @todo: why does Multiple Authors "Remove author from new posts" setting cause this to return null?
                var obj = PP_Checklists.getEditor().getEditedPostAttribute('excerpt');
            } else {
                if ($('#excerpt').length === 0) {
                    return;
                }

                var obj = $('#excerpt').val();
            }

            if (typeof obj !== 'undefined') {
                if (obj.trim().length > 0) {
                    has_excerpt = true;
                }

                $('#pp-checklists-req-filled_excerpt').trigger(
                    PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                    has_excerpt
                );
            }
        });
    }

    /*----------  Word Count ----------*/
    var lastCount = 0;
    if (PP_Checklists.is_gutenberg_active()) {
        /**
         * For Gutemberg
         */
        if ($('#pp-checklists-req-words_count').length > 0) {
            wp.data.subscribe(
                function () {
                    // @todo: why does Multiple Authors "Remove author from new posts" setting cause this to return null?
                    var content = PP_Checklists.getEditor().getEditedPostAttribute('content');

                    if (typeof content == 'undefined') {
                        return;
                    }

                    var count = wp.utils.WordCounter.prototype.count(content);

                    if (lastCount == count) {
                        return;
                    }


                    var min = parseInt(ppChecklists.requirements.words_count.value[0]),
                        max = parseInt(ppChecklists.requirements.words_count.value[1]);

                    $('#pp-checklists-req-words_count').trigger(
                        PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                        PP_Checklists.check_valid_quantity(count, min, max)
                    );

                    lastCount = count;
                }
            );
        }
    } else {
        /**
         * For the Classic Editor
         */
        var $content = $('#content');
        var lastCount = 0;
        var editor;

        /**
         * Get the words count from TinyMCE and update the status of the requirement
         */
        function update () {
            var text, count;

            if (typeof editor == 'undefined' || ! editor || editor.isHidden()) {
                // For the text tab.
                text = $content.val();
            } else {
                // For the editor tab.
                text = editor.getContent({format: 'raw'});
            }

            count = counter.count(text);

            if (lastCount == count) {
                return;
            }

            var min = parseInt(ppChecklists.requirements.words_count.value[0]),
                max = parseInt(ppChecklists.requirements.words_count.value[1]);

            $('#pp-checklists-req-words_count').trigger(
                PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                PP_Checklists.check_valid_quantity(count, min, max)
            );

            lastCount = count;
        }

        // For the editor.
        $(document).on(PP_Checklists.EVENT_TINYMCE_LOADED, function(event, tinymce) {
            editor = tinymce.editors['content'];

            if (typeof editor !== 'undefined') {

                editor.onInit.add(function () {
                    /**
                     * Bind the words count update triggers.
                     *
                     * When a node change in the main TinyMCE editor has been triggered.
                     * When a key has been released in the plain text content editor.
                     */

                    if (editor.id !== 'content') {
                        return;
                    }

                    editor.on('nodechange keyup', _.debounce(update, 500));
                });
            }
        });

        $content.on('input keyup', _.debounce(update, 500));
        update();
    }

    /*----------  Block publishing, for Gutenberg ----------*/
    if (PP_Checklists.is_gutenberg_active()) {
        $(document).on(PP_Checklists.EVENT_TIC, function (event) {
            PP_Checklists.elems.document.trigger(PP_Checklists.EVENT_VALIDATE_REQUIREMENTS);
        });
    }

    /**
     *
     * @type {Object}
     * @deprecated
     */
    window.PP_Content_Checklist = PP_Checklists;
})(jQuery, window, document, new wp.utils.WordCounter());