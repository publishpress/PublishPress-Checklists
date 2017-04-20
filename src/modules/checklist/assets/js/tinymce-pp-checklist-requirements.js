/*====================================
=            Min Word Count          =
=====================================*/
// Based on the TinyMCE word count display found at /wp-admin/js/post.js
( function( $, counter, tinymce ) {
	"use strict";

	var editor = tinyMCE.editors['content'];

	editor.onInit.add( function() {
		var $content = $( '#content' ),
			$status  = $( '#pp-checklist-req-min_word_count' ).find( '.dashicons' ),
			prevCount = 0,
			contentEditor;

		/**
		 * Get the word count from TinyMCE and update the status of the requirement
		 */
		function update() {
			var text, count;

			if ( ! contentEditor || contentEditor.isHidden() ) {
				text = $content.val();
			} else {
				text = contentEditor.getContent( { format: 'raw' } );
			}

			count = counter.count( text );

			if ( count !== prevCount ) {
				// Compare the count with the configured value
				if ( count >= objectL10n_checklist_req_min_words.req_min_words_count ) {
					// Ok
					$status.removeClass('dashicons-no');
					$status.addClass('dashicons-yes');
				} else {
					// Not ok
					$status.removeClass('dashicons-yes');
					$status.addClass('dashicons-no');
				}
			}

			prevCount = count;
		}

		/**
		 * Bind the word count update triggers.
		 *
		 * When a node change in the main TinyMCE editor has been triggered.
		 * When a key has been released in the plain text content editor.
		 */

		if ( editor.id !== 'content' ) {
			return;
		}

		contentEditor = editor;

		editor.on( 'nodechange keyup', _.debounce( update, 1000 ) );
		$content.on( 'input keyup', _.debounce( update, 1000 ) );

		update();
	} );
} )( jQuery, new wp.utils.WordCounter(), tinymce );
/*====  End of Min Word Count  ====*/