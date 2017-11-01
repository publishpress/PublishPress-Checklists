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

/*====================================
=            Min Words Count          =
=====================================*/
// Based on the TinyMCE words count display found at /wp-admin/js/post.js
( function( $, counter, tinymce, _ ) {
	"use strict";

	if ( 'undefined' === typeof objectL10n_checklist_requirements ) {
		return true;
	}

	if ( 'undefined' === typeof objectL10n_checklist_requirements.requirements.words_count ) {
		return true;
	}

	var editor = tinyMCE.editors['content'];

	editor.onInit.add( function() {
		var $content      = $( '#content' ),
			prev_count    = 0,
			content_editor;

		/**
		 * Get the words count from TinyMCE and update the status of the requirement
		 */
		function update() {
			var text, count;

			if ( ! content_editor || content_editor.isHidden() ) {
				text = $content.val();
			} else {
				text = content_editor.getContent( { format: 'raw' } );
			}

			count = counter.count( text );

			if ( count !== prev_count ) {
				var is_valid = false,
					min = parseInt(objectL10n_checklist_requirements.requirements.words_count.value[0]),
					max = parseInt(objectL10n_checklist_requirements.requirements.words_count.value[1]);

				// Compare the count with the configured value

				// Both same value = exact
				if ( min === max ) {
					is_valid = count === min;
				}

				// Min not empty, max empty or < min = only min
				if ( min > 0 && ( max === 0 || max < min ) ) {
					is_valid = count >= min;
				}

				// Min not empty, max not empty and > min = both min and max
				if ( min > 0 && max > 0 && max > min ) {
					is_valid = count >= min && count <= max;
				}

				// Min empty, max not empty and > min = only max
				if ( min === 0 && max > 0 && max > min ) {
					is_valid = count <= max;
				}

				( count >= objectL10n_checklist_requirements.requirements.words_count.value[0] )
					&& ( count <= objectL10n_checklist_requirements.requirements.words_count.value[1] );

				$( '#pp-checklist-req-words_count' ).trigger(
					PP_Content_Checklist.EVENT_UPDATE_REQUIREMENT_STATE,
					is_valid
				);
			}

			prev_count = count;
		}

		/**
		 * Bind the words count update triggers.
		 *
		 * When a node change in the main TinyMCE editor has been triggered.
		 * When a key has been released in the plain text content editor.
		 */

		if ( editor.id !== 'content' ) {
			return;
		}

		content_editor = editor;

		editor.on( 'nodechange keyup', _.debounce( update, 500 ) );
		$content.on( 'input keyup', _.debounce( update, 500 ) );

		update();
	} );
} )( jQuery, new wp.utils.WordCounter(), tinymce, _ );
/*====  End of Min Words Count  ====*/