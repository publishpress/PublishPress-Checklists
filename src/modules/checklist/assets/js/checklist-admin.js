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

	var is_publishing = false,
		is_confirmed  = false;

	$( '#publish' ).click( function() {
		is_publishing = true;
	} );

	// Adds event for the confimation button in the modal window
	$( document ).on( 'confirmation', '.remodal', function () {
		is_confirmed = true;

		// Trigger the publish button
		$( '#publish' ).trigger( 'click' );
	} );

	// Hook to the submit button
	$( 'form#post' ).submit( function( e ) {

		// Bypass all checks because the confirmation button was clicked.
		if ( is_confirmed ) {
			is_confirmed = false;

			return true;
		}

		// Check if any of the requirements is set to trigger warnings
		var $requirements_warn  = $( '.pp-checklist-req.warn' ),
			$requirements_block = $( '.pp-checklist-req.block' ),
			should_block        = false,
			$unchecked_req,
			unchecked_warn      = [],
			unchecked_block     = [];

		for ( var i = 0; i < $requirements_warn.length; i++ ) {
			var $item = $( $requirements_warn[ i ] );

			if ( is_publishing && $item.hasClass( 'status-no' ) ) {
				// Check if the requirement is not ok
				$unchecked_req = $item.find( '.status-label' );

				if ( $unchecked_req.length > 0 ) {
					unchecked_warn.push( $unchecked_req.html().trim() );
				}
			}
		}

		// Check if any of the requirements is set to block the submission
		for ( var i = 0; i < $requirements_block.length; i++ ) {
			var $item = $( $requirements_block[ i ] );

			if ( is_publishing && $item.hasClass( 'status-no' ) ) {
				// Check if the requirement is not ok
				$unchecked_req = $item.find( '.status-label' );

				if ( $unchecked_req.length > 0 ) {
					unchecked_block.push( $unchecked_req.html().trim() );
				}
			}
		}

		// Check if we have warnings to display
		if ( unchecked_warn.length > 0 || unchecked_block.length > 0 ) {
			var message = '';

			// Check if we don't have any unchecked block req
			if ( 0 === unchecked_block.length ) {
				// Only display a warning
				message = objectL10n_checklist_req_min_words.msg_missed_optional + '<div class="pp-checklist-modal-list"><ul><li>' + unchecked_warn.join('</li><li>') + '</li></ul></div>';

				// Display the confirm
				jQuery( '#pp-checklist-modal-confirm-content' ).html(message);
				jQuery( '[data-remodal-id=pp-checklist-modal-confirm]' ).remodal().open();
			} else {
				message = objectL10n_checklist_req_min_words.msg_missed_required + '<div class="pp-checklist-modal-list"><ul><li>' + unchecked_block.join('</li><li>') + '</li></ul></div>';

				if ( unchecked_warn.length > 0 ) {
					message += '' + objectL10n_checklist_req_min_words.msg_missed_important + '<div class="pp-checklist-modal-list"><ul><li>' + unchecked_warn.join('</li><li>') + '</li></ul></div>';
				}

				// Display the alert
				jQuery( '#pp-checklist-modal-alert-content' ).html(message);
				jQuery( '[data-remodal-id=pp-checklist-modal-alert]' ).remodal().open();
			}

			should_block = true;
		}

		is_publishing = false;

		return ! should_block;
	} );

	// Add constant check for the featured image
	if ( $( '#pp-checklist-req-featured_image' ).length > 0 ) {
		setInterval( function() {
			var has_image = $( '#postimagediv' ).find( '#set-post-thumbnail' ).find( 'img' ).length > 0,
				$status   = $( '#pp-checklist-req-featured_image' ).find( '.dashicons' )

			if ( has_image ) {
				// Ok
				$status.removeClass('dashicons-no');
				$status.addClass('dashicons-yes');
				$status.parent().removeClass('status-no');
				$status.parent().addClass('status-yes');
			} else {
				// Not ok
				$status.removeClass('dashicons-yes');
				$status.addClass('dashicons-no');
				$status.parent().removeClass('status-yes');
				$status.parent().addClass('status-no');
			}
		}, 500);
	}

	// Add constant check for the tags count
	if ( $( '#pp-checklist-req-min_tags_count' ).length > 0 ) {
		setInterval( function() {
			var has_min_tags = $( '.tagchecklist' ).children( 'span' ).length >= objectL10n_checklist_req_min_words.requirements.min_tags_count.value,
				$status = $( '#pp-checklist-req-min_tags_count' ).find( '.dashicons' )

			if ( has_min_tags ) {
				// Ok
				$status.removeClass('dashicons-no');
				$status.addClass('dashicons-yes');
				$status.parent().removeClass('status-no');
				$status.parent().addClass('status-yes');
			} else {
				// Not ok
				$status.removeClass('dashicons-yes');
				$status.addClass('dashicons-no');
				$status.parent().removeClass('status-yes');
				$status.parent().addClass('status-no');
			}
		}, 500);
	}
} )( jQuery );