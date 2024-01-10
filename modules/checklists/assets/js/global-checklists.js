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

(function ($, objectL10n_checklists_global_checklist) {
    'use strict';

    $(function () {
        show_post_type_requirements(objectL10n_checklists_global_checklist.first_post_type);

        $('#pp-checklists-requirements tbody').sortable({
            items : ' > tr'
        });

        // Set the event for the post type filter
        $('#pp-checklists-post-type-filter a').on('click', function (event) {
            event.preventDefault();

            var $target = $(event.toElement || event.target),
                post_type = $target.attr('href').substring(1);

            show_post_type_requirements(post_type);
        });

        // Set the mask for settings fields
        $('.pp-checklists-number').on('keypress', function (event) {
            var key = event.keyCode || event.which;
            var allowed_keys = [
                35, 36, 37, 38, 39, 40, // arrows
                8, 9, 46, 27, 13, // backspace, tab, delete, esc, enter
                48, 49, 50, 51, 52, 53, 54, 55, 56, 57 // 0-9
            ];

            // Ignore any key different than number
            if (allowed_keys.indexOf(key) < 0) {
                event.preventDefault();

                return false;
            }

            return true;
        });

        $('.pp-checklists-float').on('keypress', function (event) {
            var key = event.keyCode || event.which;
            var allowed_keys = [
                35, 36, 37, 38, 39, 40, // arrows
                44, 46, // decimal separators
                8, 9, 46, 27, 13, // backspace, tab, delete, esc, enter
                48, 49, 50, 51, 52, 53, 54, 55, 56, 57 // 0-9
            ];

            // Ignore any key different than number
            if (allowed_keys.indexOf(key) < 0) {
                event.preventDefault();

                return false;
            }

            return true;
        });

        /**
         * Show the requirements for the specific post type and hide all the
         * others.
         *
         * @param  {string} post_type
         */
        function show_post_type_requirements (post_type) {
            // Hide the requirements which are not for the current post type
            $('#pp-checklists-requirements tr.pp-checklists-requirement-row').hide();
            // Display the correct requirements
            $('#pp-checklists-requirements tr[data-post-type="' + post_type + '"]').show();
            // Mark the filter as selected
            $('#pp-checklists-post-type-filter li.nav-tab-active').removeClass('nav-tab-active');
            $('#pp-checklists-post-type-filter li.post-type-' + post_type).addClass('nav-tab-active');
        }

        /**
         * Returns the current post type, selected by the filter.
         *
         * @return string
         */
        function get_current_post_type () {
            var post_type = $('#pp-checklists-post-type-filter li.nav-tab-active a').attr('href').substring(1);

            if (post_type === '' || post_type === false || post_type === null || typeof post_type === undefined) {
                post_type = objectL10n_checklists_global_checklist.first_post_type;
            }

            return post_type;
        }

        /**
         * Method to remove custom item from the requirements list, identified
         * by the temporary ID/
         *
         * @param  {string} id
         * @param  {string} type
         */
        function remove_row (id, type) {
            // Add a special hidden input to flag the delete action
            var $input = $('<input type="hidden" />')
                .attr('name', 'publishpress_checklists_checklists_options[' + type + '_items_remove][]')
                .val(id)
                .appendTo($('#pp-checklists-requirements'));

            $('tr[data-id="' + id + '"]').remove();
        }

        /**
         * Callback for events where we want to trigger
         * a remove row action
         *
         * @param  {Event} event
         */
        function callback_remove_row (event) {
            var $target = $(event.target);

            remove_row($target.data('id'), $target.data('type'));
        }

        /**
         * Create a row inside the requirements table
         *
         * @param  {string} title
         * @param  {string} action
         *
         * @return {Element}
         */
        function create_row (id, title, action, post_type, type) {
            var $table = $('#pp-checklists-requirements'),
                $tr = $('<tr>'),
                $td = null,
                $titleField = type == 'openai' ? $('<textarea>') : $('<input type="text" />'),
                $idField = $('<input type="hidden" />'),
                $actionField = $('<select>'),
                $canIgnoreField = $('<select>'),
                $optionsField = $('<select>'),
                $option,
                $a,
                $icon,
                $suggestionItem = $('<div class="pp-custom-suggestion">'),
                $suggestionsObject = objectL10n_checklists_global_checklist[type + '_suggestions'],
                rule;

            $table.append($tr);

            $tr.addClass('pp-checklists-requirement-row')
                .attr('data-id', id)
                .attr('data-type', type)
                .attr('data-post-type', post_type);

            $td = $('<td>').appendTo($tr);

            // ID field
            $idField
                .attr(
                    'name',
                    'publishpress_checklists_checklists_options[' + type + '_items][]'
                )
                .val(id)
                .appendTo($td);

            // Title cell
            $titleField
                .attr(
                    'name',
                    'publishpress_checklists_checklists_options[' + id + '_title][' + post_type + ']'
                )
                .val(title)
                .addClass('pp-checklists-custom-item-title')
                .focus()
                .attr('data-id', id)
                .attr('placeholder', objectL10n_checklists_global_checklist[type + '_enter_name'])
                .appendTo($td);

            // Suggestion
            if (typeof $suggestionsObject !== 'undefined') {
                $suggestionItem.append('<span class="suggestion-title">' + objectL10n_checklists_global_checklist.suggestion_title + ':</span> ');
                for (var key in $suggestionsObject) {
                    if ($suggestionsObject.hasOwnProperty(key)) {
                        $suggestionItem.append('<span>&#x2022; <a href="javascript:void(0);" class="' + key + '" data-prompt="' + $suggestionsObject[key].prompt + '">' + $suggestionsObject[key].label + '</a></span> ');
                    }
                }
                $suggestionItem.appendTo($td);
            }

            // Action cell
            $td = $('<td>').appendTo($tr);
            $actionField
                .attr(
                    'name',
                    'publishpress_checklists_checklists_options[' + id + '_rule][' + post_type + ']'
                )
                .attr('data-id', id)
                .appendTo($td);

            $.each(objectL10n_checklists_global_checklist.rules, function (value, label) {
                $option = $('<option>')
                    .attr('value', value)
                    .text(label)
                    .appendTo($actionField);
            });

            // can_ignore cell
            $td = $('<td>').appendTo($tr);
            $canIgnoreField
                .attr('class', 'pp-checklists-can-ignore')
                .attr(
                    'name',
                    'publishpress_checklists_checklists_options[' + id + '_can_ignore][' + post_type + '][]'
                )
                .attr('multiple', 'multiple')
                .appendTo($td);

            $option = $('<option value=""></option>').appendTo($canIgnoreField);
            $.each(objectL10n_checklists_global_checklist.roles, function (value, label) {
                $option = $('<option>')
                    .attr('value', value)
                    .text(label)
                    .appendTo($canIgnoreField);
            });

            // Options cell
            $td = $('<td>')
              .addClass('pp-checklists-task-params')
              .appendTo($tr);

            if (type !== 'openai') {
                $optionsField
                    .attr(
                        'id',
                        '' + post_type + '-checklists-' + id + '_editable_by'
                    )
                    .attr(
                        'name',
                        'publishpress_checklists_checklists_options[' + id + '_editable_by][' + post_type + '][]'
                    )
                    .attr('multiple', 'multiple')
                    .appendTo($td);

                $option = $('<option value=""></option>').appendTo($optionsField);
                $.each(objectL10n_checklists_global_checklist.roles, function (value, label) {
                    $option = $('<option>')
                        .attr('value', value)
                        .text(label)
                        .appendTo($optionsField);
                });

                var $label = $('<p>')
                .addClass('pp-checklists-editable-by-description')
                .text(objectL10n_checklists_global_checklist.editable_by);
                $optionsField.after($label);
            }

            $a = $('<a>')
                .attr('href', 'javascript:void(0);')
                .addClass('pp-checklists-remove-custom-item')
                .attr('title', objectL10n_checklists_global_checklist.remove)
                .attr('data-id', id)
                .attr('data-type', type)
                .appendTo($td);
            $icon = $('<span>')
                .addClass('dashicons dashicons-no')
                .attr('data-id', id)
                .attr('data-type', type)
                .appendTo($a);


            // Re-initialize select 2
            $('#pp-checklists-global select').select2();

            $a.on('click', callback_remove_row);
        }

        /*----------  Custom items  ----------*/
        $('#pp-checklists-add-button').on('click', function (event) {
            var newId = uidGen(15);

            create_row(newId, '', '', get_current_post_type(), 'custom');
        });

        /*----------  OpenAI items  ----------*/
        $('#pp-checklists-openai-promt-button').on('click', function (event) {
            var newId = uidGen(15);

            create_row(newId, '', '', get_current_post_type(), 'openai');
        });
        $(document).on('click', '.pp-custom-suggestion a', function (event) {
            event.preventDefault();
            $(this).closest('td').find('.pp-checklists-custom-item-title').val($(this).data('prompt'));
        });

        $('.pp-checklists-remove-custom-item').on('click', callback_remove_row);

        /*----------  Form validation  ----------*/
        $("#pp-checklists-global").submit(function () {
            var submit_form = true,
                submit_error = '',
                required_rules = objectL10n_checklists_global_checklist.required_rules,
                required_rules_notice = objectL10n_checklists_global_checklist.submit_error,
                custom_task_error_displayed = false;

            //remove previous notice
            $(".checklists-save-notice").remove();

            //select all row
            $(".pp-checklists-requirement-row").each(function () {
                var requirement_id = $(this).attr('data-id');
                var row_requirement_title = $(this).find("td:first-child").text();
                var requirement_rule = $(this).find('#post-checklists-' + requirement_id + '_rule option:selected').val();
                var min_field = $(this).find('#post-checklists-' + requirement_id + '_min');
                var max_field = $(this).find('#post-checklists-' + requirement_id + '_max');

                //check if selected rule require validation and option is Base_counter
                if ($.inArray(requirement_rule, required_rules) !== -1 && (min_field.length > 0 || max_field.length > 0)) {

                    //void submit and add to error if none of min and max field is set
                    if (Number(min_field.val()) === 0 && Number(max_field.val()) === 0) {
                        submit_form = false
                        submit_error += '<div class="alert alert-danger alert-dismissible"><a href="javascript:void(0);" class="close">×</a>' + required_rules_notice + ' "<strong>' + row_requirement_title + '</strong>"' + '.</div>';
                    }
                }
            });

            $('.pp-checklists-custom-item-title').each(function () {
                if ($(this).val().trim() === '' && !custom_task_error_displayed) {
                    submit_form = false;
                    submit_error += '<div class="alert alert-danger alert-dismissible"><a href="javascript:void(0);" class="close">×</a> ' + objectL10n_checklists_global_checklist.custom_item_error + '</div>';
                    custom_task_error_displayed = true;
                }
            });

            if (!submit_form) {
                $("#pp-checklists-global #submit").before('<div class="checklists-save-notice">' + submit_error + '</div>');
            }

            return submit_form;
        });

        // Remove current notice on dismiss
        $(document).on('click', '#pp-checklists-global .checklists-save-notice .close', function (event) {
            event.preventDefault();
            //remove whole current notice
            $(this).parent('.alert-dismissible').remove();
        });

        // Remove notice on any number input changed
        $(document).on('change input paste', '.pp-checklists-number', function () {
            //remove previous notice
            $(".checklists-save-notice").remove();
        });

    });

    function uidGen (len) {
        var text = ' ',
            charset = 'abcdefghijklmnopqrstuvwxyz';

        for (var i = 0; i < len; i++) {
            text += charset.charAt(Math.floor(Math.random() * charset.length));
        }

        return text.trim();
    }

})(jQuery, objectL10n_checklists_global_checklist);
