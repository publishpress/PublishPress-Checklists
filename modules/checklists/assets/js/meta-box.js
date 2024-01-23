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
            'post_status': $('#post_status'),
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
             * @type {boolean}
             */
            is_publishing: false,

            /**
             * Flag for the confirmed state
             * @type {boolean}
             */
            is_confirmed: false,

            /**
             * Flag for the should_block state
             * @type {boolean}
             */
            should_block: false,

            /**
             * Flag to say the validate method is already being executed.
             * @type {boolean}
             */
            is_validating: false
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

                // For some reason, adding this again after the click is trigged solved the acf conflict issue https://github.com/publishpress/PublishPress-Checklists/issues/506
                this.state.is_confirmed = true;
                
            }.bind(this));

            if (!this.is_gutenberg_active()) {
                // Hook to the submit button
                $('form#post').submit(function (event) {
                    //do not trigger for preview action
                    if ($('input#wp-preview').val() === 'dopreview') {
                        return true;
                    }
                    
                    // Reset the should_block state
                    this.state.should_block = false;

                    this.elems.document.trigger(this.EVENT_VALIDATE_REQUIREMENTS);

                    return !this.state.should_block;
                }.bind(this));
            } else {
                $(document).on(this.EVENT_TIC, function (event) {
                    if (this.state.is_validating !== false) {
                        return;
                    }

                    var isSidebarOpened = wp.data.select('core/edit-post').isPublishSidebarOpened();

                    if (isSidebarOpened) {
                        this.elems.document.trigger(this.EVENT_VALIDATE_REQUIREMENTS);
                    } else {
                        // We need this as validate requirement is not been triggered for publish post. I'll leave the condition for now till i study this very well.
                        this.elems.document.trigger(this.EVENT_VALIDATE_REQUIREMENTS);

                    }
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

            // Add event to the button custom items
            this.elems.document.on('click', '.pp-checklists-req .pp-checklists-check-item', function (event) {
                event.preventDefault();
                var target = $(event.target);
                var target_li = target.closest('li');
                var global_this = this;

                $('.pp-checklists-req').find('.request-response').html('');
             
                if (typeof target_li !== 'undefined') {
                    target_li.find('.pp-checklists-check-item').prop('disabled', true);
                    target_li.find('.spinner').addClass('is-active');

                    var data = {
                        action: "pp_checklists_" + target_li.attr('data-source') + "_requirement",
                        requirement: ppChecklists.requirements[target_li.attr('data-id')],
                        content: PP_Checklists.get_editor_content(),
                        nonce: ppChecklists.nonce,
                    };

                    $.post(ajaxurl, data, function (response) {
                        var response_raw_content = response.content;
                        var response_content = response_raw_content.replace(/\n/g, '<br>');
                        if (response.yes_no == 'yes') {
                            $('#pp-checklists-req-' + target_li.attr('data-id')).find('.dashicons').removeClass('dashicons-yes');
                            global_this.elems.document.trigger(global_this.EVENT_TOGGLE_CUSTOM_ITEM, $('#pp-checklists-req-' + target_li.attr('data-id')));
                        } else if (response.yes_no == 'no') {
                            $('#pp-checklists-req-' + target_li.attr('data-id')).find('.dashicons').addClass('dashicons-yes');
                            global_this.elems.document.trigger(global_this.EVENT_TOGGLE_CUSTOM_ITEM, $('#pp-checklists-req-' + target_li.attr('data-id')));
                        }
                        target_li.find('.request-response').html('<div id="message" class="ppch-message notice is-dismissible updated published"><p>' + response_content + '</p><button type="button" class="notice-dismiss" onclick="this.closest(\'#message\').remove();"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
                        target_li.find('.pp-checklists-check-item').prop('disabled', false);
                        target_li.find('.spinner').removeClass('is-active');
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        target_li.find('.request-response').html('<div id="message" class="ppch-message notice is-dismissible updated published"><p>' + errorThrown + ' ' + textStatus + '</p><button type="button" class="notice-dismiss" onclick="this.closest(\'#message\').remove();"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
                        target_li.find('.pp-checklists-check-item').prop('disabled', false);
                        target_li.find('.spinner').removeClass('is-active');
                    });
                }
            }.bind(this));

            // On clicking the confirmation button in the modal window
            this.elems.document.on(this.EVENT_TOGGLE_CUSTOM_ITEM, function (event, item) {
                var $item = $(item),
                    $icon = $item.children('.dashicons'),
                    checked = $icon.hasClass('dashicons-yes');

                $icon.removeClass('dashicons-no');

                if (checked) {
                    $icon.removeClass('dashicons-yes');
                    $item.removeClass('status-yes');
                    $item.addClass('status-no');
                    $item.find('.ppch_item_requirement').val('no');
                    wp.hooks.doAction('pp-checklists.requirements-updated', $item);
                } else {
                    $icon.addClass('dashicons-yes');
                    $item.addClass('status-yes');
                    $item.removeClass('status-no');
                    $item.find('.ppch_item_requirement').val('yes');
                    wp.hooks.doAction('pp-checklists.requirements-updated', $item);
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
         * Check if the current post status is pending
         * @return {Boolean} True if pending.
         */
        is_pending: function () {
            return 'pending' === this.elems.original_post_status.val();
        },

        /**
         * Check if the current post status is draft
         * @return {Boolean} True if draft.
         */
        is_draft: function () {
            return 'draft' === this.elems.original_post_status.val() || 'auto-draft' === this.elems.original_post_status.val();
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
            this.state.is_validating = true;
            this.state.should_block = false;

            // Bypass all checks because the confirmation button was clicked.
            if (this.state.is_confirmed) {
                this.state.is_confirmed = false;
                this.state.is_validating = false;

                return;
            }

            var uncheckedItems = {
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
            var checkRequirement = function ($reqElem, list) {
                if ($reqElem.hasClass('status-no')) {
                    // Check if the requirement is not ok
                    var $uncheckedRequirements = $reqElem.find('.status-label');

                    if ($uncheckedRequirements.length > 0) {
                        list.push($uncheckedRequirements.html().trim());
                    }
                }
            }.bind(this);

            var checkRequirementAction = function (actionType) {
                var $elems = $('.pp-checklists-req.metabox-req.pp-checklists-' + actionType);

                for (var i = 0; i < $elems.length; i++) {
                    checkRequirement($($elems[i]), uncheckedItems[actionType]);
                }
            }.bind(this);

            // Check if any of the requirements is set to trigger warnings
            checkRequirementAction('warning');
            checkRequirementAction('block');

            if (this.is_gutenberg_active()) {
                this.state.is_publishing = wp.data.select('core/edit-post').isPublishSidebarOpened();
            }

            var originalPostStatus = this.elems.original_post_status.val(),
                isPublishingThePost = this.state.is_publishing,
                isUpdatingPublishedPost = this.getCurrentPostStatus() === 'publish' && originalPostStatus === 'publish';

            if (isPublishingThePost || isUpdatingPublishedPost) {
                var showBlockMessage = uncheckedItems.block.length > 0,
                    showWarning = uncheckedItems.warning.length > 0;

                if (showWarning || showBlockMessage) {
                    this.state.should_block = true;

                    var message = '';

                    if (showBlockMessage) {
                        if (PP_Checklists.is_gutenberg_active()) {
                            wp.hooks.doAction('pp-checklists.update-failed-requirements', uncheckedItems);
                        } else {
                            if (isUpdatingPublishedPost) {
                                message = ppChecklists.msg_missed_required_updating;
                            } else {
                                message = ppChecklists.msg_missed_required_publishing;
                            }

                            message += '<div class="pp-checklists-modal-list"><ul><li>' + uncheckedItems.block.join('</li><li>') + '</li></ul></div>';

                            if (uncheckedItems.warning.length > 0) {
                                if (isUpdatingPublishedPost) {
                                    message += ppChecklists.msg_missed_important_updating;
                                } else {
                                    message += ppChecklists.msg_missed_important_publishing;
                                }

                                message += '<div class="pp-checklists-modal-list"><ul><li>' + uncheckedItems.warning.join('</li><li>') + '</li></ul></div>';
                            }

                            // Display the alert
                            $('#pp-checklists-modal-alert-content').html(message);
                            $('[data-remodal-id=pp-checklists-modal-alert]').remodal().open();
                        }
                    } else if (showWarning) {
                        if (PP_Checklists.is_gutenberg_active()) {
                            wp.hooks.doAction('pp-checklists.update-failed-requirements', uncheckedItems);
                        } else {
                            // Only display a warning
                            if (isUpdatingPublishedPost) {
                                message = ppChecklists.msg_missed_optional_updating;
                            } else {
                                message = ppChecklists.msg_missed_optional_publishing;
                            }

                            message += '<div class="pp-checklists-modal-list"><ul><li>' + uncheckedItems.warning.join('</li><li>') + '</li></ul></div>';

                            if (uncheckedItems.block.length > 0) {
                                message += ppChecklists.msg_missed_required + '<div class="pp-checklists-modal-list"><ul><li>' + uncheckedItems.block.join('</li><li>') + '</li></ul></div>';
                            }

                            // Display the confirm
                            $('#pp-checklists-modal-confirm-content').html(message);
                            $('[data-remodal-id=pp-checklists-modal-confirm]').remodal().open();
                        }
                    }
                } else {
                    if (PP_Checklists.is_gutenberg_active()) {
                        wp.hooks.doAction('pp-checklists.update-failed-requirements', uncheckedItems);
                    }

                    this.state.is_publishing = false;
                    this.state.is_validating = false;

                    return;
                }
            } else {
                // we only need the failed counts to be triggered for panel validation
                wp.hooks.doAction('pp-checklists.update-failed-requirements', uncheckedItems);
            }

            this.state.is_publishing = false;
            this.state.is_validating = false;
        },

        getCurrentPostStatus: function () {
            if (PP_Checklists.is_gutenberg_active()) {
                return wp.data.select('core/editor').getEditedPostAttribute('status');
            } else {
                return this.elems.post_status.val();
            }
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
                $element.find('.ppch_item_requirement').val('yes');
                wp.hooks.doAction('pp-checklists.requirements-updated', $element);
            } else {
                // Not ok
                $icon_element.removeClass('dashicons-yes');
                $icon_element.addClass('dashicons-no');
                $icon_element.parent().removeClass('status-yes');
                $icon_element.parent().addClass('status-no');
                $element.find('.ppch_item_requirement').val('no');
                wp.hooks.doAction('pp-checklists.requirements-updated', $element);
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
         * Check for internal link from content and return result as array
         *
         *  - remove image inside tags so we don't count them as link
         *  - remove element inside <a href></a> to avoid double counting for one link in case of <a href="Link">Link</a>
         *  - check for every valid link and return array
         *  - loop array and return only valid internal links excluding other images url
         *
         * @param  {String} content
         * @param  {Array} links
         * @param  {String} website
         *
         * @return {Array}
         */
        extract_internal_links: function (content, links = [], website = window.location.host) {
            var link;
            if (content) {

                //remove image inside tags so we don't count them as link
                content = content.replace(/<img[^>]*>/g, "");

                //remove element inside <a href></a> to avoid double counting for one link in case of <a href="Link">Link</a>
                content = content.replace(/<a .*? *href="([^\'\"]+).*?<\/a>/g, "$1");

                //check for every valid link and return array
                content = content.match(/(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/gi);

                //loop array and return only valid internal links excluding other images url
                if (content) {
                    for (link of content) {
                        //skip if link is image
                        if (link.match(/\.(jpeg|jpg|gif|png|svg)$/)) continue;
                        //skip if link has different host than current website
                        if (link.indexOf(website) < 0) continue;
                        //add valid link to array
                        links.push(link);
                    }
                }


            }

            return links;

        },

        /**
         * Check for external link from content and return result as array
         *
         *  - remove image inside tags so we don't count them as link
         *  - remove element inside <a href></a> to avoid double counting for one link in case of <a href="Link">Link</a>
         *  - check for every valid link and return array
         *  - loop array and return only valid external links excluding other images url
         *
         * @param  {String} content
         * @param  {Array} links
         * @param  {String} website
         *
         * @return {Array}
         */
        extract_external_links: function (content, links = [], website = window.location.host) {
            var link,
                match,
                regex = /<a.*?href=["\']([^"\']+)["\'].*?\>(.*?)\<\/a\>/ig;
            if (content) {

                //check for external link and return array excluding other images url
                while ((match = regex.exec(content)) !== null) {
                    link = match[1];

                    //skip if link is image
                    if (link.match(/\.(jpeg|jpg|gif|png|svg)$/)) continue;
                    //skip if link point to the current website host
                    if (link.indexOf(website) > 0) continue;
                    //add valid link to array
                    links.push(link);

                }

            }

            return links;
        },

        /**
         * Check for images without alt text from content and return result as array
         *
         * @param  {String} content
         * @param  {Array} missing_alt
         *
         * @return {Array}
         */
        missing_alt_images: function (content, missing_alt = []) {
            var alt,
                regex = /<img[^>]*>/g;
        
            if (content) {
                var imgTags = content.match(regex) || [];
                imgTags.forEach(function (imgTag) {
                    alt = imgTag.match(/alt="([^"]*)"/);
        
                    if (!alt || !alt[1].replace(/\s/g, '').length) {
                        missing_alt.push(imgTag);
                    }
                });
            }
        
            return missing_alt;
        },

        extract_links_from_content: function(content) {
            let linksIterator = content.matchAll(/(?:<a[^>]+href=['"])([^'"]+)(?:['"][^>]*>)/gi);

            let linkResult = linksIterator.next();
            let linksList = [];

            while (!linkResult.done) {
                linksList.push(linkResult.value[1]);

                linkResult = linksIterator.next();
            }

            return linksList;
        },

        is_valid_link: function(link) {

            const linkWithoutFragment = link.split('#')[0];

            return linkWithoutFragment.match(/^(?:(#[-a-zA-Z0-9@:%._\+~#=]{0,256})|https?:\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b(?:[-a-zA-Z0-9()@;:%_\+.~#?&\/\/=*]*)|tel:\+?[0-9\-]+|mailto:[a-z0-9\-_\.]+@[a-z0-9\-_\.]+?[a-z0-9@\.\?=\s\%,\-&_;*]+)$/i);
        },

        /**
         * Check for links without http(s)
         *
         * @param  {String} content
         * @param  {Array} invalid_links
         *
         * @return {Array}
         */
        validate_links_format: function (content, invalid_links = []) {
            if (!content) {
                return [];
            }

            // Extract links from the href attribute.
            let linksList = PP_Checklists.extract_links_from_content(content);

            for (let i = 0; i < linksList.length; i++) {
                if (!PP_Checklists.is_valid_link(linksList[i])) {
                    invalid_links.push(linksList[i]);
                }
            }

            return invalid_links;
        },

        /**
         * Returns true if the Gutenberg editor is active on the page.
         *
         * @returns {boolean}
         */
        is_gutenberg_active: function () {

            let gutenbergActive = false;
            if (typeof wp.data !== 'undefined' && typeof wp.data.select('core') !== 'undefined' && typeof wp.data.select('core/edit-post') !== 'undefined' && typeof wp.data.select('core/editor') !== 'undefined') {
                gutenbergActive = true;
            }

            return gutenbergActive;
        },

        /**
         * Returns editor content.
         *
         * @returns {boolean}
         */
        get_editor_content: function () {
            let data = '';
  
            try { // Gutenberg
              data = PP_Checklists.getEditor().getEditedPostAttribute('content');
            } catch (error) {
              try { // TinyMCE
                let ed = tinyMCE.activeEditor;
                if ('mce_fullscreen' == ed.id) {
                  tinyMCE.get('content').setContent(ed.getContent({
                    format: 'raw'
                  }), {
                    format: 'raw'
                  });
                }
                tinyMCE.get('content').save();
                data = jQuery('#content').val();
              } catch (error) {
                try { // Quick Tags
                  data = jQuery('#content').val();
                } catch (error) { }
              }
            }
      
            // Trim data
            data = data.replace(/^\s+/, '').replace(/\s+$/, '');
      
            return data;
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
    // RankMath plugin breaks if we
    if (typeof rankMath !== 'undefined') {
        setTimeout(function () {
            PP_Checklists.init();
        }, 2500);
    } else {
        PP_Checklists.init();
    }

    /*----------  Warning icon in submit button  ----------*/
    if (ppChecklists.show_warning_icon_submit) {
        $(document).on(PP_Checklists.EVENT_TIC, function (event) {
            var has_unchecked = $('#pp-checklists-req-box').children('.status-no');
            if (has_unchecked.length > 0) {
                $('body').addClass('ppch-show-publishing-warning-icon');
            } else {
                $('body').removeClass('ppch-show-publishing-warning-icon');
            }
        });
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
                    taxonomy_rest_base = $(elem).data('extra'),
                    count = 0,
                    min_value = parseInt(ppChecklists.requirements[taxonomy + '_count'].value[0]),
                    max_value = parseInt(ppChecklists.requirements[taxonomy + '_count'].value[1]);
                var obj = '';
              if (PP_Checklists.is_gutenberg_active()) {
                if (taxonomy_rest_base && taxonomy_rest_base !== '' && taxonomy_rest_base !== 'false') {
                  obj = PP_Checklists.getEditor().getEditedPostAttribute(taxonomy_rest_base);
                } else {
                  obj = PP_Checklists.getEditor().getEditedPostAttribute(taxonomy);
                }
              } else {
                    obj = $('#' + taxonomy + 'checklist input:checked');
                }

                if (typeof obj !== 'undefined') {
                    count = obj.length;

                    $('#pp-checklists-req-' + taxonomy + '_count').trigger(
                        PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                        PP_Checklists.check_valid_quantity(count, min_value, max_value)
                    );
                }

            });
        });
    }

    /*----------  Non-hierarchical Taxonomies Number  ----------*/

    if ($('[data-type^="taxonomy_counter_non_hierarchical_"]').length > 0) {
        $('[data-type^="taxonomy_counter_non_hierarchical_"]').each(function (index, elem) {
            $(document).on(PP_Checklists.EVENT_TIC, function (event) {
                var taxonomy = $(elem).data('type').replace('taxonomy_counter_non_hierarchical_', ''),
                    count = 0,
                    min_value = parseInt(ppChecklists.requirements[taxonomy + '_count'].value[0]),
                    max_value = parseInt(ppChecklists.requirements[taxonomy + '_count'].value[1]),
                    obj = null;

                if (PP_Checklists.is_gutenberg_active()) {
                    obj = PP_Checklists.getEditor().getEditedPostAttribute(taxonomy);
                } else {
                    obj = $('#' + taxonomy + ' .tagchecklist').children('li');
                }

                if (typeof obj !== 'undefined') {
                    count = obj.length;

                    $('#pp-checklists-req-' + taxonomy + '_count').trigger(
                        PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                        PP_Checklists.check_valid_quantity(count, min_value, max_value)
                    );
                }
            });
        });
    }

    /*----------  Filled in Excerpt  ----------*/

    if ($('#pp-checklists-req-filled_excerpt').length > 0) {
        $(document).on(PP_Checklists.EVENT_TIC, function (event) {
            var count = 0,
                min_value = parseInt(ppChecklists.requirements.filled_excerpt.value[0]),
                max_value = parseInt(ppChecklists.requirements.filled_excerpt.value[1]);

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
                count = obj.length;

                $('#pp-checklists-req-filled_excerpt').trigger(
                    PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                    PP_Checklists.check_valid_quantity(count, min_value, max_value)
                );
            }
        });
    }

    /*----------  Title Count  ----------*/

    if ($('#pp-checklists-req-title_count').length > 0) {
        $(document).on(PP_Checklists.EVENT_TIC, function (event) {
            var count = 0,
                min_value = parseInt(ppChecklists.requirements.title_count.value[0]),
                max_value = parseInt(ppChecklists.requirements.title_count.value[1]);

            if (PP_Checklists.is_gutenberg_active()) {
                // @todo: why does Multiple Authors "Remove author from new posts" setting cause this to return null?
                var obj = PP_Checklists.getEditor().getEditedPostAttribute('title');
            } else {
                if ($('#title').length === 0) {
                    return;
                }

                var obj = $('#title').val();
            }

            if (typeof obj !== 'undefined') {
                count = obj.length;

                $('#pp-checklists-req-title_count').trigger(
                    PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                    PP_Checklists.check_valid_quantity(count, min_value, max_value)
                );
            }
        });
    }

    /*----------  Word Count ----------*/
    var lastCount = 0;
    if (PP_Checklists.is_gutenberg_active()) {
        /**
         * For Gutenberg
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
        function update() {
            var text, count;

            if (typeof ppChecklists.requirements.words_count === 'undefined') {
                return;
            }

            if (typeof editor == 'undefined' || !editor || editor.isHidden()) {
                // For the text tab.
                text = $content.val();
            } else {
                // For the editor tab.
                text = editor.getContent({format: 'raw'});
            }

            count = counter.count(text);

            if (lastCount === count) {
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
        $(document).on(PP_Checklists.EVENT_TINYMCE_LOADED, function (event, tinymce) {
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

    /*----------  Internal Links ----------*/
    var lastCount = 0;
    if (PP_Checklists.is_gutenberg_active()) {
        /**
         * For Gutenberg
         */
        if ($('#pp-checklists-req-internal_links').length > 0) {
            wp.data.subscribe(
                function () {
                    var content = PP_Checklists.getEditor().getEditedPostAttribute('content');

                    if (typeof content == 'undefined') {
                        return;
                    }

                    var count = PP_Checklists.extract_internal_links(content).length;

                    if (lastCount == count) {
                        return;
                    }


                    var min = parseInt(ppChecklists.requirements.internal_links.value[0]),
                        max = parseInt(ppChecklists.requirements.internal_links.value[1]);

                    $('#pp-checklists-req-internal_links').trigger(
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
        function update() {
            var text, count;

            if (typeof ppChecklists.requirements.internal_links === 'undefined') {
                return;
            }

            if (typeof editor == 'undefined' || !editor || editor.isHidden()) {
                // For the text tab.
                text = $content.val();
            } else {
                // For the editor tab.
                text = editor.getContent({format: 'raw'});
            }

            count = PP_Checklists.extract_internal_links(text).length;

            if (lastCount === count) {
                return;
            }

            var min = parseInt(ppChecklists.requirements.internal_links.value[0]),
                max = parseInt(ppChecklists.requirements.internal_links.value[1]);

            $('#pp-checklists-req-internal_links').trigger(
                PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                PP_Checklists.check_valid_quantity(count, min, max)
            );

            lastCount = count;
        }

        // For the editor.
        $(document).on(PP_Checklists.EVENT_TINYMCE_LOADED, function (event, tinymce) {
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


    /*----------  External Links ----------*/
    var lastCount = 0;
    if (PP_Checklists.is_gutenberg_active()) {
        /**
         * For Gutenberg
         */
        if ($('#pp-checklists-req-external_links').length > 0) {
            wp.data.subscribe(
                function () {
                    var content = PP_Checklists.getEditor().getEditedPostAttribute('content');

                    if (typeof content == 'undefined') {
                        return;
                    }

                    var count = PP_Checklists.extract_external_links(content).length;

                    if (lastCount == count) {
                        return;
                    }


                    var min = parseInt(ppChecklists.requirements.external_links.value[0]),
                        max = parseInt(ppChecklists.requirements.external_links.value[1]);

                    $('#pp-checklists-req-external_links').trigger(
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
        function update() {
            var text, count;

            if (typeof ppChecklists.requirements.external_links === 'undefined') {
                return;
            }

            if (typeof editor == 'undefined' || !editor || editor.isHidden()) {
                // For the text tab.
                text = $content.val();
            } else {
                // For the editor tab.
                text = editor.getContent({format: 'raw'});
            }

            count = PP_Checklists.extract_external_links(text).length;

            if (lastCount === count) {
                return;
            }

            var min = parseInt(ppChecklists.requirements.external_links.value[0]),
                max = parseInt(ppChecklists.requirements.external_links.value[1]);

            $('#pp-checklists-req-external_links').trigger(
                PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                PP_Checklists.check_valid_quantity(count, min, max)
            );

            lastCount = count;
        }

        // For the editor.
        $(document).on(PP_Checklists.EVENT_TINYMCE_LOADED, function (event, tinymce) {
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


    /*----------  Image alt ----------*/
    if (PP_Checklists.is_gutenberg_active()) {
        /**
         * For Gutenberg
         */
        if ($('#pp-checklists-req-image_alt').length > 0) {
            wp.data.subscribe(
                function () {
                    var no_missing_alt = false;
                    var content = PP_Checklists.getEditor().getEditedPostAttribute('content');

                    if (typeof content == 'undefined') {
                        return;
                    }

                    var count = PP_Checklists.missing_alt_images(content).length;

                    if (count == 0) {
                        no_missing_alt = true;
                    }

                    $('#pp-checklists-req-image_alt').trigger(
                        PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                        no_missing_alt
                    );

                }
            );
        }
    } else {
        /**
         * For the Classic Editor
         */
        var $content = $('#content');
        var editor;

        /**
         * Get the words count from TinyMCE and update the status of the requirement
         */
        function update() {
            var text, count, no_missing_alt = false;
            if (typeof ppChecklists.requirements.image_alt === 'undefined') {
                return;
            }

            if (typeof editor == 'undefined' || !editor || editor.isHidden()) {
                // For the text tab.
                text = $content.val();
            } else {
                // For the editor tab.
                text = editor.getContent({format: 'raw'});
            }

            var count = PP_Checklists.missing_alt_images(text).length;

            if (count == 0) {
                no_missing_alt = true;
            }

            $('#pp-checklists-req-image_alt').trigger(
                PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                no_missing_alt
            );

        }

        // For the editor.
        $(document).on(PP_Checklists.EVENT_TINYMCE_LOADED, function (event, tinymce) {
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

        $content.on('input keyup change', _.debounce(update, 500));
        update();
    }

    /*----------  Validate Links ----------*/
    if (PP_Checklists.is_gutenberg_active()) {
        /**
         * For Gutenberg
         */
        if ($('#pp-checklists-req-validate_links').length > 0) {
            wp.data.subscribe(
                function () {
                    var content = PP_Checklists.getEditor().getEditedPostAttribute('content');

                    if (typeof content == 'undefined') {
                        return;
                    }

                    var no_invalid_link = PP_Checklists.validate_links_format(content).length === 0;

                    $('#pp-checklists-req-validate_links').trigger(
                        PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                        no_invalid_link
                    );

                }
            );
        }
    } else {
        /**
         * For the Classic Editor
         */
        var $content = $('#content');
        var editor;

        /**
         * Get the words count from TinyMCE and update the status of the requirement
         */
        function update() {
            var text, count, no_invalid_link = false;
            if (typeof ppChecklists.requirements.validate_links === 'undefined') {
                return;
            }

            if (typeof editor == 'undefined' || !editor || editor.isHidden()) {
                // For the text tab.
                text = $content.val();
            } else {
                // For the editor tab.
                text = editor.getContent({format: 'raw'});
            }

            var count = PP_Checklists.validate_links_format(text).length;

            if (count == 0) {
                no_invalid_link = true;
            }

            $('#pp-checklists-req-validate_links').trigger(
                PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                no_invalid_link
            );

        }

        // For the editor.
        $(document).on(PP_Checklists.EVENT_TINYMCE_LOADED, function (event, tinymce) {
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

        $content.on('input keyup change', _.debounce(update, 500));
        update();
    }

    /*----------  Configure link for Checklist metabox  ----------*/
    if (ppChecklists.user_can_manage_options == 1 && $('#pp_checklist_meta').length) {
        var ppChecklistMetaboxLabel = $('#pp_checklist_meta').find('h2.hndle').val();
        $('#pp_checklist_meta').find('h2.hndle').append(
            ppChecklistMetaboxLabel +
            '<span class="postbox-title-action"><a href="' + ppChecklists.configure_url +  '" class="edit-box open-box">' + ppChecklists.label_configure + '</a></span>'
        );
    }

    /**
     *
     * @type {Object}
     * @deprecated
     */
    window.PP_Content_Checklist = PP_Checklists;
})(jQuery, window, document, new wp.utils.WordCounter());
