/**
 * @package PublishPress
 * @author PublishPress
 *
 * Copyright (C) 2020 PublishPress
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

(function ($, window, document, PP_Checklists, PPCH_WooCommerce) {
    'use strict';

    $(function () {

        function slugHasOnlyValidChars(text) {
            if (text.trim() === '') {
                return false;
            }

            var exp = /[^a-z0-9_\-]+/g;

            return !exp.test(text);
        }

        /**
         *
         * Post slug
         *
         */
        if ($('#pp-checklists-req-permalink_valid_chars').length > 0) {
            $(document).on(PP_Checklists.EVENT_TIC, function (event) {
                var slug = '',
                    slugHasValidChars = true;

                if (PP_Checklists.is_gutenberg_active()) {
                    var slugField = jQuery('.editor-post-permalink-editor__edit');

                    if (slugField.length > 0) {
                        slug = slugField.val();
                    } else {
                        var editor = PP_Checklists.getEditor(),
                            edits = editor.getPostEdits();

                        if (typeof edits.slug !== 'undefined') {
                            slug = edits.slug;
                        } else {
                            slug = editor.getCurrentPost().slug;

                            if (slug === '') {
                                if (typeof edits.title !== 'undefined') {
                                    slug = edits.title.replace(/[\s!\?]/g, '').toLocaleLowerCase();
                                }
                            }
                        }
                    }
                } else {
                    slug = $('#editable-post-name input').val();

                    if (typeof slug === 'undefined' || slug === '') {
                        slug = $('#edit-slug-box #editable-post-name-full').text();
                    }

                    if (typeof slug === 'undefined' || slug === '') {
                        /* Only for the title, we ignore some chars like space, !, ? knowing those will be automatically
                         * replaced by WP when creating the slug from the title. This makes it more intuitive while
                         * adding a title for a new post without marking the permalink as invalid before it is really
                         * created by WP.
                         */
                        slug = $('#post-body #titlewrap input').val().replace(/[\s!\?]/g, '').toLocaleLowerCase();
                    }
                }

                if (typeof slug !== "undefined") {
                    slugHasValidChars = slugHasOnlyValidChars(slug);
                } else {
                    slugHasValidChars = false;
                }

                $('#pp-checklists-req-permalink_valid_chars').trigger(
                    PP_Checklists.EVENT_UPDATE_REQUIREMENT_STATE,
                    slugHasValidChars
                );
            });
        }
    });

})(jQuery, window, document, PP_Checklists);
