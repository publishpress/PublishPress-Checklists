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

		// Set the mask for settings fields
		$( '.pp-checklist-number' ).on( 'keypress', function( event ) {
			// Ignore any key different than number
			if ( event.keyCode < 48 || event.keyCode > 57 ) {
				event.preventDefault();

				return false;
			}

			return true;
		} );

		function remove_row( id ) {
			// Add a special hidden input to flag the delete action
			var $input = $( '<input type="hidden" />')
				.attr( 'name', 'publishpress_checklist_options[custom_items_remove][]' )
				.val( id )
				.appendTo( $( '#pp-checklist-requirements' ) );

			$( 'tr[data-id="' + id + '"]' ).remove();
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

			$tr.data( 'id', id );

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
				.data( 'id', id )
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
				.data( 'id', id )
				.appendTo( $td );

			$.each( objectL10n_checklist_admin.rules, function ( value, label ) {
				$option = $( '<option>' )
					.attr( 'value', value )
					.text( label )
					.appendTo( $actionField );
			} );

			// Params cell
			$td   = $( '<td>' )
				.data( 'id', id )
				.appendTo( $tr );
			$a    = $( '<a>' )
				.attr( 'href', 'javascript:void(0);' )
				.addClass( 'pp-checklist-remove-custom-item' )
				.data( 'id', id )
				.appendTo( $td );
			$icon = $( '<span>' )
				.addClass( 'dashicons dashicons-trash' )
				.data( 'id', id )
				.appendTo( $a );

			$a.on( 'click', function( event ) {
				remove_row( id );
			} );
		}

		/*----------  Custom items  ----------*/
		$( '#pp-checklist-add-button' ).on( 'click', function( event ) {
			var newId = Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 6);

			create_row( newId, '', '', objectL10n_checklist_admin.post_type );
		} );

		$( '.pp-checklist-remove-custom-item' ).on( 'click', function( event ) {
			var target = $( event.target );

			remove_row(  target.data( 'id' ) );
		} );
	} );
} )( jQuery, objectL10n_checklist_admin );
