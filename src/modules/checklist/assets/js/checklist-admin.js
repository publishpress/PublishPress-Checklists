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

( function ($) {
	"use strict";

	var is_submit = false;

	$( '#publish' ).click( function() {
		is_submit = true;
	} );

	// Hook to the submit button
	$( 'form#post' ).submit( function( e ) {

		// Check if any of the requirements is set to trigger warnings
		var $requirements_warn = $( '.pp-checklist-req.warn' ),
			$requirements_block = $( '.pp-checklist-req.block' ),
			should_block = false,
			$unchecked_req,
			unchecked_warn = [],
			unchecked_block = [];

		for ( var i = 0; i < $requirements_warn.length; i++ ) {
			var item = $requirements_warn[ i ];

			if ( is_submit ) {
				// Check if the requirement is not ok
				$unchecked_req = $( item ).find( '.status-no' );

				if ( $unchecked_req.length > 0 ) {
					unchecked_warn.push( $unchecked_req.html() );
				}
			}

			is_submit = false;
		}

		// Check if any of the requirements is set to block the submission
		for ( var i = 0; i < $requirements_block.length; i++ ) {
			var item = $requirements_block[ i ];

			if ( is_submit ) {
				// Check if the requirement is not ok
				$unchecked_req = $( item ).find( '.status-no' );

				if ( $unchecked_req.length > 0 ) {
					unchecked_block.push( $unchecked_req.html() );
				}
			}

			is_submit = false;
		}

		// Check if we have warnings to display
		if ( unchecked_warn.length > 0 || unchecked_block.length > 0 ) {
			var message = '';

			// Check if we don't have any unchecked block req
			if ( 0 === unchecked_block.length ) {
				// Only display a warning
				message = objectL10n_checklist_req_min_words.msg_missed_optional + '\n\n - ' + unchecked_warn.join('\n - ');

				should_block = ! confirm( message );
			} else {
				message = objectL10n_checklist_req_min_words.msg_missed_required + '\n\n - ' + unchecked_block.join('\n - ');

				if ( unchecked_warn.length > 0 ) {
					message += '\n\n' + objectL10n_checklist_req_min_words.msg_missed_important + '\n\n - ' + unchecked_warn.join('\n - ');
				}

				alert( message );
				should_block = true;
			}
		}

		// Check if we should block the submission
		if ( should_block ) {
			return false;
		}
	} );
} )( jQuery );