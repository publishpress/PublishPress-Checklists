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

( function ( $, objectL10n_checklist_admin ) {
	"use strict";

	$( function() {
		// Minor fix to the style
		// Sticks the requirement settings table to the left
		$( 'table.pp-checklist-requirements-settings' ).parent().prev().hide();

		show_post_type_requirements( objectL10n_checklist_admin.first_post_type );

		// Set the event for the post type filter
		$( '#pp-checklist-post-type-filter a' ).on( 'click', function( event ) {
			event.preventDefault();

			var $target = $( event.toElement || event.target ),
				post_type = $target.attr( 'href' ).substring(1);

			show_post_type_requirements( post_type );
		} );

		// Set the mask for settings fields
		$( '.pp-checklist-number' ).on( 'keypress', function( event ) {
			var key = event.keyCode || event.which;
			var allowed_keys = [
				35, 36, 37, 38, 39, 40, // arrows
				8, 9, 46, 27, 13, // backspace, tab, delete, esc, enter
				48, 49, 50, 51, 52, 53, 54, 55, 56, 57 // 0-9
			];

			// Ignore any key different than number
			if ( allowed_keys.indexOf( key ) < 0 ) {
				event.preventDefault();

				return false;
			}

			return true;
		} );

		/**
		 * Show the requirements for the specific post type and hide all the
		 * others.
		 *
		 * @param  {string} post_type
		 */
		function show_post_type_requirements( post_type ) {
			// Hide the requirements which are not for the current post type
			$( '#pp-checklist-requirements tr.pp-checklist-requirement-row' ).hide();
			// Display the correct requirements
			$( '#pp-checklist-requirements tr[data-post-type="' + post_type + '"]' ).show();
			// Mark the filter as selected
			$( '#pp-checklist-post-type-filter a.pp-selected' ).removeClass( 'pp-selected' );
			$( '#pp-checklist-post-type-filter a[href=#' + post_type + ']' ).addClass( 'pp-selected' );
		}

		/**
		 * Returns the current post type, selected by the filter.
		 *
		 * @return string
		 */
		function get_current_post_type() {
			var post_type = $( '#pp-checklist-post-type-filter a.pp-selected' ).attr( 'href' ).substring( 1 );

			if ( post_type === '' || post_type === false || post_type === null || typeof post_type === undefined ) {
				post_type = objectL10n_checklist_admin.first_post_type;
			}

			return post_type;
		}

		/**
		 * Method to remove custom item fromt the requirements list, identified
		 * by the temporary ID/
		 *
		 * @param  {string} id
		 */
		function remove_row( id ) {
			// Add a special hidden input to flag the delete action
			var $input = $( '<input type="hidden" />')
				.attr( 'name', 'publishpress_checklist_options[custom_items_remove][]' )
				.val( id )
				.appendTo( $( '#pp-checklist-requirements' ) );

			$( 'tr[data-id="' + id + '"]' ).remove();
		}

		/**
		 * Callback for events where we want to trigger
		 * a remove row action
		 *
		 * @param  {Event} event
		 */
		function callback_remove_row( event ) {
			var $target = $( event.target );

			remove_row(  $target.data( 'id' ) );
		}

		/**
		 * Create a row inside the requirements table
		 *
		 * @param  {string} title
		 * @param  {string} action
		 *
		 * @return {Element}
		 */
		function create_row( id, title, action, post_type ) {
			var $table        = $( '#pp-checklist-requirements' ),
				$tr           = $( '<tr>' ),
				$td           = null,
				$titleField   = $( '<input type="text" />' ),
				$idField    = $( '<input type="hidden" />' ),
				$actionField  = $( '<select>' ),
				$option,
				$a,
				$icon,
				rule;

			$table.append( $tr );

			$tr.addClass( 'pp-checklist-requirement-row' )
				.attr( 'data-id', id )
				.attr( 'data-post-type', post_type );

			// Title cell
			$td = $( '<td>' ).appendTo( $tr );
			$titleField
				.attr(
					'name',
					'publishpress_checklist_options[' + id + '_title][' + post_type + ']'
				)
				.val( title )
				.addClass( 'pp-checklist-custom-item-title' )
				.focus()
				.attr( 'data-id', id )
				.appendTo( $td );

			// id fields
			$idField
				.attr(
					'name',
					'publishpress_checklist_options[custom_items][]'
				)
				.val( id )
				.appendTo( $td );

			// Action cell
			$td = $( '<td>' ).appendTo( $tr );
			$actionField
				.attr(
					'name',
					'publishpress_checklist_options[' + id + '_rule][' + post_type + ']'
				)
				.attr( 'data-id', id )
				.appendTo( $td );

			$.each( objectL10n_checklist_admin.rules, function ( value, label ) {
				$option = $( '<option>' )
					.attr( 'value', value )
					.text( label )
					.appendTo( $actionField );
			} );

			// Params cell
			$td   = $( '<td>' )
				.attr( 'data-id', id )
				.appendTo( $tr );
			$a    = $( '<a>' )
				.attr( 'href', 'javascript:void(0);' )
				.addClass( 'pp-checklist-remove-custom-item' )
				.attr( 'data-id', id )
				.appendTo( $td );
			$icon = $( '<span>' )
				.addClass( 'dashicons dashicons-trash' )
				.attr( 'data-id', id )
				.appendTo( $a );

			$a.on( 'click', callback_remove_row );
		}

		/*----------  Custom items  ----------*/
		$( '#pp-checklist-add-button' ).on( 'click', function( event ) {
			var newId = uidGen( 10 );

			create_row( newId, '', '', get_current_post_type() );
		} );

		$( '.pp-checklist-remove-custom-item' ).on( 'click', callback_remove_row );
	} );

	function uidGen(len)
	{
	    var text = " ",
	    	charset = "abcdefghijklmnopqrstuvwxyz0123456789";

	    for( var i=0; i < len; i++ )
	        text += charset.charAt( Math.floor( Math.random() * charset.length ) );

	    return text;
	}

} )( jQuery, objectL10n_checklist_admin );
